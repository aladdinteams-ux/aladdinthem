<?php
/** FRP Mesh companion administration. Presentation is isolated from the frontend. */
defined('ABSPATH') || exit;
function frpme_phone_value($value){return preg_replace('/[^0-9+]/','',(string)$value);}
function frpme_sanitize_settings($input){
 if(!is_array($input))return array();$out=frpme_options();
 foreach(array('phone_primary','phone_office','phone_secondary') as $key)if(isset($input[$key]))$out[$key]=frpme_phone_value($input[$key]);
 if(isset($input['email']))$out['email']=sanitize_email($input['email']);
 if(isset($input['address']))$out['address']=sanitize_textarea_field($input['address']);
 foreach(array('logo_id','header_template_id','footer_template_id','article_template_id','blog_template_id') as $key)if(isset($input[$key]))$out[$key]=absint($input[$key]);
 foreach(array('video_url','font_url') as $key)if(isset($input[$key]))$out[$key]=esc_url_raw($input[$key],array('http','https'));
 if(isset($input['primary_color']))$out['primary_color']=sanitize_hex_color($input['primary_color']);
 return $out;
}
function frpme_admin_init(){register_setting('frpme_settings_group','frpme_settings',array('sanitize_callback'=>'frpme_sanitize_settings','default'=>array()));}
add_action('admin_init','frpme_admin_init');
add_filter('option_page_capability_frpme_settings_group',function(){return 'edit_theme_options';});
function frpme_admin_menu(){
 add_menu_page(__('FRP Design Editor','frpmesh-editor'),__('طراحی FRP','frpmesh-editor'),'edit_theme_options','frpme-settings','frpme_settings_screen','dashicons-layout',61);
 add_submenu_page('frpme-settings',__('تنظیمات طراحی','frpmesh-editor'),__('تنظیمات طراحی','frpmesh-editor'),'edit_theme_options','frpme-settings','frpme_settings_screen');
 add_submenu_page('frpme-settings',__('ویرایش بخش‌ها','frpmesh-editor'),__('ویرایش بخش‌ها','frpmesh-editor'),'edit_theme_options','frpme-content','frpme_content_screen');
 add_submenu_page('frpme-settings',__('راه‌اندازی پایه','frpmesh-editor'),__('راه‌اندازی پایه','frpmesh-editor'),'edit_theme_options','frpme-setup','frpme_setup_screen');
}
add_action('admin_menu','frpme_admin_menu');
function frpme_admin_assets($hook){
 if(false===strpos($hook,'frpme-'))return;
 wp_enqueue_media();wp_enqueue_style('frpme-admin',FRPME_URL.'assets/css/admin.css',array(),FRPME_VERSION);
 wp_enqueue_script('frpme-admin',FRPME_URL.'assets/js/admin.js',array('media-views'),FRPME_VERSION,true);
 wp_localize_script('frpme-admin','frpmeAdminText',array('media'=>__('انتخاب تصویر','frpmesh-editor'),'unsaved'=>__('تغییرات ذخیره نشده‌اند.','frpmesh-editor'),'reset'=>__('محتوای این بخش به مقدار اصلی برگردد؟','frpmesh-editor'),'replace'=>__('محتوای برگه انتخابی با طرح FRP جایگزین شود؟','frpmesh-editor'),'noResults'=>__('موردی پیدا نشد.','frpmesh-editor'),'cancel'=>__('انصراف','frpmesh-editor'),'accept'=>__('تأیید','frpmesh-editor'),'replaceTitle'=>__('ارتقای برگه؟','frpmesh-editor'),'resetTitle'=>__('بازگشت به محتوای اصلی؟','frpmesh-editor'),'restoreTitle'=>__('بازیابی برگه؟','frpmesh-editor'),'restore'=>__('آخرین نسخهٔ قبلی برگه جایگزین شود؟','frpmesh-editor'),'count'=>__('مورد','frpmesh-editor')));
}
add_action('admin_enqueue_scripts','frpme_admin_assets');
/** Common navigation; all companion screens use the same accessible shell. */
function frpme_admin_open($title,$subtitle,$active){
 if(!current_user_can('edit_theme_options'))return;
 $links=array('frpme-settings'=>array(__('تنظیمات طراحی','frpmesh-editor'),'dashicons-admin-settings'),'frpme-content'=>array(__('ویرایش بخش‌ها','frpmesh-editor'),'dashicons-edit-page'),'frpme-native-setup'=>array(__('راه‌اندازی Elementor','frpmesh-editor'),'dashicons-layout'),'frpme-setup'=>array(__('راه‌اندازی پایه','frpmesh-editor'),'dashicons-admin-page'));
 echo '<div class="wrap frpme-admin" dir="rtl"><div class="frpme-app"><header class="frpme-top"><div class="frpme-brand"><span class="frpme-brand-mark" aria-hidden="true">F</span><span><strong>'.esc_html__('FRP Design Editor','frpmesh-editor').'</strong><small>'.esc_html__('مرکز مدیریت طراحی و محتوا','frpmesh-editor').' · v'.esc_html(FRPME_VERSION).'</small></span></div><div class="frpme-top-links"><span class="frpme-status '.(defined('ELEMENTOR_VERSION')?'is-on':'is-off').'">'.esc_html(defined('ELEMENTOR_VERSION')?__('Elementor فعال','frpmesh-editor'):__('Elementor غیرفعال','frpmesh-editor')).'</span><a class="button" href="'.esc_url(home_url('/')).'" target="_blank" rel="noopener noreferrer">'.esc_html__('مشاهده سایت','frpmesh-editor').'</a></div></header><div class="frpme-shell"><nav class="frpme-nav" aria-label="'.esc_attr__('بخش‌های افزونه','frpmesh-editor').'"><span class="frpme-nav-label">'.esc_html__('مدیریت FRP','frpmesh-editor').'</span>';
 foreach($links as $slug=>$item){$url=admin_url('admin.php?page='.$slug);echo '<a class="frpme-nav-item'.($active===$slug?' is-active':'').'" href="'.esc_url($url).'"'.($active===$slug?' aria-current="page"':'').'><span class="dashicons '.esc_attr($item[1]).'" aria-hidden="true"></span>'.esc_html($item[0]).'</a>';}
 if('frpmesh-hybrid'===get_template())echo '<div class="frpme-nav-separator"><span class="frpme-nav-label">'.esc_html__('پوسته','frpmesh-editor').'</span><a class="frpme-nav-item" href="'.esc_url(admin_url('admin.php?page=frpmt-settings')).'"><span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span>'.esc_html__('تنظیمات قالب','frpmesh-editor').'</a></div>';
 echo '</nav><main class="frpme-workspace"><div class="frpme-page-head"><div><span class="frpme-eyebrow">'.esc_html__('طراحی FRP Mesh','frpmesh-editor').'</span><h1>'.esc_html($title).'</h1><p>'.esc_html($subtitle).'</p></div></div>';
}
function frpme_admin_close(){echo '</main></div></div></div>';}
function frpme_admin_field($key,$label,$value,$type='text',$description=''){
 $id='frpme-'.$key;echo '<div class="frpme-field"><label for="'.esc_attr($id).'">'.esc_html($label).'</label><div class="frpme-field-body">';
 if('textarea'===$type)echo '<textarea rows="3" id="'.esc_attr($id).'" name="frpme_settings['.esc_attr($key).']">'.esc_textarea($value).'</textarea>';
 elseif('color'===$type)echo '<div class="frpme-color-control"><input type="color" class="frpme-color" data-target="'.esc_attr($id).'" aria-label="'.esc_attr($label).'" value="'.esc_attr(sanitize_hex_color($value)?:'#2563eb').'"><input type="text" id="'.esc_attr($id).'" name="frpme_settings['.esc_attr($key).']" value="'.esc_attr($value).'" dir="ltr" pattern="#[0-9a-fA-F]{6}" placeholder="#2563eb"></div>';
 else echo '<input type="'.esc_attr($type).'" id="'.esc_attr($id).'" name="frpme_settings['.esc_attr($key).']" value="'.esc_attr($value).'"'.(in_array($type,array('tel','email','url'),true)?' dir="ltr"':'').'>';
 if($description)echo '<small>'.esc_html($description).'</small>';echo '</div></div>';
}
function frpme_settings_screen(){
 if(!current_user_can('edit_theme_options'))return;
 frpme_admin_open(__('تنظیمات طراحی','frpmesh-editor'),__('شماره‌ها، رسانه‌ها و الگوهای طرح اصلی را مدیریت کنید.','frpmesh-editor'),'frpme-settings');
 if(isset($_GET['settings-updated'])&&'true'===sanitize_text_field(wp_unslash($_GET['settings-updated'])))echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__('تنظیمات ذخیره شد.','frpmesh-editor').'</p></div>';
 echo '<div class="frpme-info-strip"><span class="dashicons dashicons-info" aria-hidden="true"></span>'.esc_html__('تنظیمات متن و تصاویر تک‌تک بخش‌ها در «ویرایش بخش‌ها» قرار دارد. مقدارهای خالی، طراحی اولیه را نگه می‌دارند.','frpmesh-editor').'</div>';
 echo '<form id="frpme-settings-form" class="frpme-track-changes" action="'.esc_url(admin_url('options.php')).'" method="post">';settings_fields('frpme_settings_group');
 echo '<section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-phone" aria-hidden="true"></span><div><h2>'.esc_html__('اطلاعات تماس','frpmesh-editor').'</h2><p>'.esc_html__('در هدر، فوتر و بخش‌های تماس استفاده می‌شود.','frpmesh-editor').'</p></div></div><div class="frpme-fields-grid">';
 foreach(array('phone_primary'=>array(__('تلفن اصلی','frpmesh-editor'),'+989126124876'),'phone_office'=>array(__('تلفن دفتر','frpmesh-editor'),'+982133481307'),'phone_secondary'=>array(__('تلفن دوم','frpmesh-editor'),'+989113894473')) as $key=>$data)frpme_admin_field($key,$data[0],frpme_setting($key,$data[1]),'tel');
 frpme_admin_field('email',__('ایمیل','frpmesh-editor'),frpme_setting('email','asgharjalali1357@gmail.com'),'email');frpme_admin_field('address',__('نشانی','frpmesh-editor'),frpme_setting('address'),'textarea');echo '</div></section>';
 echo '<section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-format-image" aria-hidden="true"></span><div><h2>'.esc_html__('رسانه و هویت بصری','frpmesh-editor').'</h2><p>'.esc_html__('تصویر و ویدئوی اصلی را بدون دست‌کاری فایل‌های قالب تغییر دهید.','frpmesh-editor').'</p></div></div><div class="frpme-fields-grid">';
 $saved_settings=frpme_options();$logo=isset($saved_settings['logo_id'])?absint($saved_settings['logo_id']):absint(get_theme_mod('custom_logo'));echo '<div class="frpme-field"><label for="frpme-logo">'.esc_html__('لوگوی طرح اصلی','frpmesh-editor').'</label><div class="frpme-field-body"><div class="frpme-media-wrap"><span class="frpme-media-preview">';if($logo)echo wp_get_attachment_image($logo,'thumbnail',false,array('alt'=>''));echo '</span><div><input id="frpme-logo" type="number" min="0" name="frpme_settings[logo_id]" value="'.esc_attr($logo).'" readonly><button type="button" class="button frpme-media" data-return="id">'.esc_html__('انتخاب لوگو','frpmesh-editor').'</button><button type="button" class="button frpme-media-clear"'.(!$logo?' hidden':'').'>'.esc_html__('حذف','frpmesh-editor').'</button></div></div><small>'.esc_html__('اگر در تنظیمات پوسته لوگوی جداگانه تعیین شده باشد، همان اولویت دارد.','frpmesh-editor').'</small></div></div>';
 frpme_admin_field('video_url',__('آدرس ویدئوی پروژه','frpmesh-editor'),frpme_setting('video_url'),'url');frpme_admin_field('font_url',__('نشانی فونت محلی WOFF/WOFF2','frpmesh-editor'),frpme_setting('font_url'),'url');frpme_admin_field('primary_color',__('رنگ اصلی طرح','frpmesh-editor'),isset($saved_settings['primary_color'])?$saved_settings['primary_color']:'','color',__('اگر در تنظیمات پوسته رنگ اصلی وارد شود، مقدار پوسته اولویت دارد.','frpmesh-editor'));echo '</div></section>';
 echo '<section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-layout" aria-hidden="true"></span><div><h2>'.esc_html__('الگوهای سراسری Elementor','frpmesh-editor').'</h2><p>'.esc_html__('هر الگو را جداگانه انتخاب کنید؛ گزینهٔ پیش‌فرض همان HTML اصلی را نشان می‌دهد.','frpmesh-editor').'</p></div></div><div class="frpme-fields-grid">';
 $templates=defined('ELEMENTOR_VERSION')?get_posts(array('post_type'=>'elementor_library','post_status'=>'publish','posts_per_page'=>200,'orderby'=>'title','order'=>'ASC')):array();
 foreach(array('header'=>__('هدر','frpmesh-editor'),'footer'=>__('فوتر','frpmesh-editor'),'article'=>__('همه نوشته‌ها','frpmesh-editor'),'blog'=>__('آرشیو وبلاگ','frpmesh-editor')) as $key=>$label){echo '<div class="frpme-field"><label for="frpme-template-'.esc_attr($key).'">'.esc_html($label).'</label><div class="frpme-field-body"><select id="frpme-template-'.esc_attr($key).'" name="frpme_settings['.esc_attr($key).'_template_id]"><option value="0">'.esc_html__('طرح اصلی FRP','frpmesh-editor').'</option>';foreach($templates as $template)echo '<option value="'.esc_attr($template->ID).'"'.selected(absint(frpme_setting($key.'_template_id')),$template->ID,false).'>'.esc_html($template->post_title).'</option>';echo '</select>';if(!defined('ELEMENTOR_VERSION'))echo '<small>'.esc_html__('با نصب Elementor الگوهای ذخیره‌شده در این فهرست دیده می‌شوند.','frpmesh-editor').'</small>';echo '</div></div>';}
 echo '</div></section><div class="frpme-save-bar"><span class="frpme-dirty" role="status" hidden>'.esc_html__('● تغییرات ذخیره‌نشده','frpmesh-editor').'</span><button type="submit" class="button button-primary frpme-save">'.esc_html__('ذخیره تنظیمات','frpmesh-editor').'</button></div></form>';
 frpme_admin_close();
}
function frpme_content_screen(){
 if(!current_user_can('edit_theme_options'))return;$components=frpme_data('components');
 $selected=isset($_GET['component'])?sanitize_key(wp_unslash($_GET['component'])):'header';if(!isset($components[$selected]))$selected='header';$message='';
 if(isset($_POST['frpme_save_component'])||isset($_POST['frpme_reset_component'])){
  check_admin_referer('frpme_component_'.$selected);
  $stored=get_option('frpme_content',array());if(!is_array($stored))$stored=array();
  if(isset($_POST['frpme_save_component'])){$input=isset($_POST['frpme_fields'])&&is_array($_POST['frpme_fields'])?wp_unslash($_POST['frpme_fields']):array();$new=array();foreach($components[$selected]['fields'] as $field){if(!array_key_exists($field['key'],$input)||!is_scalar($input[$field['key']]))continue;$value=$input[$field['key']];if(in_array($field['kind'],array('url','image'),true))$new[$field['key']]=esc_url_raw($value,array('http','https','mailto','tel'));elseif('icon'===$field['kind']){if(in_array(ltrim($value,'#'),frpme_data('icons'),true))$new[$field['key']]=$value;}else $new[$field['key']]=sanitize_textarea_field($value);} $stored[$selected]=$new;$message=__('بخش ذخیره شد.','frpmesh-editor');}
  else{unset($stored[$selected]);$message=__('این بخش به محتوای اصلی بازگشت.','frpmesh-editor');}
  update_option('frpme_content',$stored,false);
 }
 $stored=get_option('frpme_content',array());$values=isset($stored[$selected])&&is_array($stored[$selected])?$stored[$selected]:array();$component=$components[$selected];
 frpme_admin_open(__('ویرایش بخش‌های طراحی','frpmesh-editor'),__('متن، تصویر، پیوند و آیکون‌های بخش انتخابی را مدیریت کنید.','frpmesh-editor'),'frpme-content');
 if($message)echo '<div class="notice notice-success is-dismissible"><p>'.esc_html($message).'</p></div>';
 echo '<div class="frpme-info-strip"><span class="dashicons dashicons-info" aria-hidden="true"></span>'.esc_html__('ویرایشِ انجام‌شده داخل ویجت Elementor همان صفحه بر مقدار پیش‌فرض اینجا اولویت دارد.','frpmesh-editor').'</div>';
 echo '<div class="frpme-content-layout"><aside class="frpme-content-picker"><label for="frpme-component-filter">'.esc_html__('جستجوی بخش','frpmesh-editor').'</label><input id="frpme-component-filter" type="search" placeholder="'.esc_attr__('نام بخش یا صفحه…','frpmesh-editor').'" autocomplete="off"><label for="frpme-component-select">'.esc_html__('بخش قابل ویرایش','frpmesh-editor').'</label><form method="get"><input type="hidden" name="page" value="frpme-content"><select id="frpme-component-select" name="component" size="12">';foreach($components as $key=>$entry)echo '<option value="'.esc_attr($key).'"'.selected($selected,$key,false).'>'.esc_html(strtoupper($entry['source']).' / '.$entry['title']).'</option>';echo '</select><button class="button" type="submit">'.esc_html__('باز کردن بخش','frpmesh-editor').'</button></form><span class="frpme-count">'.esc_html(sprintf(__('مجموع بخش‌ها: %d','frpmesh-editor'),count($components))).'</span></aside>';
 echo '<div class="frpme-content-editor"><div class="frpme-card frpme-section-heading"><span class="frpme-eyebrow">'.esc_html(strtoupper($component['source'])).'</span><h2>'.esc_html($component['title']).'</h2><p>'.esc_html(sprintf(__('تعداد فیلدهای قابل ویرایش: %d','frpmesh-editor'),count($component['fields']))).'</p></div><form class="frpme-track-changes" method="post">';wp_nonce_field('frpme_component_'.$selected);
 echo '<div class="frpme-field-search"><label for="frpme-field-filter" class="screen-reader-text">'.esc_html__('جستجوی فیلد','frpmesh-editor').'</label><input id="frpme-field-filter" type="search" placeholder="'.esc_attr__('جستجو در فیلدهای این بخش…','frpmesh-editor').'" autocomplete="off"><span id="frpme-field-count" role="status"></span></div><div class="frpme-editor-grid">';
 foreach($component['fields'] as $field){$value=isset($values[$field['key']])?$values[$field['key']]:$field['default'];if(in_array($field['kind'],array('url','image'),true))$value=frpme_resolve($value);$kind=$field['kind'];$id='frpme-field-'.sanitize_html_class($field['key']);$input='frpme_fields['.$field['key'].']';echo '<div class="frpme-field frpme-edit-field" data-search="'.esc_attr($field['label'].' '.$field['key']).'"><label for="'.esc_attr($id).'">'.esc_html($field['label']).'<small>'.esc_html(strtoupper($kind)).'</small></label><div class="frpme-field-body">';
  if('icon'===$kind){echo '<select id="'.esc_attr($id).'" name="'.esc_attr($input).'">';foreach(frpme_data('icons') as $icon)echo '<option value="#'.esc_attr($icon).'"'.selected($value,'#'.$icon,false).'>'.esc_html($icon).'</option>';echo '</select>';}
  elseif(in_array($kind,array('image','url','attribute'),true)){echo '<input id="'.esc_attr($id).'" type="'.('url'===$kind?'url':'text').'" name="'.esc_attr($input).'" value="'.esc_attr($value).'"'.('image'===$kind||'url'===$kind?' dir="ltr"':'').'>';if('image'===$kind)echo '<button type="button" class="button frpme-media" data-return="url">'.esc_html__('انتخاب تصویر','frpmesh-editor').'</button>';}
  else echo '<textarea id="'.esc_attr($id).'" rows="3" name="'.esc_attr($input).'">'.esc_textarea($value).'</textarea>';
  echo '</div></div>';
 }
 echo '</div><div class="frpme-save-bar"><span class="frpme-dirty" role="status" hidden>'.esc_html__('● تغییرات ذخیره‌نشده','frpmesh-editor').'</span><button class="button button-primary frpme-save" type="submit" name="frpme_save_component" value="1">'.esc_html__('ذخیره این بخش','frpmesh-editor').'</button><button class="button frpme-confirm-reset" type="submit" name="frpme_reset_component" value="1">'.esc_html__('بازگشت به متن اصلی','frpmesh-editor').'</button></div></form></div></div>';
 frpme_admin_close();
}
function frpme_setup_screen(){
 if(!current_user_can('edit_theme_options'))return;$messages=array();
 if(isset($_POST['frpme_create_pages'])){check_admin_referer('frpme_setup');foreach(array('home'=>__('صفحه اصلی','frpmesh-editor'),'about'=>__('درباره ما','frpmesh-editor'),'services'=>__('خدمات','frpmesh-editor'),'contact'=>__('تماس با ما','frpmesh-editor'),'blog'=>__('وبلاگ','frpmesh-editor')) as $slug=>$title){$existing=get_page_by_path($slug);if($existing){$messages[]=$slug.': '.__('برگه موجود تغییر نکرد.','frpmesh-editor');continue;}$id=wp_insert_post(array('post_type'=>'page','post_status'=>'draft','post_name'=>$slug,'post_title'=>$title),true);if(is_wp_error($id)){$messages[]=$id->get_error_message();continue;}update_post_meta($id,'_wp_page_template','templates/'.$slug.'.php');$messages[]=$slug.': '.__('پیش‌نویس ساخته شد.','frpmesh-editor');}}
 frpme_admin_open(__('راه‌اندازی پایه','frpmesh-editor'),__('ساخت پیش‌نویس برگه‌های مفقود بدون تغییر محتوای برگه‌های موجود.','frpmesh-editor'),'frpme-setup');
 foreach($messages as $item)echo '<div class="notice notice-info"><p>'.esc_html($item).'</p></div>';
 echo '<div class="frpme-info-strip"><span class="dashicons dashicons-info" aria-hidden="true"></span>'.esc_html__('اگر Elementor فعال است، برای ساخت خودکار برگه‌ها و الگوهای قابل ویرایش از «راه‌اندازی Elementor» استفاده کنید.','frpmesh-editor').'</div><section class="frpme-card"><div class="frpme-card-head"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span><div><h2>'.esc_html__('پیش‌نویس برگه‌های پایه','frpmesh-editor').'</h2><p>'.esc_html__('این عملیات فقط برگه‌های فاقد نامک را ایجاد می‌کند و چیزی را منتشر یا بازنویسی نمی‌کند.','frpmesh-editor').'</p></div></div><form method="post">';wp_nonce_field('frpme_setup');echo '<button type="submit" class="button button-primary" name="frpme_create_pages" value="1">'.esc_html__('ساخت پیش‌نویس‌های مفقود','frpmesh-editor').'</button></form></section>';
 frpme_admin_close();
}
