<?php
/**
 * Larijani Stone theme bootstrap.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

define( 'LS_VERSION', '1.3.0' );
define( 'LS_DIR', get_template_directory() );
define( 'LS_URI', get_template_directory_uri() );

require_once LS_DIR . '/inc/helpers.php';
require_once LS_DIR . '/inc/demo/images.php';
require_once LS_DIR . '/inc/demo/content.php';
require_once LS_DIR . '/inc/setup.php';
require_once LS_DIR . '/inc/enqueue.php';
require_once LS_DIR . '/inc/customizer.php';
require_once LS_DIR . '/inc/post-types.php';
require_once LS_DIR . '/inc/forms.php';
require_once LS_DIR . '/inc/template-functions.php';
require_once LS_DIR . '/inc/theme-builder.php';
require_once LS_DIR . '/inc/seo.php';
require_once LS_DIR . '/inc/render/header.php';
require_once LS_DIR . '/inc/render/footer.php';
require_once LS_DIR . '/inc/render/components.php';
require_once LS_DIR . '/inc/render/blog.php';
require_once LS_DIR . '/inc/render/shop.php';
require_once LS_DIR . '/inc/elementor/fallback.php';
require_once LS_DIR . '/inc/elementor/loader.php';
require_once LS_DIR . '/inc/demo/importer.php';
require_once LS_DIR . '/inc/admin/settings.php';
require_once LS_DIR . '/inc/migrations.php';

if ( class_exists( 'WooCommerce' ) ) {
	require_once LS_DIR . '/inc/woocommerce.php';
}
