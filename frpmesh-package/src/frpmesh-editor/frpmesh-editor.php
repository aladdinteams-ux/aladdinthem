<?php
/**
 * Plugin Name: FRP Mesh Design Editor
 * Plugin URI: https://example.com/frpmesh
 * Description: افزونهٔ همراه قالب FRP Mesh؛ طراحی اصلی، الگوهای بومی المنتور، ساخت خودکار صفحه‌ها و تنظیمات محتوا. ساخته‌شده توسط علاءالدین تم. (Original FRP design, native Elementor templates and WordPress settings by Aladdin Theme.)
 * Author: علاءالدین تم
 * Author URI: https://example.com
 * Version: 3.9.1
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: frpmesh-editor
 * Domain Path: /languages
 */
defined( 'ABSPATH' ) || exit;
// A second copy of the plugin (for example an old folder name left behind after a manual upload) must not redeclare anything.
if ( defined( 'FRPME_VERSION' ) ) { return; }
define( 'FRPME_VERSION', '3.9.1' );
define( 'FRPME_DIR', plugin_dir_path( __FILE__ ) );
define( 'FRPME_URL', plugin_dir_url( __FILE__ ) );
add_action('init',function(){load_plugin_textdomain('frpmesh-editor',false,dirname(plugin_basename(__FILE__)).'/languages');});
foreach ( array( 'render', 'dynamic', 'assets', 'admin', 'native', 'installer', 'elementor' ) as $frpme_module ) { require_once FRPME_DIR . 'inc/' . $frpme_module . '.php'; }
/** The base design works without Elementor; editing needs it. Say so instead of failing silently. */
add_action('admin_notices',function(){
    if(defined('ELEMENTOR_VERSION')||!current_user_can('activate_plugins'))return;
    $screen=function_exists('get_current_screen')?get_current_screen():null;
    if(!$screen||false===strpos((string)$screen->id,'frpme-')&&'plugins'!==$screen->id)return;
    echo '<div class="notice notice-warning"><p>'.esc_html__('FRP Mesh Design Editor برای ویرایش بصری صفحه‌ها به Elementor (نسخه رایگان) نیاز دارد. بدون آن، طراحی اصلی نمایش داده می‌شود اما ویرایش با Elementor در دسترس نیست.','frpmesh-editor').'</p></div>';
});
