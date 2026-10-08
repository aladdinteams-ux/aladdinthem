<?php
/**
 * Single post template.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( larijani_do_location( 'single' ) ) {
		continue;
	}
	if ( larijani_is_built_with_elementor( get_the_ID() ) ) {
		the_content();
		continue;
	}
	echo '<div class="ls-root bg-surface-canvas">';
	larijani_render_post_hero();
	echo '<section class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-12 py-10"><div class="grid grid-cols-1 lg:grid-cols-12 gap-10">';
	echo '<article id="post-' . esc_attr( get_the_ID() ) . '" class="' . esc_attr( implode( ' ', get_post_class( larijani_opt( 'blog_sidebar' ) ? 'lg:col-span-8 flex flex-col gap-10' : 'lg:col-span-12 flex flex-col gap-10' ) ) ) . '">';
	larijani_render_post_body();
	larijani_render_related_posts();
	larijani_render_post_comments();
	echo '</article>';
	if ( larijani_opt( 'blog_sidebar' ) ) {
		echo '<aside class="lg:col-span-4 space-y-6">';
		larijani_render_single_sidebar();
		echo '</aside>';
	}
	echo '</div></section></div>';
endwhile;

get_footer();
