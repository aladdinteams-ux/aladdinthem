<?php
/**
 * Real WordPress menus for the header navigation and the two footer link lists, one set per language.
 * Created with the pages (journaled, undone with the import), assigned to the theme locations and editable in
 * Appearance > Menus. The theme prints these menus instead of the built-in link rows.
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Menus {

	const THEMES = array( 'payab-sanat-asa', 'payab-sanat-asa-child' );

	/** Items per menu: group key => [ route group | 'anchor:categories', css classes ]; labels per language. */
	private static function spec() {
		return array(
			'primary' => array(
				'name'  => array( 'fa' => 'منوی اصلی', 'en' => 'Main menu', 'ar' => 'القائمة الرئيسية' ),
				'items' => array( array( '', '' ), array( 'shop-2', 'payab-mega' ), array( 'anchor:categories', '' ), array( 'videos', '' ), array( 'about-us-2', '' ), array( 'contact-us', '' ) ),
				'labels' => array(
					'fa' => array( 'صفحه اصلی', 'محصولات', 'دسته‌بندی‌ها', 'آموزش و ویدیو', 'درباره ما', 'تماس با ما' ),
					'en' => array( 'Home', 'Products', 'Categories', 'Guides & videos', 'About us', 'Contact' ),
					'ar' => array( 'الرئيسية', 'المنتجات', 'الفئات', 'الأدلة والفيديوهات', 'من نحن', 'اتصل بنا' ),
				),
			),
			'footer1' => array(
				'name'  => array( 'fa' => 'فوتر: دسترسی سریع', 'en' => 'Footer: quick links', 'ar' => 'التذييل: روابط سريعة' ),
				'items' => array( array( '', '' ), array( 'shop-2', '' ), array( 'about-us-2', '' ), array( 'contact-us', '' ) ),
				'labels' => array(
					'fa' => array( 'صفحه اصلی', 'فروشگاه', 'درباره ما', 'تماس با ما' ),
					'en' => array( 'Home', 'Shop', 'About us', 'Contact' ),
					'ar' => array( 'الرئيسية', 'المتجر', 'من نحن', 'اتصل بنا' ),
				),
			),
			'footer2' => array(
				'name'  => array( 'fa' => 'فوتر: اطلاعات مفید', 'en' => 'Footer: useful information', 'ar' => 'التذييل: معلومات مفيدة' ),
				'items' => array( array( 'videos', '' ), array( 'blog-2', '' ), array( 'gallery', '' ), array( 'industrial-certifications-and-certificates', '' ) ),
				'labels' => array(
					'fa' => array( 'ویدیوهای آموزشی', 'مقالات', 'گالری تصاویر', 'مدارک و گواهی‌ها' ),
					'en' => array( 'Video guides', 'Articles', 'Photo gallery', 'Documents & certificates' ),
					'ar' => array( 'فيديوهات تعليمية', 'مقالات', 'معرض الصور', 'الوثائق والشهادات' ),
				),
			),
		);
	}

	public static function location( $key, $lang ) {
		return 'payab-' . $key . '-' . $lang;
	}

	private static function locations() {
		$out = array();
		foreach ( array( 'fa', 'en', 'ar' ) as $l ) {
			foreach ( array_keys( self::spec() ) as $k ) {
				$out[] = self::location( $k, $l );
			}
		}
		return $out;
	}

	private static function get_locations() {
		$all = array();
		foreach ( self::THEMES as $t ) {
			$mods = get_option( 'theme_mods_' . $t, array() );
			$all  = array_merge( $all, isset( $mods['nav_menu_locations'] ) ? (array) $mods['nav_menu_locations'] : array() );
		}
		return $all;
	}

	private static function assign( array $map ) {
		foreach ( self::THEMES as $t ) {
			$mods = get_option( 'theme_mods_' . $t, array() );
			if ( ! is_array( $mods ) ) {
				$mods = array();
			}
			$mods['nav_menu_locations'] = array_merge( isset( $mods['nav_menu_locations'] ) ? (array) $mods['nav_menu_locations'] : array(), $map );
			update_option( 'theme_mods_' . $t, $mods );
		}
	}

	/** Status for the admin: location => array( menu_id, items, name ). */
	public static function status() {
		$loc = self::get_locations();
		$out = array();
		foreach ( array( 'fa', 'en', 'ar' ) as $l ) {
			foreach ( self::spec() as $k => $sp ) {
				$id        = isset( $loc[ self::location( $k, $l ) ] ) ? (int) $loc[ self::location( $k, $l ) ] : 0;
				$menu      = $id ? wp_get_nav_menu_object( $id ) : null;
				$out[ $l ][ $k ] = array( 'id' => $menu ? $id : 0, 'items' => $menu ? (int) $menu->count : 0, 'name' => $sp['name'][ $l ] );
			}
		}
		return $out;
	}

	/** Create what is missing (a location without a non-empty menu). Returns array of created menu ids. */
	public static function ensure() {
		$created = array();
		$assign  = array();
		$before  = self::get_locations();
		foreach ( array( 'fa', 'en', 'ar' ) as $l ) {
			foreach ( self::spec() as $k => $sp ) {
				$loc = self::location( $k, $l );
				$cur = isset( $before[ $loc ] ) ? (int) $before[ $loc ] : 0;
				$obj = $cur ? wp_get_nav_menu_object( $cur ) : null;
				if ( $obj && (int) $obj->count > 0 ) {
					continue;                     // the owner (or an earlier run) already has links here.
				}
				$menu_id = wp_create_nav_menu( 'Payab — ' . $sp['name'][ $l ] . ' (' . strtoupper( $l ) . ')' );
				if ( is_wp_error( $menu_id ) ) {
					$existing = wp_get_nav_menu_object( 'Payab — ' . $sp['name'][ $l ] . ' (' . strtoupper( $l ) . ')' );
					if ( ! $existing ) {
						continue;
					}
					$menu_id = (int) $existing->term_id;
				} else {
					$created[] = (int) $menu_id;
				}
				foreach ( $sp['items'] as $i => $it ) {
					$title = $sp['labels'][ $l ][ $i ];
					$args  = array( 'menu-item-title' => $title, 'menu-item-status' => 'publish', 'menu-item-position' => $i + 1, 'menu-item-classes' => $it[1] );
					if ( 0 === strpos( $it[0], 'anchor:' ) ) {
						$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => aladdin_payab_sanat_asa_route_url( $l, '' ) . '#' . substr( $it[0], 7 ) );
					} else {
						$pid = Languages::post_id( $l, $it[0] );
						if ( ! $pid ) {
							continue;
						}
						$args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => get_post_type( $pid ), 'menu-item-object-id' => $pid );
					}
					wp_update_nav_menu_item( $menu_id, 0, $args );
				}
				$assign[ $loc ] = (int) $menu_id;
			}
		}
		if ( $assign ) {
			self::assign( $assign );
			$j = get_option( Importer::JOURNAL, array() );
			if ( $j ) {
				$j['menus']         = array_values( array_unique( array_merge( isset( $j['menus'] ) ? $j['menus'] : array(), $created ) ) );
				$j['menu_locs_old'] = isset( $j['menu_locs_old'] ) ? $j['menu_locs_old'] : array_intersect_key( $before, array_flip( self::locations() ) );
				update_option( Importer::JOURNAL, $j, false );
			}
		}
		return $created;
	}

	/** Undo: delete the menus the import created and release the locations. */
	public static function undo( array $journal ) {
		$n = 0;
		foreach ( (array) ( $journal['menus'] ?? array() ) as $id ) {
			if ( wp_get_nav_menu_object( $id ) && wp_delete_nav_menu( $id ) ) {
				$n++;
			}
		}
		$loc = self::get_locations();
		$map = array();
		foreach ( self::locations() as $l ) {
			if ( isset( $loc[ $l ] ) && in_array( (int) $loc[ $l ], array_map( 'intval', (array) ( $journal['menus'] ?? array() ) ), true ) ) {
				$map[ $l ] = 0;
			}
		}
		foreach ( (array) ( $journal['menu_locs_old'] ?? array() ) as $l => $id ) {
			$map[ $l ] = (int) $id;
		}
		if ( $map ) {
			self::assign( $map );
		}
		return $n;
	}
}
