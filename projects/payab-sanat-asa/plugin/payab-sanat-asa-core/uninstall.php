<?php
/**
 * Uninstall: removes settings only. Pages, products and media are the owner's content and are kept.
 *
 * @package Aladdin\PayabSanatAsa
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( get_option( 'aladdin_payab_sanat_asa_remove_data_on_uninstall' ) ) {
	delete_option( 'aladdin_payab_sanat_asa_chrome' );
	delete_option( 'aladdin_payab_sanat_asa_settings' );
	delete_option( 'aladdin_payab_sanat_asa_import_journal' );
}
delete_option( 'aladdin_payab_sanat_asa_core_version' );
