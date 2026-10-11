# Five-minute AIX vmstat rendering

The historical `2010/month/MM/HOST/YYYYMMDD_05_HOST_aixbase.sbstats` files contain a space-separated time and serial number followed by comma-separated vmstat data. The original 2010 report configuration selected indices 14/System, 13/User, 16/I/O Wait, 6/Page Out and 5/Page In.

The modern PHP 8 GD renderer `scripts/vmstat-graph.php?file=2010/month/09/viod09/20100911_05_viod09_aixbase.sbstats` parses the named header fields, builds one bar per five-minute slot, stacks CPU user/system/wait percentages (0–100), and overlays page-in/page-out spikes using a **separate right-side scale** (maximum observed pages per five-minute sample). This is intentionally a dual-scale visual diagnostic, not a claim that paging and CPU units are comparable.

Visit `/2010/month/09/viod09/` on port 8001. Each visible .sbstats file links to and previews its PNG visualization, without modifying the original input. The graph is produced on request by GD, not saved in the data directory.

The filename is checked to remain within `2010/`. The parser requires the expected CPU columns and leaves missing five-minute slots blank. This replaces neither the legacy PHP renderer nor the sample Solana PNG generator.

Start with `bash scripts/php-serve.sh` and ensure `php -m | grep -i gd` produces `gd`. If GD is missing, install `php-gd`.
