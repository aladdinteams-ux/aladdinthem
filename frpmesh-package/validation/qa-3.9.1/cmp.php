<?php
function old_scan(){
 global $wpdb;$types=array();$last_id=0;
 do {
  $rows=$wpdb->get_results($wpdb->prepare("SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_id > %d ORDER BY meta_id ASC LIMIT 50",'_elementor_data',$last_id));
  foreach($rows as $row){$last_id=(int)$row->meta_id;$types+=frpme_legacy_types_in_data($row->meta_value);}
 } while(count($rows)===50);
 return array_keys($types);
}
$mode=getenv('MODE');$base=memory_get_peak_usage()/1048576;$t=microtime(true);
if($mode==='old'){$r=old_scan();}else{delete_option('frpme_legacy_scan_state');delete_option('frpme_legacy_scan_version');$r=frpme_scan_legacy();}
printf("%s: time=%.2fs peak=%.1fMB (+%.1fMB over baseline) ok=%s\n",$mode,microtime(true)-$t,memory_get_peak_usage()/1048576,memory_get_peak_usage()/1048576-$base,json_encode($r));
