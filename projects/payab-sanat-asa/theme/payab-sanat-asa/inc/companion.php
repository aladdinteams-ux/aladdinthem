<?php
/**
 * Companion plugin notice: installs/activates the bundled "Payab Sanat Asa Core" plugin on request.
 * Nothing is installed automatically; the admin clicks a button (capability + nonce).
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

const ALADDIN_PAYAB_SANAT_ASA_CORE_SLUG = 'payab-sanat-asa-core/payab-sanat-asa-core.php';

function aladdin_payab_sanat_asa_core_active() {
	return defined( 'ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION' );
}

add_action(
	'admin_notices',
	static function () {
		if ( aladdin_payab_sanat_asa_core_active() || ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		$installed = file_exists( WP_PLUGIN_DIR . '/' . ALADDIN_PAYAB_SANAT_ASA_CORE_SLUG );
		$bundled   = file_exists( ALADDIN_PAYAB_SANAT_ASA_DIR . '/bundled/payab-sanat-asa-core.zip' );
		if ( ! $installed && ! $bundled ) {
			return;
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=aladdin_payab_install_core' ), 'aladdin_payab_install_core' );
		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s <a class="button button-primary" href="%s">%s</a></p></div>',
			esc_html__( 'Payab Sanat Asa theme:', 'payab-sanat-asa' ),
			esc_html__( 'the companion plugin is required for languages, import, SEO and the Elementor widget.', 'payab-sanat-asa' ),
			esc_url( $url ),
			esc_html( $installed ? __( 'Activate the companion plugin', 'payab-sanat-asa' ) : __( 'Install and activate the companion plugin', 'payab-sanat-asa' ) )
		);
	}
);

add_action(
	'admin_post_aladdin_payab_install_core',
	static function () {
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'payab-sanat-asa' ), 403 );
		}
		check_admin_referer( 'aladdin_payab_install_core' );
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . ALADDIN_PAYAB_SANAT_ASA_CORE_SLUG ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$skin     = new Automatic_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$result   = $upgrader->install( ALADDIN_PAYAB_SANAT_ASA_DIR . '/bundled/payab-sanat-asa-core.zip' );
			if ( ! $result || is_wp_error( $result ) ) {
				wp_die( esc_html__( 'Installing the bundled plugin failed. Upload payab-sanat-asa-core.zip manually.', 'payab-sanat-asa' ) );
			}
		}
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$r = activate_plugin( ALADDIN_PAYAB_SANAT_ASA_CORE_SLUG );
		if ( is_wp_error( $r ) ) {
			wp_die( esc_html( $r->get_error_message() ) );
		}
		wp_safe_redirect( admin_url( 'options-general.php?page=aladdin-payab' ) );
		exit;
	}
);
