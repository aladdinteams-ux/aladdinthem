<?php
/**
 * Catalog product pages (Elementor content; no price or cart in catalog mode).
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	the_content();
}
get_footer();
