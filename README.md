sbstats
=======

A NoSQL charting solution based on GD Graphics.

Modern example: [ISO-week Solana charts and viewer](examples/solana/README.md). The legacy PHP/GD implementation remains unchanged.

Solana token discovery: [registry](examples/solana/tokens.json), [read-only collector](tools/collect_solana_market.py), and [usage / limitations](docs/solana-token-discovery.md).

Quick start from the repository root:

```sh
python3 tools/collect_solana_market.py --mode discover
```

This discovers paid-boost candidates; it is not a top-ten volume ranking. To collect market snapshots for a token, verify its exact mint and set its registry status to `verified` first.

Rank saved boosted candidates with Python:

```sh
python3 tools/rank_solana_candidates.py examples/solana/market/discovery/YYYY-MM-DD.json
```

Explore results in [the static candidate viewer](examples/solana/candidates.html). No Deno or Node.js needed.

## ETHDenver 2027 coordination

See [the project-specific ETHDenver / Zorkmid notes](docs/ethdenver-participation-metrics.md) for the proposed physical dartboard demonstration, conference materials, optional Zorkmid/ Base integration, and delivery gates. These are plans, not claims that a token, venue, or showcase is live.
