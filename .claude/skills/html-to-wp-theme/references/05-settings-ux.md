# 05 — Commercial settings, search/filter UX, menus, branding

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
