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
