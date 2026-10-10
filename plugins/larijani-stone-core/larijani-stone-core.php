<?php
/**
 * Plugin Name:       Larijani Stone Core
 * Plugin URI:        https://larijanistone.ir
 * Description:       افزونه همراه قالب لاریجانی استون: نمونه‌کارها (پروژه‌ها)، درخواست‌ها و استعلام‌ها، فرم‌های امن با آپلود خصوصی فایل. محتوای شما مستقل از قالب نگه داشته می‌شود.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            علاءالدین تم (Aladdin Theme)
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       larijani-stone-core
 * Domain Path:       /languages
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'LARIJANI_CORE_VERSION', '1.0.0' );
define( 'LARIJANI_CORE_FILE', __FILE__ );
define( 'LARIJANI_CORE_DIR', __DIR__ );

require_once LARIJANI_CORE_DIR . '/includes/post-types.php';
require_once LARIJANI_CORE_DIR . '/includes/uploads.php';
require_once LARIJANI_CORE_DIR . '/includes/forms.php';
require_once LARIJANI_CORE_DIR . '/includes/leads-admin.php';
require_once LARIJANI_CORE_DIR . '/includes/svg.php';
require_once LARIJANI_CORE_DIR . '/includes/privacy.php';
require_once LARIJANI_CORE_DIR . '/includes/migrate.php';

/**
 * Translations.
 */
function larijani_core_load_textdomain() {
	load_plugin_textdomain( 'larijani-stone-core', false, dirname( plugin_basename( LARIJANI_CORE_FILE ) ) . '/languages' );
}
add_action( 'init', 'larijani_core_load_textdomain', 0 );

/**
 * Activation: register types, flush rewrites, prepare private storage.
 */
function larijani_core_activate() {
	larijani_core_register_post_types();
	flush_rewrite_rules();
	larijani_core_private_dir();
	// Elementor editing for projects.
	$types = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	if ( is_array( $types ) && ! in_array( 'ls_project', $types, true ) ) {
		$types[] = 'ls_project';
		update_option( 'elementor_cpt_support', $types );
	}
	update_option( 'larijani_core_needs_migration', 1, false );
}
register_activation_hook( __FILE__, 'larijani_core_activate' );

/**
 * Deactivation: only rewrite rules (content is never touched).
 */
function larijani_core_deactivate() {
	wp_clear_scheduled_hook( 'larijani_core_cleanup' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'larijani_core_deactivate' );
