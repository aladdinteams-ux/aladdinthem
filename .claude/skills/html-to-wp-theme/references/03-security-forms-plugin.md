# 03 — Companion plugin, secure forms, private uploads

Reference: `plugins/larijani-stone-core/` (forms.php, uploads.php, post-types.php, leads-admin.php, svg.php, privacy.php, migrate.php, uninstall.php). Copy and rename the prefix for a new brand.

## Theme ↔ plugin split
- Plugin (`<brand>-core`, own text domain, Requires PHP 7.4 / WP 6.5) owns: CPTs/taxonomies (projects, leads) with `register_post_meta`, project meta box, leads admin, form handler, uploads, SVG sanitiser, privacy exporter/eraser, CSV export, migration of old data, uninstall (keeps data unless the admin opted in; projects never deleted).
- Theme only renders. `larijani_form_hidden_fields( $name, $schema )` delegates to `larijani_core_form_fields()` when the plugin is active; otherwise outputs the same markup and a fallback AJAX handler returns a clear "form not active, please call" message (503).
- Upgrades: if the plugin is missing on a site that already has projects/leads (one-time DB check stored in an option), the theme registers the same post types as a **compatibility fallback** so URLs/content survive; admin notice links to the wizard. Fresh installs never use it. (Theme Check flags this as wp.org-only "REQUIRED" — document it.)
- Plugin ZIP is bundled in the theme (`bundled/<plugin>.zip`) and installed from the wizard on click.
- Lead CPT: `public=false`, `show_in_rest=false`, caps mapped to `edit_others_posts`, `create_posts=do_not_allow`.

## Form protection (do all of these; a nonce alone is NOT enough and breaks with page cache)
1. **Signed schema**: renderer passes fields `key => [label, type, required, options]`; plugin normalises (types whitelist incl. `contact` = phone-or-email, labels stripped, options sanitised) and signs with HMAC (key derived from `wp_salt('auth')`, purpose-separated: `schema` vs `token`). Server accepts only schema fields; tampered schema → 400.
2. **Single-use token** fetched by JS on first interaction (`focusin/pointerdown/touchstart`) from `admin-ajax?action=<brand>_form_token`: payload {issued time, random nonce}, min age 3 s (→ 425 `too_fast` + `retry`, JS waits and resends automatically), max 2 h, claimed atomically (`INSERT IGNORE` into options) right before insert; released if storing fails; daily cron cleanup.
3. Honeypot → fake success with a tracking code in the real format.
4. Rate limits (fixed windows in transients, filterable): IP attempts 10/10 min, IP successes 5/10 min and 20/day, phone 3/h, site 200/h, token requests 30/10 min. Client IP from REMOTE_ADDR; proxies only via filter.
5. Duplicate detection: same form + IP + answers within 15 min → return the original tracking code, no new lead/e-mail.
6. Validation: Iranian mobile/landline + international phones (Persian/Arabic digits normalised), `is_email`, numbers, select/checkbox values must be in options, text ≤ 200, textarea ≤ 5000, arrays rejected in scalar fields; field-level Persian messages returned with `field` key (JS focuses it).
7. Tracking code `LS-YYMMDD-XXXXXX` (unambiguous alphabet, `random_int`, uniqueness check).
8. Generic error messages; internals only in debug log. IP stored as HMAC.
9. JS: in-flight lock (no double submit), button disabled during request, `aria-busy`, re-fetch token after success or `expired`, focus failed field.
10. Old action names (`ls_lead`) routed to the same handler (no token → "refresh the page").

## Uploads
- Allow-list with per-type limits (jpg/png/webp 10 MB, pdf 20 MB, dwg/zip 30 MB, ≤ 5 files, ≤ 50 MB total), all filterable.
- Content checks: magic bytes per type, `getimagesize` for images, finfo rejects script/executable types; ZIP inspected with ZipArchive (blocked extensions incl. double extensions, `.htaccess`, `../`/absolute paths, > 500 entries, > 200 MB unpacked, suspicious compression ratio); no ZipArchive → reject ZIP.
- Store outside the media library in `uploads/<brand>-private-<random24>/leads/Y/m/<lead>/<random32>.<ext>` with `.htaccess` (deny + PHP off), `web.config`, `index.php`; optional constant for a path outside the web root; admin button that probes public reachability.
- Download only via `admin-post.php` + nonce + `current_user_can('edit_post', $lead)`, headers `octet-stream`, `attachment`, `nosniff`, CSP sandbox, `no-store`; realpath must stay inside the private root.
- E-mail never contains file links — only a dashboard link. Files deleted with the lead. Old public attachments: admin-triggered migration that copies, verifies SHA-256, then deletes the public copy.
- SVG for administrators only, rebuilt from an allow-list (no script/foreignObject/on*/external or javascript: hrefs/entities/DOCTYPE).

## Admin
Lead meta box (fields, private file links, legacy warning), list columns (phone with tel link, form, tracking), search by tracking/phone, settings page (recipient e-mail, SVG switch, uninstall data switch, storage status test, migration, CSV export with formula-injection escaping), privacy policy text, exporter/eraser by e-mail or phone.

## Tests that must pass (scripts/qa/forms-attack.py etc.)
no token / forged token / too fast / replay / duplicate / tampered schema / honeypot; required, phone, Persian digits, invalid option, too long, unknown fields, array-in-scalar; PDF ok, PHP-as-PDF, `.php`, fake JPEG, ZIP with PHP, ZIP traversal, clean ZIP, DWG, EXE-as-DWG, >limit size, >5 files, files on a form without file field; rate limits; download by role (admin/editor 200, author/subscriber 403, anonymous and foreign nonce refused); Apache `.htaccess` 403 on file/dir/index/PHP; migration hash match; SVG and privacy.
