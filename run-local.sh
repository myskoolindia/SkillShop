#!/bin/sh
set -eu
cd "$(dirname "$0")"
root=$(pwd)
if [ ! -x .local/bin/php ] || [ ! -f vendor/autoload.php ]; then
  echo 'The project-local PHP runtime or dependencies are missing. See LOCAL_SETUP.md.' >&2
  exit 1
fi
if ! .local/bin/php .local/check-db.php; then
  .local/bin/mysqld --no-defaults --basedir=/usr --datadir="$root/.local/mysql-data" --socket="$root/.local/run/mysql.sock" --port=33307 --bind-address=127.0.0.1 --mysqlx=0 --pid-file="$root/.local/run/mysql.pid" --log-error="$root/.local/logs/mysql.log" --innodb-buffer-pool-size=64M --daemonize
  attempts=0
  until .local/bin/php .local/check-db.php; do
    attempts=$((attempts + 1))
    if [ "$attempts" -ge 10 ]; then echo 'The local database could not start. Check .local/logs/mysql.log.' >&2; exit 1; fi
    sleep 1
  done
fi
printf '%s\n' "$$" > .local/run/web.pid
# A second loopback worker handles portal-to-shop API requests without a
# single-worker HTTP request calling back into itself.
.local/bin/php -S 127.0.0.1:8082 -t club-shop scripts/local-club-router.php > .local/logs/club-shop.log 2>&1 &
club_pid=$!
sleep 1
if ! kill -0 "$club_pid" 2>/dev/null; then echo "Club Shop could not start; check .local/logs/club-shop.log" >&2; exit 1; fi
web_pid=""
cleanup() {
  if [ -n "$web_pid" ]; then kill "$web_pid" 2>/dev/null || true; fi
  kill "$club_pid" 2>/dev/null || true
}
trap cleanup EXIT
trap 'exit 0' INT TERM
.local/bin/php -S 127.0.0.1:8081 -t public scripts/local-router.php &
web_pid=$!
printf "%s\n" "SkillShop: http://localhost:8081" "Club Shop: http://localhost:8081/club-shop/"
wait "$web_pid"
