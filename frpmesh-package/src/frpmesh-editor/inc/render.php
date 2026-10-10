<?php
defined( 'ABSPATH' ) || exit;
function frpme_data( $name ) {
    static $cache = array();
    if ( ! in_array( $name, array('components','pages','icons','legacy-map'), true ) ) { return array(); }
    if ( ! isset( $cache[$name] ) ) {
        $path = FRPME_DIR . 'data/' . $name . '.json';
        $data = file_exists($path) ? json_decode(file_get_contents($path),true) : array();
        $cache[$name] = is_array($data) ? $data : array();
    }
    return $cache[$name];
}
/** Option-based mutex (same technique as WP core's upgrader lock): add_option() fails when the row already exists. */
function frpme_lock($name,$ttl=120){
    $key=sanitize_key($name).'_lock';
    if(add_option($key,time(),'','no')){return true;}
    $held=(int)get_option($key);
    if($held&&(time()-$held)<$ttl){return false;}
    delete_option($key);
    return (bool)add_option($key,time(),'','no');
}
function frpme_unlock($name){delete_option(sanitize_key($name).'_lock');}
function frpme_options() { $data = get_option('frpme_settings',array()); return is_array($data) ? $data : array(); }
function frpme_setting( $key, $default = '' ) { $options=frpme_options();$legacy=array('phone_primary'=>'mobile','phone_office'=>'office','phone_secondary'=>'secondary','email'=>'email','address'=>'address','video_url'=>'video');if(function_exists('frpmt_option') && in_array($key,array('logo_id','primary_color'),true)){ $theme_value=frpmt_option($key);if($theme_value)return $theme_value; }if(isset($options[$key])){return $options[$key];}return isset($legacy[$key]) ? get_theme_mod('frpm_'.$legacy[$key],$default) : $default; }
function frpme_source() {
    static $cache=array();$object_id=get_queried_object_id();$key=(int)$object_id;
    if(isset($cache[$key]))return $cache[$key];
    $native=get_post_meta($object_id,'_frpme_source',true);if(in_array($native,array_keys(frpme_data('pages')),true))return $cache[$key]=$native;
    if ( is_singular('post') ) { return $cache[$key]='article'; }
    if ( is_home() || is_archive() || is_search() ) { return $cache[$key]='blog'; }
    $imported=function_exists('frpme_native_document_source')?frpme_native_document_source($object_id):'';if(in_array($imported,array_keys(frpme_data('pages')),true))return $cache[$key]=$imported;
    $template = get_page_template_slug();
    foreach ( array_keys(frpme_data('pages')) as $source ) { if ( 'templates/'.$source.'.php' === $template ) { return $cache[$key]=$source; } }
    if ( is_front_page() ) { return $cache[$key]='home'; }
    return $cache[$key]='';
}
function frpme_link( $path ) {
    $hash='';
    if ( false !== strpos($path,'#') ) { list($path,$hash)=explode('#',$path,2); $hash='#'.$hash; }
    if ( ''===$path || '/'===$path ) { return home_url('/').$hash; }
    $slug=trim($path,'/');$page=false;$ids=get_option('frpme_v3_pages',array());if(in_array($slug,array('about','services','contact','blog'),true)&&!empty($ids[$slug])&&'publish'===get_post_status((int)$ids[$slug]))$page=get_post((int)$ids[$slug]);if(!$page)$page=get_page_by_path($slug);
    if(!$page&&'gallery'===$slug)return home_url('/').'#projects';
    return ($page ? get_permalink($page) : home_url($path)).$hash;
}
function frpme_resolve( $markup ) {
    $markup=preg_replace_callback('/\{\{ASSET:([a-zA-Z0-9_\/.\-]+)\}\}/',function($m){ return esc_url(FRPME_URL.$m[1]); },$markup);
    $markup=preg_replace_callback('/\{\{URL:([^}]*)\}\}/',function($m){ return esc_url(frpme_link(html_entity_decode($m[1],ENT_QUOTES,'UTF-8'))); },$markup);
    // Contact changes apply to every original occurrence, including labels and links.
    foreach ( array('primary'=>'+989126124876','office'=>'+982133481307','secondary'=>'+989113894473') as $key=>$number ) {
        $custom=frpme_setting('phone_'.$key,$number);
        $markup=str_replace('tel:'.$number,'tel:'.esc_attr($custom),$markup);
        if ($custom!==$number) {
            $display=strtr(str_replace('+98','0',$custom),array('0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹'));
            $patterns=array('primary'=>'/۰۹۱۲[\s‌]*۶۱۲[\s‌]*۴۸۷۶|۰۹۱۲۶۱۲۴۸۷۶/','office'=>'/۰۲۱[\s‌]*۳۳۴۸[\s‌]*۱۳۰۷|۰۲۱۳۳۴۸۱۳۰۷/','secondary'=>'/۰۹۱۱[\s‌]*۳۸۹[\s‌]*۴۴۷۳|۰۹۱۱۳۸۹۴۴۷۳/');
            $markup=preg_replace($patterns[$key],esc_html($display),$markup);
        }
    }
    $markup=str_replace('asgharjalali1357@gmail.com',esc_attr(frpme_setting('email','asgharjalali1357@gmail.com')),$markup);
    $address=frpme_setting('address');
    if ($address) { $markup=str_replace('بزرگراه امام رضا، بعد از میدان آقانور، بیست‌متری تختی، نبش کوچه پنجم، پلاک یک',esc_html($address),$markup); }
    $video=frpme_setting('video_url');
    if ($video) { $markup=preg_replace_callback('/(<source[^>]*src=")[^"]*(")/',function($m)use($video){return $m[1].esc_url($video).$m[2];},$markup); }
    $logo=(int)frpme_setting('logo_id',get_theme_mod('custom_logo'));
    if ($logo) {
        $url=wp_get_attachment_image_url($logo,'full');
        if ($url) { $markup=preg_replace_callback('/(<span class="brand-symbol[^>]*>\s*<img[^>]*src=")[^"]*(")/',function($m)use($url){return $m[1].esc_url($url).$m[2];},$markup); }
    }
    return $markup;
}
function frpme_component_markup( $key, $settings=array() ) {
    $components=frpme_data('components'); if (!isset($components[$key])) { return ''; }
    $component=$components[$key];$stored=get_option('frpme_content',array());
    $global=isset($stored[$key]) && is_array($stored[$key]) ? $stored[$key] : array();
    $replace=array();
    foreach ($component['fields'] as $field) {
        $control='frpme_'.$field['key'];
        $value=array_key_exists($control,$settings) ? $settings[$control] : (isset($global[$field['key']]) ? $global[$field['key']] : $field['default']);
        if ('image'===$field['kind'] && is_array($value)) { $image_url=isset($value['url']) ? $value['url'] : ''; $value= !empty($value['id']) ? (wp_get_attachment_image_url((int)$value['id'],'full') ?: $image_url) : $image_url; }
        if ('url'===$field['kind'] && is_array($value)) { $value=isset($value['url']) ? $value['url'] : ''; }
        if (!is_scalar($value)) { $value=$field['default']; } $value=(string)$value;
        if (in_array($field['kind'],array('url','image'),true)) { $value=$value===$field['default'] ? $value : esc_url($value); }
        elseif ('icon'===$field['kind']) { $icons=frpme_data('icons'); $value=in_array(ltrim($value,'#'),$icons,true) ? esc_attr($value) : esc_attr($field['default']); }
        elseif ('attribute'===$field['kind']) { $value=esc_attr($value); }
        else { $value=(isset($field['prefix']) ? $field['prefix'] : '').esc_html($value).(isset($field['suffix']) ? $field['suffix'] : ''); }
        $replace['{{FIELD:'.$field['key'].'}}']=$value;
    }
    if (!empty($component['js_config'])) { $config=array();foreach($component['fields'] as $field){$control='frpme_'.$field['key'];$value=isset($settings[$control]) ? $settings[$control] : (isset($global[$field['key']]) ? $global[$field['key']] : $field['default']);$config[$field['key']]=(isset($field['prefix']) ? $field['prefix'] : '').(is_scalar($value) ? sanitize_textarea_field($value) : $field['default']).(isset($field['suffix']) ? $field['suffix'] : '');}return '<script type="application/json" data-frpme-component="'.esc_attr($key).'" data-frpme-message-source="'.esc_attr($component['source']).'">'.wp_json_encode($config,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).'</script>'; }
    $markup=strtr($component['markup'],$replace);
    if ('blog'===$component['source'] && (is_archive() || is_search()) && false!==strpos($markup,'<h1')) { $title=is_search() ? 'نتایج جستجو: '.get_search_query() : wp_strip_all_tags(get_the_archive_title());$markup=preg_replace('/<h1[^>]*>.*?<\/h1>/s','<h1>'.esc_html($title).'</h1>',$markup); }
    $markup=preg_replace_callback('/\{\{DYNAMIC:([a-z\-]+)\}\}/',function($m)use($key,$settings){return frpme_dynamic($m[1],$key,$settings);},$markup);
    return frpme_resolve($markup);
}
function frpme_render_component($key,$settings=array()) {
    // Bundled HTML is trusted; field substitutions and dynamic data are escaped at their boundaries.
    echo frpme_component_markup($key,$settings); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
function frpme_main_open($source) {
    $pages=frpme_data('pages');if (!isset($pages[$source])) {return;}
    echo '<main';
    foreach ($pages[$source]['main_attributes'] as $key=>$value) { if (in_array($key,array('id','class','aria-label'),true)) {echo ' '.esc_attr($key).'="'.esc_attr($value).'"';} }
    echo '>';
}
function frpme_render_page($source,$settings=array()) {
    $pages=frpme_data('pages');if(!isset($pages[$source])){return;}
    echo '<div class="frpme-page" data-frpme-source="'.esc_attr($source).'">';
    frpme_render_component($pages[$source]['before'],$settings);frpme_main_open($source);
    foreach ($pages[$source]['components'] as $key){frpme_render_component($key,isset($settings[$key]) ? $settings[$key] : array());}
    echo '</main>';frpme_render_component($pages[$source]['after'],$settings);echo '</div>';
}
function frpme_sprite() {
    static $printed=false;if($printed){return;}$printed=true;
    echo file_get_contents(FRPME_DIR.'data/sprite.html'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action('wp_body_open','frpme_sprite',1);
function frpme_chrome($key) {
    $id=(int)frpme_setting($key.'_template_id');
    if ($id && class_exists('\\Elementor\\Plugin') && 'elementor_library'===get_post_type($id) && 'publish'===get_post_status($id)) {
        static $rendering=array();
        if (empty($rendering[$key])) {
            $rendering[$key]=true;$output=\Elementor\Plugin::$instance->frontend->get_builder_content_for_display($id);$rendering[$key]=false;
            if ($output) { echo $output;return; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
    frpme_render_component($key);
}
