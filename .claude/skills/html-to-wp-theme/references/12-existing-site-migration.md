# 12 — Working on an existing / live site (independence from the previous theme)

Contents: 1 Permission · 2 Inventory · 3 Snapshot · 4 Takeover · 5 Cleanup · 6 Verification

## 1. Permission
Confirm the scope: build files only, or apply on the live site. With "files only", change nothing on any site. Live changes, deletions, deactivations and real e-mails each need explicit permission. Never store or echo credentials.

## 2. Inventory before any change
Active theme and child theme, companion plugin (old and new), Elementor and Pro versions, active kit, front page and posts page, menus and their locations, page templates, **published Theme Builder templates and their conditions**, Popups, Custom Code, Custom Fonts/Icons, old companion CSS, caching plugins, CDN, injected scripts.
Identify the template that actually renders each area from the page itself (template ID / `elementor-<id>` classes in the HTML) and match it in the dashboard. A disabled control or a removed function call is not proof of independence.

## 3. Snapshot
Before changing: front page and posts page options, kit settings, menu locations, template selections and conditions, affected wrappers/CSS, plus a database backup when allowed. Take a checkpoint before and after every migration or external change.

## 4. Takeover
- Priority for header, footer, single and archive: the project's approved design is the default; using another Pro condition must be a deliberate, documented choice.
- Installing the new theme does not remove old global Pro conditions — old header/footer templates keep rendering until their conditions are removed or the templates are trashed (with permission).
- Connect the correct home page, menus, the theme's own kit and its templates; keep previous data so everything can be restored.
- Check Popups, Custom Code, old plugin CSS, cache and CDN separately. Don't deactivate unrelated plugins automatically. Clearing caches never replaces fixing an active old template.

## 5. Cleanup (only with permission)
- Decide that a page is old from ownership, content, history, links and the replacement list together; date or title alone is not enough.
- Never delete legal pages, account, shop, cart, checkout, product or customer data without identification and permission.
- Prefer Trash (restorable); permanent deletion needs separate permission.
- Record IDs, replacements and URL effects; add redirects only where needed (old URL → its real replacement), never everything to the home page.

## 6. Verification
After the change: the real rendered output on the public site and the dashboard (logged in, seen with your own eyes or screenshots), every language, header/footer/single/archive, menus, forms, caches purged. A delivered ZIP is not proof of a live installation.
