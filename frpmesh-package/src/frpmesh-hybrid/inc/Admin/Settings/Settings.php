<?php
/** Native theme settings. Values remain optional so the original design stays unchanged. */
defined('ABSPATH') || exit;
function frpmt_sections() {
 return array('dashboard'=>__('پیشخوان','frpmesh-hybrid'),'identity'=>__('هویت سایت','frpmesh-hybrid'),'colors'=>__('رنگ‌ها','frpmesh-hybrid'),'type'=>__('تایپوگرافی','frpmesh-hybrid'),'layout'=>__('چیدمان','frpmesh-hybrid'),'pages'=>__('برگه‌ها','frpmesh-hybrid'),'header'=>__('هدر','frpmesh-hybrid'),'footer'=>__('فوتر','frpmesh-hybrid'),'blog'=>__('وبلاگ','frpmesh-hybrid'),'commerce'=>__('فروشگاه','frpmesh-hybrid'),'seo'=>__('سئو','frpmesh-hybrid'),'performance'=>__('عملکرد','frpmesh-hybrid'),'advanced'=>__('پیشرفته','frpmesh-hybrid'));
}
/** Every editable key has a declared type, section, and safe fallback. */
function frpmt_schema() {
 $f=array();$add=function($section,$keys,$type,$defaults=array())use(&$f){foreach($keys as $key=>$label){$f[$key]=array('section'=>$section,'label'=>$label,'type'=>$type,'default'=>isset($defaults[$key])?$defaults[$key]:('toggle'===$type?0:''));}};
 $add('identity',array('logo_id'=>__('لوگو','frpmesh-hybrid'),'retina_logo_id'=>__('لوگوی رتینا','frpmesh-hybrid'),'mobile_logo_id'=>__('لوگوی موبایل','frpmesh-hybrid'),'favicon_id'=>__('فَووآیکون','frpmesh-hybrid')),'media');
 $add('identity',array('site_width'=>__('عرض سایت (پیکسل)','frpmesh-hybrid'),'header_logo_width'=>__('عرض لوگوی هدر (پیکسل)','frpmesh-hybrid')),'number');
 $add('identity',array('site_description'=>__('توضیح سایت','frpmesh-hybrid')),'text');
 $add('colors',array('primary_color'=>__('اصلی','frpmesh-hybrid'),'secondary_color'=>__('ثانویه','frpmesh-hybrid'),'text_color'=>__('متن','frpmesh-hybrid'),'heading_color'=>__('عنوان','frpmesh-hybrid'),'background_color'=>__('پس‌زمینه','frpmesh-hybrid'),'surface_color'=>__('سطح','frpmesh-hybrid'),'border_color'=>__('حاشیه','frpmesh-hybrid'),'success_color'=>__('موفقیت','frpmesh-hybrid'),'warning_color'=>__('هشدار','frpmesh-hybrid'),'error_color'=>__('خطا','frpmesh-hybrid')),'color');
 $add('type',array('primary_font'=>__('فونت اصلی','frpmesh-hybrid'),'heading_font'=>__('فونت عنوان','frpmesh-hybrid'),'body_font'=>__('فونت متن','frpmesh-hybrid')),'font');
 foreach(array('desktop'=>__('رومیزی','frpmesh-hybrid'),'tablet'=>__('تبلت','frpmesh-hybrid'),'mobile'=>__('موبایل','frpmesh-hybrid')) as $device=>$label){foreach(array('base','h1','h2','h3','h4','h5','h6') as $heading){$f[$heading.'_size_'.$device]=array('section'=>'type','label'=>sprintf(__('%1$s · %2$s (پیکسل)','frpmesh-hybrid'),strtoupper($heading),$label),'type'=>'number','default'=>'','device'=>$device);}}
 $add('type',array('line_height'=>__('ارتفاع خط','frpmesh-hybrid'),'letter_spacing'=>__('فاصله حروف (پیکسل)','frpmesh-hybrid')),'decimal');
 $add('layout',array('container_width'=>__('عرض کانتینر (پیکسل)','frpmesh-hybrid'),'content_width'=>__('عرض محتوا (پیکسل)','frpmesh-hybrid'),'sidebar_width'=>__('عرض ستون کناری (پیکسل)','frpmesh-hybrid'),'border_radius'=>__('گردی گوشه (پیکسل)','frpmesh-hybrid'),'section_spacing'=>__('فاصله بخش‌ها (پیکسل)','frpmesh-hybrid')),'number');
 $add('layout',array('sidebar_position'=>__('جای ستون کناری','frpmesh-hybrid')),'select');$add('layout',array('page_layout'=>__('چیدمان برگه','frpmesh-hybrid'),'archive_layout'=>__('چیدمان آرشیو','frpmesh-hybrid')),'select');
 $add('header',array('header_layout'=>__('چیدمان هدر','frpmesh-hybrid')),'select');
 $add('header',array('sticky_header'=>__('هدر چسبان','frpmesh-hybrid'),'transparent_header'=>__('هدر شفاف','frpmesh-hybrid'),'mobile_header'=>__('هدر موبایل','frpmesh-hybrid'),'mobile_menu'=>__('منوی موبایل','frpmesh-hybrid'),'header_search'=>__('جستجو','frpmesh-hybrid'),'header_social'=>__('شبکه‌های اجتماعی','frpmesh-hybrid'),'header_cta'=>__('دکمه اقدام','frpmesh-hybrid'),'header_phone'=>__('تلفن','frpmesh-hybrid'),'header_login'=>__('ورود','frpmesh-hybrid'),'header_cart'=>__('سبد خرید','frpmesh-hybrid')),'toggle');
 $add('header',array('sticky_background'=>__('رنگ هدر چسبان','frpmesh-hybrid')),'color');$add('header',array('sticky_shadow'=>__('سایه هدر چسبان','frpmesh-hybrid')),'toggle');$add('header',array('sticky_height'=>__('ارتفاع هدر چسبان','frpmesh-hybrid')),'number');$add('header',array('header_cta_text'=>__('متن دکمه اقدام','frpmesh-hybrid'),'header_phone_text'=>__('شماره تلفن','frpmesh-hybrid')),'text');$add('header',array('header_cta_url'=>__('پیوند دکمه اقدام','frpmesh-hybrid')),'url');
 $add('footer',array('footer_layout'=>__('چیدمان فوتر','frpmesh-hybrid')),'select');$add('footer',array('footer_columns'=>__('تعداد ستون‌ها','frpmesh-hybrid')),'number');$add('footer',array('footer_logo_id'=>__('لوگوی فوتر','frpmesh-hybrid')),'media');$add('footer',array('footer_description'=>__('توضیح فوتر','frpmesh-hybrid'),'footer_copyright'=>__('متن کپی‌رایت','frpmesh-hybrid')),'text');$add('footer',array('footer_widgets'=>__('ابزارک‌های فوتر','frpmesh-hybrid'),'back_to_top'=>__('بازگشت به بالا','frpmesh-hybrid')),'toggle');$add('footer',array('social_links'=>__('پیوندهای اجتماعی؛ هر سطر یک نشانی','frpmesh-hybrid')),'urls');
 $add('blog',array('blog_layout'=>__('چیدمان وبلاگ','frpmesh-hybrid')),'select');$add('blog',array('blog_columns'=>__('ستون‌های کارت','frpmesh-hybrid')),'number');$add('blog',array('blog_sidebar'=>__('ستون کناری','frpmesh-hybrid'),'blog_image'=>__('تصویر شاخص','frpmesh-hybrid'),'blog_author'=>__('نویسنده','frpmesh-hybrid'),'blog_date'=>__('تاریخ','frpmesh-hybrid'),'blog_category'=>__('دسته','frpmesh-hybrid'),'blog_reading'=>__('زمان مطالعه','frpmesh-hybrid'),'blog_related'=>__('نوشته‌های مرتبط','frpmesh-hybrid'),'blog_prev_next'=>__('نوشته قبل و بعد','frpmesh-hybrid'),'blog_comments'=>__('دیدگاه‌ها','frpmesh-hybrid'),'blog_breadcrumb'=>__('مسیر راهنما','frpmesh-hybrid')),'toggle');
 $add('commerce',array('shop_cart'=>__('نمایش سبد در هدر','frpmesh-hybrid')),'toggle');
 $add('seo',array('seo_breadcrumb'=>__('مسیر راهنمای پویا','frpmesh-hybrid')),'toggle');
 $add('performance',array('reduce_motion'=>__('کاهش حرکت درخواستی کاربر','frpmesh-hybrid')),'toggle');
 $add('advanced',array('custom_css'=>__('اعلان‌های CSS امن برای :root','frpmesh-hybrid')),'css');
 return $f;
}
function frpmt_choices($key){$sets=array('sidebar_position'=>array('none'=>__('بدون ستون','frpmesh-hybrid'),'right'=>__('راست','frpmesh-hybrid'),'left'=>__('چپ','frpmesh-hybrid')),'header_layout'=>array('default'=>__('طرح اصلی','frpmesh-hybrid'),'centered'=>__('وسط‌چین','frpmesh-hybrid'),'split'=>__('تقسیم‌شده','frpmesh-hybrid'),'two_rows'=>__('دو ردیف','frpmesh-hybrid'),'transparent'=>__('شفاف','frpmesh-hybrid')),'footer_layout'=>array('default'=>__('طرح اصلی','frpmesh-hybrid'),'columns'=>__('ستونی','frpmesh-hybrid'),'compact'=>__('فشرده','frpmesh-hybrid')),'blog_layout'=>array('default'=>__('طرح اصلی','frpmesh-hybrid'),'grid'=>__('شبکه‌ای','frpmesh-hybrid'),'list'=>__('فهرستی','frpmesh-hybrid')));$layouts=array('default'=>__('طرح اصلی','frpmesh-hybrid'),'full'=>__('تمام‌عرض','frpmesh-hybrid'),'boxed'=>__('کادری','frpmesh-hybrid'),'content_sidebar'=>__('محتوا و ستون کناری','frpmesh-hybrid'),'sidebar_content'=>__('ستون کناری و محتوا','frpmesh-hybrid'));$sets['page_layout']=$layouts;$sets['archive_layout']=$layouts;return isset($sets[$key])?$sets[$key]:array();}
function frpmt_options(){ $options=get_option('frpmt_settings',array());return is_array($options)?$options:array(); }
function frpmt_option($key){$all=frpmt_options();$schema=frpmt_schema();return isset($schema[$key])?(isset($all[$key])?$all[$key]:$schema[$key]['default']):null;}
/** Reject unknown keys and reject invalid color, URL, font, numeric and CSS input. Input is already unslashed by options.php, the Customizer and the importer. */
function frpmt_sanitize($input){$output=array();if(!is_array($input)){return $output;}foreach(frpmt_schema() as $key=>$field){if(!array_key_exists($key,$input)){continue;}$value=$input[$key];if(!is_scalar($value)){continue;}switch($field['type']){
 case 'color':$value=sanitize_hex_color($value);break;
 case 'number':$value=($value===''?'':max(0,min(2500,absint($value))));break;
 case 'decimal':$value=($value===''?'':max(-10,min(10,(float)$value)));break;
 case 'media':$value=absint($value);break;
 case 'url':$value=esc_url_raw($value,array('http','https','mailto','tel'));break;
 case 'urls':$urls=preg_split('/\r\n|\r|\n/',(string)$value);$value=implode("\n",array_filter(array_map(function($url){return esc_url_raw(trim($url),array('http','https'));},array_slice($urls,0,15))));break;
 case 'select':$choices=frpmt_choices($key);$value=isset($choices[$value])?$value:$field['default'];break;
 case 'toggle':$value=(int)(bool)$value;break;
 case 'font':$value=trim((string)$value);$value=preg_match('/^[\pL\pN\s,\-]{0,90}$/u',$value)?$value:'';break;
 case 'css':
  // Without unfiltered_html (multisite admins, DISALLOW_UNFILTERED_HTML) keep the stored CSS instead of wiping it on every save.
  if(current_user_can('unfiltered_html')){$value=substr(safecss_filter_attr((string)$value),0,8000);}
  else{$stored=frpmt_options();$value=isset($stored[$key])&&is_string($stored[$key])?$stored[$key]:'';}
  break;
 default:$value=sanitize_text_field($value);}
 $output[$key]=$value; }return $output;}
function frpmt_register_settings(){register_setting('frpmt_group','frpmt_settings',array('sanitize_callback'=>'frpmt_sanitize','default'=>array()));foreach(frpmt_sections() as $section=>$label){if($section==='dashboard')continue;add_settings_section('frpmt_'.$section,$label,'__return_false','frpmt-'.$section);foreach(frpmt_schema() as $key=>$field){if($field['section']===$section){add_settings_field('frpmt_'.$key,$field['label'],'frpmt_field', 'frpmt-'.$section,'frpmt_'.$section,array('key'=>$key,'field'=>$field,'label_for'=>'frpmt_'.$key));}}}}
add_action('admin_init','frpmt_register_settings');
function frpmt_menu(){add_menu_page(__('تنظیمات قالب','frpmesh-hybrid'),__('تنظیمات قالب','frpmesh-hybrid'),'edit_theme_options','frpmt-settings','frpmt_settings_page','dashicons-admin-appearance',59);}
add_action('admin_menu','frpmt_menu');
function frpmt_admin_assets($hook){if($hook!=='toplevel_page_frpmt-settings')return;$base=get_template_directory_uri();wp_enqueue_media();wp_enqueue_style('frpmt-admin',$base.'/assets/admin/css/theme-settings.css',array(),wp_get_theme()->get('Version'));wp_enqueue_script('frpmt-admin',$base.'/assets/admin/js/theme-settings.js',array('media-views'),wp_get_theme()->get('Version'),true);wp_localize_script('frpmt-admin','frpmtI18n',array('unsaved'=>__('تغییرات ذخیره نشده‌اند. آیا خارج می‌شوید؟','frpmesh-hybrid'),'confirm'=>__('تنظیمات انتخاب‌شده بازنشانی شوند؟','frpmesh-hybrid'),'select'=>__('انتخاب تصویر','frpmesh-hybrid'),'confirmAll'=>__('همهٔ تنظیمات این پنل پاک شود؟ این عمل روی محتوای برگه‌ها اثر ندارد.','frpmesh-hybrid'),'noResults'=>__('تنظیمی یافت نشد.','frpmesh-hybrid'),'enableFirst'=>__('برای ویرایش این مورد، ابتدا گزینهٔ مرتبط را فعال کنید.','frpmesh-hybrid'),'exportSaved'=>__('خروجی شامل آخرین تغییرات ذخیره‌شده است.','frpmesh-hybrid'),'selectFile'=>__('ابتدا یک فایل JSON انتخاب کنید.','frpmesh-hybrid'),'socialUrl'=>__('آدرس شبکه اجتماعی','frpmesh-hybrid'),'removeLink'=>__('حذف پیوند','frpmesh-hybrid')));}
add_action('admin_enqueue_scripts','frpmt_admin_assets');
// Match the capability of the panel to the Settings API save endpoint.
add_filter('option_page_capability_frpmt_group',function(){return 'edit_theme_options';});

require_once __DIR__ . '/SettingsFields.php';
require_once __DIR__ . '/SettingsPage.php';
require_once __DIR__ . '/SettingsActions.php';
