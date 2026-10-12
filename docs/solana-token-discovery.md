# SB Stats Solana discovery and market snapshots

This feature is additive. It does not alter legacy PHP/GD charts or the existing ISO-week
signature-sample series. No Termux dependency, Node.js, pip package, API key or RPC is needed.

## Run

From the repository root:

```sh
python3 tools/collect_solana_market.py --mode discover
python3 tools/collect_solana_market.py --mode track
```

Discovery writes `examples/solana/market/discovery/YYYY-MM-DD.json`.
It lists **boosted candidates**, not the highest-volume coins or investment recommendations.
Names, symbols, social links, and claims are unverified.

The watchlist is `examples/solana/tokens.json`, with the two historical sample labels and
ten research candidates. Every mint is deliberately null until independently verified.
For tracking, set the exact Solana token mint and `"status": "verified"`. A Telegram
URL, if independently confirmed, can be placed in `telegram`; the collector does **not**
scrape Telegram or claim measured Telegram engagement. Never resolve a mint solely from
a symbol, since duplicates and impersonators exist.

Tracking writes `examples/solana/market/snapshots/YYYY-MM-DD.json`.
It collects per-pool rolling 24-hour reported volumes, trades and liquidity from
DEX Screener. Pool values are not summed or represented as unique participants.
A daily snapshot is **not** the total activity for the corresponding UTC calendar day.
Do not combine this series with `sampled_distinct_signatures` as though the metrics match.

The script overwrites the same day's snapshot when rerun. For immutable historical
collection, commit the daily output once the chosen observation time has passed,
or add an append-only time-stamped observation format later.

Source documentation: https://docs.dexscreener.com/api/reference

## Recommended next work

1. Independently verify exact mints and official Telegram URLs.
2. Add a mint-filtered Solana RPC transaction collector that distinguishes transfers
   from swaps, and records provenance and coverage.
3. Add separate social observation records, including source and collection timestamp.
4. Derive ISO-week displays from actual UTC observations, never from rolling 24h snapshots.

## Rank the discovered candidates (TypeScript)

The optional TypeScript tool analyzes all saved boosted candidates and writes an
activity-based top ten. It uses Deno to execute TypeScript directly; no Node.js,
transpiler or package dependencies. Install Deno separately if needed:

```sh
deno run --allow-read --allow-write --allow-net=api.dexscreener.com \
  tools/rank_solana_candidates.ts \
  examples/solana/market/discovery/YYYY-MM-DD.json
```

Replace `YYYY-MM-DD` with the actual UTC discovery date. Results are saved
to `examples/solana/market/rankings/YYYY-MM-DD.json`. It chooses the
highest-liquidity matching pool for each exact mint, rejects tokens with less than
$100,000 reported liquidity, and scores volume (45%), liquidity (35%), and
transaction count (20%) using log scaling. It does not award points for paid
boosts and does not claim Telegram groups have been verified. The output retains
all successfully retrieved candidates and separates API errors rather than
converting failed requests to zero activity.

These are rolling 24-hour DEX Screener snapshots, not precise UTC-day counts or
wallet counts. This list represents *boosted candidates only*, not a top ten of
every Solana token.
