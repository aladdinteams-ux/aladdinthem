<?php
/** Template Name: FRP Mesh Canvas */
defined('ABSPATH') || exit;
?><!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head><body <?php body_class('frpmt-canvas'); ?>><?php wp_body_open(); ?><?php while(have_posts()){the_post();?><main id="main"><?php the_content();wp_link_pages(); ?></main><?php }wp_footer(); ?></body></html>
