#!/usr/bin/env python3
"""Rank SB Stats boosted Solana candidates via DEX Screener. Python stdlib only."""
import argparse
import datetime as dt
import json
import math
import pathlib
import re
import time
import urllib.request
import urllib.error

API = "https://api.dexscreener.com"
MINT = re.compile(r"^[1-9A-HJ-NP-Za-km-z]{32,44}$")
MIN_LIQUIDITY = 100000

def number(value, signed=False):
    if isinstance(value, bool) or not isinstance(value, (int, float)) or not math.isfinite(value):
        return None
    return float(value) if signed or value >= 0 else None

def score_part(value, reference):
    return min(1.0, math.log10(1 + value) / math.log10(1 + reference)) if value is not None else 0.0

def fetch_pairs(mint):
    request = urllib.request.Request(API + "/token-pairs/v1/solana/" + mint,
                                     headers={"User-Agent":"SBStats/1.0","Accept":"application/json"})
    with urllib.request.urlopen(request, timeout=25) as response:
        data = json.load(response)
    if not isinstance(data, list):
        raise ValueError("Expected pool list")
    return [p for p in data if p.get("chainId") == "solana" and mint in
            ((p.get("baseToken") or {}).get("address"), (p.get("quoteToken") or {}).get("address"))]

def classify(candidate, pools):
    mint = candidate["mint"]
    pool = max(pools, key=lambda p: number((p.get("liquidity") or {}).get("usd")) or 0) if pools else None
    pool = pool or {}
    liquidity = number((pool.get("liquidity") or {}).get("usd"))
    volume = number((pool.get("volume") or {}).get("h24"))
    trades = (pool.get("txns") or {}).get("h24") or {}
    buys, sells = number(trades.get("buys")), number(trades.get("sells"))
    transactions = buys + sells if buys is not None and sells is not None else None
    eligible = liquidity is not None and liquidity >= MIN_LIQUIDITY and volume is not None
    score = round(100 * (.45 * score_part(volume, 10000000) +
                         .35 * score_part(liquidity, 5000000) +
                         .20 * score_part(transactions, 10000)), 2) if eligible else 0
    token = pool.get("baseToken") if (pool.get("baseToken") or {}).get("address") == mint else pool.get("quoteToken")
    token = token or {}
    links = candidate.get("links_unverified") or []
    return dict(rank=None, mint=mint, name=token.get("name"), symbol=token.get("symbol"),
                eligible=eligible, score=score, liquidity_usd=liquidity,
                volume_24h_usd=volume, transactions_24h=transactions,
                price_change_24h_percent=number((pool.get("priceChange") or {}).get("h24"), signed=True),
                pair_address=pool.get("pairAddress"), dex=pool.get("dexId"),
                boost_amount=number(candidate.get("boost_amount")),
                total_boost_amount=number(candidate.get("total_boost_amount")),
                telegram_claimed=any(isinstance(x, dict) and "t.me/" in str(x.get("url", "")).lower() for x in links),
                telegram_verified=False, pair_url=pool.get("url"),
                claimed_url=candidate.get("claimed_url"), links_unverified=links,
                reason=("No matching Solana pool" if not pools else
                        "Missing liquidity data" if liquidity is None else
                        "Below liquidity threshold" if liquidity < MIN_LIQUIDITY else
                        "Missing volume data" if volume is None else None))

def rank_file(path, destination=None, delay=.35):
    discovery=json.loads(path.read_text(encoding="utf-8"))
    if discovery.get("schema") != "sbstats/discovery-v1" or not isinstance(discovery.get("candidates"), list):
        raise ValueError("Invalid discovery JSON")
    day=discovery["date_utc"]
    dt.date.fromisoformat(day)
    if destination is None:
        if path.parent.name != "discovery":
            raise ValueError("Provide --output when input is outside a discovery directory")
        destination=path.parent.parent/"rankings"/(day+".json")
    seen=set()
    ranked=[]
    errors=[]
    for candidate in discovery["candidates"]:
        mint=candidate.get("mint")
        if not isinstance(mint, str) or not MINT.fullmatch(mint) or mint in seen:
            continue
        seen.add(mint)
        try:
            ranked.append(classify(candidate,fetch_pairs(mint)))
        except (urllib.error.URLError, ValueError, TypeError, KeyError) as exc:
            errors.append({"mint":mint,"error":str(exc)})
        time.sleep(delay)
    ranked.sort(key=lambda x:(not x["eligible"],-x["score"],x["mint"]))
    rank=0
    for row in ranked:
        if row["eligible"]:
            rank+=1
            row["rank"]=rank
    output={
        "schema":"sbstats/candidate-ranking-v1", "date_utc":day,
        "collected_at_utc":dt.datetime.now(dt.timezone.utc).isoformat(),
        "source":API+"/token-pairs/v1/solana/{mint}", "input":str(path),
        "definition":"Paid-boost discovery ranked by highest-liquidity matching Solana pool",
        "min_liquidity_usd":MIN_LIQUIDITY,
        "scoring":"45% log volume + 35% log liquidity + 20% log trades; no boost or social score",
        "caveats":["Not top Solana tokens overall","Not verified on-chain transfers",
                   "Telegram links are unverified claims","Failed API calls are not scored as zero",
                   "Rolling 24h vendor snapshot, not a UTC daily total"],
        "top_ten":[r["mint"] for r in ranked if r["eligible"]][:10],
        "candidates":ranked,"errors":errors}
    destination.parent.mkdir(parents=True,exist_ok=True)
    temp=destination.with_suffix(".json.tmp")
    temp.write_text(json.dumps(output,indent=2)+"\n",encoding="utf-8")
    temp.replace(destination)
    print("Ranked:",len(ranked),"eligible:",rank,"top ten:",len(output["top_ten"]),"errors:",len(errors))
    print("Wrote:",destination)
    return output

if __name__=="__main__":
    parser=argparse.ArgumentParser(description=__doc__)
    parser.add_argument("discovery",type=pathlib.Path)
    parser.add_argument("--output",type=pathlib.Path)
    args=parser.parse_args()
    try: rank_file(args.discovery,args.output)
    except (OSError,ValueError,KeyError,TypeError) as exc: parser.exit(1,f"Error: {exc}\n")
