#!/usr/bin/env bash
# SB Stats PHP runner: static, modern PHP, and opt-in historical PHP/GD in one tree.
set -euo pipefail
ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
HOST="127.0.0.1"
PORT="8000"
INSTALL=0
LEGACY=0
for arg in "$@"; do
  case "$arg" in
    --install) INSTALL=1 ;;
    --legacy) LEGACY=1 ;;
    --host=*) HOST="${arg#*=}" ;;
    --port=*) PORT="${arg#*=}" ;;
    --help|-h)
      cat <<'EOF'
Usage: bash scripts/php-serve.sh [--install] [--legacy] [--host=127.0.0.1] [--port=8000]
  --install  Install PHP CLI plus GD via apt, dnf or apk if missing.
             Requires appropriate privileges (sudo when available).
  --legacy   Enable browsing original 2013 PHP endpoints. WARNING: historical
             code is not PHP 8 compatible or hardened. Local use only.
Default binds 127.0.0.1 and serves the safe new example.
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
  if [[ "$HOST" != "127.0.0.1" && "$HOST" != "localhost" && "$HOST" != "::1" ]]; then
    echo "Legacy mode is restricted to loopback. Use --host=127.0.0.1." >&2
    exit 2
  fi
  echo "Legacy PHP is enabled for local exploration; old scripts are not PHP 8 compatible."
  echo "Legacy entry: http://$HOST:$PORT/2010/"
else
  echo "Legacy PHP endpoints blocked by default. Add --legacy only for local inspection."
fi
echo "Directory index: http://$HOST:$PORT/"\necho "2010 files:      http://$HOST:$PORT/2010/"\necho "PHP sample:      http://$HOST:$PORT/examples/php/"
echo "Solana stats: http://$HOST:$PORT/examples/solana/"
SBSTATS_LEGACY="$LEGACY" php -d display_errors=1 -d log_errors=1 -d error_reporting=-1 -d short_open_tag=1 -S "$HOST:$PORT" -t "$ROOT" "$ROOT/scripts/php-router.php"
