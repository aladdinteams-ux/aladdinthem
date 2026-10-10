<?php
defined('ABSPATH') || exit;
function frpme_register_widget_category($elements_manager){
 $elements_manager->add_category('frpme',array('title'=>esc_html__('FRP Mesh','frpmesh-editor'),'icon'=>'fa fa-plug'));
}
add_action('elementor/elements/categories_registered','frpme_register_widget_category');
function frpme_widgets($manager){require_once FRPME_DIR.'inc/native-widgets.php';$manager->register(new Frpme_Field_Widget());$manager->register(new Frpme_Data_Widget());
 // Existing v2 documents retain their renderer; new projects see only the two exceptions.
 $legacy=get_option('frpme_legacy_types',array());if(!is_array($legacy))$legacy=array();
 // A previously saved v2 page remains editable even before the upgrade scan completes.
 $post_id=isset($_GET['post'])?absint(wp_unslash($_GET['post'])):get_queried_object_id();
 if($post_id)$legacy=array_unique(array_merge($legacy,array_keys(frpme_legacy_types_in_data(get_post_meta($post_id,'_elementor_data',true)))));
 if($legacy){require_once FRPME_DIR.'inc/widget.php';foreach(frpme_data('components') as $key=>$c){if(in_array('frpme-'.$key,$legacy,true))$manager->register(new Frpme_Component_Widget(array(),array('frpme_component'=>$key)));}foreach(array('home','about','services','contact') as $source){if(in_array('frpm-'.$source,$legacy,true))$manager->register(new Frpme_Legacy_Widget(array(),array('frpme_source'=>$source)));}if(in_array('frpme-complete-page',$legacy,true))$manager->register(new Frpme_Complete_Widget());}}
add_action('elementor/widgets/register','frpme_widgets');
/** Discover legacy widgets once on upgrade, in small batches outside editor requests. */
function frpme_legacy_types_in_data($data){
 $types=array();if(!is_string($data))return $types;
 preg_match_all('/"widgetType"\s*:\s*"((?:frpme-|frpm-)[^"]+)"/',$data,$matches);
 foreach($matches[1] as $type){if(!in_array($type,array('frpme-field','frpme-data'),true))$types[$type]=true;}
 return $types;
}
function frpme_scan_legacy(){
 global $wpdb;
 $types=array();$last_id=0;
 do {
  $rows=$wpdb->get_results($wpdb->prepare("SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_id > %d ORDER BY meta_id ASC LIMIT 50",'_elementor_data',$last_id));
  if(!is_array($rows))return false;
  foreach($rows as $row){$last_id=(int)$row->meta_id;$types+=frpme_legacy_types_in_data($row->meta_value);}
 } while(count($rows)===50);
 update_option('frpme_legacy_types',array_keys($types),false);
 return true;
}
function frpme_scan_legacy_on_upgrade(){
 if(!current_user_can('edit_theme_options')||get_option('frpme_legacy_scan_version')===FRPME_VERSION)return;
 // A first editor launch must never synchronously scan the entire posts table.
 if(wp_doing_ajax()||isset($_GET['action'])&&'elementor'===sanitize_key(wp_unslash($_GET['action'])))return;
 if(frpme_scan_legacy())update_option('frpme_legacy_scan_version',FRPME_VERSION,false);
}
add_action('admin_init','frpme_scan_legacy_on_upgrade');
function frpme_scan_saved_legacy($post_id){
 $data=get_post_meta((int)$post_id,'_elementor_data',true);
 $new=frpme_legacy_types_in_data($data);if(!$new)return;
 $old=get_option('frpme_legacy_types',array());if(!is_array($old))$old=array();
 $types=array_values(array_unique(array_merge($old,array_keys($new))));
 if($types!==$old)update_option('frpme_legacy_types',$types,false);
}
add_action('elementor/editor/after_save','frpme_scan_saved_legacy',10,1);
