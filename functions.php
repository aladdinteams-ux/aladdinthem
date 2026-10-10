<?php
/**
 * Larijani Stone theme bootstrap.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

define( 'LARIJANI_VERSION', '1.4.1' );
define( 'LARIJANI_DIR', get_template_directory() );
define( 'LARIJANI_URI', get_template_directory_uri() );

require_once LARIJANI_DIR . '/inc/helpers.php';
require_once LARIJANI_DIR . '/inc/demo/images.php';
require_once LARIJANI_DIR . '/inc/demo/content.php';
require_once LARIJANI_DIR . '/inc/setup.php';
require_once LARIJANI_DIR . '/inc/enqueue.php';
require_once LARIJANI_DIR . '/inc/customizer.php';
require_once LARIJANI_DIR . '/inc/post-types.php';
require_once LARIJANI_DIR . '/inc/forms.php';
require_once LARIJANI_DIR . '/inc/search-filters.php';
require_once LARIJANI_DIR . '/inc/template-functions.php';
require_once LARIJANI_DIR . '/inc/theme-builder.php';
require_once LARIJANI_DIR . '/inc/seo.php';
require_once LARIJANI_DIR . '/inc/render/header.php';
require_once LARIJANI_DIR . '/inc/render/footer.php';
require_once LARIJANI_DIR . '/inc/render/components.php';
require_once LARIJANI_DIR . '/inc/render/blog.php';
require_once LARIJANI_DIR . '/inc/render/shop.php';
require_once LARIJANI_DIR . '/inc/elementor/fallback.php';
require_once LARIJANI_DIR . '/inc/elementor/loader.php';
require_once LARIJANI_DIR . '/inc/demo/importer.php';
require_once LARIJANI_DIR . '/inc/admin/settings.php';
require_once LARIJANI_DIR . '/inc/admin/setup-wizard.php';
require_once LARIJANI_DIR . '/inc/migrations.php';
require_once LARIJANI_DIR . '/inc/compat.php';
require_once LARIJANI_DIR . '/inc/updater.php';

if ( class_exists( 'WooCommerce' ) ) {
	require_once LARIJANI_DIR . '/inc/woocommerce.php';
}
