# 09 — Pitfalls hit last time (read before starting)

| Symptom | Cause | Fix |
|---|---|---|
| Edited/re-imported Elementor page still shows old markup | `_elementor_element_cache` / `_elementor_css` / page assets | delete them after every `_elementor_data` save + clear files cache |
| Mobile rows of containers wrap or squash | default flex wrap/grow | `flex_wrap: nowrap` + responsive widths, explicit grow/shrink |
| Fallback (no Elementor) colors wrong | CSS variable inheritance from wrappers | reset vars on fallback wrappers |
| Theme CSS beats Elementor Style tab | selector specificity too high / `!important` | scope with `.ls-root`, keep ≤ 0-3-0, margins as `_margin` not utilities |
| Blog menu item active on shop/product/projects | WP `current_page_parent` on posts page | `larijani_menu_item_active()` (references/05) |
| Hero search "does nothing" | tab values ≠ real category slugs, other params ignored, unknown `product_cat` → 404 | real slugs + aliases + `ls_hs` filters + request normalisation |
| Custom dropdown invisible | copied select classes after adding the hide class | copy classes first |
| Native `<select>` list can't be glass/rounded | browser-drawn popup | combobox + listbox enhancement |
| Admin notices appear inside a custom page header | WP inserts after first h1 | `<hr class="wp-header-end">` |
| Forms break behind page cache | nonce in cached HTML | signed token fetched by AJAX on interaction |
| Time-trap bypass | trusting a client timestamp / skipping when missing | server-signed issue time |
| Customer files public | `wp_handle_upload` into media library | private store + admin download handler |
| Re-running import un-did nothing | no-op run overwrote the journal | keep previous journal when nothing changed |
| Activation changed an existing site | auto-setup on any site | only fresh/empty sites |
| Third-party form overflowed on mobile | `size="40"` inputs | `.ls-prose :is(input,select,textarea,iframe){max-width:100%}` |
| POT empty for the plugin | exclusion regex matched the absolute path | apply excludes to the path relative to root |
| `rm -rf` with globs blocked by safety checks | sandbox guard | delete explicit paths |
| Composer/PHAR downloads fail | proxy blocks dist/phar hosts | `--prefer-source` (git) / git clone |
| Version string mismatch for bundled libs | stale `?ver=` | read the real version from the file header |
| Lighthouse/real devices unavailable | sandbox | mark «انجام‌نشده», never claim |
