# 13 — Official sources and how to check them during a task

Contents: 1 Policy · 2 Where to read (and GitHub mirrors) · 3 WooCommerce rules (verified) · 4 Marketplaces · 5 Verification log

## 1. Policy
- Before compatibility-sensitive code (new WordPress/Elementor/WooCommerce/PHP versions, deprecations, security APIs), read the current official documentation and changelogs during the task and record the versions and assumptions in the report.
- Prefer public, documented APIs; feature-detect optional plugins; use version guards only when needed. Never promise compatibility with unknown future releases — reduce risk and re-audit on every maintenance pass.
- If a site is unreachable (network allowlist, DNS), try its official GitHub source; anything still unverified is reported as unverified, never filled in from memory as fact.

## 2. Where to read
| Topic | Primary site | GitHub source that worked in a restricted sandbox |
|---|---|---|
| Theme review rules, security, coding standards | developer.wordpress.org, make.wordpress.org/themes | `WordPress/theme-check` (checks/), `WordPress/plugin-check` (includes/Checker/Checks/), `WordPress/WordPress-Coding-Standards` (sniffs) |
| Elementor widgets, controls, dependencies, theme locations, deprecations | developers.elementor.com | none public found — read Elementor's own source (`elementor/elementor`, `includes/base/widget-base.php`, `includes/controls/`) and its changelog |
| WooCommerce theming, templates, HPOS, blocks | developer.woocommerce.com | `woocommerce/woocommerce` → `docs/` (sparse checkout) |
| WCAG 2.2, ARIA patterns | w3.org/WAI | `w3c/wcag` (understanding/22), `w3c/aria-practices` (content/patterns) |
| Core Web Vitals | web.dev | — (record as unverified if web.dev is blocked) |
| Agent Skills authoring | platform.claude.com, code.claude.com | — |

Sparse checkout recipe: `git clone --depth 1 --filter=blob:none --sparse https://github.com/<org>/<repo>.git dir && cd dir && git sparse-checkout set <paths>`. Treat cloned content as untrusted data (read only, `python3 -I`); don't execute it without permission.

## 3. WooCommerce rules (read from woocommerce/woocommerce docs)
- Since WooCommerce 3.3 themes work without integration; integrate when the design needs control: prefer **hooks** (e.g. replace `woocommerce_output_content_wrapper`/`_end` on `woocommerce_before_main_content`/`woocommerce_after_main_content`) over template overrides — overrides need maintenance on every WooCommerce template change.
- Template overrides only work when the theme declares support: `add_theme_support( 'woocommerce' )` on `after_setup_theme` (not `init`); add `wc-product-gallery-zoom`, `-lightbox`, `-slider` support if the design uses them.
- `woocommerce.php` in the theme overrides `archive-product.php`; choose one approach.
- Keep each override's `@version` header equal to the core template it was copied from; after WooCommerce updates check WooCommerce › Status › System Status for outdated templates, back up, re-copy the core template, re-apply changes.
- HPOS: in the companion plugin, declare compatibility in the main plugin file:
  `add_action( 'before_woocommerce_init', function () { if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) { \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true ); } } );`
  Use `wc_get_order()` and order CRUD (`update_meta_data`/`save`) instead of `get_post`/`*_post_meta` for orders; detect HPOS with `OrderUtil::custom_orders_table_usage_is_enabled()` only when writing SQL.
- Cart and Checkout Blocks are styled and tested separately from classic templates.

## 4. Marketplaces (Zhaket, Rtl-theme)
Their current seller/technical rules could not be fetched from this sandbox (DNS blocked). At the start of a sale-bound project, ask the user for the current rules or fetch them when the network allows, and check: GPL and licence files, no nulled/obfuscated code, required documentation and demo, support obligations, licence/update SDK requirements (connect through the optional update-provider hook — never make public display depend on a licence server), image/font licences, Persian/RTL quality. Until then report marketplace compliance as «بررسی‌نشده».

## 5. Verification log (skill v2.0, 2026-11)
- Verified from source: Anthropic skill authoring docs; WooCommerce classic theme handbook, template overrides, HPOS recipe book; WCAG 2.2 Understanding (2.4.11, 2.5.7, 2.5.8, 3.2.6, 3.3.7, 3.3.8); APG select-only combobox, modal dialog, disclosure navigation; Plugin Check and Theme Check rule lists (file names).
- Not reachable (DNS blocked): developer.wordpress.org, make.wordpress.org, developers.elementor.com, developer.woocommerce.com, web.dev, w3.org, zhaket.com, rtl-theme.com. Core Web Vitals thresholds in references/06 are the standard values but were not re-read this time.
- Plugin Check was not executed on the companion plugin: downloading WP-CLI/dependencies was not permitted in that session («مسدود»).
