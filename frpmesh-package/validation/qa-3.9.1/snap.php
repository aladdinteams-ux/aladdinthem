<?php
// Snapshot of all user-visible state: posts + meta (hash) + FRP/theme options.
$out=array();
$ids=get_posts(array('post_type'=>array('page','post','elementor_library'),'post_status'=>'any','numberposts'=>-1,'fields'=>'ids','orderby'=>'ID','order'=>'ASC','meta_query'=>array('relation'=>'OR',array('key'=>'_frpme_native_v3','compare'=>'EXISTS'),array('key'=>'_frpme_source','compare'=>'EXISTS'))));
foreach($ids as $id){$p=get_post($id);$m=get_post_meta($id);ksort($m);$out['post:'.$id]=array($p->post_title,$p->post_name,$p->post_status,md5($p->post_content),md5(serialize($m)));}
foreach(array('frpme_v3_pages','frpme_v3_library','frpme_settings','frpme_content','frpmt_settings','frpme_use_elementor_globals','show_on_front','page_on_front','frpme_v3_auto_setup_done') as $o)$out['opt:'.$o]=get_option($o);
$out['mods']=get_theme_mod('nav_menu_locations');
$out['menu_items']=count((array)wp_get_nav_menu_items('FRP Mesh - Primary'));
echo md5(json_encode($out))."\n"; file_put_contents(getenv('OUT'),json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
