# Templates (working code from Larijani Stone 1.4.1)

Copy, then rename prefixes (`larijani_` → `<brand>_`, `Larijani_`, `LARIJANI_`, text domains, `ls_` hooks/keys if the new theme has no legacy data) and adjust demo-specific values (categories, field labels, aliases).

- `companion-plugin/` — full companion plugin (CPTs, secure forms, private uploads, leads admin, SVG, privacy, migration, uninstall).
- `theme-inc/forms.php` — theme side of forms (delegation + fallback handler).
- `theme-inc/search-filters.php` — hero search → shop filtering, aliases, active-filter bar.
- `theme-inc/setup-wizard.php` — requirements, plugin installer, import journal + undo, missing-plugin notice.
- `theme-inc/searchform.php` — all search boxes through get_search_form() variants.
- `theme-inc/migrations.php`, `compat.php`, `updater.php` — versioned migrations, deprecated wrappers, optional update provider.
- `package.sh` — release builder.
- `tests/` — PHPUnit suite + bootstrap (LARIJANI_WP_LOAD / LARIJANI_WP_HOST).

The glass dropdown, menu active logic, settings CSS variables and the rest live in the reference repo (`assets/js/theme.js` initSelects, `inc/template-functions.php` larijani_menu_item_active, `inc/customizer.php` larijani_customizer_css, `src/tailwind.css`).
