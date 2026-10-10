<?php
global $wpdb;
$n=(int)getenv('N')?:150;
$tpl=file_get_contents(WP_CONTENT_DIR.'/plugins/frpmesh-editor/templates/home.json');
$d=json_decode($tpl,true); $json=wp_json_encode($d['content']);
for($i=0;$i<$n;$i++){
 $id=wp_insert_post(array('post_title'=>"seed $i",'post_type'=>'page','post_status'=>'publish'));
 $val=$json;
 if($i%10==0)$val=preg_replace('/"elType":"container"/','"elType":"widget","widgetType":"frpm-home","settings":[]},{"elType":"widget","widgetType":"frpme-header","settings":[]},{"elType":"container"',$val,1);
 update_post_meta($id,'_elementor_data',wp_slash($val));
}
echo "seeded $n, size each ".strlen($json)."\n";
