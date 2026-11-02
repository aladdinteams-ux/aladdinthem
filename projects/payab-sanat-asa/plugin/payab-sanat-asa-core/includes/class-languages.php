<?php
/**
 * Simple built-in multilingual layer: three languages (fa root, /en/, /ar/), translation groups, hreflang.
 * A page belongs to a language (meta _aladdin_payab_lang) and a translation group (_aladdin_payab_group,
 * the route without language prefix, e.g. "shop-2" or "product/electric-lift"; "" = home).
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Languages {

	const META_LANG  = '_aladdin_payab_lang';
	const META_GROUP = '_aladdin_payab_group';

	/** @var array|null lang => group => post id */
	private static $index = null;

	public static function init() {
		add_filter( 'aladdin_payab_sanat_asa_current_lang', array( __CLASS__, 'current_lang' ) );
		add_filter( 'aladdin_payab_sanat_asa_route_url', array( __CLASS__, 'route_url' ), 10, 3 );
		add_filter( 'aladdin_payab_sanat_asa_translation_url', array( __CLASS__, 'translation_url' ), 10, 2 );
		add_filter( 'locale', array( __CLASS__, 'front_locale' ) );
		add_action( 'save_post', array( __CLASS__, 'flush_index' ) );
		add_action( 'trashed_post', array( __CLASS__, 'flush_index' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'search_language' ) );
		add_filter( 'display_post_states', array( __CLASS__, 'post_states' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'maybe_flush' ) );
		add_filter( 'manage_pages_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_pages_custom_column', array( __CLASS__, 'column' ), 10, 2 );
	}

	/** Front-end UI strings (search form, 404, …) follow the language of the URL, not the site locale. */
	public static function front_locale( $locale ) {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'WP_CLI' ) && WP_CLI ) || ( isset( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( (string) $_SERVER['SCRIPT_NAME'], 'wp-login' ) ) ) {
			return $locale;
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$home = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$seg  = explode( '/', trim( (string) substr( (string) $path, strlen( (string) $home ) ), '/' ) );
		if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], self::codes(), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$seg[0] = sanitize_key( wp_unslash( $_GET['lang'] ) );
		}
		$map = array( 'en' => 'en_US', 'ar' => 'ar' );
		return isset( $map[ $seg[0] ] ) ? $map[ $seg[0] ] : 'fa_IR';
	}

	public static function codes() {
		return array( 'fa', 'en', 'ar' );
	}

	public static function maybe_flush() {
		if ( get_transient( 'aladdin_payab_sanat_asa_flush' ) ) {
			delete_transient( 'aladdin_payab_sanat_asa_flush' );
			flush_rewrite_rules( false );
		}
	}

	public static function flush_index() {
		self::$index = null;
	}

	/** @var array|null lang => group => post type (parallel to $index) */
	private static $types = null;

	/** Index of all translated posts (language page types, products), one query per request. */
	public static function index() {
		if ( null !== self::$index ) {
			return self::$index;
		}
		global $wpdb;
		self::$index = array( 'fa' => array(), 'en' => array(), 'ar' => array() );
		self::$types = array( 'fa' => array(), 'en' => array(), 'ar' => array() );
		$in = "'" . implode( "','", array_map( 'esc_sql', array_merge( Post_Types::slugs(), array( 'product', 'page' ) ) ) ) . "'";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL
		$rows = $wpdb->get_results(
			"SELECT p.ID, p.post_type, l.meta_value AS lang, g.meta_value AS grp
			 FROM {$wpdb->posts} p
			 JOIN {$wpdb->postmeta} l ON l.post_id = p.ID AND l.meta_key = '" . self::META_LANG . "'
			 JOIN {$wpdb->postmeta} g ON g.post_id = p.ID AND g.meta_key = '" . self::META_GROUP . "'
			 WHERE p.post_status IN ('publish','private','draft') AND p.post_type IN ($in)
			 ORDER BY p.ID ASC"
		);
		foreach ( (array) $rows as $r ) {
			if ( isset( self::$index[ $r->lang ] ) && ! isset( self::$index[ $r->lang ][ $r->grp ] ) ) {
				self::$index[ $r->lang ][ $r->grp ] = (int) $r->ID;
				self::$types[ $r->lang ][ $r->grp ] = $r->post_type;
			}
		}
		return self::$index;
	}

	/** Post id of a route; $cpt_only limits the answer to the language page types (not products). */
	public static function post_id( $lang, $group, $cpt_only = false ) {
		$idx = self::index();
		if ( ! isset( $idx[ $lang ][ $group ] ) ) {
			return 0;
		}
		if ( $cpt_only && Post_Types::slug_for( $lang ) !== self::$types[ $lang ][ $group ] ) {
			return 0;
		}
		return $idx[ $lang ][ $group ];
	}

	public static function current_lang( $lang ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return $lang;
		}
		if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], self::codes(), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return sanitize_key( wp_unslash( $_GET['lang'] ) );
		}
		if ( is_singular() ) {
			$l = get_post_meta( get_queried_object_id(), self::META_LANG, true );
			if ( $l && in_array( $l, self::codes(), true ) ) {
				return $l;
			}
		}
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$home = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$rel  = trim( (string) substr( (string) $path, strlen( (string) $home ) ), '/' );
		$seg  = explode( '/', $rel );
		if ( isset( $seg[0] ) && in_array( $seg[0], array( 'en', 'ar' ), true ) ) {
			return $seg[0];
		}
		return $lang ? $lang : 'fa';
	}

	public static function route_url( $url, $lang, $group ) {
		$id = self::post_id( $lang, $group );
		if ( $id ) {
			$link = get_permalink( $id );
			if ( $link ) {
				return $link;
			}
		}
		return $url;
	}

	public static function translation_url( $url, $lang ) {
		if ( is_singular() ) {
			$group = get_post_meta( get_queried_object_id(), self::META_GROUP, true );
			if ( '' !== $group || '0' === (string) $group || metadata_exists( 'post', get_queried_object_id(), self::META_GROUP ) ) {
				$id = self::post_id( $lang, (string) $group );
				if ( $id ) {
					return get_permalink( $id );
				}
			}
		}
		return $url;
	}

	/** Search shows results of the current language only. */
	public static function search_language( $q ) {
		if ( is_admin() || ! $q->is_main_query() || ! $q->is_search() ) {
			return;
		}
		$lang = apply_filters( 'aladdin_payab_sanat_asa_current_lang', 'fa' );
		$q->set( 'post_type', array( Post_Types::slug_for( $lang ), 'product' ) );
		$q->set(
			'meta_query',
			array(
				array(
					'key'   => self::META_LANG,
					'value' => $lang,
				),
			)
		);
	}

	public static function post_states( $states, $post ) {
		$lang = get_post_meta( $post->ID, self::META_LANG, true );
		if ( $lang ) {
			$states['aladdin_lang'] = strtoupper( $lang );
		}
		return $states;
	}

	public static function columns( $cols ) {
		$cols['aladdin_lang'] = esc_html__( 'Language', 'payab-sanat-asa-core' );
		return $cols;
	}

	public static function column( $col, $post_id ) {
		if ( 'aladdin_lang' === $col ) {
			echo esc_html( strtoupper( (string) get_post_meta( $post_id, self::META_LANG, true ) ) );
		}
	}
}
