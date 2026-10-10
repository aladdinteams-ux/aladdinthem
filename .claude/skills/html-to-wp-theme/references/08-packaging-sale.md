# 08 — Packaging for sale (Zhaket / Rtl-theme) and reporting

## Files
- `style.css` header: Theme Name, Theme URI (client site if given), `Author: علاءالدین تم (Aladdin Theme)`, Description (Persian; say Elementor free is enough and Pro optional; mention the companion plugin), Version, Requires at least, Tested up to, Requires PHP, License GPLv2+, Text Domain = slug, Domain Path, accurate Tags only (no `block-styles` unless implemented), copyright lines.
- `readme.txt` (WordPress format): description, installation, FAQ (Pro? existing content?), changelog, Copyright, Resources (every font/icon/image with licence + source; demo images: licence status stated honestly).
- `screenshot.png` 1200×900 rendered from the theme with external images replaced by neutral placeholders.
- `CHANGELOG.md` (Security / Architecture / Added / Improved / Fixed), version bumped in style.css, `LARIJANI_VERSION`, package.json, readme Stable tag.
- POT files for theme and plugin (`scripts/qa/makepot.php <root> <out> [domain] [project]`).
- Persian guide `docs/INSTALL-FA.md`: requirements table, required/optional plugins, install, fresh vs existing site, undo, settings per tab, search panel option format, forms & security, Nginx deny rule / private dir constant, cache plugins, upgrade from the previous version, sample image licence, update/licence architecture.
- Child theme (`child-theme/<slug>-child`), plugin readme.
- Optional update channel: filter `larijani_update_provider` (object with `check($version)` → `new_version`, https `package`, `url`), off by default, no external calls.
- `build/package.sh`: builds plugin ZIP (also copied into `bundled/`), theme ZIP excluding `.git node_modules src build package*.json tailwind.config.js release child-theme plugins docs/qa tests .claude CLAUDE.md`, child ZIP; prints SHA-256 + file counts. Verify no dev files inside.
- `release/release-manifest.json`: version, date, requirements (tested versions), artifacts with SHA-256, test summary, `not_executed` list, licences, `release_ready` false while any blocker remains (e.g. image licence).

## Checks before delivering
```bash
php -l on every changed file
phpcs --standard=WordPress --sniffs=WordPress.Security.EscapeOutput,WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput,WordPress.DB.PreparedSQL  → 0
phpcs --standard=PHPCompatibilityWP --runtime-set testVersion 7.4-  → 0
npx tailwindcss … --minify ; npx terser assets/js/theme.js … ; bash build/package.sh
```
Theme Check: aim for 0 REQUIRED except documented wp.org-only items (CPT fallback, bundled plugin ZIP). Remaining WPCS formatting warnings may stay (report count) — don't mass-reformat.

## Report (Persian, docs/qa/COMMERCIAL-REPORT-<ver>-FA.md)
1 bugs found (id, severity, status) · 2 security report · 3 architecture · 4 commercial features · 5 test table with real numbers + «انجام‌نشده» list (MySQL, PHP 7.4 runtime, Elementor Pro, Nginx/LiteSpeed, real cache, Yoast/Rank Math, real e-mail, real devices/screen reader/Lighthouse, multisite…) · 6 guide link · 7 remaining limitations · 8 deliverables. Then commit, push (retry 2/4/8/16 s), SendUserFile the ZIPs + report + guide, and give a short Persian summary.
