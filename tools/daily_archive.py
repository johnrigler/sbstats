#!/usr/bin/env python3
"""SB Stats daily journal -> optional overlays -> self-contained PNG.
Python stdlib for append/inspect; Pillow is needed for render/finalize.
No cron installation, network access, or automatic deletion.
"""
import argparse
import datetime as dt
import hashlib
import json
import os
from pathlib import Path
import tempfile

SCHEMA = "sbstats/journal-v1"
ARCHIVE = "sbstats/archive-v1"

def canonical(value):
    return json.dumps(value, sort_keys=True, separators=(",", ":"), ensure_ascii=False).encode("utf-8")

def digest(value):
    return hashlib.sha256(canonical(value)).hexdigest()

def observations(path):
    items = []
    with Path(path).open(encoding="utf-8") as f:
        for line_number, line in enumerate(f, 1):
            if not line.strip():
                continue
            try:
                entry = json.loads(line)
            except json.JSONDecodeError as exc:
                raise ValueError(f"Invalid journal line {line_number}: {exc}") from exc
            if entry.get("schema") != SCHEMA or not isinstance(entry.get("observed_at_utc"), str):
                raise ValueError(f"Invalid journal schema/time at line {line_number}")
            items.append(entry)
    if not items:
        raise ValueError("Journal has no observations")
    return items

def read_overlay(path):
    if not path:
        return {"schema": "sbstats/overlay-v1", "entries": []}
    obj = json.loads(Path(path).read_text(encoding="utf-8"))
    if obj.get("schema") != "sbstats/overlay-v1" or not isinstance(obj.get("entries"), list):
        raise ValueError("Overlay must contain schema sbstats/overlay-v1 and entries array")
    return obj

def append(path, source):
    item = json.loads(source)
    if not isinstance(item, dict) or not item.get("source"):
        raise ValueError("Observation must be a JSON object containing source")
    timestamp = item.get("observed_at_utc") or dt.datetime.now(dt.timezone.utc).isoformat().replace("+00:00", "Z")
    parsed = dt.datetime.fromisoformat(timestamp.replace("Z", "+00:00"))
    if parsed.tzinfo is None or parsed.utcoffset() != dt.timedelta(0):
        raise ValueError("observed_at_utc must be UTC")
    day = parsed.date().isoformat()
    if Path(path).name != day + ".jsonl":
        raise ValueError("Journal filename must match observation UTC date: YYYY-MM-DD.jsonl")
    item["schema"] = SCHEMA
    item["observed_at_utc"] = timestamp
    file = Path(path)
    file.parent.mkdir(parents=True, exist_ok=True)
    with file.open("a", encoding="utf-8") as f:
        f.write(canonical(item).decode("utf-8") + "\n")
        f.flush()
        os.fsync(f.fileno())
    return item

def payload(path, overlay):
    items = observations(path)
    day = Path(path).stem
    for item in items:
        when = dt.datetime.fromisoformat(item["observed_at_utc"].replace("Z", "+00:00"))
        if when.tzinfo is None or when.utcoffset() != dt.timedelta(0) or when.date().isoformat() != day:
            raise ValueError("Journal contains an observation outside its UTC day")
    return {"schema": ARCHIVE, "date_utc": day, "observations": items,
            "overlay": read_overlay(overlay), "journal_sha256": digest(items)}

def render(data, destination):
    from PIL import Image, ImageDraw, PngImagePlugin
    width, height = 1000, 410
    img = Image.new("RGB", (width, height), "#f7f9fc")
    draw = ImageDraw.Draw(img)
    draw.text((30, 25), "SB Stats / " + data["date_utc"], fill="#102f4a")
    draw.text((30, 56), str(len(data["observations"])) + " observations (source journal embedded)", fill="#102f4a")
    # Plot observed numeric 'value' records, if supplied. Otherwise use per-hour counts.
    numeric = [(x["observed_at_utc"], x["value"]) for x in data["observations"]
               if type(x.get("value")) in (int, float) and abs(x["value"]) < 1e100]
    if len(numeric) >= 2:
        vals = [v for _, v in numeric]
        lo, hi = min(vals), max(vals)
        if hi == lo: hi = lo + 1
        points = [(60 + int(i * 890 / (len(vals) - 1)), 340 - int((v - lo) * 235 / (hi - lo)))
                  for i, v in enumerate(vals)]
        draw.line(points, fill="#28688d", width=3)
        draw.text((35, 365), f"Numeric value series / range {lo:g} to {hi:g}", fill="#284960")
    else:
        counts = [0] * 24
        for entry in data["observations"]:
            counts[int(entry["observed_at_utc"][11:13])] += 1
        maximum = max(counts) or 1
        for hour, count in enumerate(counts):
            x = 40 + hour * 39
            draw.rectangle((x, 340 - int(count * 230 / maximum), x + 23, 340), fill="#28688d")
        draw.text((35, 365), "Observations per UTC hour (no comparable numeric value series)", fill="#284960")
    meta = PngImagePlugin.PngInfo()
    meta.add_itxt("sbstats.json", canonical(data).decode("utf-8"), zip=True)
    destination = Path(destination)
    destination.parent.mkdir(parents=True, exist_ok=True)
    fd, temp = tempfile.mkstemp(prefix="." + destination.name + ".", suffix=".png", dir=destination.parent)
    os.close(fd)
    try:
        img.save(temp, pnginfo=meta, optimize=True)
        with Image.open(temp) as check:
            recovered = json.loads(check.info["sbstats.json"])
        if recovered != data:
            raise ValueError("PNG metadata round-trip mismatch")
        os.replace(temp, destination)
    finally:
        if os.path.exists(temp):
            os.unlink(temp)
    return destination

def verify(image, source=None, overlay=None):
    from PIL import Image
    with Image.open(image) as im:
        data = json.loads(im.info["sbstats.json"])
    if data.get("schema") != ARCHIVE or digest(data.get("observations")) != data.get("journal_sha256"):
        raise ValueError("Embedded observation checksum mismatch")
    if source and payload(source, overlay) != data:
        raise ValueError("PNG metadata does not match journal and overlay")
    return data

def main():
    ap = argparse.ArgumentParser(description=__doc__)
    sub = ap.add_subparsers(dest="action", required=True)
    a = sub.add_parser("append")
    a.add_argument("journal")
    a.add_argument("--json", required=True, help="One JSON observation; must contain source")
    for action in ("render", "finalize"):
        p = sub.add_parser(action)
        p.add_argument("journal")
        p.add_argument("png")
        p.add_argument("--overlay")
        if action == "finalize":
            p.add_argument("--purge-journal", action="store_true", help="Delete journal only after verified PNG")
    v = sub.add_parser("verify")
    v.add_argument("png")
    v.add_argument("--journal")
    v.add_argument("--overlay")
    e = sub.add_parser("extract")
    e.add_argument("png")
    args = ap.parse_args()
    try:
        if args.action == "append":
            result = append(args.journal, args.json)
        elif args.action in ("render", "finalize"):
            source = Path(args.journal)
            record = payload(source, args.overlay)
            # Do not finalize an incomplete UTC day.
            if args.action == "finalize" and record["date_utc"] >= dt.datetime.now(dt.timezone.utc).date().isoformat():
                raise ValueError("Cannot finalize a current or future UTC day")
            render(record, args.png)
            verify(args.png, source, args.overlay)
            if args.action == "finalize" and args.purge_journal:
                source.unlink()
            result = {"png": args.png, "observations": len(record["observations"]),
                      "journal_purged": bool(args.action == "finalize" and args.purge_journal)}
        elif args.action == "verify":
            record = verify(args.png, args.journal, args.overlay)
            result = {"verified": True, "date_utc": record["date_utc"],
                      "observations": len(record["observations"])}
        else:
            result = verify(args.png)
        print(json.dumps(result, indent=2, ensure_ascii=False))
    except (OSError, ValueError, KeyError, TypeError) as exc:
        ap.exit(1, f"SB Stats archive error: {exc}\n")

if __name__ == "__main__":
    main()
