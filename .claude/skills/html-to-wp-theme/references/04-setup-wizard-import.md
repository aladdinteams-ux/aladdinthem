# 04 — Setup, wizard and reversible demo import

Reference: `inc/demo/importer.php`, `inc/admin/setup-wizard.php`.

## Auto-setup on activation
- Runs only on a **fresh site** (`fresh_site` option) or a site without own content (`larijani_site_has_content()`: pages without the demo meta other than privacy/sample page, or > 1 published post). Otherwise: no import, show the "run setup" notice.
- Fresh site: remove only WP's untouched defaults (Hello world, Sample Page), rename "Uncategorized", move default widgets to inactive, enable `/%postname%/`, then import pages, theme-builder templates, menus, projects (if plugin active), sample posts/products, kit; queue demo image copy in WP-Cron.
- Non-interactive activation (WP-CLI) → finish on next admin visit. Filter to disable (`ls_auto_setup_on_activation`).
- When Elementor / Pro / WooCommerce are activated later, complete the missing parts (templates, conditions, products, shop page) once.

## Wizard page (Larijani › راه‌اندازی و درون‌ریزی)
1. **Requirements** table with ✅/⚠️/❌: PHP ≥ 7.4 (8.1+ recommended), WP ≥ 6.5, memory 128M/256M, max_execution_time 60/120, upload size, extensions mbstring/fileinfo (fail), dom/zip/curl (warn), GD or Imagick, pretty permalinks, plugin dir writable.
2. **Plugins**: bundled core plugin (required), Elementor (required for editing), WooCommerce (optional), Elementor Pro (optional, never installed). Install/activate only on click: `Plugin_Upgrader` + `WP_Ajax_Upgrader_Skin`, `plugins_api` for wordpress.org, nonce per plugin + `install_plugins` & `activate_plugins`; clear message on failure (FTP credentials, no network).
3. **Demo import** with checkboxes. Defaults on a site with content: "set front page" unchecked with the current front page named in red; sample posts/products unchecked. Projects skipped with a clear message if the core plugin is inactive.
4. **Last import** card: status (done/failed/interrupted), undo button.

## Import rules (never destroy owner content)
- Create only; find existing objects by stable keys (`_ls_demo_key`, slug) → re-runs create no duplicates.
- Menus: only fill empty locations; items linked to page/term objects (custom links only as fallback, e.g. `?ls_cat=` for the no-Woo catalog).
- Theme-builder slots: only fill empty/broken ones; with Pro never add conditions if the owner already has a template with conditions for that location.
- Front page replaced only when the checkbox is checked.
- Elementor system kit values only on fresh sites.

## Journal + undo
- `larijani_run_import()` wraps the steps: `journal_start` snapshots watched options (show_on_front, page_on_front, page_for_posts, woocommerce_shop_page_id, elementor_cpt_support, permalink_structure), theme mods (menu locations, ls_tb_*), kit settings; hooks `wp_insert_post` (new posts only) and `created_term` and **persists after each insert** (survives fatals/timeouts).
- try/catch Throwable → status `failed`, generic message, details to debug log. A `running` journal older than 10 min = interrupted.
- A run that changed nothing keeps the previous journal (otherwise a no-op re-run would make the real import un-undoable).
- Undo: delete created posts unless edited after the run finished (+60 s), delete created empty terms / menus, restore options, mods and kit, clear Elementor cache, flush rewrites, report counts. Background image copies are not covered — say so.

## Tests
Fresh ZIP install → activation auto-setup; wizard: requirements render, install bundled plugin by click, CSRF without nonce 403, import projects (N created), re-run (0 created), undo (N removed, other counts unchanged); existing-content site: activation imports nothing, front page kept, notice shown, front checkbox unchecked with warning.
