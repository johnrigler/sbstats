# SB Stats / Solana ISO-week example

The existing SB Stats PHP/GD implementation and archived data are unchanged. This new static example uses data derived from the October 11, 2026 second Solana pull for Get Money and Purple Squirrel.

## Run

At the repository root:

```bash
python3 -m pip install Pillow
python3 tools/render_sol_stats.py
python3 tools/render_sol_stats.py --read examples/solana/2026/W41/get-money.png
python3 -m http.server 8000
```

Open `http://localhost:8000/examples/solana/` for previous/next ISO-week navigation. The same static directory works on IPFS or other hosting. The Python generator writes PNG graphs and the accompanying weekly JSON files. No Node, RPC key, or database required.

## Structure and guarantees

- `catalog.json` enumerates available ISO weeks; `YYYY/year.json` enumerates weeks in that ISO year; `YYYY/Www/week.json` stores **Monday-to-Sunday UTC** daily observations; `YYYY/Www/commentary.json` stores separate user annotations.
- ISO week 2026-W41 is 2026-10-05 through 2026-10-11. Week 01 contains January 4, so the ISO week-year may differ from the calendar year.
- `config.json` specifies defaults. Configuration can cascade, but old signed statistics should never change silently when a parent config is edited.
- Generated PNG files include a standard iTXt field `sbstats.json` with the exact graph values, provenance, and coverage. The pixels provide a human view. Graphics software can strip PNG metadata.
- Counts are **deduplicated successful transaction signatures involving sampled top-20 token accounts**. They can include transactions unrelated to the target mint. They are *not* complete token transfers, participant counts, or proof of no activity on quiet days.
- The JSON fixture is aggregated sample evidence. The original `pull2.tar` remains the raw evidence and is not included in this repo.
- Monthly and annual statistics should be derived from the daily observations by *calendar dates*, not by assigning full ISO weeks to months.
- Commentary is separate from measurements; signed attestations, provenance, and evidence references can be added later.

The interface is a read-only vanilla-JS demonstration. The next Solana collector should publish mint-filtered transfer histories with atomic token amounts as strings rather than floating-point JSON numbers.
