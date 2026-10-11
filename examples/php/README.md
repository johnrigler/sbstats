# PHP option for SB Stats

SB Stats began as a PHP/GD graphics system, and the original `2013/` code remains archived here.

This optional Bash launcher starts PHP's own development server, serving both modern static resources and a small PHP-as-enriched-HTML demonstration from a single directory. No Apache, Nginx, Python server or framework is required.

```bash
# From the repo root, install PHP CLI + GD only if needed:
bash scripts/php-serve.sh --install

# If you already have PHP installed:
bash scripts/php-serve.sh

# View examples:
# http://127.0.0.1:8000/examples/php/
# http://127.0.0.1:8000/examples/solana/
```

The PHP example reads the same `examples/solana/YYYY/Www/week.json` records as the static viewer and renders the daily observations in an HTML table.

**Historical code:** `bash scripts/php-serve.sh --legacy` unlocks the old `2013/demo/index.php` route for **local investigation only**. The historical scripts use short PHP open tags, older PHP APIs, implicit array keys, and unsanitized shell commands. Modern PHP 8 may emit errors or fail entirely. The launcher enables `short_open_tag=1`, but does not claim to port the old code. Do not serve the legacy mode publicly. Even in default mode, treat PHP's development server as local-only.

The script checks for PHP, optionally installs CLI/GD with apt/dnf/apk if it is missing, checks the GD module, and binds to `127.0.0.1` by default. Run `bash scripts/php-serve.sh --help` for options.
