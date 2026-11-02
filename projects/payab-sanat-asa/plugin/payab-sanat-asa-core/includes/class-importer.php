<?php
/**
 * Reversible demo import: creates the 66 language pages (22 routes x 3 languages, one post type per language) with Elementor data and a plain-HTML
 * fallback, plus 3 products x 3 languages (WooCommerce products when WooCommerce is active, pages otherwise).
 * Every created post is journaled; "Undo" moves exactly those posts to the Trash and restores changed options.
 * Existing content is never overwritten: a route that already exists is skipped.
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Importer {

	const JOURNAL = 'aladdin_payab_sanat_asa_import_journal';

	public static function init() {
		add_action( 'admin_post_aladdin_payab_import', array( __CLASS__, 'handle_import' ) );
		add_action( 'admin_post_aladdin_payab_undo', array( __CLASS__, 'handle_undo' ) );
		add_action( 'admin_init', array( __CLASS__, 'maybe_auto_import' ) );
	}

	/** Fresh = nothing of the owner's yet: no front page set, at most the default sample page and one post. */
	public static function is_fresh_site() {
		if ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) ) {
			return false;
		}
		$pages = get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'private', 'pending' ), 'posts_per_page' => 5, 'fields' => 'ids', 'no_found_rows' => true ) );
		foreach ( $pages as $id ) {
			if ( 'sample-page' !== get_post_field( 'post_name', $id ) && 'privacy-policy' !== get_post_field( 'post_name', $id ) ) {
				return false;
			}
		}
		$posts = get_posts( array( 'post_type' => 'post', 'post_status' => array( 'publish', 'draft', 'private' ), 'posts_per_page' => 3, 'fields' => 'ids', 'no_found_rows' => true ) );
		return count( $posts ) <= 1 && empty( self::journal() );
	}

	/** One-time automatic import right after the companion plugin is activated, only on a fresh site. */
	public static function maybe_auto_import() {
		if ( ! get_option( 'aladdin_payab_sanat_asa_autoimport' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		delete_option( 'aladdin_payab_sanat_asa_autoimport' );
		if ( self::is_fresh_site() ) {
			set_transient( 'aladdin_payab_import_result', self::run(), 120 );
			wp_safe_redirect( admin_url( 'admin.php?page=aladdin-payab&tab=import&aladdin_msg=imported' ) );
			exit;
		}
		wp_safe_redirect( admin_url( 'admin.php?page=aladdin-payab&tab=import' ) );
		exit;
	}

	public static function journal() {
		return get_option( self::JOURNAL, array() );
	}

	private static function pages() {
		$file = ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'data/pages.json';
		$data = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP_FileSystem.FileGet
		return is_array( $data ) ? $data : array();
	}

	/** Existing language page / product for a lang+group (any status except trash). */
	private static function find( $lang, $group ) {
		$q = get_posts(
			array(
				'post_type'      => array( Post_Types::slug_for( $lang ), 'product' ),
				'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_query'     => array(
					'relation' => 'AND',
					array( 'key' => Languages::META_LANG, 'value' => $lang ),
					array( 'key' => Languages::META_GROUP, 'value' => $group ),
				),
			)
		);
		return $q ? (int) $q[0] : 0;
	}

	/** Old (non-Payab) pages/posts that sit on the same URL as one of our routes, and the old static front page. */
	public static function conflicts() {
		$out  = array();
		$seen = array();
		foreach ( self::pages() as $p ) {
			if ( '' === $p['group'] && 'fa' === $p['lang'] ) {
				continue;
			}
			$path = ( 'fa' === $p['lang'] ? '' : $p['lang'] . '/' ) . $p['group'];
			$old  = get_page_by_path( $path, OBJECT, array( 'page', 'post' ) );
			if ( $old && ! isset( $seen[ $old->ID ] ) && ! get_post_meta( $old->ID, Languages::META_GROUP, true ) && 'trash' !== $old->post_status ) {
				$seen[ $old->ID ] = 1;
				$out[] = array( 'id' => (int) $old->ID, 'title' => get_the_title( $old ), 'path' => '/' . $path . '/', 'type' => $old->post_type, 'status' => $old->post_status );
			}
		}
		return $out;
	}

	/** Static front page that is not ours (0 = none). */
	public static function old_front_page() {
		$id = ( 'page' === get_option( 'show_on_front' ) ) ? (int) get_option( 'page_on_front' ) : 0;
		return ( $id && ! get_post_meta( $id, Languages::META_GROUP, true ) ) ? $id : 0;
	}

	/** Status matrix for the admin: group => lang => post id (0 = missing). */
	public static function matrix() {
		$groups = array();
		foreach ( self::pages() as $p ) {
			$groups[ $p['group'] ][ $p['lang'] ] = array( 'id' => self::find( $p['lang'], $p['group'] ), 'title' => $p['h1'] );
		}
		return $groups;
	}

	/** Run the import (creates only what is missing). Returns array( created => n, skipped => n, errors => [] ). */
	public static function run( $args = array() ) {
		Migrations::pages_to_cpt();
		Post_Types::register();
		Post_Types::elementor_support();
		$pages   = self::pages();
		$woo     = class_exists( 'WooCommerce' ) && post_type_exists( 'product' );
		$created = array();
		$skipped = 0;
		$errors  = array();
		$opts    = array();
		$prev    = self::journal();
		$fresh   = self::is_fresh_site();

		if ( '' === (string) get_option( 'permalink_structure' ) ) {
			$opts['permalink_structure'] = '';
			update_option( 'permalink_structure', '/%postname%/' );
		}
		foreach ( $pages as $p ) {
			$lang = $p['lang'];
			$group = $p['group'];
			if ( self::find( $lang, $group ) ) {
				$skipped++;
				continue;
			}
			$is_product = 0 === strpos( $group, 'product/' ) && $woo;
			$slug       = '' === $group ? ( 'fa' === $lang ? 'home' : 'home-' . $lang ) : str_replace( '/', '-', $group );
			$id         = wp_insert_post(
				array(
					'post_type'    => $is_product ? 'product' : Post_Types::slug_for( $lang ),
					'post_status'  => 'publish',
					'post_title'   => $p['h1'] ? wp_strip_all_tags( $p['h1'] ) : $p['title'],
					'post_name'    => ( $is_product && 'fa' !== $lang ) ? basename( $group ) . '-' . $lang : $slug,
					'post_content' => wp_slash( $p['fallback'] ),
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				$errors[] = $lang . ':' . $group . ' ' . $id->get_error_message();
				continue;
			}
			update_post_meta( $id, Languages::META_LANG, $lang );
			update_post_meta( $id, Languages::META_GROUP, $group );
			update_post_meta( $id, '_aladdin_payab_seo_title', $p['title'] );
			update_post_meta( $id, '_aladdin_payab_seo_description', $p['description'] );
			update_post_meta( $id, '_aladdin_payab_jsonld', wp_slash( wp_json_encode( array_map( static function ( $s ) { return json_decode( $s, true ); }, $p['jsonld'] ), JSON_UNESCAPED_UNICODE ) ) );
			update_post_meta( $id, '_elementor_edit_mode', 'builder' );
			update_post_meta( $id, '_elementor_template_type', 'wp-post' );
			update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
			update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $p['elementor'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
			delete_post_meta( $id, '_elementor_element_cache' );
			delete_post_meta( $id, '_elementor_css' );
			delete_post_meta( $id, '_elementor_page_assets' );
			if ( $is_product ) {
				self::product_meta( $id, $group );
			}
			$created[] = (int) $id;
		}
		// The Persian home takes "/" on a fresh site (or when no static front page is set); otherwise it lives at /payab-home/.
		if ( ! empty( $args['claim'] ) ) {
			$opts['aladdin_payab_sanat_asa_claim_home'] = get_option( 'aladdin_payab_sanat_asa_claim_home', null );
			update_option( 'aladdin_payab_sanat_asa_claim_home', 'yes' );
		} elseif ( $created && false === get_option( 'aladdin_payab_sanat_asa_claim_home', false ) ) {
			$claim = ( $fresh || 'page' !== get_option( 'show_on_front' ) || ! (int) get_option( 'page_on_front' ) ) ? 'yes' : 'no';
			add_option( 'aladdin_payab_sanat_asa_claim_home', $claim, '', false );
			$opts['aladdin_payab_sanat_asa_claim_home'] = null;      // null = delete on undo.
		}
		$trashed = array();
		if ( ! empty( $args['trash'] ) ) {
			foreach ( self::conflicts() as $c ) {
				if ( wp_trash_post( $c['id'] ) ) {
					$trashed[] = $c['id'];
				}
			}
		}
		Languages::flush_index();
		if ( $created || $trashed || ! empty( $args['claim'] ) ) {
			$old = isset( $prev['created'] ) ? $prev['created'] : array();
			update_option(
				self::JOURNAL,
				array_merge(
					array_intersect_key( $prev, array_flip( array( 'menus', 'menu_locs_old' ) ) ),
				array(
					'time'    => time(),
					'version' => ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION,
					'created' => array_values( array_unique( array_merge( $old, $created ) ) ),
					'options' => isset( $prev['options'] ) && $prev['options'] ? array_merge( $opts, $prev['options'] ) : $opts,
					'woo'     => $woo,
					'trashed' => array_values( array_unique( array_merge( isset( $prev['trashed'] ) ? $prev['trashed'] : array(), $trashed ) ) ),
				)
				),
				false
			);
			set_transient( 'aladdin_payab_sanat_asa_flush', 1, 60 );
			flush_rewrite_rules( false );
		}
		Menus::ensure();
		return array( 'created' => count( $created ), 'skipped' => $skipped, 'errors' => $errors );
	}

	/** Woo catalog data: category + featured image from the bundled theme images. */
	private static function product_meta( $id, $group ) {
		$map = array(
			'product/electric-lift'  => array( 'electric-lifts', 'lift-electric.webp', 'اختصاصی' ),
			'product/manual-lift'    => array( 'manual-lifts', 'lift-manual.webp', '' ),
			'product/transfer-sling' => array( 'transfer-slings', 'standing-sling.webp', '' ),
		);
		if ( ! isset( $map[ $group ] ) ) {
			return;
		}
		if ( taxonomy_exists( 'product_cat' ) ) {
			$term = term_exists( $map[ $group ][0], 'product_cat' );
			if ( ! $term ) {
				$term = wp_insert_term( $map[ $group ][0], 'product_cat', array( 'slug' => $map[ $group ][0] ) );
			}
			if ( ! is_wp_error( $term ) ) {
				wp_set_object_terms( $id, (int) ( is_array( $term ) ? $term['term_id'] : $term ), 'product_cat' );
			}
		}
		update_post_meta( $id, '_visibility', 'visible' );
		update_post_meta( $id, '_stock_status', 'instock' );
		$src = get_template_directory() . '/assets/images/' . $map[ $group ][1];
		if ( is_readable( $src ) && ! has_post_thumbnail( $id ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$tmp = wp_tempnam( $map[ $group ][1] );
			if ( $tmp && copy( $src, $tmp ) ) {
				$att = media_handle_sideload( array( 'name' => $map[ $group ][1], 'tmp_name' => $tmp ), $id );
				if ( ! is_wp_error( $att ) ) {
					set_post_thumbnail( $id, $att );
					update_post_meta( $id, '_aladdin_payab_imported_attachment', $att );
				}
			}
		}
	}

	public static function undo() {
		$j = self::journal();
		if ( empty( $j['created'] ) ) {
			return 0;
		}
		$n = 0;
		foreach ( array_reverse( $j['created'] ) as $id ) {
			if ( get_post( $id ) && wp_trash_post( $id ) ) {
				$n++;
			}
			$att = (int) get_post_meta( $id, '_aladdin_payab_imported_attachment', true );
			if ( $att ) {
				wp_delete_attachment( $att, true );
			}
		}
		Menus::undo( $j );
		foreach ( (array) ( $j['trashed'] ?? array() ) as $tid ) {
			if ( 'trash' === get_post_status( $tid ) ) {
				$was = get_post_meta( $tid, '_wp_trash_meta_status', true );
				wp_untrash_post( $tid );                  // WordPress restores as draft; put the old status back.
				wp_update_post( array( 'ID' => $tid, 'post_status' => $was ? $was : 'publish' ) );
			}
		}
		foreach ( (array) ( $j['options'] ?? array() ) as $k => $v ) {
			if ( null === $v ) {
				delete_option( $k );
			} else {
				update_option( $k, $v );
			}
		}
		delete_option( self::JOURNAL );
		Languages::flush_index();
		flush_rewrite_rules( false );
		return $n;
	}

	public static function handle_import() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'payab-sanat-asa-core' ), 403 );
		}
		check_admin_referer( 'aladdin_payab_import' );
		if ( empty( $_POST['confirm'] ) ) {
			wp_safe_redirect( add_query_arg( 'aladdin_msg', 'confirm', admin_url( 'admin.php?page=aladdin-payab&tab=import' ) ) );
			exit;
		}
		$r = self::run( array( 'claim' => ! empty( $_POST['claim_home'] ), 'trash' => ! empty( $_POST['trash_conflicts'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification -- checked above.
		set_transient( 'aladdin_payab_import_result', $r, 120 );
		wp_safe_redirect( add_query_arg( 'aladdin_msg', 'imported', admin_url( 'admin.php?page=aladdin-payab&tab=import' ) ) );
		exit;
	}

	public static function handle_undo() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'payab-sanat-asa-core' ), 403 );
		}
		check_admin_referer( 'aladdin_payab_undo' );
		set_transient( 'aladdin_payab_undo_result', self::undo(), 120 );
		wp_safe_redirect( add_query_arg( 'aladdin_msg', 'undone', admin_url( 'admin.php?page=aladdin-payab&tab=import' ) ) );
		exit;
	}
}
