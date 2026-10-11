# Cascading PHP directory browser

The 2010 PHP index is a historical symlink, not a reliable default page on PHP 8. The launcher now routes requests for **directories** through `scripts/directory-index.php`, listing nested files and directories with breadcrumbs, file sizes, and modification times. It preserves every original PHP script, symlink and dataset.

```bash
cd ~/sbstats
git pull
bash scripts/php-serve.sh --legacy
```

Visit `http://127.0.0.1:8000/` and follow **2010/**, **month/**, then **08/**, **09/**, and **10/**. Click individual PHP scripts to execute them.

- Without `--legacy`, directories can be browsed, but old PHP scripts cannot execute.
- With `--legacy`, PHP errors display in the browser and are logged in the terminal.
- Legacy mode only binds to loopback, never public interfaces.
- Old code may require migration to PHP 8, especially implicit array keys, old `<?` tags, removed PHP functions and GD signatures. This update is a *browse/debug entry point*, not a full port.
- Hidden files, archives, credential-like files and old `.sbstats` raw data are deliberately not linked or served through this router. The old scripts should be treated as untrusted until audited.

This is inspired by classic Apache autoindex, but is deliberately implemented as a small self-contained PHP script so that it works with PHP's built-in server, requires no Apache configuration, and cascades to every directory.
