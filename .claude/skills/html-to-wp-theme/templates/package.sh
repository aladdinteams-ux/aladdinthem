#!/usr/bin/env bash
# Build installable ZIPs: companion plugin (also bundled inside the theme),
# parent theme and child theme. Usage: bash build/package.sh
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT=$(pwd)
OUT="$ROOT/release"
TMP=$(mktemp -d)
trap 'rm -rf "$TMP"' EXIT
mkdir -p "$OUT" "$ROOT/bundled"

# 1. Companion plugin.
cp -a plugins/larijani-stone-core "$TMP/larijani-stone-core"
( cd "$TMP" && rm -f "$OUT/larijani-stone-core.zip" && zip -qrX "$OUT/larijani-stone-core.zip" larijani-stone-core -x '*.DS_Store' )
cp "$OUT/larijani-stone-core.zip" "$ROOT/bundled/larijani-stone-core.zip"

# 2. Parent theme (development and QA files excluded).
mkdir -p "$TMP/larijani-stone"
tar -c \
	--exclude='./.git' --exclude='./.gitignore' --exclude='./node_modules' --exclude='./src' --exclude='./build' \
	--exclude='./package.json' --exclude='./package-lock.json' --exclude='./tailwind.config.js' \
	--exclude='./release' --exclude='./child-theme' --exclude='./plugins' --exclude='./docs/qa' --exclude='./tests' --exclude='./.claude' --exclude='./CLAUDE.md' --exclude='*.DS_Store' \
	. | tar -x -C "$TMP/larijani-stone"
( cd "$TMP" && rm -f "$OUT/larijani-stone.zip" && zip -qrX "$OUT/larijani-stone.zip" larijani-stone )

# 3. Child theme.
cp -a child-theme/larijani-stone-child "$TMP/larijani-stone-child"
( cd "$TMP" && rm -f "$OUT/larijani-stone-child.zip" && zip -qrX "$OUT/larijani-stone-child.zip" larijani-stone-child )

for f in larijani-stone.zip larijani-stone-core.zip larijani-stone-child.zip; do
	printf '%s  %s  %s files\n' "$(sha256sum "$OUT/$f" | cut -d' ' -f1)" "$f" "$(unzip -Z1 "$OUT/$f" | grep -vc '/$')"
done
