# AI Search (GEO / AIO) Readiness — Larijani Stone 1.2.0

Scope: what a **theme** can control so that AI answer engines (Google AI Overviews, ChatGPT search, Perplexity, Bing Copilot) can crawl, understand and cite the site. Rankings or citations cannot be guaranteed or measured from a sandbox → outcome metrics are NOT EXECUTED.

| Factor | Implementation | Evidence | Status |
|---|---|---|---|
| Content in server HTML (no JS-only rendering) | all widgets render in PHP; JS only enhances (drawer, filters, calculators) | page source contains full text (crawl) | PASS |
| Semantic structure | one H1, no heading skips, `header/nav/main/footer` landmarks, lists/tables for specs | 285-check matrix + detail crawl | PASS |
| Language signal | `<html dir="rtl" lang="fa-IR">` (fixed in 1.2.0) | curl | PASS |
| Entity definition | `Organization` with `@id`, contact points, address, `sameAs` | JSON-LD parsed | PASS |
| Q&A extractability | FAQ widget → visible Q&A + `FAQPage` JSON-LD | JSON-LD parsed | PASS |
| Product facts | WooCommerce `Product/Offer` JSON-LD (fixed in 1.2.0); spec tables in HTML | JSON-LD parsed | PASS |
| Author / expertise signals (E-E-A-T) | post author box (name, role, bio meta fields), dates, reading time | visual; no `Person` schema | WARN (add via SEO plugin) |
| Freshness | published/modified dates on posts (Jalali display) | visual | PASS |
| Crawl access | core robots.txt; no theme rule blocks AI crawlers | robots.txt | PASS (owner decides on AI-bot policy) |
| `llms.txt` | not provided by the theme (site-level content decision) | — | NOT EXECUTED |
| Citable, unique copy | demo copy comes from the design; owner must publish original, factual content | — | UNKNOWN (content) |
| Measurement of AI citations | requires live site + external tools | — | NOT EXECUTED |

**GEO/AIO Readiness: PASS (technical foundations) / UNKNOWN (outcomes)**.
