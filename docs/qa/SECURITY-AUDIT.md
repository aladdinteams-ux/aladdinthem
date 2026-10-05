# Security Audit — Larijani Stone 1.2.0

Date: 2026-10-05 · Method: static review + automated sniffs + runtime checks in a sandbox.
Statuses: PASS / WARN / FAIL / UNKNOWN / NOT EXECUTED (PASS only with evidence).

## Tooling (evidence)

| Check | Tool | Result |
|---|---|---|
| PHP syntax | `php -l` (PHP 8.3.6), every `.php` file | PASS — 0 errors |
| PHP 7.4+ compatibility | PHPCompatibilityWP (`testVersion 7.4-`) | PASS — 0 errors |
| Output escaping / nonces / input sanitising / SQL / redirects | WPCS 3.1 sniffs `EscapeOutput`, `NonceVerification`, `ValidatedSanitizedInput`, `DB.PreparedSQL`, `SafeRedirect` | PASS — 0 errors in shipped code (1 finding in the dev-only CLI `build/export-templates.php` fixed; that file is not in the ZIP) |
| Suppressed escaping (`phpcs:ignore`) | manual review of 280 suppressions | PASS — they go through helpers that escape internally (`ls_icon`, `ls_link_attrs`, `ls_kses`, `ls_img`), whitelisted tags, or admin-sanitised settings |
| Secrets | regex scan (API keys, tokens, private keys, AWS/GitHub/OpenAI patterns) | PASS — none found |
| Machine paths in release | scan for `/tmp`, `/home`, `/root` | PASS — none in shipped files |

## Findings and fixes

| # | Severity | Finding | Root cause | Fix | Status |
|---|---|---|---|---|---|
| S1 | Critical | FAQ JSON-LD could break out of `<script>` (`</script>` in a question/answer) | `wp_json_encode` with `JSON_UNESCAPED_SLASHES`, no hex escaping | `ls_print_json_ld()` uses `JSON_HEX_TAG\|JSON_HEX_AMP\|JSON_HEX_APOS\|JSON_HEX_QUOT` | PASS (fixed) |
| S2 | High | Lead-form attachments saved under predictable public URLs (customer documents) | original file name kept | random 16-char prefix `lead-xxxxxxxxxxxxxxxx-name.ext` | PASS (fixed) |
| S3 | High | Leads (personal data) visible to authors/contributors | CPT used default `post` capabilities | capabilities mapped to `edit_others_posts`, `create_posts` → `do_not_allow` | PASS (fixed) |
| S4 | Medium | Unbounded number of submitted fields | no cap | max 40 fields (`ls_lead_max_fields`) | PASS (fixed) |
| S5 | Medium | Setup could overwrite site owner content (front page, Elementor system kit) | title-only matching, unconditional writes | ownership key `_ls_demo_key`, front page only on fresh sites, kit system settings only on fresh sites | PASS (fixed) |
| S6 | Info | Public lead AJAX has no nonce | intentional: full-page caching would serve stale nonces | honeypot + minimum fill time (runtime: too-fast submit → HTTP 400) + IP rate limit; uploads via `wp_handle_upload` with MIME allow-list | WARN (by design) |

## Capability / nonce matrix (verified in code)

| Action | Capability | Nonce |
|---|---|---|
| Theme settings save / reset | `edit_theme_options` | yes |
| Setup / import / re-run | `manage_options` | yes |
| Page / post / project meta boxes | `edit_post` | yes (+ autosave guard) |
| WooCommerce product "Larijani" tab | `edit_post` via WooCommerce save hook | WooCommerce nonce |
| Lead list (admin) | `edit_others_posts` | — |
| Public lead submit (AJAX) | public | none (see S6) |

## Browser exposure
No secret, API key or credential is localised to JavaScript. `wp_localize_script` exposes only the AJAX URL and UI strings (an unused, cache-unsafe `ls_lead` nonce was removed in 1.2.0).

## Not executed / unknown
- Dynamic penetration test (fuzzing, authenticated CSRF replay): NOT EXECUTED.
- Behaviour behind real page caches / WAF: UNKNOWN.

**Security Status: PASS** (no open Critical/High; one documented design WARN).
