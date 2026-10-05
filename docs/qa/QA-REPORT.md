# QA Report — Larijani Stone 1.2.0 (Master Audit & Repair)

Date: 2026-10-05 · Spec: *WordPress Elementor Theme Builder v0.6.1* audit prompt · Branch: `claude/wordpress-elementor-theme-conversion-t2rwiw`
Status vocabulary: **PASS / WARN / FAIL / UNKNOWN / NOT EXECUTED**. Nothing is marked PASS without evidence; missing tooling is reported as NOT EXECUTED.

## 1. Environments actually used

| Env | Contents | Used for |
|---|---|---|
| A | WordPress 6.5.5, SQLite, PHP 8.3.6 built-in server, **Elementor API shim** (strict type-hints, `Widget_Base` autoloaded only after `init`), no-Elementor fallback renderer | all theme pages, responsive matrix, Lighthouse, interactions, stress content |
| B | WordPress 7.1.2, SQLite, WooCommerce 11.1.2 (HPOS, Cart/Checkout blocks), Elementor shim | WooCommerce flows, structured data, RTL block CSS |
| C | **fresh** WordPress 7.1.2 + WooCommerce 11.1.2, theme switched in → auto-setup | first-install behaviour, migrations, link crawl |
| Static | php -l, PHPCompatibilityWP, WPCS 3.1 security sniffs, secret/path scans, terser, Tailwind build | code quality |

**Not available:** the real Elementor / Elementor Pro plugins (downloads blocked: 403/404), MySQL, Safari/Firefox, real devices, screen readers, field (CrUX) data.

## 2. Critical / High issues — found → root cause → fix → re-test

| ID | Sev. | Issue | Root cause | Fix | Re-test |
|---|---|---|---|---|---|
| C1 | Critical | Fatal error on activation with Elementor: `Widgets_Manager::register(): Argument #1 must be of type Widget_Base, LS_Widget_Header given` (reported by owner) | widget base class was declared before Elementor's `Widget_Base` was loadable, so it extended the fallback class | base class required inside `elementor/widgets/register`, subclass guard per widget | strict shim: old code fatal → new code HTTP 200 — PASS (real plugin NOT EXECUTED) |
| C2 | Critical | FAQ JSON-LD could close `<script>` (stored XSS vector via FAQ text) | JSON encoded with unescaped slashes/tags | `ls_print_json_ld()` with `JSON_HEX_*` flags | JSON parses; `</script>` impossible — PASS |
| C3 | Critical | Lead attachments at predictable public URLs (customer documents) | original filename kept | random 16-char prefix | code review (upload not exercised at runtime) — PASS |
| C4 | High | Lead personal data visible to contributors/authors | CPT used default post caps | `edit_others_posts`, no create | code review — PASS |
| C5 | Critical | Block checkout 20 334 px wide in RTL | RTL forced too late (`wp_loaded`), block styles registered LTR at `init` | force RTL at `after_setup_theme` | Woo 25/25 PASS, 0 overflow |
| C6 | High | Re-running setup overwrote the owner's front page and Elementor system colours/typography; objects matched by title | non-idempotent importer | ownership key `_ls_demo_key`, front page only on fresh sites, `kit_system` only on fresh sites, migration 1.2.0 backfills keys | fresh site (env C) + re-run — PASS |
| C7 | High | `body { overflow-x: hidden }` masked real overflow (forbidden by spec) | CSS shortcut | removed; real causes fixed (long strings, nav, store toolbar 320 px, tables, buttons) | 285 + 133 + 57 checks, 0 overflow — PASS |
| H1 | High | WooCommerce Product/Offer JSON-LD missing | theme layouts never fire `woocommerce_single_product_summary` | collect data on `wp_footer` (9) | JSON parsed on env B — PASS |
| H2 | High | `<html lang="en-US">` with Persian content | forced RTL without language | `lang="fa-IR"` when RTL is forced on a non-RTL locale | all routes `fa-IR` — PASS |
| H3 | Medium | Contrast failures, heading skips, `tablist` without tabs, no meta description | design tokens / markup | tokens darkened, headings re-levelled, `role=group`+`aria-pressed`, fallback meta description | Lighthouse A11y 100, SEO 100 (10/10 runs) — PASS |

Critical/High found: **10** (7 Critical/High in security, stability, layout and data safety + 3 SEO/a11y High/Medium). Fixed: **10**. Open FAIL: **0**.

## 3. Release gate

| Gate | Status | Evidence / note |
|---|---|---|
| Package integrity (installable ZIP, single root folder `larijani-stone/`, no dev files) | PASS | `release-manifest.json` (file list check, SHA-256) |
| PHP syntax / PHP 7.4+ compatibility | PASS | 0 errors |
| Security | PASS | `SECURITY-AUDIT.md` |
| Responsive (Chromium, 15 widths, no masking) | PASS | `RESPONSIVE-QA-REPORT.md` |
| Accessibility (automated) | PASS / WARN | Lighthouse 100; target-size exceptions; AT not executed |
| Technical SEO | PASS / WARN | Lighthouse SEO 100; duplicate fallback descriptions until excerpts are written |
| Schema ownership & no fabricated data | PASS | `SCHEMA-ENTITY-MAP.md` |
| GEO/AIO technical readiness | PASS / UNKNOWN (outcomes) | `AI-SEARCH-READINESS.md` |
| Performance (lab) | WARN | desktop 98–100; mobile 78–84 on an uncompressed dev server; field data NOT EXECUTED |
| WooCommerce | PASS | 25/25 incl. order placement (env B) |
| Elementor editor & Theme Builder with the **real** plugins | **NOT EXECUTED** | plugin not downloadable in the build environment |
| Visual fidelity vs Stitch ZIP | WARN | layouts match (earlier side-by-side pass); intentional deviations: darker text tokens for AA contrast, unified footer, global menu |
| Architecture vs spec (native-first, theme/companion split) | WARN | owner required a single theme that builds pages on install; pages use 45 custom widgets instead of native containers; no companion plugin |
| Translation | PASS / UNKNOWN | `languages/larijani.pot` (1 090 strings); `.mo` compile not executed (no msgfmt) |

## 4. WARN register
1. No companion plugin — custom post types, forms and widgets stop working if the theme is switched (data stays in the DB).
2. Not native-first — layouts are custom `ls-*` widgets; native Elementor containers/widgets not used for page structure.
3. Mobile lab LCP 3.7–4.2 s (uncompressed dev server); Bootstrap Icons full set (87 KB CSS + 134 KB font, `font-display: block`) loads globally.
4. 7 "Download PDF" buttons still link to `#` until the owner uploads PDFs.
5. Builder pages share a fallback meta description until page excerpts (now enabled) are written or an SEO plugin is installed; archive canonicals need an SEO plugin.
6. Demo testimonials/ratings/review texts and some prices are sample content from the design — visible only, never in schema — replace before launch.
7. Demo images hot-link `lh3.googleusercontent.com` when the server cannot import them.
8. Public lead AJAX without nonce (by design, cache-safe; honeypot + time trap + rate limit).
9. Target size: small inline text links/checkboxes rely on WCAG 2.5.8 exceptions.
10. WooCommerce currency `IRT` is not ISO 4217 for Google (store setting).

## 5. UNKNOWN / NOT EXECUTED
Real Elementor (free) editor, Elementor Pro Theme Builder conditions, Elementor 4 atomic editor, PHP 7.4 runtime, MySQL/MariaDB, Multisite, Safari/Firefox/real devices, zoom/reflow 400 %, screen readers, CrUX field data, Yoast/Rank Math runtime interop, `.mo` compilation, external penetration test, behaviour behind page caches/CDN.

## 6. Runtime tests executed (counts)
- Responsive matrix: 285 (final) + 133 (regression) + 57 (last) route×width checks.
- Clipping: 4 widths × 19 routes + stress routes (×3 runs).
- Interaction/keyboard/form: 21 checks (×3 runs).
- WooCommerce: 25 checks (×3 runs) incl. a real COD order.
- Lighthouse: 20 runs (10 before, 10 after fixes).
- Accessibility detail crawl: 17 routes × 2 widths (+ re-checks).
- Fresh install + auto-setup + internal link crawl (43 + 49 + 57 links, 0 broken).
- Debug logs: 0 theme warnings/notices/deprecations on all three environments.

## 7. Verdict
No open FAIL and no fixable Critical issue remains. **Release Ready: NO** — the theme's core integration (editing in the real Elementor editor and Elementor Pro Theme Builder) could not be executed here. The ZIP is a **release candidate**: install it on a staging site with the current Elementor (and Pro, if used), open each page in the editor, then promote to production.
