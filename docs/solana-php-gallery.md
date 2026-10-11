# Solana activity PNG gallery

The same SB Stats PHP 8/GD graph-on-first-view approach used for the 2010 AIX vmstat archive now supports `examples/solana/YYYY/Www/`.

A directory's `week.json` is the authoritative aggregated seven-day data. Opening its directory URL automatically creates three cached PNGs next to `week.json`: `get-money-daily.png`, `purple-squirrel-daily.png`, and `activity-comparison.png`. Existing fresh PNGs are reused; deleting a PNG or changing the source `week.json` rebuilds it. Each PNG is displayed large in the directory gallery, linked to the full-size file.

The figures show only **sampled unique top-account signatures per UTC day**, not actual mint-filtered transfers or complete on-chain activity. No five-minute or hourly Solana data is available in the current compact weekly fixtures, so no intraday distribution is invented.

```bash
cd ~/sbstats
git pull
bash scripts/php-serve.sh
# http://127.0.0.1:8001/examples/solana/2024/W26/
```

Requires PHP GD: `php -m | grep -i gd`. A weekly JSON record remains the recoverable data source beside the PNG. The older Python-generated PNGs with embedded metadata are preserved unchanged. This PHP implementation uses normal GD PNG files so it can render directly on a minimal PHP host.

The gallery displays both token series at full width and a combined comparison graph, without requiring a Python invocation.
