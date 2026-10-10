<?php
function ok($c,$m){echo ($c?'PASS ':'FAIL ').$m."\n";}
wp_set_current_user(1);
// unslash: value arriving from options.php is already unslashed
$r=frpmt_sanitize(array('header_cta_text'=>'A\\B "q"','primary_color'=>'#abcdef','h1_size_desktop'=>'9999','header_cta_url'=>'javascript:alert(1)','primary_font'=>'Vazir; } body{x','sticky_header'=>'1','unknown'=>'x','social_links'=>"https://a.com\njavascript:x\nhttp://b.com"));
ok($r['header_cta_text']==='A\\B "q"','backslash/quotes survive a single save: '.$r['header_cta_text']);
ok($r['primary_color']==='#abcdef','color ok');
ok($r['h1_size_desktop']===2500,'number clamped');
ok($r['header_cta_url']==='','js url rejected: '.var_export($r['header_cta_url'],true));
ok($r['primary_font']==='','bad font rejected');
ok(!isset($r['unknown']),'unknown key dropped');
ok($r['social_links']==="https://a.com\nhttp://b.com",'urls filtered');
// css kept for users lacking unfiltered_html
update_option('frpmt_settings',array('custom_css'=>'--x:1px'));
define('DISALLOW_UNFILTERED_HTML',true);
ok(!current_user_can('unfiltered_html'),'unfiltered_html disabled in test');
$r=frpmt_sanitize(array('custom_css'=>'--evil:2px;color:red'));
ok($r['custom_css']==='--x:1px','stored CSS kept (not wiped) for restricted user: '.$r['custom_css']);
