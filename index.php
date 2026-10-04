<?php
/**
 * Main template (blog index, archives, search fallback).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ls_do_location( 'archive' ) ) {
	echo '<div class="ls-root">';
	ls_render_blog_hero( array( 'title' => is_home() && get_option( 'page_for_posts' ) ? get_the_title( (int) get_option( 'page_for_posts' ) ) : '' ) );
	if ( is_home() && ! is_paged() ) {
		echo '<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 mb-16 w-full">';
		ls_render_featured_post();
		echo '</section>';
	}
	echo '<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20 w-full"><div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">';
	echo '<div class="' . ( ls_opt( 'blog_sidebar' ) ? 'lg:col-span-8' : 'lg:col-span-12' ) . ' flex flex-col">';
	ls_render_posts_grid(
		array(
			'source'    => 'current',
			'bar_title' => is_search() ? __( 'نتایج جستجو', 'larijani' ) : __( 'جدیدترین مقالات و راهنماهای کارگاهی', 'larijani' ),
			'columns'   => ls_opt( 'blog_sidebar' ) ? 2 : 3,
		)
	);
	echo '</div>';
	if ( ls_opt( 'blog_sidebar' ) ) {
		echo '<aside class="lg:col-span-4 flex flex-col gap-6">';
		ls_render_archive_sidebar();
		echo '</aside>';
	}
	echo '</div></section></div>';
}

get_footer();
