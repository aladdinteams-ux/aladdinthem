<?php
defined('ABSPATH') || exit;
/** Persian names for pages/templates created by the installer (slugs stay English for compatibility). */
function frpme_source_title($source,$library=false){
 $names=array('home'=>'صفحهٔ اصلی','about'=>'درباره ما','services'=>'خدمات','contact'=>'تماس با ما','blog'=>'وبلاگ','article'=>'مقاله','header'=>'سربرگ','footer'=>'پابرگ');
 $title=isset($names[$source])?$names[$source]:$source;
 return $library?'الگوی FRP Mesh — '.$title:$title;
}
function frpme_install_document($source,$id=0,$library=false,$status='draft',$skip_backup=false){if(!in_array($source,array('home','about','services','contact','blog','article','header','footer'),true))return new WP_Error('frpme_source','Unknown template');$d=json_decode(file_get_contents(FRPME_DIR.'templates/'.$source.'.json'),true);if(empty($d['content']))return new WP_Error('frpme_template','Invalid template');if($id&&(!current_user_can('edit_post',$id)||'page'!==get_post_type($id)&&'elementor_library'!==get_post_type($id)))return new WP_Error('frpme_access','Access denied');
 if(!$id){$id=wp_insert_post(array('post_title'=>frpme_source_title($source,$library),'post_name'=>'frp-v3-'.$source,'post_type'=>$library?'elementor_library':'page','post_status'=>$library?'publish':$status),true);if(is_wp_error($id))return $id;}elseif(!$library&&!$skip_backup){$backup=array();foreach(array('_elementor_data','_elementor_page_settings','_elementor_edit_mode','_wp_page_template','_frpme_source') as $k)$backup[$k]=get_post_meta($id,$k,true);update_post_meta($id,'_frpme_v3_backup',wp_slash($backup));}
 update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode(frpme_native_tree($d['content']))));update_post_meta($id,'_elementor_edit_mode','builder');update_post_meta($id,'_elementor_page_settings',array('hide_title'=>'yes'));update_post_meta($id,'_frpme_native_v3',1);update_post_meta($id,'_frpme_source',$source);if(defined('ELEMENTOR_VERSION'))update_post_meta($id,'_elementor_version',ELEMENTOR_VERSION);
 if($library){$type=in_array($source,array('header','footer'),true)?'section':'page';update_post_meta($id,'_elementor_template_type',$type);wp_set_object_terms($id,$type,'elementor_library_type');}else{update_post_meta($id,'_wp_page_template','templates/'.$source.'.php');$pages=get_option('frpme_v3_pages',array());$pages[$source]=$id;update_option('frpme_v3_pages',$pages,false);}delete_post_meta($id,'_elementor_css');delete_post_meta($id,'_elementor_element_cache');return $id;}
// Runs once after the companion plugin and this theme are both active. Never overwrites existing pages.
// get_template() so a child theme of FRP Mesh Hybrid is accepted as well.
function frpme_v3_is_ready(){return 'frpmesh-hybrid'===get_template() && class_exists('\\Elementor\\Plugin');}
function frpme_v3_existing_page($source){
 $pages=get_option('frpme_v3_pages',array());
 if(!empty($pages[$source]) && 'page'===get_post_type((int)$pages[$source])){
  $id=(int)$pages[$source];if('publish'===get_post_status($id))return $id;
  if('draft'===get_post_status($id)&&get_post_meta($id,'_frpme_native_v3',true)){wp_update_post(array('ID'=>$id,'post_status'=>'publish'));return $id;}
 }
 $by_slug=get_page_by_path($source,OBJECT,'page');
 if(!$by_slug)$by_slug=get_page_by_path('frp-v3-'.$source,OBJECT,'page');
 if(!$by_slug)return 0;
 if('publish'===$by_slug->post_status)return (int)$by_slug->ID;
 if('draft'===$by_slug->post_status&&get_post_meta($by_slug->ID,'_frpme_native_v3',true)){wp_update_post(array('ID'=>$by_slug->ID,'post_status'=>'publish'));return (int)$by_slug->ID;}
 return 0;
}
function frpme_v3_bootstrap(){
 if(!current_user_can('edit_theme_options')||!frpme_v3_is_ready())return new WP_Error('frpme_setup','Elementor and FRP theme must be active.');
 // Two simultaneous admin requests must not both create the same pages.
 if(!frpme_lock('frpme_v3_bootstrap',300))return new WP_Error('frpme_busy','Setup is already running.');
 $report=frpme_v3_bootstrap_run();
 frpme_unlock('frpme_v3_bootstrap');
 return $report;
}
function frpme_v3_bootstrap_run(){
 $report=array('created'=>array(),'existing'=>array(),'errors'=>array());
 $pages=get_option('frpme_v3_pages',array());if(!is_array($pages))$pages=array();$new_ids=array();
 // Reserve page IDs before importing any design JSON: {{URL}} references must resolve to final permalinks.
 foreach(array('home'=>'خانه','about'=>'درباره ما','services'=>'خدمات','contact'=>'تماس','blog'=>'وبلاگ') as $source=>$title){
  $id=frpme_v3_existing_page($source);
  if($id){$pages[$source]=$id;$report['existing'][$source]=$id;continue;}
  $id=wp_insert_post(array('post_title'=>$title,'post_name'=>$source,'post_type'=>'page','post_status'=>'publish'),true);
  if(is_wp_error($id)){$report['errors'][]=$source.': '.$id->get_error_message();continue;}
  $pages[$source]=$id;$new_ids[$source]=$id;$report['created'][$source]=$id;
 }
 update_option('frpme_v3_pages',$pages,false);
 $library=get_option('frpme_v3_library',array());if(!is_array($library))$library=array();
 foreach(array('home','about','services','contact','blog','article','header','footer') as $source){
  if(!empty($library[$source]) && 'elementor_library'===get_post_type((int)$library[$source]))continue;
  $id=frpme_install_document($source,0,true);
  if(is_wp_error($id)){$report['errors'][]=$source.': '.$id->get_error_message();continue;}
  $library[$source]=$id;
 }
 update_option('frpme_v3_library',$library,false);
 foreach($new_ids as $source=>$id){
  $result=frpme_install_document($source,$id,false,'publish',true);
  if(is_wp_error($result)){$report['errors'][]=$source.': '.$result->get_error_message();}
 }
 $options=frpme_options();
 foreach(array('header','footer','blog','article') as $source){
  $key=$source.'_template_id';
  if(empty($options[$key]) && !empty($library[$source]))$options[$key]=(int)$library[$source];
 }
 update_option('frpme_settings',$options);
 // Preserve an existing static homepage. Set the new homepage on fresh installs only.
 if(!empty($pages['home']) && 'page'!==get_option('show_on_front')){
  update_option('show_on_front','page');update_option('page_on_front',(int)$pages['home']);
 }
 // The blog is an editable Page: assigning page_for_posts would bypass its Elementor content.
 $locations=get_theme_mod('nav_menu_locations',array());
 if(empty($locations['primary']) && function_exists('wp_create_nav_menu')){
  $menu=wp_get_nav_menu_object('FRP Mesh - Primary');
  if(!$menu){$menu=wp_create_nav_menu('FRP Mesh - Primary');}
  $menu_id=is_wp_error($menu)?0:(is_object($menu)?(int)$menu->term_id:(int)$menu);
  if($menu_id&&!is_wp_error($menu_id) && !wp_get_nav_menu_items($menu_id)){
   foreach(array('home'=>'خانه','about'=>'درباره ما','services'=>'خدمات','gallery'=>'نمونه‌کارها','blog'=>'وبلاگ','contact'=>'تماس') as $source=>$title){
    if('gallery'===$source){if(empty($pages['home']))continue;$args=array('menu-item-title'=>$title,'menu-item-type'=>'custom','menu-item-url'=>get_permalink((int)$pages['home']).'#projects','menu-item-status'=>'publish');}
    elseif(!empty($pages[$source])){$args=array('menu-item-title'=>$title,'menu-item-type'=>'post_type','menu-item-object'=>'page','menu-item-object-id'=>(int)$pages[$source],'menu-item-status'=>'publish');}
    else continue;
    wp_update_nav_menu_item($menu_id,0,$args);
   }
  }
  if($menu_id&&!is_wp_error($menu_id)){$locations['primary']=$menu_id;set_theme_mod('nav_menu_locations',$locations);}
 }
 update_option('frpme_v3_bootstrap_report',$report,false);
 return $report;
}
function frpme_v3_automatic_setup(){
 // Importing thirteen large documents must not delay an editor or AJAX request.
 if(wp_doing_ajax()||(isset($_GET['action'])&&'elementor'===sanitize_key(wp_unslash($_GET['action']))))return;
 if(!frpme_v3_is_ready()||get_option('frpme_v3_auto_setup_done'))return;
 if(!current_user_can('edit_theme_options'))return;
 // After a failed attempt wait before retrying instead of re-importing on every admin request.
 if(get_transient('frpme_v3_auto_setup_pause'))return;
 $result=frpme_v3_bootstrap();
 if(!is_wp_error($result)) {
  if(!$result['errors'])update_option('frpme_v3_auto_setup_done',1,false);
  else set_transient('frpme_v3_auto_setup_pause',1,15*MINUTE_IN_SECONDS);
  update_option('frpme_v3_setup_notice',empty($result['errors'])?'ready':'incomplete',false);
 }
}
add_action('admin_init','frpme_v3_automatic_setup',20);
add_action('after_switch_theme',function(){delete_option('frpme_v3_auto_setup_done');});
add_action('admin_notices',function(){
 if(!current_user_can('edit_theme_options'))return;
 $notice=get_option('frpme_v3_setup_notice');if(!$notice)return;
 delete_option('frpme_v3_setup_notice');
 $message='ready'===$notice?'صفحات FRP Mesh آماده‌اند. وضعیت و لینک ویرایش را در راه‌انداز بررسی کنید.':'ساخت برخی صفحات FRP Mesh کامل نشد. جزئیات را در راه‌انداز ببینید.';
 echo '<div class="notice notice-'.('ready'===$notice?'success':'warning').' is-dismissible"><p>'.esc_html($message).' <a href="'.esc_url(admin_url('admin.php?page=frpme-native-setup')).'">باز کردن راه‌انداز</a></p></div>';
});

function frpme_native_setup(){if(!current_user_can('edit_theme_options'))return;$messages=array();$scan_message=frpme_legacy_scan_handle_post();if($scan_message)$messages[]=$scan_message;if(!class_exists('\\Elementor\\Plugin')){frpme_admin_open(__('راه‌اندازی Elementor','frpmesh-editor'),__('برای فعال شدن این بخش ابتدا Elementor را نصب کنید.','frpmesh-editor'),'frpme-native-setup');echo '<section class="frpme-card"><p>'.esc_html__('Elementor فعال نیست؛ بخش پایهٔ افزونه بدون آن کار می‌کند.','frpmesh-editor').'</p></section>';frpme_admin_close();return;}
 if(isset($_POST['frpme_globals_style'])){check_admin_referer('frpme_native_setup');update_option('frpme_use_elementor_globals',!empty($_POST['use_elementor_globals'])?1:0,false);$messages[]=__('اتصال رنگ‌ها و فونت‌های سراسری ذخیره شد.','frpmesh-editor');}
 if(isset($_POST['frpme_create'])){check_admin_referer('frpme_native_setup');$result=frpme_v3_bootstrap();if(is_wp_error($result))$messages[]=$result->get_error_message();else{$messages[]=__('صفحات و الگوهای مفقود ساخته و لینک‌ها بررسی شدند.','frpmesh-editor');foreach($result['errors'] as $error)$messages[]=$error;}}
 if(isset($_POST['frpme_globals'])){check_admin_referer('frpme_native_setup');$options=frpme_options();foreach(get_option('frpme_v3_library',array()) as $s=>$id){if(in_array($s,array('header','footer','blog','article'),true))$options[$s.'_template_id']=(int)$id;}update_option('frpme_settings',$options);$messages[]=__('الگوهای سراسری متصل شدند.','frpmesh-editor');}
 $page=isset($_POST['page_id'])?absint($_POST['page_id']):0;
 if(isset($_POST['frpme_replace'])){
  check_admin_referer('frpme_native_setup');$source=isset($_POST['source'])?sanitize_key(wp_unslash($_POST['source'])):'';
  if('page'===get_post_type($page)&&current_user_can('edit_post',$page)&&in_array($source,array('home','about','services','contact','blog'),true)){$r=frpme_install_document($source,$page);$messages[]=is_wp_error($r)?$r->get_error_message():__('محتوای برگه با حفظ عنوان، نامک و انتشار جایگزین شد.','frpmesh-editor');}
  else $messages[]=__('یک برگه معتبر و نوع طرح را برای ارتقا انتخاب کنید.','frpmesh-editor');
 }
 if(isset($_POST['frpme_restore'])){
  check_admin_referer('frpme_native_setup');$backup=get_post_meta($page,'_frpme_v3_backup',true);
  if('page'===get_post_type($page)&&current_user_can('edit_post',$page)&&is_array($backup)){
   foreach($backup as $key=>$value){if(in_array($key,array('_elementor_data','_elementor_page_settings','_elementor_edit_mode','_wp_page_template','_frpme_source'),true))update_post_meta($page,$key,wp_slash($value));}
   delete_post_meta($page,'_frpme_native_v3');delete_post_meta($page,'_elementor_css');delete_post_meta($page,'_elementor_element_cache');$messages[]=__('آخرین محتوای قبلی بازگردانده شد.','frpmesh-editor');
  }else $messages[]=__('برای برگه انتخابی نسخه پشتیبان قابل بازیابی پیدا نشد.','frpmesh-editor');
 }
 $names=array('home'=>__('خانه','frpmesh-editor'),'about'=>__('درباره ما','frpmesh-editor'),'services'=>__('خدمات','frpmesh-editor'),'contact'=>__('تماس','frpmesh-editor'),'blog'=>__('وبلاگ','frpmesh-editor'));
 frpme_admin_open(__('راه‌اندازی Elementor','frpmesh-editor'),__('صفحه‌ها، الگوهای سراسری و اتصال طرح را از همین‌جا بررسی کنید.','frpmesh-editor'),'frpme-native-setup');
 foreach($messages as $item)echo '<div class="notice notice-info is-dismissible"><p>'.esc_html($item).'</p></div>';
 $pages=get_option('frpme_v3_pages',array());$pages=is_array($pages)?$pages:array();$library=get_option('frpme_v3_library',array());$library=is_array($library)?$library:array();
 $ready=0;foreach($names as $key=>$title)if(!empty($pages[$key])&&'page'===get_post_type(absint($pages[$key])))$ready++;
 echo '<div class="frpme-stats"><div><span>'.esc_html__('برگه‌های موجود','frpmesh-editor').'</span><strong>'.esc_html($ready.'/'.count($names)).'</strong></div><div><span>'.esc_html__('الگوهای قابل ویرایش','frpmesh-editor').'</span><strong>'.esc_html(count($library)).'</strong></div><div><span>'.esc_html__('اتصال طراحی سراسری','frpmesh-editor').'</span><strong>'.esc_html(get_option('frpme_use_elementor_globals')?__('فعال','frpmesh-editor'):__('اختیاری','frpmesh-editor')).'</strong></div></div>';
 echo '<div class="frpme-native-layout"><div><section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span><div><h2>'.esc_html__('وضعیت صفحه‌های اصلی','frpmesh-editor').'</h2><p>'.esc_html__('صفحه‌های موجود دست‌نخورده می‌مانند؛ فقط موارد مفقود ساخته می‌شوند.','frpmesh-editor').'</p></div></div><div class="frpme-page-list">';
 foreach($names as $key=>$title){$id=!empty($pages[$key])&&'page'===get_post_type(absint($pages[$key]))?absint($pages[$key]):0;$native=$id&&(bool)get_post_meta($id,'_frpme_native_v3',true);echo '<div class="frpme-page-row"><div><strong>'.esc_html($title).'</strong><small>'.esc_html($id?($native?__('قابل ویرایش در Elementor','frpmesh-editor'):__('برگه موجود؛ طرح قبلی محفوظ است','frpmesh-editor')):__('ساخته نشده','frpmesh-editor')).'</small></div><div><span class="frpme-badge '.($native?'is-ready':($id?'is-warning':'is-missing')).'">'.esc_html($native?__('آماده','frpmesh-editor'):($id?__('موجود','frpmesh-editor'):__('مفقود','frpmesh-editor'))).'</span>';if($id)echo '<a class="button" href="'.esc_url(admin_url('post.php?post='.$id.'&action=elementor')).'">'.esc_html__('ویرایش','frpmesh-editor').'</a>';echo '</div></div>';}
 echo '</div><form method="post" class="frpme-card-action">';wp_nonce_field('frpme_native_setup');echo '<button class="button button-primary" type="submit" name="frpme_create" value="1">'.esc_html__('ساخت موارد مفقود و بررسی فهرست','frpmesh-editor').'</button></form><p class="frpme-help">'.esc_html__('اگر صفحه اصلی ثابت از پیش انتخاب شده باشد، راه‌انداز آن را تغییر نمی‌دهد. برگهٔ وبلاگ را در «برگه نوشته‌ها» انتخاب نکنید تا داخل Elementor قابل ویرایش بماند.','frpmesh-editor').'</p></section>';
 echo '<section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span><div><h2>'.esc_html__('رنگ و فونت سراسری Elementor','frpmesh-editor').'</h2><p>'.esc_html__('ابتدا رنگ‌ها و فونت‌ها را در Elementor ← Site Settings مطابق طرح اصلی تنظیم کنید.','frpmesh-editor').'</p></div></div><form method="post">';wp_nonce_field('frpme_native_setup');echo '<label class="frpme-toggle"><input type="checkbox" name="use_elementor_globals" value="1" '.checked((bool)get_option('frpme_use_elementor_globals'),true,false).'><span>'.esc_html__('استفاده از Global Colors و Global Fonts در صفحات FRP','frpmesh-editor').'</span></label><div class="frpme-inline-actions"><button type="submit" name="frpme_globals_style" value="1" class="button button-primary">'.esc_html__('ذخیره اتصال رنگ و فونت','frpmesh-editor').'</button><button type="submit" name="frpme_globals" value="1" class="button">'.esc_html__('اتصال دوباره الگوهای سراسری','frpmesh-editor').'</button></div></form><p class="frpme-help">'.esc_html__('تا زمانی که این گزینه روشن نشده، رنگ و فونت اصلی طرح حفظ می‌شود.','frpmesh-editor').'</p></section></div>';
 echo '<aside class="frpme-native-side">';frpme_legacy_scan_panel();echo '<section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-editor-code" aria-hidden="true"></span><div><h2>'.esc_html__('ارتقای برگه موجود','frpmesh-editor').'</h2><p>'.esc_html__('این اقدام محتوای Elementor برگه انتخابی را جایگزین می‌کند.','frpmesh-editor').'</p></div></div><div class="frpme-info-strip">'.esc_html__('پیش از ارتقا از سایت نسخه پشتیبان تهیه کنید. یک نسخه از دادهٔ برگه پیش از جایگزینی ذخیره می‌شود.','frpmesh-editor').'</div><form method="post" class="frpme-upgrade-form">';wp_nonce_field('frpme_native_setup');echo '<label for="frpme-upgrade-page">'.esc_html__('انتخاب برگه','frpmesh-editor').'</label>';wp_dropdown_pages(array('name'=>'page_id','id'=>'frpme-upgrade-page','show_option_none'=>__('انتخاب برگه','frpmesh-editor'),'option_none_value'=>0));echo '<label for="frpme-upgrade-type">'.esc_html__('نوع طرح','frpmesh-editor').'</label><select name="source" id="frpme-upgrade-type">';foreach($names as $key=>$title)echo '<option value="'.esc_attr($key).'">'.esc_html($title).'</option>';echo '</select><button type="submit" name="frpme_replace" value="1" class="button button-primary frpme-confirm-replace">'.esc_html__('ارتقای برگهٔ انتخابی','frpmesh-editor').'</button><button type="submit" name="frpme_restore" value="1" class="button frpme-confirm-restore">'.esc_html__('بازیابی آخرین نسخهٔ قبلی','frpmesh-editor').'</button></form></section>';
 echo '<section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-layout" aria-hidden="true"></span><div><h2>'.esc_html__('الگوهای ذخیره‌شده','frpmesh-editor').'</h2><p>'.esc_html__('هر الگو به‌صورت جداگانه در Elementor باز می‌شود.','frpmesh-editor').'</p></div></div><div class="frpme-template-list">';foreach($library as $key=>$id){if('elementor_library'!==get_post_type(absint($id)))continue;echo '<a href="'.esc_url(admin_url('post.php?post='.absint($id).'&action=elementor')).'">'.esc_html(ucfirst($key)).'<span aria-hidden="true">↖</span></a>';}echo '</div></section></aside></div>';
 frpme_admin_close();}
add_action('admin_menu',function(){add_submenu_page('frpme-settings','Native Elementor setup','راه‌اندازی المنتور نسخه ۳','edit_theme_options','frpme-native-setup','frpme_native_setup');});
function frpme_native_clear_cache(){$ids=get_posts(array('post_type'=>array('page','elementor_library'),'post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','meta_key'=>'_frpme_native_v3','meta_value'=>1,'no_found_rows'=>true));foreach($ids as $id)delete_post_meta($id,'_elementor_element_cache');}
add_action('update_option_frpme_settings','frpme_native_clear_cache');add_action('update_option_theme_mods_frpmesh-hybrid','frpme_native_clear_cache');
