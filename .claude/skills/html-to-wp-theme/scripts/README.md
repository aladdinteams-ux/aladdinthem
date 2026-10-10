# Scripts (copied from the Larijani Stone build; adapt ports/paths)

Placeholders: `$SCRATCH` = the session scratchpad directory; Chromium is `/opt/pw-browsers/chromium-1194/chrome-linux/chrome` and Playwright `/opt/node22/lib/node_modules/playwright` in this environment (adjust if different). Most scripts read `PORT` from the environment.

## sandbox/
- `wp-config.sample.php` — SQLite + debug log + blocked external HTTP; set WP_HOME/SITEURL port.
- `router.php` — PHP built-in server router (`php -S localhost:PORT -t <wp> router.php`).
- `test-user.php` (mu-plugin) — CLI scripts defining `LS_TEST_USER` act as user 1.
- `zz-ls-test-limits.php` (mu-plugin, sandbox only) — raises form limits when the request has `X-LS-QA: limits`.
- `fresh-install.php`, `activate-plugins.php <plugin-file…>`, `cookie.php` (admin cookie string), `zip-install.php` (install theme + child from ZIPs with Theme_Upgrader).

## qa/
| Script | Use |
|---|---|
| `natshot.js OUT_DIR '{"name":"/path/"}' [widths]` | full-page screenshots (external images replaced by grey) |
| `pdiff.py A_DIR B_DIR` | pixel diff, prints per file and WORST % |
| `crawl.js ./routes.json` | status / overflow / PHP errors / H1 per route × width, prints `bad=` |
| `interact.js` | menu, focus, search, form submit (token flow), tokenless rejection |
| `woo.js <product-path> <category-path>` | shop → cart → checkout blocks → order → account |
| `editor-load.js COOKIE PAGE_ID`, `editor-edit.js COOKIE COPY_ID`, `editor-style.js COOKIE COPY_ID` (+ `FRONT=/copy-path/`) | real Elementor editor checks; use `dup-page.php <wp> <host>` to make a copy |
| `forms-attack.py BASE /contact/ token|validate|files|rate` + `reset-rl.php <wp>` | form security suite |
| `roles.php` + `roles.py` | private file download per role |
| `legacy-lead.php`, `svg-privacy.php` | old-version data + SVG/privacy tests |
| `settings-test.py`, `wizard-test.py [undo]`, `existing-site.php`, `site-count.php`, `setmods.php` | settings, wizard, undo, safe activation |
| `hero-search.js` (env PORT, TAB, W) | search panel + glass dropdown + results |
| `motion.js` | prefers-reduced-motion |
| `screenshot.js OUT.png` | 1200×900 theme screenshot |
| `admin-shot.js COOKIE OUT.png` | settings page screenshot + footer credit |
| `makepot.php ROOT OUT [domain] [project]` | POT generator |
| `tc-run.php <wp> <host> <theme-check-dir> <slug>` | Theme Check as text |
