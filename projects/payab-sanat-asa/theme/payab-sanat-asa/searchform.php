<?php
/**
 * Search form.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

$aladdin_lang = aladdin_payab_sanat_asa_lang();
?>
<form role="search" method="get" class="header-search-form aladdin-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="aladdin-s"><?php esc_html_e( 'Search', 'payab-sanat-asa' ); ?></label>
	<input id="aladdin-s" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'payab-sanat-asa' ); ?>"/>
	<?php if ( 'fa' !== $aladdin_lang ) : ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( $aladdin_lang ); ?>"/>
	<?php endif; ?>
	<button type="submit"><?php esc_html_e( 'Search', 'payab-sanat-asa' ); ?></button>
</form>
