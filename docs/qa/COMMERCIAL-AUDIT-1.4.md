# Commercial readiness audit — Larijani Stone 1.3.0 → 1.4.0

Date: 2026-10-08 · Baseline: release 1.3.0 (commit `c73275d`, local tag `backup-1.3.0-pre-commercial`, original ZIP kept).
Statuses: PASS / WARN / FAIL / UNKNOWN / NOT EXECUTED. Each item lists the evidence used to find it and, after the fix, how it was verified.

## Tools run on the baseline
- **Theme Check** (WordPress/theme-check, GitHub `20260901`) on the theme installed from the 1.3.0 ZIP: **7 REQUIRED, 5 WARNING, 6 RECOMMENDED, 3 INFO**.
- **PHPCS / WPCS 3.1**: security sniffs 0; `PrefixAllGlobals`: 381 (264 functions, 53 classes, 32 globals, 27 hook names, 3 constants) because the `ls` prefix is shorter than 3 characters.
- Manual code review of every PHP/JS/CSS file listed in the brief (forms, uploads, AJAX, settings, importer, Elementor, WooCommerce).

## Findings

| ID | Area | Severity | Finding | Evidence |
|---|---|---|---|---|
| A1 | Packaging | High (market rejection) | No `screenshot.png` | Theme Check REQUIRED |
| A2 | Packaging | High | No `readme.txt` | Theme Check REQUIRED |
| A3 | i18n | Medium | Text domain `larijani` ≠ theme slug `larijani-stone` | Theme Check REQUIRED |
| A4 | Architecture | High | `register_post_type` / `register_taxonomy` (projects, leads) in the theme — content disappears when the theme is switched | Theme Check REQUIRED |
| A5 | Architecture/Security | High | `upload_mimes` filter in the theme allows **unsanitised SVG** for administrators (SVG may carry JavaScript) | Theme Check REQUIRED + review `inc/setup.php` |
| A6 | Compatibility | High | 2-letter prefix `ls_` / `LS_` on 264 functions and 53 classes → fatal "cannot redeclare" if any plugin uses the same names | WPCS PrefixAllGlobals |
| A7 | Packaging | Low | QA JSON with lowercase "wordpress" keys shipped inside the theme ZIP | Theme Check REQUIRED |
| A8 | Legal | Medium | No copyright notice | Theme Check WARNING |
| S1 | Forms | High | Minimum-fill-time check can be bypassed: it trusts the client `ls_ts` value and is skipped when the field is missing | review `ls_handle_lead()` |
| S2 | Forms | High | Tracking code `LS-` + 5 random digits: guessable, collisions possible (90 000 values) | review |
| S3 | Forms | Medium | No duplicate-submission protection (double click / resubmit creates several leads and e-mails) | review |
| S4 | Forms | Medium | Server does not know which fields are required or their types: labels/types are posted by the client; only phone is validated; e-mail not validated | review |
| S5 | Forms | Medium | Rate limit only per IP (8 / 10 min) and always uses `REMOTE_ADDR` (no trusted-proxy option, no per-phone/global limit) | review |
| S6 | Uploads | **High** | Lead attachments (customer drawings) are stored in the public uploads folder and media library; reachable by URL (only the file name is random); e-mail links point to the public URL | review `ls_handle_lead_files()` |
| S7 | Uploads | High | ZIP/DWG accepted by extension/MIME map only; no magic-byte check, no inspection of ZIP contents | review |
| S8 | Uploads | Medium | No protection against script execution / directory listing for stored attachments | review |
| P1 | Performance | Low | Post view counter writes post meta on every page view | review `ls_track_views()` |
| M1 | A11y | Medium | `prefers-reduced-motion` only respected for reveal animations; smooth scrolling, counters and slider scrolling still animate | review CSS/JS |
| M2 | Theme review | Low | `.bypostauthor`, `.gallery-caption` styles missing | Theme Check RECOMMENDED |
| C1 | Settings | Medium (commercial) | Missing buyer settings: font family, base type size, button style, content width, mobile options; favicon/site icon not surfaced in the theme panel | review settings fields |
| C2 | Setup | Medium (commercial) | No server requirements check, no plugin installer, no import rollback / per-step error report | review importer |
| C3 | Distribution | Medium | No companion plugin, no update/licence architecture, no Persian install / import / settings guides | review |
| L1 | Licences | Medium | Demo images come from Google Stitch (AI-generated design assets, hot-linked or copied into the media library); redistribution licence is **UNKNOWN** | review `inc/demo/images.php` |

Already PASS on the baseline (re-verified after changes): PHP syntax, PHP 7.4 compatibility (static), WPCS security sniffs, capability + nonce checks on settings/setup/meta boxes, JSON-LD escaping, no secrets.

## Plan (1.4.0)
1. Prefix rename to `larijani_` / `Larijani_` / `LARIJANI_` (functions, classes, constants) with **back-compat wrappers** for the documented template functions and unchanged option keys, meta keys, hook names, CSS classes and Elementor widget names (no data or design change). Text domain → `larijani-stone`.
2. Companion plugin **Larijani Stone Core**: post types, taxonomy, meta registration, leads admin, form handler, secure uploads, SVG sanitiser. Same post-type/meta keys → existing content is kept.
3. Forms: server-signed form token (fill time + age), signed field schema (required/type/label), unguessable tracking code, duplicate detection, e-mail/phone validation, multi-key rate limiting, private file storage with magic-byte checks and admin-only download, migration of old public attachments.
4. Settings: typography/font, button radius, content width, mobile options, site icon link, reset.
5. Setup wizard: requirements, plugin install with consent, import with per-step log and rollback.
6. Reduced motion, small theme-review CSS, screenshot, readme, guides, licences, POT, packaging, tests.
