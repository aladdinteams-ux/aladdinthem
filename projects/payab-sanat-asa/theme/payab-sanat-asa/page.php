<?php
/**
 * Pages (Elementor content, or the saved HTML fallback when Elementor is off).
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	the_content();
	wp_link_pages();
}
get_footer();
