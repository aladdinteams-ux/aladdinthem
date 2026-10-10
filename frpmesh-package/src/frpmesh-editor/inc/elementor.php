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
/** Pick out legacy FRP widget types from one `_elementor_data` document. */
function frpme_legacy_types_in_data($data){
 $types=array();if(!is_string($data)||false===strpos($data,'frpm'))return $types;
 preg_match_all('/"widgetType"\s*:\s*"((?:frpme-|frpm-)[^"]+)"/',$data,$matches);
 foreach($matches[1] as $type){if(!in_array($type,array('frpme-field','frpme-data'),true))$types[$type]=true;}
 return $types;
}
/**
 * Legacy widget discovery.
 *
 * The scan walks wp_postmeta by meta_id windows, so every step is a short, indexed query. The database filters
 * candidate rows with LIKE and only matching documents are loaded, one at a time, so memory stays flat even for
 * very large Elementor documents. Progress lives in the `frpme_legacy_scan_state` option; a lock option stops two
 * requests scanning at once; WP-Cron (or a short slice in admin when cron is disabled) resumes it until complete.
 * Found widget types are merged into `frpme_legacy_types`, never replaced, so earlier results are kept.
 */
function frpme_legacy_scan_defaults(){return array('status'=>'idle','version'=>'','cursor'=>0,'max_id'=>0,'types'=>array(),'matched'=>0,'attempts'=>0,'started'=>0,'updated'=>0,'errors'=>array());}
function frpme_legacy_scan_state(){$state=get_option('frpme_legacy_scan_state',array());return is_array($state)?array_merge(frpme_legacy_scan_defaults(),$state):frpme_legacy_scan_defaults();}
function frpme_legacy_scan_save($state){$state['updated']=time();$state['errors']=array_slice(array_map('strval',(array)$state['errors']),-10);update_option('frpme_legacy_scan_state',$state,false);}
function frpme_legacy_scan_done(){return get_option('frpme_legacy_scan_version')===FRPME_VERSION;}
function frpme_legacy_scan_memory_ok(){
 $limit=wp_convert_hr_to_bytes((string)ini_get('memory_limit'));if($limit<=0)return true;
 return memory_get_usage(true)<$limit*0.7;
}
/**
 * Run one resumable slice. $budget is seconds (0 = unlimited). Returns the saved state, or false when another
 * process holds the lock.
 */
// phpcs:disable WordPress.DB.DirectDatabaseQuery -- the scan reads wp_postmeta in id windows on purpose; results must be uncached and never loaded as whole rows.
function frpme_legacy_scan_run($budget=5){
 global $wpdb;
 if(frpme_legacy_scan_done())return frpme_legacy_scan_state();
 if(!frpme_lock('frpme_legacy_scan',max(120,(int)$budget*3)))return false;
 $state=frpme_legacy_scan_state();
 if('running'!==$state['status']||$state['version']!==FRPME_VERSION){
  $state=frpme_legacy_scan_defaults();$state['status']='running';$state['version']=FRPME_VERSION;$state['started']=time();
 }
 $started=microtime(true);$window=max(100,(int)apply_filters('frpme_legacy_scan_window',2000));
 $like=array('%'.$wpdb->esc_like('"frpme-').'%','%'.$wpdb->esc_like('"frpm-').'%');
 // Rows saved after this point are handled by frpme_scan_saved_legacy(); the maximum only bounds the walk.
 $max=$wpdb->get_var($wpdb->prepare("SELECT MAX(meta_id) FROM {$wpdb->postmeta} WHERE meta_key = %s",'_elementor_data'));
 $failed=false;$worked=false;
 if($wpdb->last_error){$failed=true;$state['errors'][]=gmdate('c').' '.$wpdb->last_error;}
 else $state['max_id']=(int)$max;
 // At least one window per run is always processed, so a tiny budget can never stall the scan.
 while(!$failed&&$state['cursor']<$state['max_id']){
  if($worked&&$budget>0&&(microtime(true)-$started)>=$budget)break;
  if($worked&&!frpme_legacy_scan_memory_ok())break;
  $worked=true;
  $from=(int)$state['cursor'];$to=$from+$window;
  $ids=$wpdb->get_col($wpdb->prepare("SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_id > %d AND meta_id <= %d AND (meta_value LIKE %s OR meta_value LIKE %s) ORDER BY meta_id ASC",'_elementor_data',$from,$to,$like[0],$like[1]));
  if(!is_array($ids)||$wpdb->last_error){$failed=true;$state['errors'][]=gmdate('c').' '.($wpdb->last_error?:'query failed');break;}
  $interrupted=false;$processed=false;
  foreach($ids as $meta_id){
   if($processed&&($budget>0&&(microtime(true)-$started)>=$budget||!frpme_legacy_scan_memory_ok())){$interrupted=true;break;}$processed=true;
   $value=$wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_id = %d",(int)$meta_id));
   $wpdb->flush();
   foreach(frpme_legacy_types_in_data($value) as $type=>$unused)$state['types'][$type]=true;
   $state['matched']++;$state['cursor']=(int)$meta_id;unset($value);
  }
  if($interrupted)break;
  $state['cursor']=$to;
  frpme_legacy_scan_save($state);
 }
 if($failed){
  $state['attempts']++;$state['status']=$state['attempts']>=5?'failed':'running';
 }elseif($state['cursor']>=$state['max_id']){
  $types=get_option('frpme_legacy_types',array());if(!is_array($types))$types=array();
  update_option('frpme_legacy_types',array_values(array_unique(array_merge($types,array_keys($state['types'])))),false);
  update_option('frpme_legacy_scan_version',FRPME_VERSION,true);
  $state['status']='complete';
 }
 frpme_legacy_scan_save($state);
 frpme_unlock('frpme_legacy_scan');
 return $state;
}
// phpcs:enable WordPress.DB.DirectDatabaseQuery
/** Synchronous full scan, kept for existing callers and WP-CLI. Returns true when finished. */
function frpme_scan_legacy(){
 if(frpme_legacy_scan_done())return true;
 $guard=0;
 do{$state=frpme_legacy_scan_run(0);$guard++;}while(is_array($state)&&'running'===$state['status']&&$guard<1000);
 return is_array($state)&&'complete'===$state['status'];
}
function frpme_legacy_scan_schedule($delay=30){
 if(frpme_legacy_scan_done()||wp_next_scheduled('frpme_legacy_scan_event'))return;
 wp_schedule_single_event(time()+max(5,(int)$delay),'frpme_legacy_scan_event');
}
function frpme_legacy_scan_event(){
 $state=frpme_legacy_scan_run(15);
 // A busy lock or unfinished slice both mean "try again later"; a failed scan waits for an administrator.
 if(false===$state||(is_array($state)&&'running'===$state['status']))frpme_legacy_scan_schedule(60);
}
add_action('frpme_legacy_scan_event','frpme_legacy_scan_event');
/**
 * Cheap on every admin request: one autoloaded option read. Heavy work happens in WP-Cron, never in the Elementor
 * editor or AJAX. Sites that disable WP-Cron get one short slice per admin page load instead.
 */
function frpme_scan_legacy_on_upgrade(){
 if(frpme_legacy_scan_done()||!current_user_can('edit_theme_options'))return;
 if(wp_doing_ajax()||isset($_GET['action'])&&'elementor'===sanitize_key(wp_unslash($_GET['action'])))return;
 $state=frpme_legacy_scan_state();if('failed'===$state['status'])return;
 if(defined('DISABLE_WP_CRON')&&DISABLE_WP_CRON){frpme_legacy_scan_run(2);return;}
 frpme_legacy_scan_schedule(10);
}
add_action('admin_init','frpme_scan_legacy_on_upgrade');
register_deactivation_hook(FRPME_DIR.'frpmesh-editor.php',function(){wp_clear_scheduled_hook('frpme_legacy_scan_event');frpme_unlock('frpme_legacy_scan');});
/** Status card and manual controls on the Elementor setup screen. */
function frpme_legacy_scan_panel(){
 $state=frpme_legacy_scan_state();$done=frpme_legacy_scan_done();
 $labels=array('idle'=>__('در انتظار','frpmesh-editor'),'running'=>__('در حال اجرا','frpmesh-editor'),'complete'=>__('کامل شد','frpmesh-editor'),'failed'=>__('ناموفق','frpmesh-editor'));
 $status=$done?'complete':$state['status'];$percent=$done?100:($state['max_id']>0?min(99,(int)floor($state['cursor']*100/$state['max_id'])):0);
 echo '<section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-database" aria-hidden="true"></span><div><h2>'.esc_html__('بررسی ویجت‌های نسخه قبل','frpmesh-editor').'</h2><p>'.esc_html__('این بررسی مرحله‌ای در پس‌زمینه اجرا می‌شود و هیچ داده‌ای را تغییر نمی‌دهد.','frpmesh-editor').'</p></div></div>';
 echo '<p><strong>'.esc_html(isset($labels[$status])?$labels[$status]:$status).'</strong> · '.esc_html(sprintf(__('پیشرفت: %1$d٪ — سندهای دارای ویجت قدیمی: %2$d','frpmesh-editor'),$percent,(int)$state['matched'])).'</p>';
 foreach($state['errors'] as $error)echo '<p class="frpme-help">'.esc_html($error).'</p>';
 if(!$done){echo '<form method="post">';wp_nonce_field('frpme_legacy_scan');echo '<button class="button" type="submit" name="frpme_legacy_scan_now" value="1">'.esc_html__('اجرای مرحله بعد','frpmesh-editor').'</button> <button class="button" type="submit" name="frpme_legacy_scan_restart" value="1">'.esc_html__('شروع دوباره بررسی','frpmesh-editor').'</button></form>';}
 echo '</section>';
}
function frpme_legacy_scan_handle_post(){
 if(!current_user_can('edit_theme_options'))return '';
 if(isset($_POST['frpme_legacy_scan_restart'])){check_admin_referer('frpme_legacy_scan');delete_option('frpme_legacy_scan_state');delete_option('frpme_legacy_scan_version');frpme_unlock('frpme_legacy_scan');}
 if(isset($_POST['frpme_legacy_scan_now'])||isset($_POST['frpme_legacy_scan_restart'])){check_admin_referer('frpme_legacy_scan');$state=frpme_legacy_scan_run(10);return false===$state?__('بررسی دیگری در حال اجراست؛ کمی بعد دوباره تلاش کنید.','frpmesh-editor'):__('مرحله بعدی بررسی اجرا شد.','frpmesh-editor');}
 return '';
}
function frpme_scan_saved_legacy($post_id){
 $data=get_post_meta((int)$post_id,'_elementor_data',true);
 $new=frpme_legacy_types_in_data($data);if(!$new)return;
 $old=get_option('frpme_legacy_types',array());if(!is_array($old))$old=array();
 $types=array_values(array_unique(array_merge($old,array_keys($new))));
 if($types!==$old)update_option('frpme_legacy_types',$types,false);
}
add_action('elementor/editor/after_save','frpme_scan_saved_legacy',10,1);

/** Generated Elementor CSS from an older plugin version still contains the widget-level defaults; rebuild it once per version. */
add_action('admin_init',function(){
 if(get_option('frpme_css_version')===FRPME_VERSION||!current_user_can('edit_theme_options')||!class_exists('\\Elementor\\Plugin')||!isset(\Elementor\Plugin::$instance->files_manager))return;
 \Elementor\Plugin::$instance->files_manager->clear_cache();
 update_option('frpme_css_version',FRPME_VERSION,true);
});
