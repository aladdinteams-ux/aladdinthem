# 01 — Intake, baseline and architecture

Contents: 1 Intake · 2 Approved baseline · 3 Reference theme map · 4 Theme vs companion plugin · 5 Theme skeleton · 6 Templates · 7 Data model

## 1. Intake (P0)
1. Put every supplied archive in its own empty directory (untrusted data; read with `python3 -I`, never execute it). Record each source file with size and SHA-256 (`sha256sum`) in the checkpoint.
2. Inventory: pages and routes, languages and directions, sections, components, interactions (menus, tabs, sliders, accordions, filters, modals, forms, calculators, maps), CSS/JS, fonts, icon families, media, dynamic data (products, posts, projects, prices), third-party dependencies (CDNs, Google Fonts, analytics), and remote/hotlinked assets (licence + privacy + availability risk).
3. Component map — classify every component as **Elementor-native** (Container + core widgets), **Elementor template / theme location** (header, footer, single, archive, product, 404), **WordPress-native** (menus, posts, search, comments), **custom Elementor widget** (data-driven or interactive), or **companion-plugin service** (forms, CPTs, filters, import, integrations). Write it as a table (component → implementation → controls → data source).
4. Decide with the user: font, languages and multilingual method (Polylang, WPML, or none — references/11), products (WooCommerce when cart/prices/orders are needed, a CPT for a catalog only), form recipients and test-mail permission, dark-mode default, live-site scope, target marketplace.
5. Plan + task list (the session's task tool, or the checkpoint file). Run `scripts/preflight.sh` before taking screenshots.

## 2. Approved baseline
- The approved real view is the reference: colours, fonts, sizes, spacing, alignment, radii, shadows, images, header, footer, menu, and interaction states (hover, focus, open menus).
- Capture reference screenshots of the source (Playwright; 390 and 1440 px at least, plus every breakpoint the design uses; light and dark; every language) → `nat/REF/`. Wait for fonts (`document.fonts.ready`) and disable transitions before capturing.
- A screenshot that shows an editor/site difference is a bug report, not a new design.

## 3. Reference theme map (when the user gives a reference theme, e.g. FRPMesh)
For each of its settings/features record one status: **موجود** (exists here), **نیازمند پیاده‌سازی** (to implement), **رزرو‌شده** (shown but intentionally not active — label it), **نامرتبط** (not relevant). Implement useful behaviour, not option names. The number of options in a reference is not a target ("92 options" ≠ "92 working features").

## 4. Theme vs companion plugin (default for new projects)
| Theme (presentation) | Companion plugin `aladdin-<project>-core` |
|---|---|
| style.css, functions.php, template hierarchy, template parts | Custom Elementor widgets, controls, dynamic tags |
| Design tokens (CSS variables, optional theme.json), assets, fonts, icons | CPTs, taxonomies, registered meta, persistent settings independent of theme |
| Theme supports, menus, image sizes, theme-builder locations | Forms, uploads, leads, AJAX/REST endpoints, integrations |
| PHP fallback renderers for header/footer (used when the plugin or Elementor is missing) | Editable header/footer widgets used inside Elementor templates |
| Header/footer renderers and fallbacks when plugins are missing | Demo import, migrations, setup services |
- Use only public WordPress, Elementor and WooCommerce APIs; never edit core or third-party files, never depend on private/internal APIs as shortcuts; feature-detect optional plugins and degrade gracefully.
- If the user explicitly wants a theme without a plugin, explain the trade-offs (wp.org plugin-territory rules, content lost on theme switch) and don't silently drop needed features.
- The Larijani reference keeps widgets in the theme (older decision); new projects move widgets to the plugin, the theme keeps a minimal fallback renderer.
- Load assets only where needed (widget `get_style_depends()` / `get_script_depends()`, conditional enqueue).

## 5. Theme skeleton
- Slugs, text domains and prefixes: SKILL.md §0.1 naming table (never 2-letter prefixes).
- `functions.php` only defines constants and requires `inc/*`.
- **CSS**: Tailwind 3 compiled at build time from `src/` with `important: '.aladdin-<project>'` (scope class), `corePlugins.preflight: false` + reset scoped to `:where(.aladdin-<project>)`; colours as CSS variables with Elementor Global Colour fallback (`--x: var(--e-global-color-<id>, var(--x-default))`), `color-mix()` for alpha; typography, container width and radius as variables so settings change them without altering defaults; keep selector specificity ≤ 0-3-0 so Elementor Style-tab rules win; content globs include PHP, JS and layout HTML; safelist dynamic classes.
- Semantic colour tokens per component (surface, text, title, border, accent) with dark-mode values (references/10).
- Fonts bundled with licence file, `font-display: swap`, preload only the font in use; icon fonts bundled (never show ligature names such as `water_drop`).
- JS: vanilla modules, `defer`, no globals beyond one namespace object, `once()` guards, re-init inside the Elementor editor preview, destroy/re-init safely.
- RTL/LTR per language (`dir`, `lang`), logical CSS properties.

## 6. Templates
`index, page, single, archive, search, 404, comments, searchform (all search boxes through get_search_form() variants), archive-/single-/taxonomy-<cpt>, woocommerce/*`. Each calls a `do_location()` helper first so an Elementor template (Pro condition or the built-in theme builder) can replace it.

## 7. Data model
- One field list drives both the Customizer and the settings page; central defaults; sanitise per type.
- Versioned idempotent migrations (admin only; stored version updated only after all steps pass).
- Deprecated wrappers when renaming public helpers (never `eval`).
- Settings export/import: versioned JSON without secrets.
