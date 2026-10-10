<?php defined('ABSPATH') || exit; ?>
<!doctype html><html class="frpme-html" <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head><body dir="rtl" <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="frpme-skip" href="#main"><?php echo esc_html__('رفتن به محتوای اصلی','frpmesh-hybrid'); ?></a>
<?php if(frpmt_elementor_location('header')){}elseif(function_exists('frpme_chrome')){frpme_chrome('header');}else{get_template_part('template-parts/header/fallback');} ?>
