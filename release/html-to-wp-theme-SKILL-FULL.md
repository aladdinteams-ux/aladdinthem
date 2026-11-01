<!-- Combined, read-only copy of the html-to-wp-theme skill (SKILL.md + references 01–13). The installable skill is the folder / ZIP. -->
---
name: html-to-wp-theme
description: Converts HTML design sources (HTML/CSS/JS files, ZIPs, Google Stitch or design-tool exports, screenshots, a reference theme) into a commercial, installable WordPress theme plus companion plugin that stays fully editable in Elementor, with secure forms, safe and reversible setup/import, settings panel, WooCommerce, multilingual/RTL, dark mode, tests and sale packaging for Zhaket/Rtl-theme. Also repairs, extends or migrates sites built this way. Use when the user gives HTML or a design and asks for a theme ("قالب درست کن", "پوسته وردپرس بساز", "این HTML رو قالب کن", "تبدیل به وردپرس/المنتور", "تمام دستورالعمل پروژه قالب علاءالدین را اجرا کن", "make a WordPress theme from this"), or asks to fix/extend/re-test such a theme. Author brand: Aladdin Theme (علاءالدین تم).
---

# HTML → commercial WordPress/Elementor theme (Aladdin Theme)

Skill version 2.0 · Built from the Larijani Stone project (repo `aladdinteams-ux/aladdinthem`) and the Aladdin WordPress project handbook (Navar Khatar experience).

Act as a senior WordPress engineer, Elementor specialist, UI engineer and release-QA owner. Deliver a working product (ZIPs, tests, guide, report), not a partial sample — unless the user explicitly asks only for a plan. The user writes Persian: **reply in Persian**; code, commits and code comments in English; buyer documentation in Persian.

## 0. Pick the mode first
| Mode | When | Phases | Deliverables |
|---|---|---|---|
| **Build** | New HTML/design → new theme | P0–P9 | theme + plugin + child ZIPs, manifest, guide, report |
| **Repair / extend** | Change or fix an existing theme repo built this way | P0 (scope + baseline of the affected parts) → only the phases the change touches → P8 regression → P9 | new version ZIPs + changelog + report |
| **Live site** | Install on, fix or take over a real site (old header still showing, duplicate pages, migration) | references/12 first (preconditions: which theme/version/ZIP is installed, whether Elementor Pro is active), then 09 §2–3; build phases only if files must change | change log of the site + evidence + restore steps (+ ZIPs only if files changed) |
| **Plan only / dry run** | The user asks for a plan, estimate or review | P0 reading only | written plan, questions, risks — no changes |
Precedence when sources disagree: the user's latest instruction → SKILL.md contract → references 01–13 → references/14-handbook-fa.md (original intent; if it is stricter on safety, follow it). If still unclear, ask. The user's later decision **not to use Elementor Pro** overrides the handbook's Pro items (§6 Pro features): only the existing-site cleanup of old Pro templates remains.

## 0.1 Naming (one table for every project)
| Item | Rule | Example (project `sanat`) |
|---|---|---|
| Theme folder/slug and theme text domain | product name chosen with the user | `sanat` |
| Companion plugin slug and its own text domain | `<theme-slug>-core` | `sanat-core` |
| PHP namespace (classes) | `Aladdin\<Project>` | `Aladdin\Sanat\Forms` |
| Functions, options, hooks, meta keys, AJAX/REST names | `aladdin_<project>_` / `aladdin/<project>/v1` | `aladdin_sanat_form_token`, `_aladdin_sanat_demo_key` |
| CSS scope class and handles | `aladdin-<project>` / `aladdin-<project>-*` | `.aladdin-sanat`, `aladdin-sanat-theme` |
`<project>` = the theme slug with hyphens turned into underscores for PHP names (`larijani-stone` → `aladdin_larijani_stone_`, namespace `Aladdin\LarijaniStone`) and kept with hyphens for CSS/handles (`aladdin-larijani-stone`).
The Larijani reference code uses older names (`larijani_`, `ls_`, `LS-` tracking codes); when copying from references 03–05 or templates/, rename with this table. Limits and file types in those files are defaults to adapt per project.

## 0.2 Where to work
Ask for a new repository for a new project. If none is available and the designated repo is the Larijani reference theme, build in `projects/<theme-slug>/` on the designated branch (the reference theme's package script excludes `projects/`) — never mix files into the reference theme itself. The checkpoint lives at `<project folder>/docs/progress/CHECKPOINT.md`. In live-site mode keep the checkpoint and a site change log (`docs/site-changes/<date>.md`: actions, IDs, before/after, evidence, restore steps — no credentials or personal data) in the project folder and commit them even when no theme files changed. If the session's task-list or file-sending tools are missing, keep the checklist in the checkpoint file and list output paths in the reply.

## 1. Working contract (always applies)

1. **Read the real input first.** Open every supplied file; never infer contents from file names or earlier conversations. Never assume access, files or test results from a previous project — re-fetch and re-validate them.
2. **The approved look is the baseline.** Adding editability or settings must not change it. A request to redesign one component changes only that component.
3. **Real editability with free Elementor only.** Every visible element gets a real control: free Elementor container/core widget first, otherwise a new custom widget from the companion plugin. **Elementor Pro is never used or required** (no Pro widgets, forms, popups, dynamic tags or theme-builder conditions). One HTML widget for a whole page or section is never acceptable.
4. **Namespacing.** Use the `aladdin` namespace for everything project-specific: PHP namespace `Aladdin\<Project>`, function/option/hook prefix `aladdin_<project>_`, CSS scope class `aladdin-<project>`, script/style handles `aladdin-<project>-*`, text domain = theme slug. (The Larijani reference repo predates this rule and uses `larijani_`/`ls_`.)
5. **Shared chrome.** Header and footer follow one approved structure on every page and language unless the project decides an explicit exception.
6. **Design for all states together**: light/dark mode, every project language and direction, mobile, tablet and desktop.
7. **Fonts.** Use the font the user approved for this project, bundled locally with its licence. If none is specified, choose one and record the choice and loading path. Never carry the previous project's font or languages over without reason.
8. **Settings must work.** Every visible option is implemented and tested; anything not implemented is labelled «رزرو‌شده» in the UI and the report.
9. **Permissions.** Don't ask again for permitted, reversible work. Get explicit permission for data deletion, out-of-scope changes, live-site changes, sending real e-mails/forms, and permanent deletes (Trash first).
10. **Safety of data.** Never delete or overwrite the owner's content without permission; every import is journaled and undoable; back up before changing (git tag + previous ZIP + DB/option snapshot on live sites).
11. **Secrets.** Never write passwords, cookies, tokens, payment or private data into code, reports, prompts or progress files.
12. **Truthful evidence.** Every test result is one of: **موفق** (passed, with evidence), **ناموفق** (failed), **مسدود** (blocked: tool, access or network unavailable — say what), **بررسی‌نشده** (not checked). «انجام‌نشده» in older instructions means مسدود or بررسی‌نشده — say which. (Feature statuses for a reference-theme map are a separate vocabulary, references/01 §3.) Never present old evidence as new; date-label it. Never weaken a test after it fails — separate environment problems from product problems first. PHP lint is not a compatibility test; a submitted login form is not a successful login.
13. **Source of truth.** For compatibility-sensitive code, check current official docs during the task (references/13). If the network blocks them, use their official GitHub sources; anything still unverified is reported as unverified.
14. Develop on the designated git branch, commit with clear messages, push with retry; never open a PR unless asked.

## 2. Workflow

Phases are a management tool. Merge or split them to fit the project size, but never drop a requirement. After each phase: save a checkpoint (templates/checkpoint.md), then continue only when that phase's prerequisites really pass.

Copy this checklist into the task list and keep it updated:

```
- [ ] P0 Intake: preflight, inventory + checksums, baseline screenshots (all languages/modes), reference-theme map, decisions (products: WooCommerce if cart/price/orders are needed, CPT if catalog only; multilingual plugin), plan  (references/01, 09, 11; existing site → 12)
- [ ] P1 Architecture: theme (presentation) + companion plugin (widgets, CPTs, forms, import, migration), tokens, assets  (references/01, 02)
- [ ] P2 Elementor: native containers/widgets map, custom widgets, kit, theme builder, fallback, editor↔site parity  (references/02, 11)
- [ ] P3 Chrome & UI: header/mega menu/sticky, mobile off-canvas, selects, dark mode, tables, forms, breadcrumbs, media  (references/10)
- [ ] P4 Plugin & security: secure forms, private uploads, leads, privacy  (references/03)
- [ ] P5 Setup & migration: wizard, reversible import, existing-site takeover, cleanup with permission  (references/04, 12)
- [ ] P6 Settings & commercial UX: settings panel, search/filters, branding  (references/05)
- [ ] P7 Quality: performance, SEO/schema/GEO, accessibility (WCAG 2.2), languages/responsive matrix  (references/06, 11)
- [ ] P8 QA: real sandboxes, regression, attacks, editor saves, Woo, checkers  (references/07, scripts/)
- [ ] P9 Release: package, manifest, Persian guide, report, deliver  (references/08)
```

**Verification loop (each phase):** implement → run the phase's checks → fix → re-run → only then mark done. The visual baseline check (pixel diff against approved screenshots) runs after every change that touches CSS, markup or settings.

Before starting read references/09-pitfalls.md: §1 for Build/Repair, §2 for Live site, §3 always.

## 3. Reference files (read the one you need, when you need it)

| File | Read when |
|---|---|
| references/01-architecture.md | Intake, baseline, theme/plugin split, tokens, templates |
| references/02-elementor.md | Elementor widgets (free Elementor only, no Pro), native layouts, kit, built-in theme builder |
| references/03-security-forms-plugin.md | Forms, uploads, leads, privacy, companion plugin |
| references/04-setup-wizard-import.md | Activation, wizard, plugin installer, import journal, undo |
| references/05-settings-ux.md | Settings panel, search filters, dropdowns, menu state, branding |
| references/06-perf-seo-a11y.md | Core Web Vitals, SEO/schema/GEO/AIO, WCAG 2.2 |
| references/07-qa-sandbox.md | Sandboxes, test matrix, debugging, evidence rules |
| references/08-packaging-sale.md | ZIPs, headers, readme, manifest, guide, report |
| references/09-pitfalls.md | Before starting and whenever something behaves strangely |
| references/10-header-menus-ui.md | Header, mega menu, sticky, off-canvas, selects, dark mode, tables, forms, breadcrumbs, media |
| references/11-parity-i18n-responsive.md | Editor↔site parity, languages/RTL, device matrix |
| references/12-existing-site-migration.md | Working on a live/existing site, previous theme or leftover Elementor Pro templates to remove, cleanup |
| references/13-official-sources.md | Checking current WordPress/Elementor/WooCommerce/W3C rules, marketplaces |
| references/14-handbook-fa.md | The user's original Persian project handbook (source of the contract above); re-read when a rule's intent is unclear |

Scripts (scripts/README.md) are run, not read: e.g. "Run `scripts/qa/pdiff.py REF NEW`". Run `bash ${CLAUDE_SKILL_DIR}/scripts/preflight.sh` (the script inside this skill's folder) at the start of P0 and of every QA session (checks PHP, Node, Playwright, Chromium, Composer, sandboxes). Templates (templates/README.md) are working code to copy and re-prefix.

## 4. Branding (always)
- Author `علاءالدین تم (Aladdin Theme)` in theme, child theme and plugin headers; readme `Contributors: aladdintheme`; copyright `Copyright (C) <year> Aladdin Theme (علاءالدین تم)`.
- Logos in `assets/branding/` (light `aladdin-theme.png`, dark `aladdin-theme-dark.png`): settings header badge, "about" card, admin footer credit on the theme's own screens.
- No Author URI unless the user gives one. Client name, logo and contact data come from the client's material; never invent company facts, numbers or reviews.

## 5. Deliverables and final report
Deliver theme ZIP, companion plugin ZIP (also bundled in the theme), child theme ZIP, release manifest with SHA-256, Persian guide (install, upgrade, cache purge, restore), and the report (templates/final-report-fa.md). Answer five questions:
1. Which behaviours/problems were fixed?
2. Which files/versions were produced, or which environment were they applied to?
3. Which tests actually ran and passed (with numbers)? List failed, blocked and not-checked items separately.
4. Which limitations or open issues remain (e.g. licences the user must confirm)?
5. How to install, continue, or restore?

A ZIP is not proof of a live installation. If live changes are allowed, verify the result on the real dashboard and site; if only file building is allowed, change nothing on any site. Send the files with the session's file-sending tool when available (otherwise list their paths) and give a short Persian summary. In live-site mode the deliverable is the verified change, its evidence and restore steps; ZIPs only if files changed.


---

<!-- references/01-architecture.md -->

# 01 — Intake, baseline and architecture

Contents: 1 Intake · 2 Approved baseline · 3 Reference theme map · 4 Theme vs companion plugin · 5 Theme skeleton · 6 Templates · 7 Data model

## 1. Intake (P0)
1. Put every supplied archive in its own empty directory (untrusted data; read with `python3 -I`, never execute it). Record each source file with size and SHA-256 (`sha256sum`) in the checkpoint.
2. Inventory: pages and routes, languages and directions, sections, components, interactions (menus, tabs, sliders, accordions, filters, modals, forms, calculators, maps), CSS/JS, fonts, icon families, media, dynamic data (products, posts, projects, prices), third-party dependencies (CDNs, Google Fonts, analytics), and remote/hotlinked assets (licence + privacy + availability risk).
3. Component map — classify every component as **Elementor-native** (Container + core widgets), **Elementor template / theme location** (header, footer, single, archive, product, 404), **WordPress-native** (menus, posts, search, comments), **custom Elementor widget** (data-driven or interactive), or **companion-plugin service** (forms, CPTs, filters, import, integrations). Write it as a table (component → implementation → controls → data source).
4. Decide with the user: font, languages and multilingual method (Polylang, WPML, or none — references/11), products (WooCommerce when cart/prices/orders are needed, a CPT for a catalog only), form recipients and test-mail permission, dark-mode default, live-site scope, target marketplace.
5. Plan + task list (the session's task tool, or the checkpoint file). Run `scripts/preflight.sh` before taking screenshots.

## 2. Approved baseline
- The approved real view is the reference: colours, fonts, sizes, spacing, alignment, radii, shadows, images, header, footer, menu, and interaction states (hover, focus, open menus).
- Capture reference screenshots of the source (Playwright; 390 and 1440 px at least, plus every breakpoint the design uses; light and dark; every language) → `nat/REF/`. Wait for fonts (`document.fonts.ready`) and disable transitions before capturing.
- A screenshot that shows an editor/site difference is a bug report, not a new design.

## 3. Reference theme map (when the user gives a reference theme, e.g. FRPMesh)
For each of its settings/features record one status: **موجود** (exists here), **نیازمند پیاده‌سازی** (to implement), **رزرو‌شده** (shown but intentionally not active — label it), **نامرتبط** (not relevant). Implement useful behaviour, not option names. The number of options in a reference is not a target ("92 options" ≠ "92 working features").

## 4. Theme vs companion plugin (default for new projects)
| Theme (presentation) | Companion plugin `<theme-slug>-core` |
|---|---|
| style.css, functions.php, template hierarchy, template parts | Custom Elementor widgets, controls, dynamic tags |
| Design tokens (CSS variables, optional theme.json), assets, fonts, icons | CPTs, taxonomies, registered meta, persistent settings independent of theme |
| Theme supports, menus, image sizes, theme-builder locations | Forms, uploads, leads, AJAX/REST endpoints, integrations |
| PHP fallback renderers for header/footer (used when the plugin or Elementor is missing) | Editable header/footer widgets used inside Elementor templates |
| Header/footer renderers and fallbacks when plugins are missing | Demo import, migrations, setup services |
- Use only public WordPress, Elementor and WooCommerce APIs; never edit core or third-party files, never depend on private/internal APIs as shortcuts; feature-detect optional plugins and degrade gracefully.
- If the user explicitly wants a theme without a plugin, explain the trade-offs (wp.org plugin-territory rules, content lost on theme switch) and don't silently drop needed features.
- The Larijani reference keeps widgets in the theme (older decision); new projects move widgets to the plugin, the theme keeps a minimal fallback renderer.
- Load assets only where needed (widget `get_style_depends()` / `get_script_depends()`, conditional enqueue).

## 5. Theme skeleton
- Slugs, text domains and prefixes: SKILL.md §0.1 naming table (never 2-letter prefixes).
- `functions.php` only defines constants and requires `inc/*`.
- **CSS**: Tailwind 3 compiled at build time from `src/` with `important: '.aladdin-<project>'` (scope class), `corePlugins.preflight: false` + reset scoped to `:where(.aladdin-<project>)`; colours as CSS variables with Elementor Global Colour fallback (`--x: var(--e-global-color-<id>, var(--x-default))`), `color-mix()` for alpha; typography, container width and radius as variables so settings change them without altering defaults; keep selector specificity ≤ 0-3-0 so Elementor Style-tab rules win; content globs include PHP, JS and layout HTML; safelist dynamic classes.
- Semantic colour tokens per component (surface, text, title, border, accent) with dark-mode values (references/10).
- Fonts bundled with licence file, `font-display: swap`, preload only the font in use; icon fonts bundled (never show ligature names such as `water_drop`).
- JS: vanilla modules, `defer`, no globals beyond one namespace object, `once()` guards, re-init inside the Elementor editor preview, destroy/re-init safely.
- RTL/LTR per language (`dir`, `lang`), logical CSS properties.

## 6. Templates
`index, page, single, archive, search, 404, comments, searchform (all search boxes through get_search_form() variants), archive-/single-/taxonomy-<cpt>, woocommerce/*`. Each calls a `do_location()` helper first so an Elementor template chosen in the theme's built-in theme builder can replace it (no Elementor Pro).

## 7. Data model
- One field list drives both the Customizer and the settings page; central defaults; sanitise per type.
- Versioned idempotent migrations (admin only; stored version updated only after all steps pass).
- Deprecated wrappers when renaming public helpers (never `eval`).
- Settings export/import: versioned JSON without secrets.


---

<!-- references/02-elementor.md -->

# 02 — Elementor integration (free Elementor only — no Elementor Pro)

Contents: 1 Choice order · 2 Custom widgets · 3 Native layouts · 4 Kit · 5 Built-in theme builder · 6 Fallback · 7 Verification

## 1. Choice order for every component
1. Free Elementor Container + core widgets with responsive controls.
2. Dynamic data / WordPress API (menus, posts, products, site settings), exposed through the companion plugin's own controls or dynamic tags.
3. A purpose-built custom widget in the companion plugin (whatever the free core widgets cannot do: forms, popups/modals, sliders, filters, tables, mega menu, header/footer parts).
4. A companion-plugin service plus limited, isolated code.

**Elementor Pro is not used.** Never use Pro widgets (Form, Popup, Posts, Nav Menu, Loop, Mega Menu…), Pro dynamic tags, Pro Theme Builder conditions or any Pro-specific code; never ask the buyer to buy Pro. Everything Pro would give is built with free Elementor plus the theme's own widgets and built-in theme builder. If a buyer happens to have Pro installed, the theme must simply keep working (no fatal errors, no reliance on it).
Normal page content is never hidden in one inseparable block. If Elementor's wrappers break an approved header/footer structure, provide a dedicated editable widget for that part and still keep a useful native pattern.

## 2. Custom widgets (in the companion plugin)
- Register on `elementor/widgets/register`; extend `\Elementor\Widget_Base`; implement `get_name`, `get_title`, `get_icon`, `get_categories`, `get_keywords`, `get_style_depends`, `get_script_depends`; register assets with WordPress first.
- One base class with helpers (`section`, `ctl`, `rep`, `end`); defaults = the design's real content.
- Controls for everything visible: text, rich text, image + alt, icon, button, link, card, column count, colours, fonts, spacing, sizes, alignment — responsive where the design changes per breakpoint. Data structures get their own controls: form fields (type, label, options, required, disabled), select options, tables (columns, rows, cells), repeaters for repeated items. A style control that is not wired to the real data is not acceptable.
- Escape in `render()` (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`); sanitise anything saved outside Elementor.
- Frontend JS via handlers that survive editor re-renders; no dependence on undocumented Elementor DOM internals.
- Images: hero/LCP eager + `fetchpriority="high"`, others lazy.

## 3. Native layouts (static sections)
- Builders produce Elementor JSON: Containers (flex/grid, boxed width, responsive gap/padding/width, `flex_wrap: nowrap` + responsive widths for mobile rows, explicit grow/shrink) and core widgets with theme classes as `_css_classes` and margins as `_margin` so the Style tab keeps working.
- After saving `_elementor_data` delete `_elementor_element_cache`, `_elementor_css`, `_elementor_page_assets` and clear the files cache.
- Ensure the Container experiment is active on old sites. Offer opt-in "rebuild layouts" for pages created by an older version (back up `_elementor_data`, keep IDs/slugs/menus).

## 4. Kit (Site Settings)
- Add theme colours as custom global colours and the bundled font. System colours, typography, container width and widget spacing belong to the user: fill them only when empty or on a brand-new site; later syncs change only values the theme set.

## 5. Built-in theme builder (instead of Pro Theme Builder)
- Theme mods per location (header, footer, single post, archive, single product, shop, 404, and per language when multilingual) → a saved Elementor template; `do_location()` renders it and enqueues its CSS. Editing happens in free Elementor.
- Never write Pro theme-location or condition code. Old Pro templates on an existing site are only something to find and remove with permission (references/12).

## 6. Fallback without Elementor
Render saved `_elementor_data` (containers, core widgets, theme widgets) with Elementor-like markup and the scoped CSS so the site still looks right; show a notice recommending Elementor.

## 7. Verification (real editor, Elementor 3.x and 4.x)
Panel lists all widgets; preview renders; zero JS errors; edit a native widget and a custom widget → save → reopen → front shows both; Style-tab colour, typography, button background and grid columns override theme CSS; compare editor and site at the same width, language and mode (references/11). "Elementor opened" is not proof that saving or parity works.


---

<!-- references/03-security-forms-plugin.md -->

# 03 — Companion plugin, secure forms, private uploads

> Names in this file (`larijani_*`, `ls_*`, `LS-` codes) and numeric limits come from the Larijani reference implementation. Rename with the naming table in SKILL.md §0.1 and adapt limits/file types to the project.

Reference: `plugins/larijani-stone-core/` (forms.php, uploads.php, post-types.php, leads-admin.php, svg.php, privacy.php, migrate.php, uninstall.php). Copy and rename the prefix for a new brand.

## Theme ↔ plugin split
- Plugin (`<theme-slug>-core`, text domain `<theme-slug>-core`, Requires PHP 7.4 / WP 6.5) owns: CPTs/taxonomies (projects, leads) with `register_post_meta`, project meta box, leads admin, form handler, uploads, SVG sanitiser, privacy exporter/eraser, CSV export, migration of old data, uninstall (keeps data unless the admin opted in; projects never deleted).
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
- Store outside the media library in `uploads/aladdin-<project>-private-<random24>/leads/Y/m/<lead>/<random32>.<ext>` with `.htaccess` (deny + PHP off), `web.config`, `index.php`; optional constant for a path outside the web root; admin button that probes public reachability.
- Download only via `admin-post.php` + nonce + `current_user_can('edit_post', $lead)`, headers `octet-stream`, `attachment`, `nosniff`, CSP sandbox, `no-store`; realpath must stay inside the private root.
- E-mail never contains file links — only a dashboard link. Files deleted with the lead. Old public attachments: admin-triggered migration that copies, verifies SHA-256, then deletes the public copy.
- SVG for administrators only, rebuilt from an allow-list (no script/foreignObject/on*/external or javascript: hrefs/entities/DOCTYPE).

## Admin
Lead meta box (fields, private file links, legacy warning), list columns (phone with tel link, form, tracking), search by tracking/phone, settings page (recipient e-mail, SVG switch, uninstall data switch, storage status test, migration, CSV export with formula-injection escaping), privacy policy text, exporter/eraser by e-mail or phone.

## Tests that must pass (scripts/qa/forms-attack.py etc.)
no token / forged token / too fast / replay / duplicate / tampered schema / honeypot; required, phone, Persian digits, invalid option, too long, unknown fields, array-in-scalar; PDF ok, PHP-as-PDF, `.php`, fake JPEG, ZIP with PHP, ZIP traversal, clean ZIP, DWG, EXE-as-DWG, >limit size, >5 files, files on a form without file field; rate limits; download by role (admin/editor 200, author/subscriber 403, anonymous and foreign nonce refused); Apache `.htaccess` 403 on file/dir/index/PHP; migration hash match; SVG and privacy.

## General rules (WordPress security model)
- A nonce proves intent (CSRF), never authorisation: always pair it with `current_user_can()` on the specific object.
- Validate/sanitise input on arrival, escape output late and in context (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_*`), `$wpdb->prepare()` for every custom query (prefer core APIs), `wp_safe_redirect()` for redirects, `defined( 'ABSPATH' ) || exit;` in every PHP file.
- REST routes: `permission_callback` always present (`__return_true` only for genuinely public read-only data), argument schemas with `sanitize_callback`/`validate_callback`.
- Error messages, logs, settings exports and reports never contain secrets, personal data or file-system paths.
- Never send a test submission to a real recipient without permission; use a sandbox mailbox or log the mail.
- In WooCommerce code, use the CRUD APIs (`wc_get_order()`, `$order->update_meta_data()` + `save()`) instead of post/postmeta functions, and declare HPOS compatibility in the plugin (references/13).


---

<!-- references/04-setup-wizard-import.md -->

# 04 — Setup, wizard and reversible demo import

> Names in this file (`larijani_*`, `ls_*`, `LS-` codes) and numeric limits come from the Larijani reference implementation. Rename with the naming table in SKILL.md §0.1 and adapt limits/file types to the project.

Reference: `inc/demo/importer.php`, `inc/admin/setup-wizard.php`.

## Auto-setup on activation
- Runs only on a **fresh site** (`fresh_site` option) or a site without own content (`larijani_site_has_content()`: pages without the demo meta other than privacy/sample page, or > 1 published post). Otherwise: no import, show the "run setup" notice.
- Fresh site: remove only WP's untouched defaults (Hello world, Sample Page), rename "Uncategorized", move default widgets to inactive, enable `/%postname%/`, then import pages, theme-builder templates, menus, projects (if plugin active), sample posts/products, kit; queue demo image copy in WP-Cron.
- Non-interactive activation (WP-CLI) → finish on next admin visit. Filter to disable (`ls_auto_setup_on_activation`).
- When Elementor or WooCommerce are activated later, complete the missing parts (templates, products, shop page) once.

## Wizard page (Larijani › راه‌اندازی و درون‌ریزی)
1. **Requirements** table with ✅/⚠️/❌: PHP ≥ 7.4 (8.1+ recommended), WP ≥ 6.5, memory 128M/256M, max_execution_time 60/120, upload size, extensions mbstring/fileinfo (fail), dom/zip/curl (warn), GD or Imagick, pretty permalinks, plugin dir writable.
2. **Plugins**: bundled core plugin (required), Elementor (required for editing), WooCommerce (optional). Install/activate only on click: `Plugin_Upgrader` + `WP_Ajax_Upgrader_Skin`, `plugins_api` for wordpress.org, nonce per plugin + `install_plugins` & `activate_plugins`; clear message on failure (FTP credentials, no network).
3. **Demo import** with checkboxes. Defaults on a site with content: "set front page" unchecked with the current front page named in red; sample posts/products unchecked. Projects skipped with a clear message if the core plugin is inactive.
4. **Last import** card: status (done/failed/interrupted), undo button.

## Import rules (never destroy owner content)
- Create only; find existing objects by stable keys (`_ls_demo_key`, slug) → re-runs create no duplicates.
- Menus: only fill empty locations; items linked to page/term objects (custom links only as fallback, e.g. `?ls_cat=` for the no-Woo catalog).
- Theme-builder slots: only fill empty/broken ones; never write Elementor Pro conditions.
- Front page replaced only when the checkbox is checked.
- Elementor system kit values only on fresh sites.

## Journal + undo
- `larijani_run_import()` wraps the steps: `journal_start` snapshots watched options (show_on_front, page_on_front, page_for_posts, woocommerce_shop_page_id, elementor_cpt_support, permalink_structure), theme mods (menu locations, ls_tb_*), kit settings; hooks `wp_insert_post` (new posts only) and `created_term` and **persists after each insert** (survives fatals/timeouts).
- try/catch Throwable → status `failed`, generic message, details to debug log. A `running` journal older than 10 min = interrupted.
- A run that changed nothing keeps the previous journal (otherwise a no-op re-run would make the real import un-undoable).
- Undo: delete created posts unless edited after the run finished (+60 s), delete created empty terms / menus, restore options, mods and kit, clear Elementor cache, flush rewrites, report counts. Background image copies are not covered — say so.

## Tests
Fresh ZIP install → activation auto-setup; wizard: requirements render, install bundled plugin by click, CSRF without nonce 403, import projects (N created), re-run (0 created), undo (N removed, other counts unchanged); existing-content site: activation imports nothing, front page kept, notice shown, front checkbox unchecked with warning.

## Handbook rules for import and migration (apply to every project)
- Record ownership of every imported object (meta key such as `_aladdin_<project>_demo_key`); re-runs must never duplicate pages, menus, products or kit entries.
- Preserve IDs, URLs, custom text and images, Elementor data, uploads and user CSS across import, upgrade and rebuild.
- Test these orders and states: fresh install · upgrade from the previous version · theme then plugin · plugin then theme · a missing optional dependency installed later · interruption in the middle · retry · restore.
- Show visible states in the UI: pending, active, error, restored. Never hide a partial failure behind a success message.
- A restore must not be undone immediately by an automatic re-run (set a "restored" flag that auto-setup respects).
- Never repeat heavy or fragile operations on every admin request; run once per version/state change.
- Build links from object IDs and real permalinks, never from guessed slugs (an existing `contact-us` makes the new page `contact-us-2`).


---

<!-- references/05-settings-ux.md -->

# 05 — Commercial settings, search/filter UX, menus, branding

> Names in this file (`larijani_*`, `ls_*`, `LS-` codes) and numeric limits come from the Larijani reference implementation. Rename with the naming table in SKILL.md §0.1 and adapt limits/file types to the project.

Contents: Settings panel · Search panel that filters · Glass dropdowns · Menu active state · Branding

## Settings panel scope (handbook) — cover what applies to the project
| Section | Expected controls |
|---|---|
| Identity | main, mobile, retina and footer logos; favicon (site icon); brand description; logo size |
| Colours | primary, secondary, text, heading, background, surface, border; light and dark values |
| Typography | font family, weight, size, line height, letter spacing (headings/body), tablet and mobile scale |
| Layout | site width, container, content width, section spacing, radius |
| Header | structure variant, sticky, mobile, menu, phone, CTA — only options that are really implemented |
| Footer | logo, description, copyright, links, columns, back-to-top |
| Content | archive layout, card columns, images, metadata, breadcrumbs |
| Advanced | SEO-related toggles, performance, motion, safe custom CSS (sanitised, no `</style>`) |
- Don't duplicate settings WordPress or Elementor already provide (site icon, kit) — link to them.
- Defaults reproduce the approved UI exactly. Test save → reload, empty values, defaults and custom values. Distinguish active options from «رزرو‌شده» ones in the UI.
- Writing settings: capability + nonce, sanitise per type (URL, colour, number, text, CSS), escape in the output context. Settings export/import is versioned JSON without secrets.

## Theme settings page (+ same fields in the Customizer)
Tabs: Overview (plugin status, leads count, created pages with "edit with Elementor", **reset all settings** with backup + one-click restore, **about card**), Brand & colors (+ links to Customizer `custom_logo` and `site_icon`), Contact & socials, Header, Footer, Blog, Forms, **Typography & layout**, Theme builder.
- Every option's default reproduces the HTML design exactly ("طرح اصلی"). Only non-default values print CSS (`larijani_customizer_css()` inline on the main stylesheet).
- Typography & layout: font (bundled / system font — then don't preload the bundled font / own WOFF2 URL: http(s) same host or site-relative path only, `.woff/.woff2` only, no quote/paren/backslash/space), heading scale, body scale, tablet and mobile multipliers (media queries setting `--ls-fs-r`), content width (`--ls-container`), button radius (selector `.ls-root :is(button[type=submit], a[class~="bg-primary"], …)` — Elementor's Button widget keeps its own settings), opt-out of external sample images.
- Sync Elementor kit fonts / default container width only while they still hold theme-set values.
- Saving/reset/restore: `edit_theme_options` + nonce; per-tab reset also takes a backup.
- Add `<hr class="wp-header-end">` after the custom page header so WordPress puts admin notices below it.

## Search panel that really filters (hero widget)
- Tabs = real product category slugs (`mold`, `machinery`, `chemical`). Without WooCommerce the tab param is `ls_cat` and the action is the theme catalog page.
- Field option line: `Label|value|tab1,tab2` — value = comma-separated search words (matched in title/excerpt/content/SKU) or a product tag / attribute slug (exact taxonomy filter). Empty value = "all". Options scoped to tabs (JS rebuilds the select on tab change). Field mode: `filter` or `info` (order size → bulk-price note on results).
- Hidden `ls_hs=param1,param2` lists filtering params, so generic names never hijack other pages. Empty selects are disabled on submit (clean URLs).
- Server (`inc/search-filters.php`): `request` filter maps old/unknown `?product_cat=` (aliases; unknown → whole shop, never 404); `pre_get_posts` + `posts_where` apply tax/word filters to the main shop query; `larijani_search_filter_bar()` shows chips with remove links + "remove all" + empty state.
- Client catalog (no WooCommerce): read `ls_cat` (with aliases mold/molds, chemical/materials) and `ls_hs` word groups; every group must match the item text; toggle a `[data-ls-empty]` block.
- Keep aliases for values saved by older versions.

## Glass dropdowns (`select[data-ls-select]`)
- Keep the native `<select>` in the form (hidden, `aria-hidden`, `tabindex=-1`, value synced); insert a button `role=combobox` (`aria-haspopup=listbox`, `aria-expanded`, `aria-controls`, `aria-labelledby` = the field label, `aria-activedescendant`) and a `ul role=listbox` of `li role=option aria-selected`.
- Copy the select's classes to the button **before** adding the hide class to the select.
- Keyboard: ArrowUp/Down open/move, Home/End, Enter/Space select, Escape close (focus back), Tab selects + closes, type-ahead; click outside closes; label click focuses the button; rebuild on `ls:options`.
- CSS: radius ~1.15 rem, `rgba(255,255,255,.74)` + `backdrop-filter: blur(20px) saturate(180%)`, white border, soft shadow, `width: max-content; min-width:100%; max-width:min(22rem, 100vw-2rem)`, `white-space: nowrap` options, selected option tinted with `color-mix(var(--ls-primary) 15%)` + check icon, chevron rotates, fallback opaque background when `backdrop-filter` is unsupported, no animation under reduced motion.

## Menu active state
WordPress adds `current_page_parent` to the posts page for every non-page view (shop, product, CPT). Compute it yourself:
- `current-menu-item / current_page_item / ancestor / parent` → active;
- WooCommerce views (`is_shop/is_product/is_product_taxonomy`) → only the shop page item;
- project archive/single/taxonomy → the portfolio page item;
- `current_page_parent` only counts for blog contexts (home, single post, category, tag, date, author).
Verify `aria-current="page"` on home, shop, category, product, blog, post, projects, project, contact.

## Branding: Aladdin Theme (علاءالدین تم)
- `larijani_theme_author()` (filterable) returns name, English name, logo, dark logo, about text (no invented facts).
- Settings header badge "طراحی و توسعه — علاءالدین تم" with the light logo (hidden < 782 px), about card on Overview (dark logo, theme name + version, about text, short theme description, buttons to the wizard and the Persian guide), admin footer credit on theme screens only.
- Headers: theme `Author: علاءالدین تم (Aladdin Theme)`, child and plugin the same; readme `Contributors: aladdintheme`; copyright Aladdin Theme. Logos in readme Resources as the author's trademark.


---

<!-- references/06-perf-seo-a11y.md -->

# 06 — Performance, SEO/schema/GEO, accessibility (without changing the look)

Contents: 1 Performance · 2 SEO · 3 Schema, GEO and AIO · 4 Accessibility (WCAG 2.2) · 5 WooCommerce

## 1. Performance (Core Web Vitals)
- Targets at the 75th percentile of page loads: LCP ≤ 2.5 s, INP ≤ 200 ms, CLS ≤ 0.1 (standard web.dev thresholds — re-check web.dev when reachable).
- LCP: the hero image is eager + `fetchpriority="high"` and never lazy; preload only the font actually used; no render-blocking third-party CSS; responsive `srcset`/sizes.
- CLS: reserve space (width/height or aspect-ratio) for every image, video, embed and ad slot; `font-display: swap` with a metric-compatible fallback; no content injected above existing content after load.
- INP: keep event handlers short; break long tasks; no full-DOM observers or repeated re-styling loops; defer non-critical JS.
- Load CSS/JS conditionally (widget dependencies, Woo assets only on Woo pages); version assets by file mtime.
- Database: no queries in loops; cache expensive lookups; atomic counters (`UPDATE … = … + 1`); skip prefetch/HEAD requests for view counting.
- Cache-friendly forms (signed token fetched on interaction; no nonce baked into cached HTML; admin-ajax must not be cached).
- Attribute hosting, CDN and external-script problems separately from theme problems. Claim a PageSpeed/Lighthouse score only with a real run and its evidence; never trade security or accessibility for a score.
- Hotlinked design images (e.g. `lh3.googleusercontent.com`): external dependency + visitor privacy + unknown licence → copy locally on request, offer an opt-out, report as a release blocker until licensed images are supplied.

## 2. SEO
- Check: HTTP status codes, canonical, robots/noindex (search results), sitemap, crawlable server-rendered content, titles, one H1 per page, logical heading order, internal links, search, archives, pagination, hreflang and translated URLs.
- With an active SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO, The SEO Framework, Slim SEO) the theme outputs no meta description/OG/canonical/JSON-LD of its own — verify no duplicates with a real plugin.
- Breadcrumbs consistent on all pages, wrapping, direction-aware separators; a post links to its real archive.

## 3. Schema, GEO and AIO
- Define entities first (Organization/brand, Product, Article, LocalBusiness) with stable `@id` URLs; JSON-LD must match visible content.
- Never invent ratings, reviews, experience claims, certificates, counts or business claims; FAQPage only from visible FAQ content; escape JSON so `</script>` cannot break out.
- GEO/AIO: write clear direct answers and definitions, semantic structure, extractable and attributable content (who/what/where, units, dates). Never promise first-page rankings or AI citations.

## 4. Accessibility (WCAG 2.2 AA)
- Landmarks, logical headings, labels for every input (screen-reader label if the design hides it), alt text, skip link, visible focus, full keyboard operation, `prefers-reduced-motion` (no smooth scroll/animations/counters under reduce), sufficient contrast in light **and** dark mode (references/10), RTL/LTR correctness.
- New in WCAG 2.2 (W3C Understanding docs, read from w3c/wcag):
  - 2.4.11 Focus Not Obscured (Minimum): a focused item is at least partially visible — sticky headers and cookie bars must not cover it (use `scroll-padding-top`).
  - 2.5.7 Dragging Movements: every drag action (sliders, range filters, carousels) has a simple pointer alternative (buttons/inputs).
  - 2.5.8 Target Size (Minimum): pointer targets at least 24 × 24 CSS px, or spaced so a 24 px circle around each does not overlap another target.
  - 3.2.6 Consistent Help: help/contact links appear in the same relative place on every page.
  - 3.3.7 Redundant Entry: don't ask for the same information twice in one process (pre-fill or allow selection).
  - 3.3.8 Accessible Authentication (Minimum): no puzzle/recall/transcription test to log in without an alternative (allow paste and password managers).
- Widget patterns follow the WAI-ARIA Authoring Practices (references/10): select-only combobox, modal dialog (off-canvas), disclosure navigation (mega menu).
- Third-party content inside articles: `max-width: 100%` for inputs/selects/textareas/iframes/video.

## 5. WooCommerce
Use real products and real orders in tests; test classic templates and Cart/Checkout Blocks separately; never report the shop as verified without a real order run (references/13 for official rules).


---

<!-- references/07-qa-sandbox.md -->

# 07 — QA in real sandboxes (results must be real)

wordpress.org, packagist dist downloads and phar.phpunit.de are usually blocked by the proxy; **GitHub git clones work**.

## Build the sandboxes (scratchpad, PHP built-in server + SQLite)
```bash
S=<scratchpad>
git clone -q --depth 1 --branch 7.1.2 https://github.com/WordPress/WordPress $S/wpel        # core (also 6.5.x for min version)
git clone -q --depth 1 https://github.com/WordPress/sqlite-database-integration $S/wpel/wp-content/plugins/sqlite-database-integration-main
cp $S/wpel/wp-content/plugins/sqlite-database-integration-main/db.copy $S/wpel/wp-content/db.php   # set the plugin path inside db.php
# wp-config: DB_DIR/DB_FILE, WP_DEBUG_LOG to file, WP_DEBUG_DISPLAY false, WP_HOME/SITEURL http://localhost:PORT, WP_HTTP_BLOCK_EXTERNAL true
# mu-plugin test-user.php: if LS_TEST_USER defined → determine_current_user returns 1 (CLI scripts act as admin)
ln -s <repo> $S/wpel/wp-content/themes/<slug>        # theme from the working tree; a 2nd site installs from the release ZIP
(nohup php -d upload_max_filesize=64M -d post_max_size=64M -d memory_limit=512M -S localhost:8083 -t $S/wpel $S/routerel.php > $S/serverel.log 2>&1 &)
```
Router (`scripts/sandbox/router.php`) serves existing files, else `index.php`. **Servers die when the container restarts** — restart them (empty responses = server down).

Elementor from source (both 3.x and 4.x): `git clone --depth 1 --branch v4.0.8 https://github.com/elementor/elementor`, drop `elementor/wp-one-package` and require-dev from composer.json, `composer install --no-dev --no-scripts`, `npm ci --ignore-scripts`, `npm run build:packages`, `npx grunt styles`, `npx grunt scripts`. WooCommerce: release ZIP from GitHub (`github.com/woocommerce/woocommerce/releases/download/<ver>/woocommerce.zip`). Other plugins for compatibility (CF7 `takayukister/contact-form-7`, `Automattic/wp-super-cache`, `sybrew/the-seo-framework`) by git clone. PHPUnit: `composer require --dev phpunit/phpunit:9.6.21 --prefer-source` (git sources work when dist downloads fail). Theme Check: `git clone https://github.com/WordPress/theme-check`. PHPCS: composer `wp-coding-standards/wpcs ^3.1` + `phpcompatibility/phpcompatibility-wp`. Apache for `.htaccess` tests: `apt-get install -y apache2`, copy the uploads tree to `/var/www/…`, vhost with `AllowOverride All` on another port.

Sites used last time: 8083 (WP 7.1.2 + Elementor 4.0.8 + Woo, theme symlinked), 8084 (Elementor 3.35.9 + Woo, upgraded data), 8085 (installed from the ZIP, no Woo, fresh).

## Test matrix (record numbers; every item gets موفق / ناموفق / مسدود / بررسی‌نشده)
| Area | Script / method |
|---|---|
| Reference screenshots + pixel diff (15 routes × 390/1440) | `scripts/qa/natshot.js`, `scripts/qa/pdiff.py` (threshold 24, report WORST %, same heights) |
| Route crawl (status, horizontal overflow, PHP errors in HTML, one H1, JS errors) | `scripts/qa/crawl.js routes.json` |
| Keyboard/menu/search/forms in browser | `scripts/qa/interact.js` |
| WooCommerce (shop, category, product, cart/checkout blocks, place order, account) | `scripts/qa/woo.js <product> <cat>` |
| Elementor editor load / edit+publish / Style-tab override (on a **copy** of the page) | `scripts/qa/editor-*.js` + `dup-page.php`, `FRONT=/copy/` |
| Form attacks, validation, files, rate limits | `scripts/qa/forms-attack.py BASE /contact/ token|validate|files|rate` (reset limits between groups with `reset-rl.php`; sandbox-only mu-plugin raises limits for file cases) |
| Private download by role | `roles.php` + `roles.py` |
| Settings / wizard / undo / existing-site activation | `settings-test.py`, `wizard-test.py`, `existing-site.php` |
| Hero search + glass dropdown | `hero-search.js` (TAB, W, PORT env) |
| Reduced motion | `motion.js` |
| PHPUnit (validators, signing, uploads, settings CSS, search parsing) | `tests/*Test.php` with bootstrap loading a sandbox `wp-load.php` |
| Theme Check on the ZIP-installed theme | `tc-run.php <wp> <host> <theme-check> <slug>` |
| PHPCS security sniffs = 0, PHPCompatibility 7.4+ = 0 | phpcs commands in 08 |
| Debug log lines from theme/plugin files = 0 | grep `themes/<slug>\|plugins/<slug>-core` |

## Harness gotchas
- `requests` drops a manual `Cookie` header on redirects → POST with `allow_redirects=False`, then GET.
- `wp_nonce_url()` returns `&amp;` → `html.unescape` before requesting.
- CLI-made nonces need a real session: create the token with `WP_Session_Tokens::create`, build cookies with it and set `$_COOKIE[LOGGED_IN_COOKIE]` before `wp_create_nonce`.
- WP 7 prints `<style id="…-inline-css">` with double quotes.
- The first admin visit after activating Elementor may redirect to onboarding — warm up `/wp-admin/` first.
- Per-IP limits trip when test groups run back-to-back from 127.0.0.1 — reset between groups.
- PHP built-in server ignores `.htaccess` — test protection on real Apache.
- Woo Action Scheduler "Unable to claim actions" on SQLite is an environment issue (check it exists before your change).

## Official checkers
- **Theme Check** (`WordPress/theme-check` on GitHub) — run on the theme installed from the release ZIP; its checks cover style.css header, screenshot, licences/copyright, escaping, plugin territory, bundled ZIPs, text domain, search form, title tag, deprecated functions, CDN usage.
- **Plugin Check** (`WordPress/plugin-check` on GitHub, WP-CLI `wp plugin check <slug>`) — run on the companion plugin. Its checks: late escaping, nonce verification, direct DB queries, safe redirect, enqueued scripts in footer/scope/size, non-blocking scripts, performant WP_Query params, i18n usage, prefixing, setting sanitisation, uninstall, updater code, trademarks, direct file access, code obfuscation, minified files without source, file types, localhost URLs, offloading files to CDNs, no unfiltered uploads, readme and header fields. Needs Composer dependencies and WP-CLI; if downloads are not permitted, report «مسدود» with the reason.
- **PHPCS** with WordPress-Extra security sniffs (EscapeOutput, NonceVerification, ValidatedSanitizedInput, PreparedSQL) = 0, PHPCompatibilityWP for the minimum PHP = 0.

## Debugging rules (handbook)
- "There has been a critical error on this website" names no cause. Get the fatal/exception, file, line, stack trace, PHP version, active plugins and versions, and the action that triggered it (debug.log, server log, recovery-mode e-mail).
- Check: load order (plugin vs theme, `plugins_loaded`/`after_setup_theme`), duplicate function/class names, missing class/method (version mismatch), memory and time limits, stale generated files (Elementor CSS, minified bundles), caches (page, object, CDN, browser).
- A temporary way back into the dashboard is not a root-cause fix. Fixing the header/footer does not prove every PHP error is gone.
- A transient "Error establishing a database connection" is not a theme bug without log evidence; record it as environment.
- In the test environment, block unrelated external requests (`WP_HTTP_BLOCK_EXTERNAL`) — never by weakening security or changing a live site's services.

## Evidence rules
- Each result is موفق / ناموفق / مسدود / بررسی‌نشده, with the command or screenshot that proves it, the environment and versions, and the date.
- Never reuse evidence from a previous version as a new run; label old evidence with its date/version.
- Never weaken a test after a failure; first separate an environment problem from a product problem.
- Report sampling honestly ("15 routes × 2 widths", not "all pages").
- Important tests every release: install and upgrade, missing optional dependency, Elementor save and render, re-run import, edits preserved, partial failure and rollback, real routes, JavaScript errors.


---

<!-- references/08-packaging-sale.md -->

# 08 — Packaging for sale (Zhaket / Rtl-theme) and reporting

## Files
- `style.css` header: Theme Name, Theme URI (client site if given), `Author: علاءالدین تم (Aladdin Theme)`, Description (Persian; say free Elementor is all that is needed — no Elementor Pro; mention the companion plugin), Version, Requires at least, Tested up to, Requires PHP, License GPLv2+, Text Domain = slug, Domain Path, accurate Tags only (no `block-styles` unless implemented), copyright lines.
- `readme.txt` (WordPress format): description, installation, FAQ (Elementor Pro is not needed and not used; existing content?), changelog, Copyright, Resources (every font/icon/image with licence + source; demo images: licence status stated honestly).
- `screenshot.png` 1200×900 rendered from the theme with external images replaced by neutral placeholders.
- `CHANGELOG.md` (Security / Architecture / Added / Improved / Fixed), version bumped in style.css, `LARIJANI_VERSION`, package.json, readme Stable tag.
- POT files for theme and plugin (`scripts/qa/makepot.php <root> <out> [domain] [project]`).
- Persian guide `docs/INSTALL-FA.md`: requirements table, required/optional plugins, install, fresh vs existing site, undo, settings per tab, search panel option format, forms & security, Nginx deny rule / private dir constant, cache plugins, upgrade from the previous version, sample image licence, update/licence architecture.
- Child theme (`child-theme/<slug>-child`), plugin readme.
- Optional update channel: filter `larijani_update_provider` (object with `check($version)` → `new_version`, https `package`, `url`), off by default, no external calls.
- `build/package.sh`: builds plugin ZIP (also copied into `bundled/`), theme ZIP excluding `.git node_modules src build package*.json tailwind.config.js release child-theme plugins docs/qa tests .claude CLAUDE.md`, child ZIP; prints SHA-256 + file counts. Verify no dev files inside.
- `release/release-manifest.json`: version, date, requirements (tested versions), artifacts with SHA-256, test summary, `not_executed` list, licences, `release_ready` false while any blocker remains (e.g. image licence).

## Checks before delivering
```bash
php -l on every changed file
phpcs --standard=WordPress --sniffs=WordPress.Security.EscapeOutput,WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput,WordPress.DB.PreparedSQL  → 0
phpcs --standard=PHPCompatibilityWP --runtime-set testVersion 7.4-  → 0
npx tailwindcss … --minify ; npx terser assets/js/theme.js … ; bash build/package.sh
```
Theme Check: aim for 0 REQUIRED except documented wp.org-only items (CPT fallback, bundled plugin ZIP). Remaining WPCS formatting warnings may stay (report count) — don't mass-reformat.

## Consistency checks before release (handbook)
- Version in the style.css/plugin header, PHP constants, asset version keys and the manifest all match.
- ZIP root folder = slug; archive opens cleanly (`unzip -t`); SHA-256 recorded.
- No temporary development files, secrets, private logs or unrelated internal images in any package.
- A licence or update adapter must never break public display when the licence server is unreachable.
- Assets, public hooks, permissions and dependencies are documented (developer hooks doc).

## Report (Persian, docs/qa/COMMERCIAL-REPORT-<ver>-FA.md)
1 bugs found (id, severity, status) · 2 security report · 3 architecture · 4 commercial features · 5 test table with real numbers using the four statuses موفق / ناموفق / مسدود / بررسی‌نشده; typical blocked/not-checked items to list explicitly: MySQL, PHP 7.4 runtime, Nginx/LiteSpeed, real cache, Yoast/Rank Math, real e-mail, real devices/screen reader/Lighthouse, multisite…) · 6 guide link · 7 remaining limitations · 8 deliverables. Then commit, push (retry 2/4/8/16 s), send the ZIPs + report + guide with the session's file-sending tool (or list their paths), and give a short Persian summary.


---

<!-- references/09-pitfalls.md -->

# 09 — Pitfalls already paid for (read before starting)

Contents: 1 Larijani Stone (technical) · 2 Navar Khatar (process) · 3 Never repeat

These are experiences, not guarantees. IDs, counts and versions below belong to those projects only.

## 1. Larijani Stone — technical traps
| Symptom | Cause | Fix |
|---|---|---|
| Edited/re-imported Elementor page still shows old markup | `_elementor_element_cache` / `_elementor_css` / page assets | delete them after every `_elementor_data` save + clear files cache |
| Mobile rows of containers wrap or squash | default flex wrap/grow | `flex_wrap: nowrap` + responsive widths, explicit grow/shrink |
| Fallback (no Elementor) colours wrong | CSS variable inheritance from wrappers | reset variables on fallback wrappers |
| Theme CSS beats the Elementor Style tab | selector specificity / `!important` | scope class, specificity ≤ 0-3-0, margins as `_margin` |
| Blog menu item active on shop/product/projects | WordPress `current_page_parent` on the posts page | own active-state function (references/05) |
| Hero search "does nothing" | tab values ≠ real category slugs; other params ignored; unknown `product_cat` → 404 | real slugs + aliases + filter params + request normalisation |
| Custom dropdown invisible | copied the select's classes after adding the hide class | copy classes first |
| Native `<select>` list can't be rounded/glass | the browser draws the popup | combobox + listbox enhancement (references/10) |
| Admin notices inside a custom page header | WordPress inserts after the first h1 | `<hr class="wp-header-end">` |
| Forms break behind page cache | nonce in cached HTML | signed token fetched on interaction |
| Time-trap bypass | trusted client timestamp / skipped when missing | server-signed issue time |
| Customer files public | `wp_handle_upload` into the media library | private store + admin download handler |
| Re-running import made the real import un-undoable | no-op run overwrote the journal | keep the previous journal when nothing changed |
| Activation changed an existing site | auto-setup on any site | only fresh/empty sites |
| Third-party form overflowed on mobile | `size="40"` inputs | `max-width: 100%` for content form fields |
| POT empty for the plugin | exclusion regex matched the absolute path | apply excludes to the path relative to the root |
| Composer/PHAR downloads fail | proxy blocks dist/phar hosts | Composer `--prefer-source` / git clone; if policy blocks external code, report «مسدود» |
| Bundled library cache never refreshes | wrong `?ver=` string | read the real version from the file header |

## 2. Navar Khatar — process lessons
| Observed | Transferable action |
|---|---|
| Elementor preview differed from the site | extra wrappers and inherited default styles change width, colour, spacing; compare both render paths at the same level (references/11) |
| Complex header/footer | a dedicated editable widget kept the approved structure; normal page content stays editable piece by piece |
| Cramped desktop menu | rebalance brand / menu / CTA and move secondary items into the mega menu per width and language |
| Header slid under the top bar | two independent sticky rows; make top bar + nav one sticky unit (references/10) |
| Old off-canvas panel still opened | new panel CSS alone was not enough; control the hamburger directly, remove old listeners, bust bundle caches |
| Unreadable text in dark mode | buttons and labels were missed; include the specs button and dynamic text in the contrast matrix |
| Transient colour differences | hover and transitions skewed comparisons; compare the same state after transitions end |
| `select` without rounded corners | OS-drawn popup; custom UI that keeps value and accessibility |
| Wide table | scroll inside its wrapper, sensible column widths and cell padding; the page never scrolls horizontally |
| Field glued to its button | explicit gap; don't turn one screenshot's number into a rule for every form |
| Reference theme with 92 options | some were reserved; "92 options" ≠ "92 working features" |
| Migration backup bug | found only by a real test; verify exact data match, custom text, re-run and restore |
| Local test vs real Pro | locally the old path was bypassed, but published old Pro templates still rendered on the real site |
| Old header/footer on the live site | published Pro templates with "entire site" conditions (and post/archive templates) had to be moved to Trash with permission; new output verified |
| Page cleanup | with permission, old pages/drafts moved to Trash; new pages kept — counts are project-specific |
| URL `contact-us-2` | an old slug existed; build links from IDs and real permalinks |
| WordPress "critical error" | the generic message gave no cause; fixing header/footer did not prove all PHP errors were fixed |
| Transient DB connection error | happened once and recovered; without logs it is environment, not theme |
| Per-version tests | some versions had language/menu/migration tests but no full PageSpeed run; report each version's evidence separately |
| Admin login | "form submitted" ≠ logged in; confirm by seeing the dashboard; never store credentials |

## 3. Never repeat
- Declaring independence from the previous header before verifying on the real site that old Pro templates no longer render.
- Classifying content as old by date alone — check ownership, content, history, links and the replacement list within the permitted scope.
- Publishing previous-version evidence as a new test.
- Putting a whole page in one HTML widget for speed.
- Treating every colour difference as a CSS bug (check hover, transition, font loading first).
- Deleting unrelated data or plugins to fix a display problem.
- Clearing caches instead of fixing an active old template.
- Assuming the root error is fixed because the dashboard opens again.
- Weakening test criteria after a failure.
- Treating the number of settings, pages or tests as a quality measure — real behaviour and recoverability are the measure.


---

<!-- references/10-header-menus-ui.md -->

# 10 — Header, menus and UI components

Contents: 1 Header & mega menu · 2 Sticky header · 3 Mobile off-canvas · 4 Custom select (APG) · 5 Dark mode · 6 Tables · 7 Forms · 8 Breadcrumbs · 9 Fonts, icons, media

Keyboard/ARIA specs below come from the W3C WAI-ARIA Authoring Practices (APG) source (`w3c/aria-practices`).

## 1. Header and mega menu
- Align brand, menu, language switcher, theme (light/dark) toggle and CTA cleanly; when the design asks, the desktop menu sits between the logo and the CTA button.
- Secondary items go into a mega menu with a clear hierarchy, not a cramped top row; rebalance per width and language (long German/English vs short Persian labels).
- Mega/sub-menus: rounded corners, proper surface colour, padding, shadow consistent with the design; correct position in RTL and LTR; open/close with mouse and keyboard; close on Escape and outside click.
- Disclosure navigation pattern (APG): each top item with a dropdown is a `<button aria-expanded aria-controls>`; Tab/Shift+Tab move through top buttons and into open dropdown links; Enter/Space toggles; Escape closes the open dropdown and returns focus to its button; optional arrow/Home/End movement; the current page link has `aria-current="page"`.
- Active item logic: own function (WordPress marks the posts page `current_page_parent` for every non-page view — references/05).

## 2. Sticky header
- Treat the top bar and the nav row as **one sticky unit**; two independent sticky rows overlap.
- Account for real height (measure, don't hard-code), spacing, `z-index`, the WordPress admin bar (`body.admin-bar` offset 32 px desktop / 46 px mobile), and window resize.
- Sticky header must not hide focused elements (WCAG 2.4.11): set `scroll-padding-top` to the sticky height.

## 3. Mobile off-canvas
- Build a mobile structure; never copy the desktop mega menu into the panel. Width suited to phones, limited height with internal scroll, safe-area insets (`env(safe-area-inset-*)`), readable rows, collapsible groups, clear close button, languages and CTA reachable.
- Modal dialog pattern (APG): `role="dialog"`, `aria-modal="true"`, `aria-labelledby` (visible title) or `aria-label`; on open move focus inside (first focusable element or the title); Tab/Shift+Tab wrap inside; Escape closes; on close return focus to the hamburger; lock body scroll and restore the scroll position.
- Remove old listeners and stale bundles so a previous panel can't open; test narrow phones, tablets, landscape, scrolling with the menu open, switching language or theme while open. Hiding a broken element to pass a test is not a fix.

## 4. Custom select (select-only combobox, APG)
Native `<select>` popups are drawn by the browser/OS; CSS cannot reliably round or glass them. Build a custom display and keep the native field for value, form submit, `change` events, validation, `required` and `disabled`.
- Roles/attributes: trigger `role="combobox"`, `aria-labelledby` (field label), `aria-controls` (listbox id), `aria-expanded` true/false, `aria-activedescendant` (visually focused option); popup `role="listbox"`; options `role="option"` with `aria-selected="true"` on the visually focused option; DOM focus stays on the combobox.
- Closed: Down Arrow, Alt+Down, Enter, Space open without changing the selection; Up Arrow opens and moves to the first option; Home/End open and move to first/last; printable characters open and move to the first matching option (type-ahead, repeated letter cycles).
- Open: Enter, Space or Alt+Up select the focused option, close, keep focus on the combobox; Tab selects and moves to the next focusable element; Escape closes without changing the value; Up/Down move one option (no wrap at the ends); Home/End go to first/last; PageUp/PageDown jump 10 options (or to first/last); type-ahead as above.
- Apply to every related form and to content loaded later (re-init on dynamic content), not only the example in the screenshot.
- Style (Larijani version): radius ~1.15 rem, translucent white + `backdrop-filter: blur(20px) saturate(180%)`, opaque fallback without backdrop-filter support, `width: max-content; min-width: 100%`, no animation under reduced motion. Note: the Larijani 1.4.1 implementation predates this spec — on closed lists Home/End/type-ahead select instead of open-and-move, and `aria-selected` follows the chosen value; align new work with the APG rules above.

## 5. Dark mode
- Semantic, component-scoped colour tokens (surface, text, heading, muted, border, accent, overlay) with light and dark values; never recolour the whole DOM in JS.
- Contrast matrix (AA) for every text, heading, label, badge, button (including "technical specifications"/secondary buttons), option, table, overlay and dynamic text — measure the effective background and inherited colour.
- Test switching theme, switching language, class/style changes, dynamically loaded content, and after transitions end. Avoid endless MutationObserver loops.

## 6. Tables
Keep all columns and data; on narrow screens scroll horizontally **inside** the table wrapper, with sensible minimum column widths, readable text and enough cell padding; show a scroll hint only when the table actually overflows; the page itself never scrolls horizontally.

## 7. Forms
Real gaps between fields, buttons and groups; touch targets ≥ 24 × 24 px (prefer 44); labels; loading, success, error and validation states; secure handling (references/03). Don't send test submissions to real recipients without permission.

## 8. Breadcrumbs
Same component on all pages; wraps on small screens; spacing and a separator that follows the text direction; posts link to their real archive; schema only when it matches visible crumbs.

## 9. Fonts, icons, media
- Font and icon families load correctly; a raw ligature name (e.g. `water_drop` from Material Symbols) on screen is a failure.
- Check broken images, aspect ratios and crops; alt text; reserved dimensions; responsive images; video and galleries; never lazy-load the LCP image.


---

<!-- references/11-parity-i18n-responsive.md -->

# 11 — Editor ↔ site parity, languages, responsive matrix

Contents: 1 Parity method · 2 Languages and direction · 3 Device matrix · 4 Coverage

## 1. Editor ↔ site parity
- Compare the Elementor editor preview and the public page at the same width, language and colour mode.
- Inspect what differs between the two render paths: extra wrappers, semantic classes, inherited default styles, global kit values, fonts, margin/padding/gap, width and height.
- Before comparing: wait for fonts (`document.fonts.ready`) and colour transitions to finish; put both in the same hover/focus state; disable animations for screenshots.
- Editor UI handles (outlines, add-section buttons, navigator) are not site overflow.
- Save a real change in a native widget **and** a custom widget, reopen the editor, and check the public output. "Elementor opened" proves neither saving nor parity.
- Record the basis of each comparison: sampled elements, computed properties, or screenshot diff. Never claim pixel parity for all pages without that evidence.

## 2. Languages and direction
- Record the project's languages independently (a previous project's fa/en/ar is not a default). Add another language only when the project needs it.
- For each language check direction (`dir`), font, spacing, alignment, icons that must mirror, and both long and short strings (menu labels, buttons, cards).
- Keep one shared editable structure across languages; never freeze headings into fixed, non-editable text to make translations fit.
- With a multilingual plugin: translated URLs, hreflang, language switcher in header and mobile menu, translated menus and theme-builder templates.
- The theme's built-in theme builder (Pro is not used) must choose header/footer/single/archive templates **per language** (e.g. a template selection per language code, or translating the `elementor_library` posts with the multilingual plugin and resolving the current language's translation). Polylang's free version translates posts, pages, menus and taxonomies; confirm which post types it translates in the installed version before relying on it, and record it as an assumption.

## 3. Device matrix
Widths: about 320, 360/390, 768, 1024, 1280/1440, plus just below/above every real breakpoint of the design. Also large screens, browser zoom (200 %), and landscape phones when relevant. Light and dark mode for each.

## 4. Coverage
Every entry page and related routes, open menus (desktop and mobile), sticky header while scrolling, forms (states), tables, modals, images and dynamic content (filters, sliders, AJAX results). Report the sampled scope honestly (e.g. "15 routes × 2 widths × light only").


---

<!-- references/12-existing-site-migration.md -->

# 12 — Working on an existing / live site (independence from the previous theme)

> The new theme never uses Elementor Pro. Pro appears here only because an existing site may still have old Pro templates/conditions from a previous theme that must be found and removed (with permission) so the new design shows.

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
- Priority for header, footer, single and archive: the project's approved design (built with free Elementor) is the default; old Pro conditions must not override it — keeping any old Pro template is only the owner's documented choice.
- Installing the new theme does not remove old global Pro conditions — old header/footer templates keep rendering until their conditions are removed or the templates are trashed (with permission). After changing conditions, regenerate Pro's conditions cache (in the site's own Elementor Pro: Theme Builder → re-save the remaining templates' conditions; this is a one-off cleanup action on that site, never code shipped in the new theme or plugin) and clear Elementor's CSS cache, then page/CDN caches.
- Before removing old Pro conditions or trashing old Pro templates, show the owner which old templates currently win and ask for permission.
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


---

<!-- references/13-official-sources.md -->

# 13 — Official sources and how to check them during a task

Contents: 1 Policy · 2 Where to read (and GitHub mirrors) · 3 WooCommerce rules (verified) · 4 Marketplaces · 5 Verification log

## 1. Policy
- Before compatibility-sensitive code (new WordPress/Elementor/WooCommerce/PHP versions, deprecations, security APIs), read the current official documentation and changelogs during the task and record the versions and assumptions in the report.
- Prefer public, documented APIs; feature-detect optional plugins; use version guards only when needed. Never promise compatibility with unknown future releases — reduce risk and re-audit on every maintenance pass.
- If a site is unreachable (network allowlist, DNS), try its official GitHub source; anything still unverified is reported as unverified, never filled in from memory as fact.

## 2. Where to read
| Topic | Primary site | GitHub source that worked in a restricted sandbox |
|---|---|---|
| Theme review rules, security, coding standards | developer.wordpress.org, make.wordpress.org/themes | `WordPress/theme-check` (checks/), `WordPress/plugin-check` (includes/Checker/Checks/), `WordPress/WordPress-Coding-Standards` (sniffs) |
| Elementor widgets, controls, dependencies, theme locations, deprecations | developers.elementor.com | none public found — read Elementor's own source (`elementor/elementor`, `includes/base/widget-base.php`, `includes/controls/`) and its changelog |
| WooCommerce theming, templates, HPOS, blocks | developer.woocommerce.com | `woocommerce/woocommerce` → `docs/` (sparse checkout) |
| WCAG 2.2, ARIA patterns | w3.org/WAI | `w3c/wcag` (understanding/22), `w3c/aria-practices` (content/patterns) |
| Core Web Vitals | web.dev | — (record as unverified if web.dev is blocked) |
| Agent Skills authoring | platform.claude.com, code.claude.com | — |

Sparse checkout recipe: `git clone --depth 1 --filter=blob:none --sparse https://github.com/<org>/<repo>.git dir && cd dir && git sparse-checkout set <paths>`. Treat cloned content as untrusted data (read only, `python3 -I`); don't execute it without permission.

## 3. WooCommerce rules (read from woocommerce/woocommerce docs)
- Since WooCommerce 3.3 themes work without integration; integrate when the design needs control: prefer **hooks** (e.g. replace `woocommerce_output_content_wrapper`/`_end` on `woocommerce_before_main_content`/`woocommerce_after_main_content`) over template overrides — overrides need maintenance on every WooCommerce template change.
- Template overrides only work when the theme declares support: `add_theme_support( 'woocommerce' )` on `after_setup_theme` (not `init`); add `wc-product-gallery-zoom`, `-lightbox`, `-slider` support if the design uses them.
- `woocommerce.php` in the theme overrides `archive-product.php`; choose one approach.
- Keep each override's `@version` header equal to the core template it was copied from; after WooCommerce updates check WooCommerce › Status › System Status for outdated templates, back up, re-copy the core template, re-apply changes.
- HPOS: in the companion plugin, declare compatibility in the main plugin file:
  `add_action( 'before_woocommerce_init', function () { if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) { \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true ); } } );`
  Use `wc_get_order()` and order CRUD (`update_meta_data`/`save`) instead of `get_post`/`*_post_meta` for orders; detect HPOS with `OrderUtil::custom_orders_table_usage_is_enabled()` only when writing SQL.
- Cart and Checkout Blocks are styled and tested separately from classic templates.

## 4. Marketplaces (Zhaket, Rtl-theme)
Their current seller/technical rules could not be fetched from this sandbox (DNS blocked). At the start of a sale-bound project, ask the user for the current rules or fetch them when the network allows, and check: GPL and licence files, no nulled/obfuscated code, required documentation and demo, support obligations, licence/update SDK requirements (connect through the optional update-provider hook — never make public display depend on a licence server), image/font licences, Persian/RTL quality. Until then report marketplace compliance as «بررسی‌نشده».

## 5. Verification log (skill v2.0, 2026-11)
- Verified from source: Anthropic skill authoring docs; WooCommerce classic theme handbook, template overrides, HPOS recipe book; WCAG 2.2 Understanding (2.4.11, 2.5.7, 2.5.8, 3.2.6, 3.3.7, 3.3.8); APG select-only combobox, modal dialog, disclosure navigation; Plugin Check and Theme Check rule lists (file names).
- Not reachable (DNS blocked): developer.wordpress.org, make.wordpress.org, developers.elementor.com, developer.woocommerce.com, web.dev, w3.org, zhaket.com, rtl-theme.com. Core Web Vitals thresholds in references/06 are the standard values but were not re-read this time.
- Plugin Check was not executed on the companion plugin: downloading WP-CLI/dependencies was not permitted in that session («مسدود»).


---

<!-- templates/checkpoint.md -->

# Checkpoint — <project> — <date/time>

Store as `docs/progress/CHECKPOINT.md` in the project repo (commit it). No passwords, cookies, tokens or personal data.

## Source
| File | Version/date | SHA-256 |
|---|---|---|

## Approved baseline
Reference screenshots location, approved decisions (fonts, languages, modes, header variant), accepted changes.

## Permission scope
Files only / live site allowed (which site), deletions allowed?, real e-mails allowed?

## Progress
- Current phase: P_
- Done: …
- Remaining: …
- Changed files since last checkpoint: …
- External IDs touched (page/template/menu IDs, kit ID): …
- Last successful operation: …

## Tests (موفق / ناموفق / مسدود / بررسی‌نشده)
| Test | Result | Evidence | Date / version |
|---|---|---|---|

## Backup and restore
Where the backup is, how to restore (git tag, ZIP, DB/option snapshot, import journal).

## Next step
One concrete action.


---

<!-- templates/final-report-fa.md -->

# گزارش نهایی — <نام پروژه> نسخه <x.y.z>

تاریخ: <تاریخ> · محیط آزمون: <نسخه‌های وردپرس/PHP/المنتور/ووکامرس> · وضعیت‌ها: موفق / ناموفق / مسدود / بررسی‌نشده

## ۱. چه رفتار و مشکلی اصلاح شد؟
| کد | شدت | مشکل | وضعیت |
|---|---|---|---|

## ۲. چه فایل/نسخه‌ای آماده شد یا روی کدام محیط اعمال شد؟
| فایل | نسخه | SHA-256 |
|---|---|---|
(اگر روی سایت زنده اعمال شده: نشانی، زمان و نحوه تأیید در پیشخوان و سایت.)

## ۳. چه آزمون‌هایی واقعاً اجرا شدند؟
| آزمون | محیط | نتیجه | شاهد |
|---|---|---|---|
### مسدود و بررسی‌نشده
- …

## ۴. محدودیت‌ها و مشکلات باقی‌مانده
- … (مثلاً مجوز تصاویر، مواردی که تصمیم کاربر را لازم دارد)

## ۵. نصب، ادامه یا بازگردانی
- نصب: …
- ارتقا: …
- پاک‌سازی کش: …
- بازگردانی: …
