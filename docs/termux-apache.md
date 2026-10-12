# Termux: Apache + PHP-FPM

PHP 8.5.1 on some Android/Termux installations reports `Cannot create lock - Permission denied (13)` when launched with `php -S`, even with `php -n`. This is not a SB Stats GD error.

Install `pkg install apache2 php-fpm php-gd`. Enable Apache modules `rewrite_module`, `proxy_module`, and `proxy_fcgi_module` in `$PREFIX/etc/apache2/httpd.conf`. The Termux apache2 package supports `$PREFIX/etc/apache2/conf.d/*.conf`.

From the repo run `bash scripts/termux-apache.sh`. It writes a dedicated localhost:8001 Apache virtual host and local PHP-FPM pool, preserves directory-index routing, and disables historical PHP execution unless `--legacy` is specified (loopback only). It does not use `php -S`.

Check `httpd -M` for required modules. Stop with `httpd -k stop` and `kill $(cat "$PREFIX/var/run/sbstats/fpm.pid")`.

**Security:** This is a development server. Do not expose historical PHP or raw file access to the Internet. The Apache router enforces exclusions and dispatches directory URLs to the inherited index. This configuration is not production-hardened.
