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
# Termux ships these modules but may leave them disabled by default.
# Use a separate generated include file, without changing the packaged httpd.conf.
MODULECONF="$PREFIX/etc/apache2/conf.d/00-sbstats-modules.conf"
: > "$MODULECONF"
for spec in "rewrite rewrite_module" "proxy proxy_module" "proxy_fcgi proxy_fcgi_module"; do
  read -r name module <<< "$spec"
  if ! httpd -M 2>&1 | grep -q "$module"; then
    so="$PREFIX/libexec/apache2/mod_$name.so"
    if [[ ! -f "$so" ]]; then
      echo "Missing Apache module: $so" >&2
      echo "Check: find \"$PREFIX\" -name 'mod_$name.so'" >&2
      exit 1
    fi
    printf 'LoadModule %s "%s"\n' "$module" "$so" >> "$MODULECONF"
  fi
done
# Build a private Apache config from the packaged one, then include our snippets.
# This leaves Termux's original httpd.conf untouched.
PRIVATECONF="$STATE/httpd.conf"
cp "$PREFIX/etc/apache2/httpd.conf" "$PRIVATECONF"
# The packaged configuration may already load conf.d; do not include it twice.
# The private copy removes any inherited SB Stats include and appends one explicit include.
sed -i '/^[[:space:]]*IncludeOptional[[:space:]].*conf\\.d/d; /^[[:space:]]*Include[[:space:]].*conf\\.d/d' "$PRIVATECONF"
printf '\nServerName localhost\nInclude "%s"\nInclude "%s"\n' "$MODULECONF" "$APCONF" >> "$PRIVATECONF"
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
httpd -t -f "$PRIVATECONF"
if [[ ! -f "$STATE/fpm.pid" ]] || ! kill -0 "$(cat "$STATE/fpm.pid")" 2>/dev/null; then
  php-fpm -y "$FPMCONF"
fi
httpd -f "$PRIVATECONF" -k start
echo "SB Stats at http://$HOST:$PORT/"
echo "FPM logs: $PREFIX/var/log/sbstats/fpm.log"
echo "Apache logs: $PREFIX/var/log/sbstats/apache-error.log"
