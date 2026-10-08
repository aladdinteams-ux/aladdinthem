<?php
/**
 * Backward compatibility for code written against Larijani Stone < 1.4.
 *
 * 1.4.0 renamed every theme function/class from the 2-letter "ls_" prefix to
 * "larijani_" (a short prefix can collide with plugins and cause fatal
 * "cannot redeclare" errors). The documented template functions keep their old
 * names as thin deprecated wrappers — only when no other code defines them.
 * Option keys, meta keys, hook names (ls_*) and CSS classes are unchanged.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'ls_opt' ) ) {
	/**
	 * Deprecated alias of larijani_opt().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_opt( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_opt' );
		return larijani_opt( ...$args );
	}
}

if ( ! function_exists( 'ls_render_site_header' ) ) {
	/**
	 * Deprecated alias of larijani_render_site_header().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_render_site_header( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_render_site_header' );
		return larijani_render_site_header( ...$args );
	}
}

if ( ! function_exists( 'ls_render_site_footer' ) ) {
	/**
	 * Deprecated alias of larijani_render_site_footer().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_render_site_footer( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_render_site_footer' );
		return larijani_render_site_footer( ...$args );
	}
}

if ( ! function_exists( 'ls_render_catalog' ) ) {
	/**
	 * Deprecated alias of larijani_render_catalog().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_render_catalog( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_render_catalog' );
		return larijani_render_catalog( ...$args );
	}
}

if ( ! function_exists( 'ls_product_card' ) ) {
	/**
	 * Deprecated alias of larijani_product_card().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_product_card( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_product_card' );
		return larijani_product_card( ...$args );
	}
}

if ( ! function_exists( 'ls_post_card' ) ) {
	/**
	 * Deprecated alias of larijani_post_card().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_post_card( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_post_card' );
		return larijani_post_card( ...$args );
	}
}

if ( ! function_exists( 'ls_section_heading' ) ) {
	/**
	 * Deprecated alias of larijani_section_heading().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_section_heading( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_section_heading' );
		return larijani_section_heading( ...$args );
	}
}

if ( ! function_exists( 'ls_jalali_date' ) ) {
	/**
	 * Deprecated alias of larijani_jalali_date().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_jalali_date( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_jalali_date' );
		return larijani_jalali_date( ...$args );
	}
}

if ( ! function_exists( 'ls_icon' ) ) {
	/**
	 * Deprecated alias of larijani_icon().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_icon( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_icon' );
		return larijani_icon( ...$args );
	}
}

if ( ! function_exists( 'ls_img' ) ) {
	/**
	 * Deprecated alias of larijani_img().
	 *
	 * @deprecated 1.4.0
	 * @param mixed ...$args Arguments.
	 * @return mixed
	 */
	function ls_img( ...$args ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- legacy public API.
		_deprecated_function( __FUNCTION__, '1.4.0', 'larijani_img' );
		return larijani_img( ...$args );
	}
}
