#!/usr/bin/env bash
# Build installable ZIPs: companion plugin (also bundled inside the theme), theme, child theme + manifest.
# Usage: bash build/package.sh
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT=$(pwd); OUT="$ROOT/release"; TMP=$(mktemp -d); trap 'rm -rf "$TMP"' EXIT
mkdir -p "$OUT" theme/payab-sanat-asa/bundled
VER=$(grep -m1 '^ \* Version:' plugin/payab-sanat-asa-core/payab-sanat-asa-core.php | sed 's/.*: *//')

cp -a plugin/payab-sanat-asa-core "$TMP/payab-sanat-asa-core"
( cd "$TMP" && rm -f "$OUT/payab-sanat-asa-core.zip" && zip -qrX "$OUT/payab-sanat-asa-core.zip" payab-sanat-asa-core -x '*.DS_Store' )
cp "$OUT/payab-sanat-asa-core.zip" theme/payab-sanat-asa/bundled/payab-sanat-asa-core.zip

mkdir -p "$TMP/payab-sanat-asa"
tar -c -C theme/payab-sanat-asa --exclude='*.DS_Store' . | tar -x -C "$TMP/payab-sanat-asa"
( cd "$TMP" && rm -f "$OUT/payab-sanat-asa.zip" && zip -qrX "$OUT/payab-sanat-asa.zip" payab-sanat-asa )

cp -a child/payab-sanat-asa-child "$TMP/payab-sanat-asa-child"
( cd "$TMP" && rm -f "$OUT/payab-sanat-asa-child.zip" && zip -qrX "$OUT/payab-sanat-asa-child.zip" payab-sanat-asa-child )

for f in payab-sanat-asa.zip payab-sanat-asa-core.zip payab-sanat-asa-child.zip; do
	unzip -tq "$OUT/$f" >/dev/null
	printf '%s  %s  %s files\n' "$(sha256sum "$OUT/$f" | cut -d' ' -f1)" "$f" "$(unzip -Z1 "$OUT/$f" | grep -vc '/$')"
done | tee "$OUT/SHA256SUMS.txt"
echo "version $VER"
