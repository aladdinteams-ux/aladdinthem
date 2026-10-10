# 02 — Elementor integration (free first, Pro optional)

## Custom widgets
- One base class (`Larijani_Widget_Base`) extending `\Elementor\Widget_Base`, or a tiny fallback class when Elementor is inactive so saved pages still render.
- Helpers: `section()`, `ctl( id, type, label, default, args )`, `rep( id, label, fields, defaults, title_field )`, `end()`, `t()` (inline-edit text), `on()` (switch). Defaults = the HTML's real content, so a dropped widget looks like the design immediately.
- Every visible text, link, image, icon, color and visibility switch is a control. Shared "style" section with CSS-variable controls (colors, radius, spacing, typography, container width selector `{{WRAPPER}} .max-w-7xl`).
- Categories: `larijani-stone` (+ one for single/archive/theme-builder widgets). Register on `elementor/widgets/register`; load widget files only then.
- Images: `larijani_img( $media, $class, $alt, $size, $lazy )` — `$lazy = 'high'` for hero/LCP images (eager + `fetchpriority="high"`), `true` elsewhere.
- Forms inside widgets output `larijani_form_hidden_fields( $form_name, $schema )` with the full field schema (see references/03).
- Dynamic tags for contact data (phone, email, address, WhatsApp) — work in free and Pro.

## Native layouts (preferred for static sections)
- Builders in `inc/demo/native.php` produce Elementor JSON: Containers (flex/grid, boxed width, responsive gaps/padding/widths, `flex_wrap: nowrap` + responsive widths for mobile rows, custom grow/shrink) and core widgets with the theme classes as `_css_classes` (`ls-n …`) and margins as `_margin` (not utility classes) so the Style tab keeps working.
- After saving `_elementor_data` always delete `_elementor_element_cache`, `_elementor_css`, `_elementor_page_assets` (otherwise old markup shows) and clear `files_manager` cache.
- Make sure the Container experiment is active on old sites (`elementor_experiment-container`).
- Provide an opt-in "rebuild layouts" for pages created by an older version (backup `_elementor_data` first, keep IDs/slugs/menus).
- A filter to fall back to theme-widget layouts (`ls_native_layouts`).

## Kit (Site Settings)
- Add theme colors as custom global colors (`ls…` ids) and Vazirmatn as a font (`elementor/fonts/additional_fonts`).
- System colors/typography/container width/space-between-widgets are the user's: fill only when empty or on a brand-new site; later syncs only change entries that still hold a theme-set value (font, container width).

## Theme builder
- Register Elementor Pro locations (`elementor/theme/register_locations`) — Pro is optional.
- Built-in theme builder for free Elementor: theme mods `ls_tb_header`, `ls_tb_footer`, `ls_tb_single_post`, `ls_tb_archive`, `ls_tb_single_product`, `ls_tb_shop`, `ls_tb_404` pick a saved template; `larijani_do_location()` renders it (enqueue its CSS).
- When importing with Pro: only set `_elementor_conditions` if the owner has no own template with conditions for that location; regenerate the conditions cache.

## Fallback renderer
Without Elementor, render saved `_elementor_data` (containers + core widgets + theme widgets) with Elementor-like markup and the scoped CSS, so the site looks the same; show a notice recommending Elementor.

## Verify in the real editor (references/07)
Panel loads with all theme widgets, preview renders, zero JS errors, edit a heading → publish → front shows it, Style-tab color/size/button background/grid columns override theme CSS. Test on Elementor 3.x and 4.x.
