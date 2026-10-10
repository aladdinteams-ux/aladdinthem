<?php
/** Optional scoped overrides only; defaults leave the original template intact. */
defined('ABSPATH') || exit;
function frpmt_assets(){
 $base=get_template_directory_uri();wp_register_style('frpmt-fallback',$base.'/assets/css/fallback.css',array(),wp_get_theme()->get('Version'));
 if(!function_exists('frpme_chrome'))wp_enqueue_style('frpmt-fallback');
 $s=frpmt_options();if(!$s)return;
 $map=array('primary_color'=>'primary','secondary_color'=>'secondary','text_color'=>'text','heading_color'=>'heading','background_color'=>'background','surface_color'=>'surface','border_color'=>'border','success_color'=>'success','warning_color'=>'warning','error_color'=>'error');$vars='';$bridge='';foreach($map as $key=>$token){if(empty($s[$key]))continue;$color=sanitize_hex_color($s[$key]);if(!$color)continue;$vars.='--theme-'.$token.':'.$color.';';if($key==='primary_color')$bridge.='--primary:'.$color.';';if($key==='text_color')$bridge.='--text:'.$color.';';if($key==='background_color')$bridge.='--bg:'.$color.';';if($key==='border_color')$bridge.='--border:'.$color.';';}
 foreach(array('container_width'=>'container','border_radius'=>'radius','site_width'=>'site-width') as $key=>$token)if(!empty($s[$key]))$vars.='--theme-'.$token.':'.absint($s[$key]).'px;';
 $css=$vars?':root{'.$vars.'}':'';if($bridge)$css.='.frpme-design{'.$bridge.'}';
 foreach(array('desktop'=>'','tablet'=>'@media(max-width:1024px)','mobile'=>'@media(max-width:767px)') as $device=>$media){$rules='';foreach(array('base'=>'body','h1'=>'h1','h2'=>'h2','h3'=>'h3','h4'=>'h4','h5'=>'h5','h6'=>'h6') as $name=>$selector){$key=$name.'_size_'.$device;if(!empty($s[$key]))$rules.=($name==='base'?'body':'body.frpme-body '.$selector.',body:not(.frpme-body) '.$selector).'{font-size:'.absint($s[$key]).'px}';}if($rules)$css.=$media? $media.'{'.$rules.'}' : $rules;}
 foreach(array('primary_font'=>'body','body_font'=>'body','heading_font'=>':is(h1,h2,h3,h4,h5,h6)') as $key=>$selector){
  if(empty($s[$key])||!preg_match('/^[\pL\pN\s,\-]{1,90}$/u',$s[$key]))continue;
  $target='body'===$selector?'body.frpme-body,body:not(.frpme-body)':'body.frpme-body '.$selector.',body:not(.frpme-body) '.$selector;
  $css.=$target.'{font-family:'.$s[$key].',Tahoma,Arial,sans-serif}';
 }
 if(!empty($s['container_width']))$css.='.frpme-design .container{max-width:'.absint($s['container_width']).'px}';
 if(!empty($s['sticky_header']))$css.='.frpme-body header:not(.elementor-location-header),.frpmt-header{position:sticky;top:0;z-index:999}'.(!empty($s['sticky_background'])?'.frpmt-header{background:'.sanitize_hex_color($s['sticky_background']).'}':'').(!empty($s['sticky_shadow'])?'.frpmt-header{box-shadow:0 4px 20px #0002}':'');
 if(!empty($s['custom_css']))$css.=':root{'.safecss_filter_attr($s['custom_css']).'}';
 if($css){if(wp_style_is('frpme-bridge','enqueued'))wp_add_inline_style('frpme-bridge',$css);else{wp_enqueue_style('frpmt-fallback');wp_add_inline_style('frpmt-fallback',$css);}}
}
add_action('wp_enqueue_scripts','frpmt_assets',1000);
