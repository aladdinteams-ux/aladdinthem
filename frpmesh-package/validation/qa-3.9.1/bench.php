<?php
$els=array();for($i=0;$i<3000;$i++)$els[]=array('id'=>dechex($i+100000),'elType'=>'widget','widgetType'=>'heading','settings'=>array('title'=>'Heading '.$i,'align'=>'center','typography_font_size'=>array('unit'=>'px','size'=>20)),'elements'=>array());
$json=wp_json_encode(array(array('id'=>'abcd','elType'=>'container','settings'=>array(),'elements'=>$els)));
$id=wp_insert_post(array('post_title'=>'normal big','post_type'=>'page','post_status'=>'publish'));update_post_meta($id,'_elementor_data',wp_slash($json));
function old_doc_source($id){$d=json_decode(get_post_meta($id,'_elementor_data',true),true);if(!is_array($d))return '';$map=frpme_native_map();$scan=function($ns)use(&$scan,$map){foreach($ns as $n){$k=isset($n['settings']['frpme_native'])?$n['settings']['frpme_native']:'';if(isset($map[$k]['attrs']['data-frpme-source']))return $map[$k]['attrs']['data-frpme-source'];if(!empty($n['elements'])){$s=$scan($n['elements']);if($s)return $s;}}return '';};return $scan($d);}
get_post_meta($id,'_elementor_data',true);
$n=50;$t=microtime(true);for($i=0;$i<$n;$i++)old_doc_source($id);$a=(microtime(true)-$t)/$n*1000;
$t=microtime(true);for($i=0;$i<$n;$i++)frpme_native_document_source($id);$b=(microtime(true)-$t)/$n*1000;
printf("doc size=%.0fKB  old=%.2fms  new=%.4fms per frpme_source() call on a normal Elementor page\n",strlen($json)/1024,$a,$b);
