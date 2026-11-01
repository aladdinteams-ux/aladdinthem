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
