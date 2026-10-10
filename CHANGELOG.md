# Changelog — Larijani Stone

## 1.4.1 — 2026-10-10

### Fixed
- **Header menu**: on the shop, product and product-category pages (and on projects) the "Blog" item was highlighted. WordPress marks the posts page as parent of every non-page view; the theme now highlights the shop page for WooCommerce views, the portfolio page for projects and the blog only for posts.
- **Home page search panel** now really filters: tabs send the real product categories (`mold`, `machinery`, `chemical`; old values such as `molds` are mapped, unknown categories show the whole shop instead of an error page); each select option carries search words or a product tag/attribute slug and filters the WooCommerce shop (title, descriptions, SKU) or, without WooCommerce, the theme catalog page in the browser. Options can be limited to tabs; "order size" is informational and shows a bulk-price note. Active filters are listed above the results with remove links; no-match combinations show a clear empty state.

### Added
- Rounded "glass" dropdowns (translucent with backdrop blur) for the search panel selects, keyboard and screen-reader accessible (combobox + listbox), native select kept for the form.
- Theme author **Aladdin Theme (علاءالدین تم)**: theme/plugin/child headers, readme, author badge and "about" card in the theme settings, credit in the admin footer of the theme screens.

## 1.4.0 — 2026-10-08 (commercial release)

### Security
- **Forms** are handled by the new companion plugin *Larijani Stone Core*: signed field schema (server enforces fields, labels, types, required flags and options), signed single-use submission token fetched on first interaction (minimum 3 s, maximum 2 h, atomic claim against replay and double submit), honeypot, rate limits per IP (attempts and successes), per phone and site-wide, duplicate detection (15 min), Iranian/international phone and e-mail validation, unguessable tracking codes `LS-YYMMDD-XXXXXX`, generic error messages.
  The 1.3 time trap trusted a client-supplied timestamp and was skipped when it was missing; tracking codes were 5 random digits.
- **Uploads**: extension allow-list, per-type size limits, magic-byte/image checks, ZIP inspection (blocked entry types, path traversal, zip bombs), private storage outside the media library (`.htaccess`/`web.config` deny, random directory, optional `LARIJANI_CORE_PRIVATE_DIR`), admin/editor-only download through a nonce-checked handler. 1.3 stored customer files as public media with public links in e-mails.
- Admin-triggered, hash-verified migration of 1.3 public attachments into private storage.
- SVG uploads (administrators) are rebuilt from an allow-list instead of being accepted raw.
- Setup actions (plugin install, import, undo, reset/restore) require nonces and capabilities.

### Architecture
- Projects (`ls_project`, `ls_project_cat`) and inquiries (`ls_lead`) moved to the companion plugin with identical keys (no data conversion). Sites updated from 1.3 keep their projects/inquiries visible through a compatibility fallback until the plugin is installed.
- PHP prefix `ls_` → `larijani_` (functions, classes, constants, globals); text domain `larijani-stone`. Deprecated `ls_*` wrappers for the public helpers; hook names, options and meta keys unchanged.

### Added
- Typography & layout settings (font, heading/body scale, tablet/mobile scale, content width, button radius, opt-out of external sample images); reset all settings with backup/restore; logo and site icon shortcuts.
- Setup wizard: server requirements, plugin install/activation on click (bundled core plugin, Elementor, WooCommerce), import journal with undo; activation never imports into a site with existing content; theme-builder slots / Pro conditions and the front page are never taken over silently.
- Optional update-provider hook (disabled by default, no external calls).
- `readme.txt`, `screenshot.png`, Persian guide `docs/INSTALL-FA.md`, plugin POT, `build/package.sh`.

### Improved
- `prefers-reduced-motion`: smooth scrolling, counters, sliders and transitions respect it.
- Hero/LCP images get `fetchpriority="high"`; Vazirmatn is preloaded only when used; view counter is a single atomic query and ignores prefetch/HEAD.
- All search boxes use `get_search_form()` (filterable) and have labels.
- Bootstrap Icons stylesheet version string matches the bundled 1.13.1.

## 1.3.0 — 2026-10-06 (native Elementor layouts)

### Changed
- **Pages and templates are built with Elementor Containers (Flexbox/Grid) instead of Section/Column.**
- **Static design sections now use Elementor's core widgets** (Heading, Text Editor, Button, Icon, Image, Icon List, Star Rating, Progress Bar): section headings, icon/benefit cards (5 styles), about + stats, testimonials grid, call-to-action bands (4 styles), process steps, contact banner, contact cards. Every text, icon, link and image is edited with Elementor's own controls; Style-tab values override the theme classes (verified in Elementor 4.0.8: colour, typography, button background, grid columns).
- Data-driven/interactive parts (header/footer, hero search, products/catalog/product page, posts, lead forms, FAQ schema, sliders, charts) remain theme widgets — see `docs/ELEMENTOR-NATIVE-MAP.md`.
- Menus created on install link to the **page objects** and WooCommerce **product categories** (no hard-coded URLs); without WooCommerce, category links open the catalog pre-filtered (`?ls_cat=`).
- Elementor's Container feature is switched on during setup if an old site still has it off (`ls_enable_elementor_containers`).

### Added
- Setup option **"Rebuild layouts"** for pages/templates created by the theme (keeps IDs, slugs and menus; previous Elementor data backed up in `_ls_elementor_data_backup`). Admin notice after updating from 1.2 when old layouts exist.
- No-Elementor fallback renders Containers and the core widgets with Elementor's markup (same look without Elementor).
- `ls_native_layouts` filter (return `false` for the 1.2 theme-widget layouts).

### Fixed
- Re-imported layouts were hidden behind Elementor's element/CSS caches (`_elementor_element_cache`, `_elementor_css`) — caches are cleared on save.
- Low-contrast grey labels in the classic hero, categories, showcase cards and light header (AA 4.5:1).
- Progress bar accessible name; WooCommerce shop archive meta description fallback.

## 1.2.0 — 2026-10-05 (QA audit release)

### Security
- FAQ JSON-LD is now printed with HTML-significant characters hex-escaped (a question/answer could previously close the `<script>` element).
- Lead-form attachments get unguessable file names (customer documents were stored under predictable public URLs).
- Leads (personal data) are restricted to editors/administrators (`edit_others_posts`); authors/contributors no longer see the menu.
- Lead submissions are bounded to 40 fields; superglobal access hardened.

### Fixed
- **Fatal error with Elementor** `Widgets_Manager::register(): Argument #1 must be of type Widget_Base` (1.1.1 hotfix, kept).
- RTL is now applied before `init`, so block styles registered on `init` (e.g. WooCommerce Cart/Checkout blocks) load their `-rtl.css`; this removed a 20 000 px horizontal scroll on the block checkout.
- Removed `body { overflow-x: hidden }` (it masked overflow and can break sticky headers); real overflow causes were fixed instead.
- Long unbroken strings (model codes, URLs) wrap instead of widening cards/headings (`overflow-wrap: anywhere` scoped to theme markup).
- Header navigation wraps instead of pushing the action buttons off-screen with long/many menu items; CTA uses its short label at 1024–1279 px.
- Store page toolbar overflow at 320 px.
- Content tables scroll inside the article on narrow screens.
- Long button labels wrap instead of being clipped.
- Product cards / section headings no longer link to `#`; they fall back to the sample product, shop or blog page.
- Re-running the setup no longer overwrites Elementor's system colours/typography (only on brand-new sites) and never replaces an existing static front page.
- Setup objects are matched by an ownership key (`_ls_demo_key`), not by title only.
- WooCommerce template overrides declare `@version`, keep the `WC_Product` guard and `wc_product_class()` wrapper.
- Child-theme compatible stylesheet enqueue (parent style no longer loads the child `style.css`).

- WooCommerce `Product`/`Offer` JSON-LD was missing on product pages (the theme layouts never fired `woocommerce_single_product_summary`); it is now collected before WooCommerce prints it.
- `<html lang>` is `fa-IR` when RTL is forced on a non-RTL site language (was `en-US` with Persian content).
- Removed an unused, cache-unsafe nonce from the front-end script data.

### Accessibility
- Mobile menu and search dialogs: `role="dialog"`, focus trap, Escape, focus restoration to the opener.
- Accessible names for CTA/newsletter phone fields and the calculator slider; alt text for the demo article images.
- Heading hierarchy fixed (sidebar/footer/card titles); names and badges are no longer headings.
- Minimum 24 px targets for the price filter button and the hero filter selects.
- Colour contrast (WCAG AA 4.5:1): `outline` #757870 → #686B63, `accent-emerald` #059669 → #047857, `accent-amber` #B45309 → #92400E; small grey labels slate-400 → slate-500 on light backgrounds. (Slight, intentional deviation from the design palette.)
- No heading-level skips on any tested route (contact cards, page-hero card, product tabs, store grid has a visually hidden H2).
- Hero search type buttons use `role="group"` + `aria-pressed` instead of an incomplete `tablist`.

### Added
- Fallback `<meta name="description">` (excerpt → tagline → footer about text) and page excerpts, only when no SEO plugin is active.
- Organization JSON-LD (front page) and `noindex,follow` for search results — only when no SEO plugin is active (`ls_seo_plugin_active`).
- Versioned migrations (`inc/migrations.php`), child theme package, translation template `languages/larijani.pot`.
- Minified `theme.min.js` / `admin.min.js` (deferred), block-library CSS skipped on Elementor-built pages.
- `Tested up to: 7.1` (runtime-tested on WordPress 7.1.2 + WooCommerce 11.1.2).
- Docs: developer hooks, QA reports, schema/entity map, AI-search readiness, performance, security, accessibility, release manifest.

## 1.1.1 — 2026-10-05
- Fix fatal error with the real Elementor plugin (widget base class bound too early).

## 1.1.0 — 2026-10-04
- Automatic setup on activation, dashboard settings page, no-Elementor fallback renderer, design fidelity pass, store page, theme.json.

## 1.0.0 — 2026-10-04
- Initial release.
