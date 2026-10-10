<?php
defined('ABSPATH') || exit;
function frpme_version($path){return file_exists(FRPME_DIR.$path) ? (string)filemtime(FRPME_DIR.$path) : FRPME_VERSION;}
function frpme_register_assets(){
    wp_register_style('frpme-base',FRPME_URL.'assets/css/base.css',array(),frpme_version('assets/css/base.css'));
    foreach(array_keys(frpme_data('pages')) as $source){wp_register_style('frpme-reference-'.$source,FRPME_URL.'assets/css/'.$source.'.css',array(),frpme_version('assets/css/'.$source.'.css'));}
    $source=frpme_source();$base=$source ? 'frpme-reference-'.$source : 'frpme-base';
    wp_register_style('frpme-chrome',FRPME_URL.'assets/css/chrome.css',array($base),frpme_version('assets/css/chrome.css'));
    wp_register_style('frpme-bridge',FRPME_URL.'assets/css/bridge.css',array('frpme-chrome'),frpme_version('assets/css/bridge.css'));
    $deps=array();
    wp_register_script('frpme-runtime',FRPME_URL.'assets/js/runtime.js',$deps,frpme_version('assets/js/runtime.js'),array('strategy'=>'defer','in_footer'=>true));
    $content=get_option('frpme_content',array());$messages=array();foreach(array('header','home','about','services','contact','blog','article') as $source){if(isset($content[$source.'-messages'])){$messages[$source]=array();$components=frpme_data('components');if(isset($components[$source.'-messages'])){foreach($components[$source.'-messages']['fields'] as $field){if(isset($content[$source.'-messages'][$field['key']])){$messages[$source][$field['key']]=(isset($field['prefix'])?$field['prefix']:'').$content[$source.'-messages'][$field['key']].(isset($field['suffix'])?$field['suffix']:'');}}}}}wp_add_inline_script('frpme-runtime','window.FrpMeshEditorText='.wp_json_encode($messages,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).';','before');
    foreach(array('header','home','about','services','contact','blog','article') as $page){wp_register_script('frpme-program-'.$page,FRPME_URL.'assets/js/'.$page.'.js',array('frpme-runtime'),frpme_version('assets/js/'.$page.'.js'),array('strategy'=>'defer','in_footer'=>true));}
}
add_action('wp_enqueue_scripts','frpme_register_assets',5);
function frpme_enqueue(){
    if ('frpmesh-hybrid' !== get_template() && !frpme_source()) {return;}
    if(in_array(get_page_template_slug(),array('elementor_canvas','page-templates/canvas.php'),true) && !frpme_source())return;
    if (!wp_style_is('frpme-bridge','registered')){frpme_register_assets();}
    wp_enqueue_style('frpme-bridge');wp_enqueue_script('frpme-program-header');wp_enqueue_script('frpme-native-layout',FRPME_URL.'assets/js/native-layout.js',array('frpme-runtime'),frpme_version('assets/js/native-layout.js'),array('strategy'=>'defer','in_footer'=>true));
    $source=frpme_source();if($source && !(is_singular('post') && post_password_required())){wp_enqueue_script('frpme-program-'.$source);}
    $css='';$primary=frpme_setting('primary_color');if($primary){$css.='.frpme-design{--primary:'.sanitize_hex_color($primary).'}';}
    $font=frpme_setting('font_url');if($font){$css.='@font-face{font-family:FrpMeshLocal;src:url('.wp_json_encode(esc_url_raw($font)).');font-display:swap}.frpme-body.frpme-design{font-family:FrpMeshLocal,Tahoma,Arial,sans-serif}';}
    if(get_option('frpme_use_elementor_globals')){
        $css.='.frpme-body.frpme-design{--primary:var(--e-global-color-primary,#2563eb);--accent:var(--e-global-color-accent,#0ea5e9);--text:var(--e-global-color-text,#0f172a);--muted:var(--e-global-color-secondary,#475569);font-family:var(--e-global-typography-text-font-family,Vazirmatn),Tahoma,Arial,sans-serif}' .
            '.frpme-body.frpme-design :is(h1,h2,h3,h4,h5,h6){font-family:var(--e-global-typography-primary-font-family,Vazirmatn),Tahoma,Arial,sans-serif}';
    }
    if($css){wp_add_inline_style('frpme-bridge',$css);}
}
add_action('wp_enqueue_scripts','frpme_enqueue',999);
add_filter('body_class',function($classes){if(('frpmesh-hybrid'===get_template() || frpme_source()) && !(in_array(get_page_template_slug(),array('elementor_canvas','page-templates/canvas.php'),true) && !frpme_source())){$classes[]='frpme-body';$classes[]='frpme-design';if(isset($_GET['elementor-preview'])){$classes[]='frpme-editing';}}return $classes;});
