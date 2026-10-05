# Accessibility Audit — Larijani Stone 1.2.0

Target: WCAG 2.2 AA. Tools: Lighthouse 12.8.2 (axe-core rules), custom Playwright checks (headings, labels, names, alt, duplicate IDs, target size), keyboard interaction script. Manual screen-reader testing: **NOT EXECUTED**.

## Automated results (final)

| Check | Evidence | Status |
|---|---|---|
| Lighthouse Accessibility | **100** on home, store, product, contact, post — mobile and desktop (10 runs) | PASS |
| One H1 / no heading skips | 285-check matrix + detail crawl of 17 routes at 390/1280 | PASS |
| Images without `alt` | 0 | PASS |
| Form controls without accessible name | 0 real cases (crawler flagged WP's comment `<input type=submit value=…>`, which is named by its `value`) | PASS |
| Buttons/links without a name | 0 | PASS |
| Duplicate IDs | 0 | PASS |
| `lang` / `dir` | `lang="fa-IR" dir="rtl"` | PASS |
| Landmarks | header, nav, main, footer on every route; skip link to `#ls-main` | PASS |
| Colour contrast | Lighthouse: 0 failures after token fixes | PASS |
| Target size (2.5.8) | interactive controls ≥ 24 px except inline footer/breadcrumb text links (16–22 px tall) and 16 px checkboxes inside full-width `<label>`s | WARN — rely on the spacing/inline exceptions |

## Keyboard & focus (runtime, 21/21 PASS)
Drawer menu opens with Enter, `aria-expanded` toggles, `role="dialog"` + `aria-modal`, focus moves inside, Tab is trapped, Escape closes, focus returns to the opener, page scroll unlocks, backdrop click closes; search dialog focuses its input; skip link is the first focus stop; visible focus ring on navigation.

## Fixes in 1.2.0
- Contrast: `outline` #757870→#686B63, `accent-emerald` #059669→#047857, `accent-amber` #B45309→#92400E, small grey labels slate-400→slate-500 on light backgrounds.
- Heading hierarchy: sidebar/footer/card titles re-levelled; names/badges are no longer headings; contact cards, page-hero card and product spec title are H2; store grid has a visually hidden H2.
- Hero search type buttons: `role="group"` + `aria-pressed` (was an incomplete `tablist`).
- Accessible names for CTA/newsletter phone inputs and the calculator slider; alt text in the demo article.
- Dialog semantics, focus trap and focus restoration for the drawer and search panels.

## Not executed / unknown
- NVDA/JAWS/VoiceOver/TalkBack: NOT EXECUTED.
- 200–400 % zoom & reflow, forced-colours mode, reduced motion review: NOT EXECUTED.
- Accessibility of the Elementor editor UI itself: out of scope.

**Accessibility Status: PASS (automated) with WARN** (target-size exceptions; manual AT testing not executed).
