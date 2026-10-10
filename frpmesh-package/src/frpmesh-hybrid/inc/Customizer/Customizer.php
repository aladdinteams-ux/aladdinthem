<?php
/** Customizer edits the same option array as the Settings API. */
defined('ABSPATH') || exit;
function frpmt_customize($manager){
 $sections=array('identity'=>__('هویت FRP Mesh','frpmesh-hybrid'),'colors'=>__('رنگ‌های FRP Mesh','frpmesh-hybrid'),'type'=>__('فونت‌های FRP Mesh','frpmesh-hybrid'),'header'=>__('هدر FRP Mesh','frpmesh-hybrid'),'footer'=>__('فوتر FRP Mesh','frpmesh-hybrid'));
 foreach($sections as $section=>$label)$manager->add_section('frpmt_'.$section,array('title'=>$label,'priority'=>150));
 foreach(frpmt_schema() as $key=>$field){if(!isset($sections[$field['section']])||!in_array($field['type'],array('color','text','font','toggle','select','number'),true))continue;$id='frpmt_settings['.$key.']';$manager->add_setting($id,array('type'=>'option','default'=>$field['default'],'capability'=>'edit_theme_options','sanitize_callback'=>function($value)use($key){$result=frpmt_sanitize(array($key=>$value));return isset($result[$key])?$result[$key]:'';}));$opts=array('label'=>$field['label'],'section'=>'frpmt_'.$field['section'],'settings'=>$id);if($field['type']==='color'){$manager->add_control(new WP_Customize_Color_Control($manager,'frpmt_'.$key,$opts));}else{$opts['type']=$field['type']==='toggle'?'checkbox':($field['type']==='number'?'number':($field['type']==='select'?'select':'text'));if($field['type']==='select')$opts['choices']=frpmt_choices($key);$manager->add_control('frpmt_'.$key,$opts);}}
}
add_action('customize_register','frpmt_customize');
