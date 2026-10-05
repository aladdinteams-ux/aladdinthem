# Schema & Entity Map — Larijani Stone 1.2.0

Principle: one owner per schema type; no fabricated data (no invented ratings, reviews, prices, awards or credentials). The theme only emits data that is visibly present on the page or entered by the site owner in **Dashboard › Larijani Stone**.

## Ownership

| Entity / type | Where | Owner without SEO plugin | Owner with SEO plugin | Source of values | Evidence |
|---|---|---|---|---|---|
| `Organization` (`@id` = `/#organization`) | front page | theme `ls_organization_schema()` | SEO plugin (theme silent) | theme settings: brand name, footer "about", email, phones → `ContactPoint` (`+98…`, `areaServed: IR`), address → `PostalAddress`, social links → `sameAs`, custom logo | JSON parsed on home (crawl) — PASS |
| `FAQPage` | pages with the FAQ widget (services, contact) | theme FAQ widget, once per page, not in the editor | none by default (filter `ls_theme_schema_enabled` → `faq`) | visible FAQ items | JSON parsed — PASS |
| `Product` + `Offer` | WooCommerce product pages | WooCommerce core (`WC_Structured_Data`) — theme triggers collection because its layouts do not fire `woocommerce_single_product_summary` (fixed in 1.2.0) | WooCommerce / SEO plugin | WooCommerce product data | JSON parsed on WP 7.1.2 + WC 11.1.2: `Product{name,sku,url,description,offers}`; no `aggregateRating`/`review` because the product has no real reviews — PASS |
| `BreadcrumbList` | — | not emitted (theme breadcrumbs are visual only) | SEO plugin | — | WARN (use an SEO plugin) |
| `WebSite` / `SearchAction` | — | not emitted | SEO plugin | — | N/A |
| `Article` / `BlogPosting` | posts | not emitted | SEO plugin | — | WARN (use an SEO plugin) |
| `LocalBusiness` | — | not emitted (address is free text, no geo/opening-hours structure) | SEO plugin | — | N/A |

## Safety
- All theme JSON-LD goes through `ls_print_json_ld()`: `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT` — user text cannot close the `<script>` element (fixed in 1.2.0).
- Product-card "rating"/"review" visuals in the demo are sample text in widgets and are **not** turned into schema. Replace the sample testimonials/reviews with real ones before launch.

## Notes / WARN
- WooCommerce currency `IRT` (Toman) is not an ISO 4217 code; Google expects `IRR`. This is store configuration, not theme output.
- Demo products have no images in the sandbox, so `image` was absent there; real products with featured images include it.

**Schema Status: PASS** (no duplicate owners, no fabricated data, valid JSON on all tested pages; WARN items are delegated types).
