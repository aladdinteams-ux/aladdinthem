=== Larijani Stone Core ===
Contributors: aladdintheme
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Companion plugin of the Larijani Stone theme: portfolio projects, customer inquiries and secure forms with private file uploads.

== Description ==

* Post types `ls_project` (projects, with `ls_project_cat`) and `ls_lead` (inquiries). Keys are identical to theme 1.3, so existing content is kept as is.
* Secure public forms: signed field schema, signed single-use submission token (minimum fill-in time, expiry), honeypot, per-IP / per-phone / site-wide rate limits, duplicate detection, server-side validation, unguessable tracking codes.
* Private uploads (JPG, PNG, WEBP, PDF, DWG, ZIP): content checks, ZIP inspection, storage outside the media library, admin-only download with nonce and capability checks.
* Hash-verified migration of attachments saved publicly by theme 1.3.
* CSV export, WordPress privacy exporter/eraser, sanitised SVG uploads for administrators.
* Data is kept on deactivation and uninstall unless "delete data on uninstall" is enabled. Projects are never deleted.

== Filters ==

* `larijani_core_form_settings` – limits (min seconds, token TTL, rate limits, lengths).
* `larijani_core_upload_rules`, `larijani_core_upload_limits` – allowed types and sizes.
* `larijani_core_client_ip` – client IP behind a trusted reverse proxy.
* `larijani_core_is_spam` – plug an external spam service.
* `larijani_core_lead_email_to` – notification recipient.
* Action `ls_lead_submitted` (compatible with theme 1.3) and `larijani_core_lead_submitted`.

Define `LARIJANI_CORE_PRIVATE_DIR` in wp-config.php to store files outside the web root.

== Changelog ==

= 1.0.0 =
* First release (functionality moved from the Larijani Stone theme 1.3 with stronger security).
