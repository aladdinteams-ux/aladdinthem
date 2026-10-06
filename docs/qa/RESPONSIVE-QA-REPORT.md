# Responsive QA Report — Larijani Stone 1.2.0

Engine: Playwright + Chromium (headless). Server: WordPress 6.5.5 sandbox (Elementor API shim) for theme pages; WordPress 7.1.2 + WooCommerce 11.1.2 for store flows.
Rule: horizontal overflow must be fixed at the cause; `overflow-x: hidden` masking is not allowed. **`body { overflow-x: hidden }` was removed in 1.2.0** and every run below was made without it (the crawler also asserts `bodyOverflowHidden = false`).

## Matrix (final run, `matrix-final`)

Widths: 320, 360, 375, 390, 430, 640, 767, 768, 820, 1023, 1024, 1025, 1280, 1440, 1920 px (all breakpoint edges included).
Routes (19): 404, home, home-classic, services, contact, portfolio, catalog (`/shop/`), store (`/products/`), product sample, blog, post (designed article), post-plain, category, search, search-empty, project, projects archive, **stress page**, **stress post**.

| Check | Result |
|---|---|
| Document horizontal overflow (`scrollWidth > clientWidth`) | **0 / 285** — PASS |
| Elements extending past the viewport (offenders) | 0 / 285 — PASS |
| Exactly one H1 | 285 / 285 — PASS |
| HTTP 5xx / PHP errors in markup | 0 — PASS |
| `body` overflow masking | none — PASS |

Regression after the final a11y/contrast fixes (`matrix-regress`, 7 widths × 19 routes = 133 checks): **0 overflow, 0 heading skips** — PASS.

## Content clipping (text cut inside `overflow:hidden` boxes)
`clip.js` at 320/390/768/1280 on all routes and on the stress routes: **0 routes with clipping** — PASS.

## Stress content
A stress page/post with very long unbroken Persian/Latin strings (model codes, URLs), long titles, a long primary-menu item, a wide table and many tags. Fixes made because of it: `overflow-wrap:anywhere` on theme markup, wrapping header navigation, short CTA label at 1024–1279 px, wrapping buttons, scrollable content tables, store toolbar at 320 px. All stress checks now PASS.

## WooCommerce (WP 7.1.2 + WC 11.1.2, HPOS, Cart/Checkout blocks) at 390 and 1280 px
Shop, category, product search, product, add to cart (+notice), cart, checkout (blocks), place order (COD), thank-you page, my account: **25/25 PASS**, no overflow. Root-cause fix: RTL is now forced at `after_setup_theme` so block styles load their `-rtl.css` (the LTR checkout CSS had created a 20 334 px-wide page).

## Interaction at 390 px
Drawer menu via keyboard, `aria-expanded`, dialog role, focus trap, Escape, focus restore, scroll unlock, backdrop close, repeated open/close, search dialog, skip link, focus visibility, form validation, AJAX submit, anti-spam time trap: **21/21 PASS**.

## Not executed
- Real devices, Safari/WebKit and Firefox: NOT EXECUTED (Chromium only).
- Landscape phones / zoom 200–400 %: NOT EXECUTED.
- Real Elementor 4.0.8: 16 routes × 15 widths = 240/240 PASS (1.3.0); Elementor 3.35.9: 32/32 PASS. The editor's own responsive preview was not exercised separately.

**Responsive Status: PASS** (Chromium, 15 widths, no masking).
