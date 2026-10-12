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
