#!/bin/sh
set -eu
cd "$(dirname "$0")"
works() {
  "$1" -e 'const [a,b] = process.versions.node.split(".").map(Number); process.exit(a > 22 || (a === 22 && b >= 13) ? 0 : 1)' >/dev/null 2>&1
}
if [ -n "${KALPANA_NODE:-}" ]; then
  runtime="$KALPANA_NODE"
elif command -v node >/dev/null 2>&1 && works "$(command -v node)"; then
  runtime="$(command -v node)"
else
  runtime=""
  for candidate in "${NVM_DIR:-$HOME/.nvm}"/versions/node/v*/bin/node; do
    if [ -x "$candidate" ] && works "$candidate"; then runtime="$candidate"; break; fi
  done
fi
if [ -z "$runtime" ] || ! works "$runtime"; then
  echo 'Kalpana requires Node 22.13 or newer. Set KALPANA_NODE to its executable.' >&2
  exit 1
fi
exec "$runtime" --env-file-if-exists=.env src/start.mjs "$@"
