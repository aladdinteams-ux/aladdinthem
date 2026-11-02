<?php
/**
 * Reversible demo import: creates the 66 pages (22 routes x 3 languages) with Elementor data and a plain-HTML
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

	/** Existing post for a lang+group (any status except trash). */
	private static function find( $lang, $group ) {
		$q = get_posts(
			array(
				'post_type'      => array( 'page', 'product' ),
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

	private static function placeholder( $lang, $slug, $parent, $redirect, &$created ) {
		$existing = get_posts(
			array(
				'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'private' ), 'posts_per_page' => 1, 'fields' => 'ids',
				'meta_query' => array(
					array( 'key' => '_aladdin_payab_placeholder', 'value' => $lang . ':' . $slug ),
				),
			)
		);
		if ( $existing ) {
			return (int) $existing[0];
		}
		$id = wp_insert_post(
			array(
				'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $slug, 'post_name' => $slug, 'post_parent' => $parent,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return 0;
		}
		update_post_meta( $id, '_aladdin_payab_placeholder', $lang . ':' . $slug );
		update_post_meta( $id, '_aladdin_payab_redirect', $redirect );
		update_post_meta( $id, Languages::META_LANG, $lang );
		$created[] = (int) $id;
		return (int) $id;
	}

	/** Run the import. Returns array( created => n, skipped => n, errors => [] ). */
	public static function run() {
		$pages   = self::pages();
		$woo     = class_exists( 'WooCommerce' ) && post_type_exists( 'product' );
		$created = array();
		$skipped = 0;
		$errors  = array();
		$opts    = array();
		$prev    = self::journal();

		if ( '' === (string) get_option( 'permalink_structure' ) ) {
			$opts['permalink_structure'] = '';
			update_option( 'permalink_structure', '/%postname%/' );
		}
		$homes = array();
		// Order: home pages first, then by depth so parents exist.
		usort(
			$pages,
			static function ( $a, $b ) {
				return substr_count( $a['group'], '/' ) <=> substr_count( $b['group'], '/' ) ?: strcmp( $a['group'], $b['group'] );
			}
		);
		$ids = array();
		foreach ( array( 'fa', 'en', 'ar' ) as $lang ) {
			$ids[ $lang ] = array();
		}
		foreach ( $pages as $p ) {
			$lang = $p['lang']; $group = $p['group'];
			$exist = self::find( $lang, $group );
			if ( $exist ) {
				$skipped++;
				$ids[ $lang ][ $group ] = $exist;
				continue;
			}
			$is_product = 0 === strpos( $group, 'product/' );
			$slug       = '' === $group ? ( 'fa' === $lang ? 'payab-home' : $lang ) : basename( $group );
			$parent     = 0;
			if ( 'fa' !== $lang && '' !== $group ) {
				$parent = $ids[ $lang ][''] ?? 0;
			}
			if ( '' !== $group && ! ( $is_product && $woo ) ) {
				$dir = dirname( $group );
				if ( '.' !== $dir ) {
					$top = explode( '/', $dir );
					$walk = '';
					foreach ( $top as $seg ) {
						$walk = '' === $walk ? $seg : $walk . '/' . $seg;
						if ( isset( $ids[ $lang ][ $walk ] ) ) {
							$parent = $ids[ $lang ][ $walk ];
						} else {
							$redir = 'articles' === $seg ? 'blog-2' : ( 'product' === $seg ? 'shop-2' : $seg );
							$parent = self::placeholder( $lang, $seg, $parent, $redir, $created );
							$ids[ $lang ][ $walk ] = $parent;
						}
					}
				}
			}
			$title = $p['h1'] ? wp_strip_all_tags( $p['h1'] ) : $p['title'];
			$args  = array(
				'post_type'    => ( $is_product && $woo ) ? 'product' : 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => ( $is_product && $woo && 'fa' !== $lang ) ? $slug . '-' . $lang : $slug,
				'post_parent'  => ( $is_product && $woo ) ? 0 : $parent,
				'post_content' => wp_slash( $p['fallback'] ),
				'menu_order'   => 0,
			);
			$id = wp_insert_post( $args, true );
			if ( is_wp_error( $id ) ) {
				$errors[] = $lang . ':' . $group . ' ' . $id->get_error_message();
				continue;
			}
			update_post_meta( $id, Languages::META_LANG, $lang );
			update_post_meta( $id, Languages::META_GROUP, $group );
			update_post_meta( $id, '_aladdin_payab_seo_title', $p['title'] );
			update_post_meta( $id, '_aladdin_payab_seo_description', $p['description'] );
			update_post_meta( $id, '_aladdin_payab_jsonld', wp_slash( wp_json_encode( array_map( static function ( $s ) { return json_decode( $s, true ); }, $p['jsonld'] ), JSON_UNESCAPED_UNICODE ) ) );
			update_post_meta( $id, '_wp_page_template', 'default' );
			update_post_meta( $id, '_elementor_edit_mode', 'builder' );
			update_post_meta( $id, '_elementor_template_type', 'wp-page' );
			update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
			update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $p['elementor'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) );
			delete_post_meta( $id, '_elementor_element_cache' );
			delete_post_meta( $id, '_elementor_css' );
			delete_post_meta( $id, '_elementor_page_assets' );
			if ( $is_product && $woo ) {
				self::product_meta( $id, $group );
			}
			$created[] = (int) $id;
			$ids[ $lang ][ $group ] = (int) $id;
		}
		Languages::flush_index();

		$fa_home = $ids['fa'][''] ?? 0;
		if ( $fa_home && ! empty( $created ) && in_array( $fa_home, $created, true ) ) {
			$opts['show_on_front'] = get_option( 'show_on_front' );
			$opts['page_on_front'] = get_option( 'page_on_front' );
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $fa_home );
		}
		if ( function_exists( 'wc_get_page_id' ) ) {
			// Catalog mode: no cart/checkout needed; nothing to change in WooCommerce page settings.
			unset( $opts['woo'] );
		}
		if ( $created ) {
			// Keep the first (real) import journal when a later run created nothing; merge when it did.
			$old = isset( $prev['created'] ) ? $prev['created'] : array();
			update_option(
				self::JOURNAL,
				array(
					'time'    => time(),
					'version' => ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION,
					'created' => array_values( array_unique( array_merge( $old, $created ) ) ),
					'options' => isset( $prev['options'] ) && $prev['options'] ? array_merge( $opts, $prev['options'] ) : $opts,
					'woo'     => $woo,
				),
				false
			);
			set_transient( 'aladdin_payab_sanat_asa_flush', 1, 60 );
			flush_rewrite_rules( false );
		}
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
		foreach ( (array) ( $j['options'] ?? array() ) as $k => $v ) {
			update_option( $k, $v );
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
		$r = self::run();
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
