# Developer hooks (public API) — Larijani Stone

All hooks are prefixed `ls_` and are considered stable from 1.2.0 (native layout API from 1.3.0). Use them from a child theme (`child-theme/larijani-stone-child`) or a plugin.

## Filters

| Filter | Arguments | Default / purpose |
|---|---|---|
| `ls_auto_setup_on_activation` | `bool $run` | `true` — create pages/menus/templates when the theme is activated. |
| `ls_import_demo_images` | `bool $run` | `true` — copy the design images to the media library in the background (WP-Cron). |
| `ls_option_fields` | `array $fields` | Theme option definitions shared by the dashboard settings page and the Customizer. |
| `ls_elementor_widgets` | `array $map` | Widget slug ⇒ class map registered with Elementor. |
| `ls_tb_template_for` | `int $id, string $location` | Saved Elementor template used for a location without Elementor Pro. |
| `ls_fallback_render` | `bool $enabled` | `true` — render saved Elementor layouts when Elementor is not installed. |
| `ls_dequeue_block_styles` | `bool $enabled` | `true` — skip block-library CSS on Elementor-built pages without blocks. |
| `ls_seo_plugin_active` | `bool $active` | Detected Yoast / Rank Math / SEOPress / AIOSEO / TSF / Slim SEO. |
| `ls_theme_schema_enabled` | `bool $enabled, string $type` | `organization` and `faq` JSON-LD are printed only without an SEO plugin. |
| `ls_organization_schema` | `array $node` | Organization JSON-LD node (front page). |
| `ls_lead_mimes` | `array $mimes` | Allowed upload types for lead forms. |
| `ls_lead_max_upload` | `int $bytes` | Max upload size per file (20 MB). |
| `ls_lead_max_fields` | `int $count` | Max fields accepted per submission (40). |
| `ls_wc_loop_card_style` | `string $style` | Card style for WooCommerce loops (`catalog`, `store`, `showcase`, `classic`, `compact`). |
| `ls_breadcrumb_trail` | `array $trail` | Breadcrumb items. |
| `ls_content_width` | `int $px` | `$content_width`. |
| `ls_migrations` | `array $steps` | Version ⇒ callback migration steps (see `inc/migrations.php`). |
| `ls_native_layouts` | `bool $native, array $row` | `true` — build static sections from Elementor core widgets in Containers (1.3.0+). Return `false` to get the 1.2 theme-widget layouts. |
| `ls_enable_elementor_containers` | `bool $enable` | `true` — switch on Elementor's Container feature during setup when an old site still has it off. |
| `ls_wc_product_structured_data` | `bool $enable` | `true` — collect WooCommerce Product/Offer JSON-LD for the theme's product layouts. |
| `ls_content_lang` | `string $lang` | `fa-IR` when RTL is forced on a non-RTL site language (`''` keeps WordPress' value). |
| `ls_meta_description_enabled` / `ls_meta_description` | `bool` / `string` | Fallback meta description (only without an SEO plugin). |

## Actions

| Action | Arguments | When |
|---|---|---|
| `ls_lead_submitted` | `int $lead_id, array $rows, string $form_name` | After a lead form submission was stored (integrate CRM/SMS here; keep secrets server-side). |
| `ls_import_images_event` | — | WP-Cron event that sideloads the design images. |

## Template functions

`ls_opt( $key )`, `ls_render_site_header( $args )`, `ls_render_site_footer( $args )`, `ls_render_catalog( $args )`, `ls_product_card( $data, $style )`, `ls_post_card( $data, $style )`, `ls_section_heading( $args )`, `ls_jalali_date( $timestamp )`.

## Ownership meta written by the setup

`_ls_demo_page` (pages/templates created by the theme), `_ls_demo_key` (sample posts/projects/products), `_ls_demo_image` (design image key). Re-running the setup never touches objects without these keys.
`_ls_elementor_data_backup` / `_ls_elementor_data_backup_date` — previous Elementor data kept when a page/template is rebuilt with the opt-in "rebuild" option.

## Native layout builder (`inc/demo/native.php`)

`ls_n_c( $children, $opts )` (Container), `ls_n_w( $type, $settings, $classes )` (any core widget), `ls_n_heading()`, `ls_n_text()`, `ls_n_button()`, `ls_n_iconw()`, `ls_n_section()`, `ls_n_section_heading()`, `ls_n_span( $span, $gap )`, and one recipe per converted section: `ls_nr_icon_cards()`, `ls_nr_about()`, `ls_nr_testimonials()`, `ls_nr_cta()`, `ls_nr_steps()`, `ls_nr_page_banner()`, `ls_nr_contact_cards()`. Margin utilities (`mt-*`, `mb-*`, `sm:`/`lg:`) on widgets are converted to Elementor's own Margin setting; other utility classes stay in *Advanced › CSS Classes*.
