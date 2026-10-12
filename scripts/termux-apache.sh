#!/usr/bin/env bash
# Start Apache + embedded PHP using prefork MPM on Termux.
set -euo pipefail
ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
PREFIX="${PREFIX:-/data/data/com.termux/files/usr}"
PORT=8001
HOST=0.0.0.0
LEGACY=0
for arg in "$@";do case "$arg" in
  --port=*) PORT="${arg#*=}";;
  --host=*) HOST="${arg#*=}";;
  --legacy) LEGACY=1;;
  --help|-h) echo 'Usage: bash scripts/termux-apache.sh [--port=8001] [--host=0.0.0.0] [--legacy]';exit 0;;
  *) echo "Unknown option: $arg" >&2;exit 2;;
esac;done
if ! command -v httpd >/dev/null;then
 echo 'Install Apache: pkg install apache2' >&2;exit 1
fi
if [[ ! -f "$PREFIX/libexec/apache2/libphp.so" ]];then
 echo 'Install the embedded PHP Apache module: pkg install php-apache' >&2
 exit 1
fi
if [[ "$LEGACY" == 1 && "$HOST" != 127.0.0.1 ]];then echo 'Legacy mode is loopback-only.' >&2;exit 2;fi
STATE="$PREFIX/var/run/sbstats"
mkdir -p "$STATE" "$PREFIX/var/log/sbstats" "$PREFIX/etc/apache2/conf.d"
APCONF="$STATE/sbstats-vhost.conf"
# Isolate SB Stats from recursively loaded conf.d files and old generated listeners.
PRIVATECONF="$STATE/httpd.conf"
MODULECONF="$STATE/modules.conf"
cp "$PREFIX/etc/apache2/httpd.conf" "$PRIVATECONF"
# Apache's default may include conf.d through a wildcard, including files we
# previously generated. Remove inherited includes from this private copy.
sed -i '/^[[:space:]]*IncludeOptional[[:space:]]/d; /^[[:space:]]*Include[[:space:]]/d' "$PRIVATECONF"
# Non-ZTS php-apache requires prefork. The Termux default uses worker.
PREFORK="$PREFIX/libexec/apache2/mod_mpm_prefork.so"
if [[ ! -f "$PREFORK" ]]; then
  echo "PHP Apache requires prefork, but $PREFORK is absent." >&2
  echo "Installed Apache cannot run non-thread-safe php-apache. No fallback to worker." >&2
  exit 1
fi
sed -i -E '/^[[:space:]]*LoadModule[[:space:]]+mpm_(worker|event|prefork)_module[[:space:]]/d' "$PRIVATECONF"
printf '\nLoadModule mpm_prefork_module "%s"\n' "$PREFORK" >> "$PRIVATECONF"

: > "$MODULECONF"
for spec in "rewrite rewrite_module" "php php_module"; do
 read -r name module <<< "$spec"
 if ! grep -Eq "^[[:space:]]*LoadModule[[:space:]]+$module[[:space:]]" "$PRIVATECONF"; then
   if [[ "$name" == php ]];then so="$PREFIX/libexec/apache2/libphp.so";else so="$PREFIX/libexec/apache2/mod_$name.so";fi
   [[ -f "$so" ]] || { echo "Missing Apache module: $so" >&2;exit 1;}
   printf 'LoadModule %s "%s"\n' "$module" "$so" >> "$MODULECONF"
 fi
done
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
  SetHandler application/x-httpd-php
 </Files>
 ErrorLog "$PREFIX/var/log/sbstats/apache-error.log"
 CustomLog "$PREFIX/var/log/sbstats/apache-access.log" common
</VirtualHost>
EOF
httpd -t -f "$PRIVATECONF"
httpd -f "$PRIVATECONF" -k start
echo "SB Stats listens on $HOST:$PORT"
echo "From another device use the address of this Android device:"
if command -v tailscale >/dev/null 2>&1; then
  TAIL_IP="$(tailscale ip -4 2>/dev/null | head -n 1 || true)"
  if [[ "$TAIL_IP" =~ ^100\\.[0-9]+\\.[0-9]+\\.[0-9]+$ ]]; then
    echo "  Tailscale: http://$TAIL_IP:$PORT/"
  fi
fi
if command -v ip >/dev/null 2>&1; then
  LAN_IP="$(ip -4 -o addr show scope global 2>/dev/null | awk '$4 !~ /^100\\./ {split($4,a,"/"); print a[1]; exit}' || true)"
  if [[ -n "$LAN_IP" ]]; then echo "  LAN: http://$LAN_IP:$PORT/"; fi
fi
echo "If no address was found, run: tailscale ip -4"
echo "Do not use 0.0.0.0 as the browser address."
echo "PHP runs in Apache through php-apache (no PHP-FPM process)."
echo "Apache logs: $PREFIX/var/log/sbstats/apache-error.log"
