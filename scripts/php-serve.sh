#!/usr/bin/env bash
# SB Stats PHP runner: static, modern PHP, and opt-in historical PHP/GD in one tree.
set -euo pipefail
ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
HOST="0.0.0.0"
PORT="8001"
INSTALL=0
LEGACY=0
ALLOW_REMOTE_LEGACY=0
CLEAN_PHP=0
for arg in "$@"; do
  case "$arg" in
    --install) INSTALL=1 ;;
    --clean-php) CLEAN_PHP=1 ;;
    --legacy) LEGACY=1 ;;
    --allow-remote-legacy) ALLOW_REMOTE_LEGACY=1 ;;
    --host=*) HOST="${arg#*=}" ;;
    --port=*) PORT="${arg#*=}" ;;
    --help|-h)
      cat <<'EOF'
Usage: bash scripts/php-serve.sh [--install] [--clean-php] [--legacy] [--allow-remote-legacy] [--host=0.0.0.0] [--port=8001]
  --clean-php  Diagnose PHP server startup without php.ini or extensions (graphs need GD).
  --install  Install PHP CLI plus GD via apt, dnf or apk if missing.
             Requires appropriate privileges (sudo when available).
  --legacy   Enable browsing original 2013 PHP endpoints. WARNING: historical
             code is not PHP 8 compatible or hardened.
  --allow-remote-legacy  Explicitly permit legacy PHP on network interfaces.
Default binds 0.0.0.0:8001. Other devices connect using this host’s IP address.
For local-only service specify --host=127.0.0.1.
EOF
      exit 0 ;;
    *) echo "Unknown option: $arg" >&2; exit 2 ;;
  esac
done
if ! command -v php >/dev/null 2>&1; then
  if [[ "$INSTALL" != 1 ]]; then
    echo "PHP CLI is not installed. Run: bash scripts/php-serve.sh --install" >&2
    exit 1
  fi
  if command -v sudo >/dev/null 2>&1 && [[ "$(id -u)" != 0 ]]; then ADMIN=(sudo); else ADMIN=(); fi
  if command -v apt-get >/dev/null 2>&1; then
    "${ADMIN[@]}" apt-get update
    "${ADMIN[@]}" apt-get install -y php-cli php-gd
  elif command -v dnf >/dev/null 2>&1; then
    "${ADMIN[@]}" dnf install -y php-cli php-gd
  elif command -v apk >/dev/null 2>&1; then
    "${ADMIN[@]}" apk add php php-gd
  else
    echo "No supported package manager detected. Install PHP CLI and GD manually." >&2
    exit 1
  fi
fi
php -v | head -n 1
if ! php -m | grep -qi '^gd$'; then
  echo "Note: PHP GD extension not loaded; original GD graph scripts may fail." >&2
fi
if [[ "$LEGACY" == 1 ]]; then
  if [[ "$HOST" != "127.0.0.1" && "$HOST" != "localhost" && "$HOST" != "::1" && "$ALLOW_REMOTE_LEGACY" != 1 ]]; then
    echo "Legacy PHP can execute unsafe shell commands. Use --host=127.0.0.1 or explicitly add --allow-remote-legacy (trusted network only)." >&2
    exit 2
  fi
  if [[ "$ALLOW_REMOTE_LEGACY" == 1 ]]; then
    echo "WARNING: legacy PHP is remotely accessible. Use only on a trusted network; do not expose port $PORT to the Internet." >&2
  fi
  echo "Legacy PHP is enabled for local exploration; old scripts are not PHP 8 compatible."
  echo "Legacy entry: http://$HOST:$PORT/2010/"
else
  echo "Legacy PHP endpoints blocked by default. Add --legacy only for local inspection."
fi
echo "Directory index: http://$HOST:$PORT/ (replace 0.0.0.0 with the host IP from other devices)"
echo "2010 files:      http://$HOST:$PORT/2010/"
echo "PHP sample:      http://$HOST:$PORT/examples/php/"
echo "Solana stats: http://$HOST:$PORT/examples/solana/"
PHP_ARGS=()
if [[ "$CLEAN_PHP" == 1 ]]; then
  PHP_ARGS=(-n)
  echo "Diagnostic mode: PHP configuration and extensions disabled (including GD)."
  echo "If this still says Cannot create lock, the issue is not caused by php.ini."
fi
SBSTATS_LEGACY="$LEGACY" PHP_CLI_SERVER_WORKERS=1 php "${PHP_ARGS[@]}" -d display_errors=1 -d log_errors=1 -d error_reporting=-1 -d short_open_tag=1 -S "$HOST:$PORT" -t "$ROOT" "$ROOT/scripts/php-router.php"
