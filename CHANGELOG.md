# Changelog — Larijani Stone

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
