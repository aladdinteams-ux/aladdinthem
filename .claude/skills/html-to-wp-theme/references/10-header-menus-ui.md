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
