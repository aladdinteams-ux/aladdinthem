<?php
/**
 * 404.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="section aladdin-404">
	<div class="container">
		<h1><?php esc_html_e( 'Page not found', 'payab-sanat-asa' ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist or has moved.', 'payab-sanat-asa' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="btn btn-primary" href="<?php echo esc_url( aladdin_payab_sanat_asa_route_url( aladdin_payab_sanat_asa_lang(), '' ) ); ?>"><?php esc_html_e( 'Back to home', 'payab-sanat-asa' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
