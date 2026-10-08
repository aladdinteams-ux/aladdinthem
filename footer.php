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
	if ( ! larijani_render_elementor_template( larijani_tb_template_for( 'footer' ) ) ) {
		larijani_render_site_footer();
	}
}
wp_footer();
?>
</body>
</html>
