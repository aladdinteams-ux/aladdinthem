<?php
function reset_scan(){foreach(array('frpme_legacy_scan_state','frpme_legacy_scan_version','frpme_legacy_scan_lock_lock','frpme_legacy_types') as $o)delete_option($o);}
function ok($c,$m){echo ($c?'PASS ':'FAIL ').$m."\n";}
// 1 preserved legacy types
reset_scan();update_option('frpme_legacy_types',array('frpme-old-type'));
add_filter('frpme_legacy_scan_window',function(){return 100;});
$slices=0;do{$st=frpme_legacy_scan_run(0.0001);$slices++;if($slices>500)break;}while($st['status']==='running');
ok($st['status']==='complete','resumable scan completes ('.$slices.' slices, window=100)');
$types=get_option('frpme_legacy_types');
ok(in_array('frpme-old-type',$types,true)&&in_array('frpm-home',$types,true)&&in_array('frpme-header',$types,true),'previous types preserved + new merged: '.json_encode($types));
ok(get_option('frpme_legacy_scan_version')===FRPME_VERSION,'version flag set');
// 2 lock
reset_scan();ok(frpme_lock('frpme_legacy_scan',120),'lock acquired');
ok(false===frpme_legacy_scan_run(1),'second runner refused while locked');
frpme_unlock('frpme_legacy_scan');
ok(is_array(frpme_legacy_scan_run(1)),'runs after unlock');
// 3 stale lock stolen
reset_scan();update_option('frpme_legacy_scan_lock',time()-1000);
ok(is_array(frpme_legacy_scan_run(1)),'stale lock taken over');
// 4 progress mid-way
reset_scan();$st=frpme_legacy_scan_run(0.0001);ok($st['status']==='running'||$st['status']==='complete','state persisted: '.$st['status'].' cursor='.$st['cursor']);
// 5 db error handling
reset_scan();global $wpdb;$orig=$wpdb->postmeta;$wpdb->postmeta='nonexistent_tbl';$wpdb->suppress_errors(true);
for($i=0;$i<6;$i++){$st=frpme_legacy_scan_run(1);}
$wpdb->postmeta=$orig;ok($st&&('failed'===$st['status']||'running'===$st['status']&&$st['attempts']>0)||$st===false,'DB error recorded, no fatal: '.json_encode($st?array($st['status'],$st['attempts'],count($st['errors'])):$st));
ok(!frpme_legacy_scan_done(),'failed scan is not marked complete');
// 6 cron schedule
reset_scan();wp_clear_scheduled_hook('frpme_legacy_scan_event');frpme_legacy_scan_schedule(10);ok((bool)wp_next_scheduled('frpme_legacy_scan_event'),'cron event scheduled');
frpme_legacy_scan_schedule(10);$c=_get_cron_array();$n=0;foreach($c as $t=>$h)if(isset($h['frpme_legacy_scan_event']))$n+=count($h['frpme_legacy_scan_event']);ok($n===1,'no duplicate cron events ('.$n.')');
do_action('frpme_legacy_scan_event');ok(frpme_legacy_scan_done(),'cron callback completes scan');
