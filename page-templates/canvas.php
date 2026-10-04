<?php
/**
 * Template Name: بوم خالی لاریجانی (بدون هدر و فوتر)
 * Template Post Type: page, post, ls_project
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'ls-canvas' ); ?>>
<?php
wp_body_open();
while ( have_posts() ) {
	the_post();
	the_content();
}
wp_footer();
?>
</body>
</html>
