#!/usr/bin/env bash
# Start Apache + PHP-FPM on Termux without PHP's broken CLI-server lock.
set -euo pipefail
ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
PREFIX="${PREFIX:-/data/data/com.termux/files/usr}"
PORT=8001
HOST=127.0.0.1
LEGACY=0
for arg in "$@";do case "$arg" in
  --port=*) PORT="${arg#*=}";;
  --host=*) HOST="${arg#*=}";;
  --legacy) LEGACY=1;;
  --help|-h) echo 'Usage: bash scripts/termux-apache.sh [--port=8001] [--host=127.0.0.1] [--legacy]';exit 0;;
  *) echo "Unknown option: $arg" >&2;exit 2;;
esac;done
if ! command -v httpd >/dev/null || ! command -v php-fpm >/dev/null;then
 echo 'Install dependencies: pkg install apache2 php-fpm php-gd' >&2;exit 1
fi
if [[ "$LEGACY" == 1 && "$HOST" != 127.0.0.1 ]];then echo 'Legacy mode is loopback-only.' >&2;exit 2;fi
STATE="$PREFIX/var/run/sbstats"
mkdir -p "$STATE" "$PREFIX/var/log/sbstats" "$PREFIX/etc/apache2/conf.d"
FPMCONF="$STATE/fpm.conf"
POOLCONF="$STATE/pool.conf"
APCONF="$PREFIX/etc/apache2/conf.d/zz-sbstats.conf"
cat > "$POOLCONF" <<EOF
[sbstats]
listen = 127.0.0.1:19081
pm = static
pm.max_children = 2
clear_env = no
env[SBSTATS_LEGACY] = $LEGACY
php_admin_value[display_errors] = On
EOF
cat > "$FPMCONF" <<EOF
[global]
pid = $STATE/fpm.pid
error_log = $PREFIX/var/log/sbstats/fpm.log
daemonize = yes
include=$POOLCONF
EOF
# The packaged Termux httpd.conf loads conf.d/*.conf. Check modules before modifying it.
MODULES="$(httpd -M 2>&1 || true)"
if ! grep -q 'rewrite_module' <<< "$MODULES";then
 echo 'Apache mod_rewrite must be enabled in httpd.conf.' >&2;exit 1
fi
if ! grep -q 'proxy_fcgi_module' <<< "$MODULES";then
 echo 'Enable proxy_module and proxy_fcgi_module in httpd.conf.' >&2;exit 1
fi
cat > "$APCONF" <<EOF
Listen $HOST:$PORT
<VirtualHost $HOST:$PORT>
 ServerName localhost
 DocumentRoot "$ROOT"
 <Directory "$ROOT">
  Require all granted
  AllowOverride None
  Options -Indexes -FollowSymLinks
 </Directory>
 RewriteEngine On
 RewriteRule ^ /scripts/apache-router.php [END]
 <Files "apache-router.php">
  SetHandler "proxy:fcgi://127.0.0.1:19081"
 </Files>
 ErrorLog "$PREFIX/var/log/sbstats/apache-error.log"
 CustomLog "$PREFIX/var/log/sbstats/apache-access.log" common
</VirtualHost>
EOF
httpd -t
php-fpm -y "$FPMCONF"
httpd -k restart || httpd -k start
echo "SB Stats at http://$HOST:$PORT/"
echo "FPM logs: $PREFIX/var/log/sbstats/fpm.log"
echo "Apache logs: $PREFIX/var/log/sbstats/apache-error.log"
