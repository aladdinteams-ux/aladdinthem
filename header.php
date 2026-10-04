<?php
/**
 * Header template.
 *
 * Order of precedence: Elementor Pro Theme Builder "Header" → template chosen in
 * Customizer › تم‌بیلدر → the theme's built-in header.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#ls-main"><?php esc_html_e( 'رفتن به محتوا', 'larijani' ); ?></a>
<?php
if ( ! ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) ) {
	$ls_header_tpl = ls_tb_template_for( 'header' );
	if ( $ls_header_tpl ) {
		echo '<div class="ls-tb-header">';
		$ls_header_done = ls_render_elementor_template( $ls_header_tpl );
		echo '</div>';
		if ( ! $ls_header_done ) {
			ls_render_site_header();
		}
	} else {
		ls_render_site_header();
	}
}
?>
<main id="ls-main" class="ls-main w-full min-h-[50vh]" role="main">
