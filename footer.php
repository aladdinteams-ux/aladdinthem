<?php
/**
 * Footer template.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;
?>
</main>
<?php
if ( ! ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) ) {
	if ( ! ls_render_elementor_template( ls_tb_template_for( 'footer' ) ) ) {
		ls_render_site_footer();
	}
}
wp_footer();
?>
</body>
</html>
