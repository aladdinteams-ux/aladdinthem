<?php
/**
 * Plugin Name: FRP Mesh Design Editor
 * Description: Original FRP design, native Elementor templates and WordPress settings.
 * Version: 3.6.0
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Text Domain: frpmesh-editor
 */
defined( 'ABSPATH' ) || exit;
define( 'FRPME_VERSION', '3.6.0' );
define( 'FRPME_DIR', plugin_dir_path( __FILE__ ) );
define( 'FRPME_URL', plugin_dir_url( __FILE__ ) );
add_action('init',function(){load_plugin_textdomain('frpmesh-editor',false,dirname(plugin_basename(__FILE__)).'/languages');});
foreach ( array( 'render', 'dynamic', 'assets', 'admin', 'native', 'installer', 'elementor' ) as $frpme_module ) { require_once FRPME_DIR . 'inc/' . $frpme_module . '.php'; }
