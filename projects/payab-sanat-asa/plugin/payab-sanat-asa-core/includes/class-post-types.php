<?php
/**
 * One page post type per language: aladdin_page_fa, aladdin_page_en, aladdin_page_ar.
 * Public URLs are built from the translation group, e.g. /shop-2/ (fa), /en/shop-2/, /ar/shop-2/, so the
 * approved URL structure is kept without core pages. Resolved by Post_Types::resolve() (no rewrite rules needed).
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Post_Types {

	/** @var array<string,array{slug:string,label:string,singular:string,menu:string}> */
	public static function types() {
		return array(
			'fa' => array( 'slug' => 'aladdin_page_fa', 'label' => 'صفحات فارسی', 'singular' => 'صفحه فارسی', 'icon' => 'dashicons-admin-page', 'pos' => 21 ),
			'en' => array( 'slug' => 'aladdin_page_en', 'label' => 'English pages', 'singular' => 'English page', 'icon' => 'dashicons-admin-page', 'pos' => 22 ),
			'ar' => array( 'slug' => 'aladdin_page_ar', 'label' => 'الصفحات العربية', 'singular' => 'صفحة عربية', 'icon' => 'dashicons-admin-page', 'pos' => 23 ),
		);
	}

	public static function slugs() {
		return wp_list_pluck( self::types(), 'slug' );
	}

	public static function slug_for( $lang ) {
		$t = self::types();
		return isset( $t[ $lang ] ) ? $t[ $lang ]['slug'] : '';
	}

	public static function lang_of_type( $post_type ) {
		foreach ( self::types() as $lang => $t ) {
			if ( $t['slug'] === $post_type ) {
				return $lang;
			}
		}
		return '';
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
		add_filter( 'post_type_link', array( __CLASS__, 'permalink' ), 10, 2 );
		add_action( 'parse_request', array( __CLASS__, 'resolve' ), 1 );
		add_filter( 'redirect_canonical', array( __CLASS__, 'no_canonical_redirect' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'elementor_support' ) );
		add_filter( 'wp_unique_post_slug', array( __CLASS__, 'keep_slug' ), 10, 6 );
		add_filter( 'template_include', array( __CLASS__, 'template' ), 20 );
	}

	public static function register() {
		foreach ( self::types() as $lang => $t ) {
			register_post_type(
				$t['slug'],
				array(
					'labels'              => array(
						'name'               => $t['label'],
						'singular_name'      => $t['singular'],
						'menu_name'          => $t['label'],
						'add_new_item'       => 'fa' === $lang ? 'افزودن صفحه' : ( 'en' === $lang ? 'Add page' : 'إضافة صفحة' ),
						'edit_item'          => 'fa' === $lang ? 'ویرایش صفحه' : ( 'en' === $lang ? 'Edit page' : 'تحرير الصفحة' ),
						'all_items'          => $t['label'],
						'search_items'       => 'fa' === $lang ? 'جست‌وجوی صفحات' : ( 'en' === $lang ? 'Search pages' : 'بحث الصفحات' ),
					),
					'public'              => true,
					'publicly_queryable'  => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_rest'        => true,
					'menu_position'       => $t['pos'],
					'menu_icon'           => $t['icon'],
					'capability_type'     => 'page',
					'map_meta_cap'        => true,
					'hierarchical'        => false,
					'has_archive'         => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'page-attributes' ),
				)
			);
		}
	}

	/** Public URL: language prefix + group path (fa home is "/" when it claims the front page). */
	public static function url_for( $lang, $group ) {
		$group = trim( (string) $group, '/' );
		if ( 'fa' === $lang && '' === $group ) {
			return 'yes' === get_option( 'aladdin_payab_sanat_asa_claim_home', 'yes' ) ? home_url( '/' ) : home_url( '/payab-home/' );
		}
		return home_url( '/' . ( 'fa' === $lang ? '' : $lang . '/' ) . ( '' === $group ? '' : $group . '/' ) );
	}

	public static function permalink( $link, $post ) {
		$lang = self::lang_of_type( $post->post_type );
		if ( ! $lang ) {
			return $link;
		}
		return self::url_for( $lang, (string) get_post_meta( $post->ID, Languages::META_GROUP, true ) );
	}

	/** Map the request path to a language page (before WordPress runs its main query). */
	public static function resolve( $wp ) {
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		foreach ( array( 's', 'feed', 'preview', 'p', 'page_id', 'attachment_id', 'author', 'm', 'cat', 'tag', 'post_type' ) as $k ) {
			if ( ! empty( $wp->query_vars[ $k ] ) || isset( $_GET[ $k ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				return;
			}
		}
		$path = trim( (string) $wp->request, '/' );
		$seg  = '' === $path ? array() : explode( '/', $path );
		$lang = 'fa';
		if ( $seg && in_array( $seg[0], array( 'en', 'ar' ), true ) ) {
			$lang = array_shift( $seg );
		}
		$group = implode( '/', $seg );
		if ( 'fa' === $lang && 'payab-home' === $group ) {
			$group = '';
		}
		if ( 'fa' === $lang && '' === $group && 'yes' !== get_option( 'aladdin_payab_sanat_asa_claim_home', 'yes' ) ) {
			return;       // the owner keeps their own front page; the Persian home lives at /payab-home/.
		}
		$id = Languages::post_id( $lang, $group, true );
		if ( $id ) {
			$wp->query_vars   = array( 'post_type' => self::slug_for( $lang ), 'p' => $id );
			$wp->matched_rule = '';
			return;
		}
		// Structural URLs that never were pages: send visitors to the matching list.
		$redir = array( 'articles' => 'blog-2', 'product' => 'shop-2' );
		if ( isset( $redir[ $group ] ) && Languages::post_id( $lang, $redir[ $group ], true ) ) {
			wp_safe_redirect( aladdin_payab_sanat_asa_route_url( $lang, $redir[ $group ] ), 302 );
			exit;
		}
	}

	public static function no_canonical_redirect( $redirect, $requested ) {
		if ( is_singular( self::slugs() ) ) {
			return false;
		}
		return $redirect;
	}

	/** Slugs are unique per post type only; keep the clean slug (group with dashes). */
	public static function keep_slug( $slug, $post_id, $status, $post_type, $parent, $original ) {
		return self::lang_of_type( $post_type ) ? $original : $slug;
	}

	/** Language pages use the theme's page template. */
	public static function template( $template ) {
		if ( is_singular( self::slugs() ) ) {
			$page = locate_template( array( 'page.php' ) );
			if ( $page ) {
				return $page;
			}
		}
		return $template;
	}

	/** Make the language post types editable with free Elementor. */
	public static function elementor_support() {
		$cur = get_option( 'elementor_cpt_support', false );
		if ( false === $cur ) {
			$cur = array( 'page', 'post' );
		}
		$new = array_values( array_unique( array_merge( (array) $cur, self::slugs() ) ) );
		if ( $new !== (array) $cur ) {
			update_option( 'elementor_cpt_support', $new );
		}
	}
}
