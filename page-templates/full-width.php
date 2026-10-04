<?php
/**
 * Template Name: تمام‌عرض لاریجانی (بدون کادر)
 * Template Post Type: page, post, ls_project
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	the_content();
}
get_footer();
