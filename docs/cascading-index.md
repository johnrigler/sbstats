# SB Stats cascading directory entry

`index.php` is now a **real file in the repository root**, replacing the broken symlink that referred to `../index.php` outside the checkout. The PHP router delegates *all directory requests* to this same file, passing the actual directory as context.

The index reads the filesystem in real time, shows folders first, displays names, sizes, modification times, breadcrumbs and inline thumbnails of PNG/JPEG/GIF/WebP images. It does not execute PHP scripts during listing. Clicking a PHP file is separate and, for historical scripts, allowed only in local `--legacy` mode.

```bash
cd ~/sbstats
git pull
bash scripts/php-serve.sh --legacy
```

Browse `http://127.0.0.1:8001/`, `/2010/`, `/2010/month/`, or historical symlinks such as `/2010/index.php`. Every directory inherits the same browser. The modern Solana viewer at `/examples/solana/` is still separately accessible.

This is a local inspection tool, not a safe public web server. The historical 2010/2013 applications are not yet ported to PHP 8.2. The listing hides selected sensitive extensions and directories, and does not display raw `.sbstats` datasets. Modern PHP pages can live beside images, JSON and commentary in each directory without needing a generated navigation manifest.

SB Stats stays a separate repository. SB Shell can later mount it at a URL prefix or consume it as an independent component. No repository merge or irreversible changes to archival 2010 files are needed.
