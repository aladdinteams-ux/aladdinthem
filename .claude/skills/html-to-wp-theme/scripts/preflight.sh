#!/usr/bin/env bash
# Preflight for QA work: reports which tools/sandboxes are available. Never installs anything.
# Usage: bash preflight.sh [PORT...]   (default ports: 8083 8084 8085)
ok() { printf '  [ok]   %s\n' "$1"; }
no() { printf '  [MISS] %s — %s\n' "$1" "$2"; }
echo "Tools:"
command -v php >/dev/null && ok "php $(php -r 'echo PHP_VERSION;')" || no php "needed for WordPress sandboxes and PHP lint"
for ext in mbstring dom fileinfo zip gd; do php -m 2>/dev/null | grep -qi "^$ext$" && ok "php-$ext" || no "php-$ext" "some tests need it"; done
command -v node >/dev/null && ok "node $(node -v)" || no node "needed for Playwright scripts"
PW=${PLAYWRIGHT_MODULE:-/opt/node22/lib/node_modules/playwright}; [ -d "$PW" ] && ok "playwright module ($PW)" || no playwright "set PLAYWRIGHT_MODULE to its path"
CH=${CHROMIUM:-$(ls -d /opt/pw-browsers/chromium-*/chrome-linux/chrome 2>/dev/null | head -1)}; [ -x "$CH" ] && ok "chromium ($CH)" || no chromium "set CHROMIUM to the browser binary"
command -v composer >/dev/null && ok "composer" || no composer "needed for PHPCS/PHPUnit (use --prefer-source behind proxies)"
command -v python3 >/dev/null && python3 -c 'import requests, PIL' 2>/dev/null && ok "python3 + requests + Pillow" || no "python3 requests/Pillow" "needed for HTTP attack tests and pixel diff"
command -v zip >/dev/null && ok zip || no zip "needed for packaging"
echo "Sandboxes:"
for p in ${@:-8083 8084 8085}; do c=$(curl -s -o /dev/null -w '%{http_code}' --max-time 3 "http://localhost:$p/"); [ "$c" = "200" ] && ok "localhost:$p" || no "localhost:$p" "HTTP $c — start: php -S localhost:$p -t <wp> router.php (servers stop when the container restarts)"; done
