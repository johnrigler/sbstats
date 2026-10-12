# SB Stats on Android Termux

Tested startup workflow on Android Termux with PHP 8.5.1 and Apache 2.4.68 (October 2026).

## Install

```sh
pkg update
pkg install git apache2 php-apache
cd ~/sbstats
git pull
bash scripts/termux-apache.sh
```

The launcher uses **Apache prefork MPM** with the non-thread-safe `php-apache` module, rather than Apache worker, PHP-FPM, or `php -S`. On this Android installation, `php -S` and PHP-FPM failed with `Cannot create lock - Permission denied (13)`, while threaded Apache worker rejected the non-thread-safe PHP module. Prefork started successfully.

The launcher generates a private Apache configuration under `$PREFIX/var/run/sbstats/`, and it does not require editing Termux's packaged `httpd.conf`. It listens on port **8001** on all interfaces by default, runs the cascading PHP directory index, and leaves legacy PHP disabled.

## Find the reachable address

To print the Tailscale IP, if Tailscale is installed and connected:

```sh
tailscale ip -4
```

From another device on the same tailnet, open `http://100.x.x.x:8001/`, replacing the example address with the actual output. The startup script also tries to display a usable Tailscale and LAN URL. **0.0.0.0 is a bind address, not a URL to open from another device.** Check that Tailscale is connected on both devices.

Try `http://100.x.x.x:8001/examples/solana/2026/` for the Solana year overview.

## Diagnostics

```sh
httpd -t -f "$PREFIX/var/run/sbstats/httpd.conf"
tail -n 60 "$PREFIX/var/log/sbstats/apache-error.log"
php -m | grep -i gd
```

The final command tests CLI GD only; PHP's Apache module may have different loaded extensions. PNG generation requires GD in the Apache PHP environment. A successful Apache startup does not by itself establish working PHP execution or graph rendering.

For local-only access use `bash scripts/termux-apache.sh --host=127.0.0.1`. Do not expose this development server to the public Internet, especially with legacy PHP enabled.
