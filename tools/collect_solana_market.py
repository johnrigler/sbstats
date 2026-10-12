#!/usr/bin/env python3
"""SB Stats read-only Solana DEX snapshots. Python stdlib only; no RPC key.

Discovery: DEX Screener top boosted Solana token identities, not a top volume ranking.
Tracking: exact registry mint addresses only. Tickers never identify a token.
"""
import argparse
import datetime as dt
import json
import pathlib
import urllib.error
import urllib.request

ROOT = pathlib.Path(__file__).resolve().parents[1]
BASE = "https://api.dexscreener.com"
UA = "SBStats/1.0 (public research; read-only)"
def fetch(route):
    request = urllib.request.Request(BASE + route, headers={"User-Agent": UA, "Accept": "application/json"})
    with urllib.request.urlopen(request, timeout=25) as response:
        return json.load(response)

def save(path, value):
    path.parent.mkdir(parents=True, exist_ok=True)
    temp = path.with_suffix(path.suffix + ".tmp")
    temp.write_text(json.dumps(value, indent=2, sort_keys=True) + "\n", encoding="utf-8")
    temp.replace(path)

def valid_mint(mint):
    alphabet = set("123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz")
    return isinstance(mint, str) and 32 <= len(mint) <= 44 and set(mint) <= alphabet

def discover(day, target, max_results):
    rows = fetch("/token-boosts/top/v1")
    seen, candidates = set(), []
    for item in rows:
        address = item.get("tokenAddress")
        if item.get("chainId") != "solana" or not valid_mint(address) or address in seen:
            continue
        seen.add(address)
        candidates.append({
            "mint": address, "claimed_url": item.get("url"), "boost_amount": item.get("amount"),
            "total_boost_amount": item.get("totalAmount"), "links_unverified": item.get("links", [])
        })
        if len(candidates) >= max_results:
            break
    output = {"schema":"sbstats/discovery-v1","date_utc":day,"source":BASE+"/token-boosts/top/v1",
              "selection":"top paid boosts, not top organic trading activity","candidates":candidates}
    save(target / "discovery" / (day + ".json"), output)
    return len(candidates)

def track(day, target, registry):
    entries = json.loads(registry.read_text(encoding="utf-8"))["tokens"]
    results = []
    for item in entries:
        mint = item.get("mint")
        if item.get("chain") != "solana" or not valid_mint(mint) or item.get("status") != "verified":
            continue
        pairs = fetch("/token-pairs/v1/solana/" + mint)
        matched = [p for p in pairs if p.get("chainId") == "solana" and mint in (
            p.get("baseToken", {}).get("address"), p.get("quoteToken", {}).get("address"))]
        # Snapshot each matching pool, not a sum across pools (avoid implied aggregation).
        pools = [{"pair_address":p.get("pairAddress"),"dex":p.get("dexId"),
                  "url":p.get("url"),"liquidity_usd":(p.get("liquidity") or {}).get("usd"),
                  "volume_24h_usd":(p.get("volume") or {}).get("h24"),
                  "txns_24h":(p.get("txns") or {}).get("h24"),
                  "price_change_24h_percent":(p.get("priceChange") or {}).get("h24"),
                  "boosts_active":(p.get("boosts") or {}).get("active")}
                 for p in matched]
        results.append({"id":item["id"],"mint":mint,"pools":pools})
    output = {"schema":"sbstats/dex-snapshot-v1","date_utc":day,
              "collected_at_utc":dt.datetime.now(dt.timezone.utc).isoformat(),
              "source":BASE+"/token-pairs/v1/solana/{mint}",
              "method":"DEX Screener reported rolling 24h pool statistics; NOT on-chain verified transfers or daily UTC totals",
              "tokens":results}
    save(target / "snapshots" / (day + ".json"), output)
    return len(results)

def main():
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument("--mode", choices=("discover","track","both"), default="both")
    p.add_argument("--registry", type=pathlib.Path, default=ROOT/"examples/solana/tokens.json")
    p.add_argument("--output", type=pathlib.Path, default=ROOT/"examples/solana/market")
    p.add_argument("--date", help="UTC observation date YYYY-MM-DD (default today)")
    p.add_argument("--limit", type=int, default=30)
    a = p.parse_args()
    day = a.date or dt.datetime.now(dt.timezone.utc).date().isoformat()
    if dt.date.fromisoformat(day) > dt.datetime.now(dt.timezone.utc).date():
        p.error("future observation dates are not allowed")
    if not 1 <= a.limit <= 100:
        p.error("--limit must be between 1 and 100")
    try:
        if a.mode in ("discover","both"):
            print("Discovered boosted Solana token candidates:",discover(day,a.output,a.limit))
        if a.mode in ("track","both"):
            print("Snapshotted verified token mints:",track(day,a.output,a.registry))
    except (urllib.error.URLError, ValueError, KeyError, TypeError) as exc:
        p.exit(1,"Collection failed: %s\n" % exc)
if __name__ == "__main__":
    main()
