---
name: html-to-wp-theme
description: Convert HTML design files (static HTML/Tailwind exports, Google Stitch screens, landing pages, a ZIP of HTML pages) into a commercial-grade, RTL-ready WordPress + Elementor theme with a companion plugin, secure forms, setup wizard, WooCommerce support, tests and sale packaging for Zhaket / Rtl-theme. Use whenever the user gives HTML (file, ZIP, pasted markup or screenshots of a design) and asks for a theme ("قالب درست کن", "پوسته وردپرس بساز", "این HTML رو قالب کن", "تبدیل به قالب وردپرس/المنتور", "make a WordPress theme from this"), or asks to extend/fix a theme built with this process. Author of every theme is Aladdin Theme (علاءالدین تم).
---

# HTML → commercial WordPress/Elementor theme (Aladdin Theme)

This skill is the full method used to build the "Larijani Stone" theme (repo `aladdinteams-ux/aladdinthem`).
Follow it end to end every time the user hands over HTML and asks for a theme. The user writes Persian:
**reply in Persian**, keep code/commits/docs-for-code in English, user-facing docs in Persian.

## Non-negotiable rules (the user's own requirements)

1. **Design fidelity first.** The rendered theme must look like the HTML (colors, fonts, spacing, header/footer, animations). After the first faithful version, *no later change may break the look*: prove it with a pixel diff against reference screenshots (≤ ~0.1 % except intended changes).
2. **Everything editable in Elementor** (free version is enough). Prefer Elementor **core widgets + Containers**; build a custom widget only where core widgets can't do it. Style-tab values must override theme CSS. **Never require Elementor Pro**; support it optionally and say so.
3. **Install = ready site**: pages, menus (linked to objects, not hard-coded URLs), header/footer, front page, demo content are created by the setup — but **never delete or overwrite the owner's content without permission**, and every import must be undoable.
4. **Security is real, not just a nonce**: signed form schema + single-use token + rate limits + server validation + private uploads (see references/03).
5. **Content belongs in a companion plugin** (CPTs, leads, forms, uploads) with unchanged data keys; theme keeps a fallback so upgrades lose nothing.
6. **Never fake results.** Every test is either run (with numbers) or listed as «انجام‌نشده». Never call something done that wasn't implemented and verified. No fake schema/reviews/ratings.
7. **Backup before changing** (git tag + copy of the previous ZIP). Fix existing code; don't rewrite without reason; don't delete files without checking dependencies.
8. Work on the designated git branch, commit with clear messages, push with retries; **no PR unless asked**.

## Workflow (do the phases in order; each has a checkpoint)

| Phase | What | Reference |
|---|---|---|
| 0 | Read all HTML; inventory pages, sections, components, forms, fonts, colors, images, scripts; list what each needs (static → core widgets, dynamic → custom widget/template). Write a short plan + task list. | references/01-architecture.md |
| 1 | Theme skeleton: scoped Tailwind build, tokens → CSS vars, RTL, fonts bundled, header/footer renderers, templates (index/page/single/archive/404/search/Woo). | references/01-architecture.md |
| 2 | Elementor: native layouts (containers + core widgets) for static sections, custom widgets for data/interactive parts, kit colors/fonts, built-in theme builder for free Elementor, fallback renderer without Elementor. | references/02-elementor.md |
| 3 | Companion plugin + secure forms + private uploads + leads admin + privacy. | references/03-security-forms-plugin.md |
| 4 | Setup: auto-setup only on fresh sites, wizard (requirements, plugin install on click, reversible import), menus/front page rules. | references/04-setup-wizard-import.md |
| 5 | Commercial settings (colors, fonts, type scale, mobile/tablet, width, buttons, reset/restore), search/filter UX, glass dropdowns, active menu logic, branding. | references/05-settings-ux.md |
| 6 | Performance, SEO, accessibility. | references/06-perf-seo-a11y.md |
| 7 | QA in real sandboxes (WP + real Elementor 3.x and 4.x built from source + WooCommerce), pixel diff, attacks, PHPUnit, Theme Check, PHPCS. | references/07-qa-sandbox.md + scripts/ |
| 8 | Sale package: style.css header, readme.txt, screenshot.png 1200×900, CHANGELOG, POT, licenses, Persian guides, child theme, plugin ZIP bundled, manifest, honest report. | references/08-packaging-sale.md |

Before starting, skim references/09-pitfalls.md — it lists every trap hit last time (it saves hours).

## Branding (always)
- Author: `علاءالدین تم (Aladdin Theme)` in theme/child/plugin headers, readme `Contributors: aladdintheme`, copyright `Copyright (C) <year> Aladdin Theme (علاءالدین تم)`.
- Logos: `assets/branding/aladdin-theme.png` (light) and `aladdin-theme-dark.png` (blue) — copy into the theme's `assets/images/`, show an author badge in the settings header, an "about" card on the overview tab, and a footer credit on the theme's admin screens (see references/05).
- No Author URI unless the user provides it. Theme/brand name, client logo and contact data come from the HTML/client — never invent company facts or numbers.

## Deliverables checklist (end of every build)
Theme ZIP · companion plugin ZIP (also bundled inside the theme) · child theme ZIP · release manifest with SHA-256 · Persian install/usage guide · bug list + fixed list · security report · features list · test report with real numbers and «انجام‌نشده» items · remaining limitations. Send the ZIPs and reports with SendUserFile and summarise in Persian (what was done, verified numbers, what's not tested, what the user must decide — e.g. image licences).
