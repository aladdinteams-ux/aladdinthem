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
