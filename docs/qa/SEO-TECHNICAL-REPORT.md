# Technical SEO Report — Larijani Stone 1.2.0

Date: 2026-10-05 · Environment: WordPress 6.5.5 (sandbox, Elementor API shim) and WordPress 7.1.2 + WooCommerce 11.1.2.
Evidence: Playwright crawl of 19 routes × 15 widths (`matrix-final`), Lighthouse 12.8.2 SEO category on 5 templates × 2 form factors, `curl` of head output.

## Ownership model (no duplicate SEO output)

| Output | No SEO plugin | Yoast / Rank Math / SEOPress / AIOSEO / TSF / Slim SEO active |
|---|---|---|
| `<title>` | WordPress core (`title-tag` support) | plugin |
| Canonical | WordPress core (`rel_canonical` on singular) | plugin |
| Meta description | theme fallback (`ls_meta_description`): excerpt → content → tagline → footer "about" text | plugin (theme prints nothing) |
| Robots | core + theme: search results `noindex, follow` | plugin |
| Organization JSON-LD | theme (front page only) | plugin (theme prints nothing) |
| FAQPage JSON-LD | theme FAQ widget (once per page) | plugin / none |
| Product / Offer schema | WooCommerce core structured data | WooCommerce / plugin |
| XML sitemap | WordPress core `wp-sitemap.xml` (HTTP 200 verified) | plugin |

Detection: `ls_seo_plugin_active()` (filterable). Per-type override: `ls_theme_schema_enabled` filter.
Plugin interop was verified **in code only**; Yoast/Rank Math were not installed at runtime → NOT EXECUTED.

## Results

| Check | Evidence | Status |
|---|---|---|
| Exactly one `<h1>` per page | 285/285 route×width checks | PASS |
| No heading-level skips | detail crawl 17 routes × 390/1280 after fixes | PASS |
| `<title>` present & unique per template | crawl | PASS |
| Canonical on singular pages | crawl (home, pages, posts, projects) | PASS |
| Canonical on archives/blog/category | not output by core without an SEO plugin | WARN (core behaviour; install an SEO plugin) |
| Search results not indexed | `noindex, follow` on `/?s=` | PASS |
| robots.txt + sitemap | core robots.txt with `Sitemap:`; `/wp-sitemap.xml` 200 | PASS |
| `lang` / `dir` | `<html dir="rtl" lang="fa-IR">` (fixed in 1.2.0: was `lang="en-US"` with Persian content when the site locale is English) | PASS |
| Meta description | Lighthouse `meta-description` passes on all tested pages (fixed in 1.2.0) | PASS |
| Unique meta descriptions | builder pages without excerpts share the site/footer description | WARN — fill page excerpts (now enabled) or use an SEO plugin |
| Image `alt` | 0 images without `alt` (crawl) | PASS |
| Crawlable links | 0 `href="#"` on cards/headings; 7 PDF-download buttons still `#` until the owner uploads PDFs | WARN |
| Lighthouse SEO score | 100 on home, store, product, contact, post (mobile + desktop) | PASS (lab) |
| Indexability of content without JS | all content server-rendered (PHP); JS only enhances | PASS |
| Hreflang / multilingual | not applicable (single language) | N/A |

## Recommendations (owner)
1. Install one SEO plugin (Rank Math or Yoast) for per-page titles/descriptions, archive canonicals, breadcrumbs schema.
2. Replace the 7 placeholder PDF links with real files (Elementor → widget → link).
3. Write an excerpt for each landing page.

**Technical SEO Status: PASS with WARN** (no FAIL; WARNs are content/owner tasks and core behaviour).
