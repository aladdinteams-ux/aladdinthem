# 12 — Working on an existing / live site (independence from the previous theme)

Contents: 1 Permission and access · 2 Inventory · 3 Snapshot · 4 Takeover · 5 Cleanup and duplicate pages · 6 Verification

## 1. Permission and access
Confirm the scope: build files only, or apply on the live site. With "files only", change nothing on any site. Live changes, deletions, deactivations and real e-mails each need explicit permission. Never store or echo credentials.
Access options, in order of preference: WP-CLI over SSH run by the user or a configured tool; a browser tool in which the user is already logged in; the user performing the clicks while you guide them. Never ask the user to paste passwords into the chat; if credentials are unavoidable, prefer an application password the user can revoke.

## 2. Inventory before any change
Active theme and child theme, companion plugin (old and new), Elementor and Pro versions, active kit, front page and posts page, menus and their locations, page templates, **published Theme Builder templates and their conditions**, Popups, Custom Code, Custom Fonts/Icons, old companion CSS, caching plugins, CDN, injected scripts.
Identify the template that actually renders each area from the page itself (template ID / `elementor-<id>` classes in the HTML) and match it in the dashboard. A disabled control or a removed function call is not proof of independence.

## 3. Snapshot
Before changing: front page and posts page options, kit settings, menu locations, template selections and conditions, affected wrappers/CSS, plus a database backup when allowed. Take a checkpoint before and after every migration or external change.

## 4. Takeover
- Priority for header, footer, single and archive: the project's approved design is the default; using another Pro condition must be a deliberate, documented choice.
- Installing the new theme does not remove old global Pro conditions — old header/footer templates keep rendering until their conditions are removed or the templates are trashed (with permission). After changing conditions, regenerate Pro's conditions cache (Theme Builder → re-save the template's conditions, or in code `\ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_cache()->regenerate()` as in the Larijani importer — that call was never run against a real Pro install, so verify it) and clear Elementor's CSS cache, then page/CDN caches.
- Conflict with the import rule in references/04 ("never add conditions over the owner's template"): that rule protects owners during an automatic import. When the user asked to take over the site with the new design, show which old templates win and ask before removing their conditions.
- Connect the correct home page, menus, the theme's own kit and its templates; keep previous data so everything can be restored.
- Check Popups, Custom Code, old plugin CSS, cache and CDN separately. Don't deactivate unrelated plugins automatically. Clearing caches never replaces fixing an active old template.

## 5. Cleanup (only with permission)
- Decide that a page is old from ownership, content, history, links and the replacement list together; date or title alone is not enough.
- Never delete legal pages, account, shop, cart, checkout, product or customer data without identification and permission.
- Prefer Trash (restorable); permanent deletion needs separate permission.
- Record IDs, replacements and URL effects; add redirects only where needed (old URL → its real replacement), never everything to the home page.
- Existing duplicate such as `contact-us-2`: decide (with the user) which page is canonical; move the other to Trash (WordPress renames a trashed page's slug to `…__trashed`, which frees the slug); set the canonical page's slug to `contact-us`; rebuild menu items from page IDs; add one 301 redirect from the `-2` URL using the site's existing redirect tool (Redirection plugin or the SEO plugin's redirect manager) with permission — theme code only as a last resort — and verify with `curl -I`; re-check internal links and the sitemap.

## 6. Verification
After the change, collect evidence: screenshots of the public pages and the dashboard, the page HTML showing the new template IDs (`elementor-<id>`), every language, header/footer/single/archive, menus, forms, after purging caches. A delivered ZIP is not proof of a live installation.
