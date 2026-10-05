# Developer hooks (public API) — Larijani Stone

All hooks are prefixed `ls_` and are considered stable from 1.2.0. Use them from a child theme (`child-theme/larijani-stone-child`) or a plugin.

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

## Actions

| Action | Arguments | When |
|---|---|---|
| `ls_lead_submitted` | `int $lead_id, array $rows, string $form_name` | After a lead form submission was stored (integrate CRM/SMS here; keep secrets server-side). |
| `ls_import_images_event` | — | WP-Cron event that sideloads the design images. |

## Template functions

`ls_opt( $key )`, `ls_render_site_header( $args )`, `ls_render_site_footer( $args )`, `ls_render_catalog( $args )`, `ls_product_card( $data, $style )`, `ls_post_card( $data, $style )`, `ls_section_heading( $args )`, `ls_jalali_date( $timestamp )`.

## Ownership meta written by the setup

`_ls_demo_page` (pages/templates created by the theme), `_ls_demo_key` (sample posts/projects/products), `_ls_demo_image` (design image key). Re-running the setup never touches objects without these keys.
