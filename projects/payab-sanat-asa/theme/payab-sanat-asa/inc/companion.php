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

/** Activating the theme starts setup: install/activate the companion plugin, which then imports pages on a fresh site. */
add_action(
	'after_switch_theme',
	static function () {
		update_option( 'aladdin_payab_sanat_asa_setup', 1, false );
	}
);

add_action(
	'admin_init',
	static function () {
		if ( ! get_option( 'aladdin_payab_sanat_asa_setup' ) || ! current_user_can( 'install_plugins' ) || wp_doing_ajax() ) {
			return;
		}
		delete_option( 'aladdin_payab_sanat_asa_setup' );
		if ( aladdin_payab_sanat_asa_core_active() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=aladdin-payab&tab=import' ) );
			exit;
		}
		$err = aladdin_payab_sanat_asa_install_core();
		if ( true === $err ) {
			wp_safe_redirect( admin_url( 'admin.php?page=aladdin-payab&tab=import' ) );
		} else {
			set_transient( 'aladdin_payab_sanat_asa_setup_error', $err, 300 );
			wp_safe_redirect( admin_url( 'themes.php?page=payab-sanat-asa-setup' ) );
		}
		exit;
	}
);

/** Install (when needed) and activate the bundled companion plugin. Returns true or an error message. */
function aladdin_payab_sanat_asa_install_core() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( ! file_exists( WP_PLUGIN_DIR . '/' . ALADDIN_PAYAB_SANAT_ASA_CORE_SLUG ) ) {
		$zip = ALADDIN_PAYAB_SANAT_ASA_DIR . '/bundled/payab-sanat-asa-core.zip';
		if ( ! file_exists( $zip ) ) {
			return __( 'The bundled plugin file was not found. Upload payab-sanat-asa-core.zip from Plugins > Add New.', 'payab-sanat-asa' );
		}
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $zip );
		if ( ! $result || is_wp_error( $result ) ) {
			return __( 'Installing the bundled plugin failed. Upload payab-sanat-asa-core.zip manually.', 'payab-sanat-asa' );
		}
	}
	$r = activate_plugin( ALADDIN_PAYAB_SANAT_ASA_CORE_SLUG );
	return is_wp_error( $r ) ? $r->get_error_message() : true;
}

add_action(
	'admin_menu',
	static function () {
		if ( aladdin_payab_sanat_asa_core_active() ) {
			return;
		}
		add_theme_page( __( 'Payab setup', 'payab-sanat-asa' ), __( 'Payab setup', 'payab-sanat-asa' ), 'install_plugins', 'payab-sanat-asa-setup', static function () {
			$err = get_transient( 'aladdin_payab_sanat_asa_setup_error' );
			echo '<div class="wrap"><h1>' . esc_html__( 'Payab Sanat Asa setup', 'payab-sanat-asa' ) . '</h1><hr class="wp-header-end"/>';
			if ( $err ) {
				echo '<div class="notice notice-error"><p>' . esc_html( $err ) . '</p></div>';
			}
			echo '<p>' . esc_html__( 'The companion plugin adds the settings page, languages and the page import. Click the button to install and activate it; on a fresh site the pages are created automatically.', 'payab-sanat-asa' ) . '</p>';
			printf( '<a class="button button-primary button-hero" href="%s">%s</a></div>', esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=aladdin_payab_install_core' ), 'aladdin_payab_install_core' ) ), esc_html__( 'Install and activate the companion plugin', 'payab-sanat-asa' ) );
		} );
	}
);

add_action(
	'admin_post_aladdin_payab_install_core',
	static function () {
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'payab-sanat-asa' ), 403 );
		}
		check_admin_referer( 'aladdin_payab_install_core' );
		$err = aladdin_payab_sanat_asa_install_core();
		if ( true !== $err ) {
			wp_die( esc_html( $err ) );
		}
		wp_safe_redirect( admin_url( 'admin.php?page=aladdin-payab&tab=import' ) );
		exit;
	}
);
