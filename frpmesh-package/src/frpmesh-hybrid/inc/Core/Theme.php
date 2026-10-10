<?php
defined('ABSPATH') || exit;
function frpmt_setup(){foreach(array('title-tag','post-thumbnails','automatic-feed-links','responsive-embeds') as $support){add_theme_support($support);}add_theme_support('html5',array('search-form','comment-form','comment-list','gallery','caption','script','style'));add_theme_support('custom-logo',array('height'=>68,'width'=>72,'flex-height'=>true,'flex-width'=>true));add_image_size('frpm-card',720,480,true);}
add_action('after_setup_theme','frpmt_setup');
function frpmt_init(){load_theme_textdomain('frpmesh-hybrid',get_template_directory().'/languages');register_nav_menus(array('primary'=>esc_html__('منوی اصلی','frpmesh-hybrid')));}
add_action('init','frpmt_init');
function frpmt_has_builder(){if(!class_exists('\\Elementor\\Plugin')){return false;}$doc=\Elementor\Plugin::$instance->documents->get(get_the_ID());return $doc && $doc->is_built_with_elementor();}
function frpmt_is_editor_preview(){
    if(!class_exists('\\Elementor\\Plugin')){return false;}
    $plugin=\Elementor\Plugin::$instance;
    return $plugin && isset($plugin->preview) && is_callable(array($plugin->preview,'is_preview_mode')) && $plugin->preview->is_preview_mode();
}
function frpmt_elementor_location($location){
    return function_exists('elementor_theme_do_location') && !frpmt_is_editor_preview() && elementor_theme_do_location($location);
}
function frpmt_full_widget(){
    $raw=get_post_meta(get_the_ID(),'_elementor_data',true);
    // Legacy widget names always contain "frpm"; most pages do not, so skip the JSON decode for them.
    if(!is_string($raw)||false===strpos($raw,'frpm')){return false;}
    $data=json_decode($raw,true);if(!is_array($data)){return false;}
    $walk=function($items)use(&$walk){foreach($items as $item){if(isset($item['widgetType']) && in_array($item['widgetType'],array('frpm-home','frpm-about','frpm-services','frpm-contact','frpme-complete-page'),true)){return true;}if(!empty($item['elements']) && $walk($item['elements'])){return true;}}return false;};return $walk($data);
}
// Older companion plugins do not expose the native-document API.
function frpmt_is_native_document($id){
    return function_exists('frpme_native_document') && frpme_native_document($id);
}
function frpmt_builder_layout($source,$callback){
    $pages=frpme_data('pages');$page=$pages[$source];echo '<div class="frpme-page" data-frpme-source="'.esc_attr($source).'">';frpme_render_component($page['before']);frpme_main_open($source);call_user_func($callback);echo '</main>';frpme_render_component($page['after']);echo '</div>';
}
function frpmt_page($source){
    get_header();if(have_posts()){the_post();}
    if(frpmt_elementor_location('single')){get_footer();return;}
    if(!function_exists('frpme_render_page')){echo '<main id="main">';echo '<h1>'.esc_html(get_the_title()).'</h1>'; the_content();wp_link_pages();echo '</main>';}
    elseif(is_singular() && (frpmt_has_builder() || frpmt_is_editor_preview())){
        if(frpmt_is_editor_preview() || frpmt_full_widget() || frpmt_is_native_document(get_the_ID())){the_content();}else{frpmt_builder_layout($source,function(){the_content();});}
    }else{frpmt_reference($source);}
    get_footer();
}
function frpmt_reference($source){
    $id=(int)frpme_setting($source.'_template_id');
    if($id && class_exists('\\Elementor\\Plugin') && 'elementor_library'===get_post_type($id) && 'publish'===get_post_status($id)){
        if(frpmt_is_native_document($id)){echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($id);}else{frpmt_builder_layout($source,function()use($id){echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($id);});}
    }else{frpme_render_page($source);}
}
add_action('admin_notices',function(){if(function_exists('frpme_render_page') && !function_exists('frpme_native_document') && current_user_can('activate_plugins')){echo '<div class="notice notice-error"><p>'.esc_html__('افزونه همراه FRP Mesh قدیمی یا ناقص است. برای ویرایش بومی المنتور، FRP Mesh Design Editor نسخه ۳ یا جدیدتر را از بسته همراه نصب و فعال کنید.','frpmesh-hybrid').'</p></div>';}});

/** The theme works alone with a lightweight layout; tell administrators what the full design needs, only where it matters. */
add_action('admin_notices',function(){
    if(!current_user_can('activate_plugins'))return;
    $screen=function_exists('get_current_screen')?get_current_screen():null;
    if(!$screen||!in_array($screen->id,array('themes','plugins','toplevel_page_frpmt-settings'),true))return;
    if(!defined('FRPME_VERSION')){
        echo '<div class="notice notice-info"><p>'.esc_html__('افزونه همراه «FRP Mesh Design Editor» فعال نیست. قالب با چیدمان ساده کار می‌کند؛ برای طراحی کامل FRP Mesh افزونه همراه را از همین بسته نصب و فعال کنید.','frpmesh-hybrid').'</p></div>';
    }elseif(!defined('ELEMENTOR_VERSION')){
        echo '<div class="notice notice-warning"><p>'.esc_html__('Elementor (نسخه رایگان) فعال نیست. طراحی اصلی نمایش داده می‌شود، اما ویرایش بصری صفحه‌ها به Elementor نیاز دارد.','frpmesh-hybrid').'</p></div>';
    }
});
add_action('elementor/theme/register_locations',function($manager){$manager->register_all_core_location();});
