<?php
/**
 * Version migrations (admin only; the stored version changes only after every step passed).
 * 1.1.0: pages imported by 1.0.x (core "page" type with a parent tree) move to the per-language post types.
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Migrations {

	const OPT = 'aladdin_payab_sanat_asa_core_version';

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_run' ), 20 );
	}

	public static function maybe_run() {
		if ( ! current_user_can( 'manage_options' ) || version_compare( (string) get_option( self::OPT, '0' ), '1.1.0', '>=' ) ) {
			return;
		}
		if ( self::pages_to_cpt() ) {
			update_option( self::OPT, ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION, false );
		}
	}

	public static function pages_to_cpt() {
		$legacy = get_posts(
			array(
				'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'private', 'pending' ), 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
				'meta_query' => array( 'relation' => 'OR', array( 'key' => Languages::META_GROUP, 'compare' => 'EXISTS' ), array( 'key' => '_aladdin_payab_placeholder', 'compare' => 'EXISTS' ) ),
			)
		);
		if ( ! $legacy ) {
			return true;
		}
		$front = (int) get_option( 'page_on_front' );
		foreach ( $legacy as $id ) {
			if ( get_post_meta( $id, '_aladdin_payab_placeholder', true ) ) {
				wp_trash_post( $id );        // structural parents are not needed any more.
				continue;
			}
			$lang  = get_post_meta( $id, Languages::META_LANG, true );
			$group = (string) get_post_meta( $id, Languages::META_GROUP, true );
			$type  = Post_Types::slug_for( $lang );
			if ( ! $type ) {
				continue;
			}
			wp_update_post( array( 'ID' => $id, 'post_type' => $type, 'post_parent' => 0, 'post_name' => '' === $group ? ( 'fa' === $lang ? 'home' : 'home-' . $lang ) : str_replace( '/', '-', $group ) ) );
			update_post_meta( $id, '_elementor_template_type', 'wp-post' );
			if ( $id === $front ) {
				update_option( 'show_on_front', 'posts' );
				update_option( 'page_on_front', 0 );
				update_option( 'aladdin_payab_sanat_asa_claim_home', 'yes' );
			}
		}
		Languages::flush_index();
		return true;
	}
}
