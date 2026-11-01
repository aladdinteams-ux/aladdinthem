# 06 — Performance, SEO/schema/GEO, accessibility (without changing the look)

Contents: 1 Performance · 2 SEO · 3 Schema, GEO and AIO · 4 Accessibility (WCAG 2.2) · 5 WooCommerce

## 1. Performance (Core Web Vitals)
- Targets at the 75th percentile of page loads: LCP ≤ 2.5 s, INP ≤ 200 ms, CLS ≤ 0.1 (standard web.dev thresholds — re-check web.dev when reachable).
- LCP: the hero image is eager + `fetchpriority="high"` and never lazy; preload only the font actually used; no render-blocking third-party CSS; responsive `srcset`/sizes.
- CLS: reserve space (width/height or aspect-ratio) for every image, video, embed and ad slot; `font-display: swap` with a metric-compatible fallback; no content injected above existing content after load.
- INP: keep event handlers short; break long tasks; no full-DOM observers or repeated re-styling loops; defer non-critical JS.
- Load CSS/JS conditionally (widget dependencies, Woo assets only on Woo pages); version assets by file mtime.
- Database: no queries in loops; cache expensive lookups; atomic counters (`UPDATE … = … + 1`); skip prefetch/HEAD requests for view counting.
- Cache-friendly forms (signed token fetched on interaction; no nonce baked into cached HTML; admin-ajax must not be cached).
- Attribute hosting, CDN and external-script problems separately from theme problems. Claim a PageSpeed/Lighthouse score only with a real run and its evidence; never trade security or accessibility for a score.
- Hotlinked design images (e.g. `lh3.googleusercontent.com`): external dependency + visitor privacy + unknown licence → copy locally on request, offer an opt-out, report as a release blocker until licensed images are supplied.

## 2. SEO
- Check: HTTP status codes, canonical, robots/noindex (search results), sitemap, crawlable server-rendered content, titles, one H1 per page, logical heading order, internal links, search, archives, pagination, hreflang and translated URLs.
- With an active SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO, The SEO Framework, Slim SEO) the theme outputs no meta description/OG/canonical/JSON-LD of its own — verify no duplicates with a real plugin.
- Breadcrumbs consistent on all pages, wrapping, direction-aware separators; a post links to its real archive.

## 3. Schema, GEO and AIO
- Define entities first (Organization/brand, Product, Article, LocalBusiness) with stable `@id` URLs; JSON-LD must match visible content.
- Never invent ratings, reviews, experience claims, certificates, counts or business claims; FAQPage only from visible FAQ content; escape JSON so `</script>` cannot break out.
- GEO/AIO: write clear direct answers and definitions, semantic structure, extractable and attributable content (who/what/where, units, dates). Never promise first-page rankings or AI citations.

## 4. Accessibility (WCAG 2.2 AA)
- Landmarks, logical headings, labels for every input (screen-reader label if the design hides it), alt text, skip link, visible focus, full keyboard operation, `prefers-reduced-motion` (no smooth scroll/animations/counters under reduce), sufficient contrast in light **and** dark mode (references/10), RTL/LTR correctness.
- New in WCAG 2.2 (W3C Understanding docs, read from w3c/wcag):
  - 2.4.11 Focus Not Obscured (Minimum): a focused item is at least partially visible — sticky headers and cookie bars must not cover it (use `scroll-padding-top`).
  - 2.5.7 Dragging Movements: every drag action (sliders, range filters, carousels) has a simple pointer alternative (buttons/inputs).
  - 2.5.8 Target Size (Minimum): pointer targets at least 24 × 24 CSS px, or spaced so a 24 px circle around each does not overlap another target.
  - 3.2.6 Consistent Help: help/contact links appear in the same relative place on every page.
  - 3.3.7 Redundant Entry: don't ask for the same information twice in one process (pre-fill or allow selection).
  - 3.3.8 Accessible Authentication (Minimum): no puzzle/recall/transcription test to log in without an alternative (allow paste and password managers).
- Widget patterns follow the WAI-ARIA Authoring Practices (references/10): select-only combobox, modal dialog (off-canvas), disclosure navigation (mega menu).
- Third-party content inside articles: `max-width: 100%` for inputs/selects/textareas/iframes/video.

## 5. WooCommerce
Use real products and real orders in tests; test classic templates and Cart/Checkout Blocks separately; never report the shop as verified without a real order run (references/13 for official rules).
