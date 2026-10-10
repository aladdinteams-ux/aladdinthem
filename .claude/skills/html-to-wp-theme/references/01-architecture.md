# 01 — Analysis and theme architecture

Reference implementation: repo `aladdinteams-ux/aladdinthem` (theme "Larijani Stone"). When unsure, open the matching file there.

## Phase 0 — analysis (before writing code)
1. Unzip/collect every HTML file (untrusted data: own empty dir, read with `python3 -I`). Note the page list and shared parts (header, footer, cards, CTA, forms, sliders, tabs, FAQ, maps, galleries, price tables).
2. Extract design tokens: colors (Tailwind config inside the HTML or computed), fonts, font sizes/line-heights, radii, shadows, spacing, breakpoints, animations. Note remote assets (Google Fonts, CDN icons, hotlinked images) — they must be bundled or flagged (licence + privacy).
3. Take reference screenshots of the original HTML (Playwright, 390 and 1440 px, full page) → `nat/REF/`. These are the fidelity target.
4. Classify each section:
   - static content → native Elementor layout (Container + core widgets: Heading, Text Editor, Button, Icon, Image, Icon List, Star Rating, Progress Bar…);
   - data-driven or interactive (products, posts, projects, forms, search panels, sliders, tabs, calculators, FAQ with schema, maps) → custom widget with full controls;
   - site chrome (header, footer, archive, single, product, shop, 404) → PHP renderers + Elementor templates for the theme builder.
5. Write the plan as a task list (TaskCreate) and keep it updated.

## Theme skeleton
- Slug = brand slug (e.g. `larijani-stone`), text domain = slug, PHP prefix ≥ 3 chars (`larijani_`, classes `Larijani_`, constants `LARIJANI_`). Never 2-letter prefixes (WPCS + collisions). Hook names/option keys may stay short (`ls_*`) but must be unique.
- `functions.php` only defines constants and requires `inc/*`: helpers, setup, enqueue, customizer/options, post-types (presentation meta boxes only + legacy fallback), forms (theme side), search-filters, template-functions, theme-builder, seo, render/*, elementor/{fallback,loader}, demo/{images,content,pages,native,importer}, admin/{settings,setup-wizard}, migrations, compat, updater, woocommerce (if active).
- **CSS**: Tailwind 3 built at dev time (`npm run build`) from `src/tailwind.css`:
  - `important: '.ls-root'` (utilities win over `.elementor *` without `!important`), `corePlugins.preflight: false` + a reset scoped to `:where(.ls-root)`;
  - colors as CSS variables with Elementor Global Color fallback: `--ls-primary: var(--e-global-color-lsprimary, var(--ls-c-primary))`; Tailwind colors use `color-mix(in srgb, var(--x) calc(<alpha-value>*100%), transparent)`;
  - typography tokens via variables (`--ls-font`, `--ls-fs-h`, `--ls-fs-b`, `--ls-fs-r`) and `maxWidth.7xl = var(--ls-container, 80rem)` so settings work without changing the default look;
  - keep selector specificity ≤ 0-3-0 so Elementor Style-tab rules (0-4-0) override the theme;
  - `content` globs must include PHP, JS and any HTML used by layouts; safelist dynamic classes (grid-cols-N).
- Fonts bundled locally (e.g. Vazirmatn variable WOFF2 + OFL.txt), `font-display: swap`, preload only the font actually used.
- Icons bundled (Bootstrap Icons CSS + fonts + LICENSE) and registered as an Elementor icon library tab.
- JS: one vanilla file (`assets/js/theme.js` → terser `theme.min.js`), `defer`, works inside the Elementor editor preview (re-init on `elementor/frontend/init` hooks), `once()` guards, no jQuery dependency.
- RTL: force `dir="rtl"` / `lang="fa-IR"` option (on by default for Persian designs) applied on `after_setup_theme` priority 1 so block styles pick their `-rtl.css`.
- Jalali dates option, Persian digits helper (`toFa`, `toEn`), `larijani_fa_num()`.

## Templates
`index.php, page.php, single.php, archive.php, search.php, 404.php, comments.php, searchform.php (all search boxes through get_search_form() with ls_variant), archive-<cpt>.php, single-<cpt>.php, taxonomy-<tax>.php, woocommerce/*` — each first calls `larijani_do_location( 'single'|'archive'|… )` so an Elementor template (Pro condition or the built-in theme builder) can replace it.

## Header/footer
PHP renderers with settings (style variants, top bar, sticky, search drawer, CTA, mobile drawer with focus trap, Escape, aria-expanded, scroll lock). Menus come from `wp_get_nav_menu_items` + `_wp_menu_item_classes_by_context`; active state via `larijani_menu_item_active()` (see references/05 — WordPress marks the posts page `current_page_parent` on every non-page view).

## Data model
- Options: theme mods `ls_*` with central defaults (`larijani_option_defaults()`), one field list (`larijani_option_fields()`) used by both Customizer and the theme settings page.
- Content types (projects, leads) live in the companion plugin (references/03).
- Versioned idempotent migrations (`inc/migrations.php`), run in admin only, stored version updated only after all steps succeed.
- Deprecated wrappers (`inc/compat.php`) when renaming public helpers — never `eval`.
