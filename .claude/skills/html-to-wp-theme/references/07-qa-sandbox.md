# 07 — QA in real sandboxes (results must be real)

wordpress.org, packagist dist downloads and phar.phpunit.de are usually blocked by the proxy; **GitHub git clones work**.

## Build the sandboxes (scratchpad, PHP built-in server + SQLite)
```bash
S=<scratchpad>
git clone -q --depth 1 --branch 7.1.2 https://github.com/WordPress/WordPress $S/wpel        # core (also 6.5.x for min version)
git clone -q --depth 1 https://github.com/WordPress/sqlite-database-integration $S/wpel/wp-content/plugins/sqlite-database-integration-main
cp $S/wpel/wp-content/plugins/sqlite-database-integration-main/db.copy $S/wpel/wp-content/db.php   # set the plugin path inside db.php
# wp-config: DB_DIR/DB_FILE, WP_DEBUG_LOG to file, WP_DEBUG_DISPLAY false, WP_HOME/SITEURL http://localhost:PORT, WP_HTTP_BLOCK_EXTERNAL true
# mu-plugin test-user.php: if LS_TEST_USER defined → determine_current_user returns 1 (CLI scripts act as admin)
ln -s <repo> $S/wpel/wp-content/themes/<slug>        # theme from the working tree; a 2nd site installs from the release ZIP
(nohup php -d upload_max_filesize=64M -d post_max_size=64M -d memory_limit=512M -S localhost:8083 -t $S/wpel $S/routerel.php > $S/serverel.log 2>&1 &)
```
Router (`scripts/sandbox/router.php`) serves existing files, else `index.php`. **Servers die when the container restarts** — restart them (empty responses = server down).

Elementor from source (both 3.x and 4.x): `git clone --depth 1 --branch v4.0.8 https://github.com/elementor/elementor`, drop `elementor/wp-one-package` and require-dev from composer.json, `composer install --no-dev --no-scripts`, `npm ci --ignore-scripts`, `npm run build:packages`, `npx grunt styles`, `npx grunt scripts`. WooCommerce: release ZIP from GitHub (`github.com/woocommerce/woocommerce/releases/download/<ver>/woocommerce.zip`). Other plugins for compatibility (CF7 `takayukister/contact-form-7`, `Automattic/wp-super-cache`, `sybrew/the-seo-framework`) by git clone. PHPUnit: `composer require --dev phpunit/phpunit:9.6.21 --prefer-source` (git sources work when dist downloads fail). Theme Check: `git clone https://github.com/WordPress/theme-check`. PHPCS: composer `wp-coding-standards/wpcs ^3.1` + `phpcompatibility/phpcompatibility-wp`. Apache for `.htaccess` tests: `apt-get install -y apache2`, copy the uploads tree to `/var/www/…`, vhost with `AllowOverride All` on another port.

Sites used last time: 8083 (WP 7.1.2 + Elementor 4.0.8 + Woo, theme symlinked), 8084 (Elementor 3.35.9 + Woo, upgraded data), 8085 (installed from the ZIP, no Woo, fresh).

## Test matrix (record numbers; anything not run = «انجام‌نشده»)
| Area | Script / method |
|---|---|
| Reference screenshots + pixel diff (15 routes × 390/1440) | `scripts/qa/natshot.js`, `scripts/qa/pdiff.py` (threshold 24, report WORST %, same heights) |
| Route crawl (status, horizontal overflow, PHP errors in HTML, one H1, JS errors) | `scripts/qa/crawl.js routes.json` |
| Keyboard/menu/search/forms in browser | `scripts/qa/interact.js` |
| WooCommerce (shop, category, product, cart/checkout blocks, place order, account) | `scripts/qa/woo.js <product> <cat>` |
| Elementor editor load / edit+publish / Style-tab override (on a **copy** of the page) | `scripts/qa/editor-*.js` + `dup-page.php`, `FRONT=/copy/` |
| Form attacks, validation, files, rate limits | `scripts/qa/forms-attack.py BASE /contact/ token|validate|files|rate` (reset limits between groups with `reset-rl.php`; sandbox-only mu-plugin raises limits for file cases) |
| Private download by role | `roles.php` + `roles.py` |
| Settings / wizard / undo / existing-site activation | `settings-test.py`, `wizard-test.py`, `existing-site.php` |
| Hero search + glass dropdown | `hero-search.js` (TAB, W, PORT env) |
| Reduced motion | `motion.js` |
| PHPUnit (validators, signing, uploads, settings CSS, search parsing) | `tests/*Test.php` with bootstrap loading a sandbox `wp-load.php` |
| Theme Check on the ZIP-installed theme | `tc-run.php <wp> <host> <theme-check> <slug>` |
| PHPCS security sniffs = 0, PHPCompatibility 7.4+ = 0 | phpcs commands in 08 |
| Debug log lines from theme/plugin files = 0 | grep `themes/<slug>\|plugins/<slug>-core` |

## Harness gotchas
- `requests` drops a manual `Cookie` header on redirects → POST with `allow_redirects=False`, then GET.
- `wp_nonce_url()` returns `&amp;` → `html.unescape` before requesting.
- CLI-made nonces need a real session: create the token with `WP_Session_Tokens::create`, build cookies with it and set `$_COOKIE[LOGGED_IN_COOKIE]` before `wp_create_nonce`.
- WP 7 prints `<style id="…-inline-css">` with double quotes.
- The first admin visit after activating Elementor may redirect to onboarding — warm up `/wp-admin/` first.
- Per-IP limits trip when test groups run back-to-back from 127.0.0.1 — reset between groups.
- PHP built-in server ignores `.htaccess` — test protection on real Apache.
- Woo Action Scheduler "Unable to claim actions" on SQLite is an environment issue (check it exists before your change).
