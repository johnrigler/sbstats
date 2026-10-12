# SB Stats daily journal and image archive

The historical SB Stats model remains: a five-minute cron job appends measurements. Code deployment is manual (git pull over SSH). This new optional tool is **not wired into cron or the current Solana collector**, and does not install a daemon.

## File lifecycle

- `YYYY-MM-DD.jsonl` is the UTC daily, append-only operational journal; each line is one independent `sbstats/journal-v1` JSON observation with a `source` and `observed_at_utc`.
- `YYYY-MM-DD.overlay.json` is an optional, independently authored `sbstats/overlay-v1` object with an `entries` array. It holds annotations and assertions; distinguish source evidence, manual notes and derived interpretations.
- `YYYY-MM-DD.png` is the derived chart **and** a portable data archive. Its compressed `sbstats.json` iTXt entry contains the complete journal observations and overlay at render time, with an SHA-256 digest of the canonicalized observations.

No separate processed intermediate is needed. The PNG can be regenerated from the journal and overlay while a day is still live. This is a proof-of-format general chart (numeric `value` series if compatible, otherwise observation counts by hour), not a replacement for the existing metric-specific weekly charts.

### Manual smoke test

Requires Python 3 and Pillow for rendering (`python3 -m pip install Pillow`). Journal appends themselves use only the standard library.

```sh
python3 tools/daily_archive.py append /tmp/sbstats/2026-10-11.jsonl --json '{"observed_at_utc":"2026-10-11T00:00:00Z","source":"test","subject":"SOL","metric":"example","value":10}'
python3 tools/daily_archive.py append /tmp/sbstats/2026-10-11.jsonl --json '{"observed_at_utc":"2026-10-11T00:05:00Z","source":"test","subject":"SOL","metric":"example","value":11}'
python3 tools/daily_archive.py render /tmp/sbstats/2026-10-11.jsonl /tmp/sbstats/2026-10-11.png
python3 tools/daily_archive.py verify /tmp/sbstats/2026-10-11.png --journal /tmp/sbstats/2026-10-11.jsonl
python3 tools/daily_archive.py extract /tmp/sbstats/2026-10-11.png
```

Example optional overlay:

```json
{"schema":"sbstats/overlay-v1","entries":[{"time":"2026-10-11T00:05:00Z","kind":"note","label":"Example only","source":"manual"}]}
```

Render with `--overlay /path/to/2026-10-11.overlay.json` and pass the same overlay to `verify` when comparing source files.

### End-of-day finalization

After a day has ended **in UTC**:

```sh
python3 tools/daily_archive.py finalize /path/2026-10-11.jsonl /path/2026-10-11.png --overlay /path/2026-10-11.overlay.json
```

By default, finalization does **not** remove the journal. Add `--purge-journal` only when the image has been written and independently reopened, embedded observations match the source, and the operator accepts deleting the journal. The overlay is never automatically removed. The PNG is never automatically purged. To save storage later, the operator may explicitly remove a derived image **only while a verified source journal and overlay still exist so it can be regenerated**. Do not remove both representations of an observation. Keep raw evidence separately if the archive contains only summaries.

Publishing uses an atomic replacement of a temporary PNG in the destination directory. Journal appending flushes to disk. Multiple independent writers should be serialized by cron or a file lock; concurrency control and crash-recovery automation are not yet implemented.

### Collector integration still needed

Current `tools/collect_solana_market.py` overwrites daily discovery and snapshot JSON. It has **not** yet been converted to journal append operations. The source fields and rolling 24-hour windows must be preserved when bridging these formats; successive rolling snapshots are not additive daily totals. For multi-metric observations, retain explicit units and use separately labeled metric-specific charts rather than treating heterogeneous values as one numerical series.

Suggested rigler.org layout outside the Git checkout:

```text
~/sbstats/                  # manual Git deployment
~/sbstats-data/live/        # active .jsonl
~/sbstats-data/overlays/    # manual/derived JSON
~/sbstats-data/archive/     # historical PNG files
```

No secrets, wallet signing keys or private API credentials belong in public embedded image metadata.
