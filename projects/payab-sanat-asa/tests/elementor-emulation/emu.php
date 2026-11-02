<?php
/**
 * Sandbox mu-plugin: renders imported pages with an EMULATED Elementor DOM + emulated base CSS when ?emu=1 is present.
 * ?emu=1&nolayer=1 additionally turns the theme's cascade-layer protection off (shows what unprotected Elementor CSS does).
 * Test aid only. Real Elementor markup/CSS was not available (see the QA report).
 */
if ( empty( $_GET['emu'] ) ) { return; } // phpcs:ignore
add_action( 'plugins_loaded', function () {
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		eval( 'namespace Elementor { class Controls_Manager { const TEXT="t";const SELECT="s";const MEDIA="m";const TEXTAREA="a";const TAB_ADVANCED="adv"; const WYSIWYG="w"; } abstract class Widget_Base { public $s=array(); function get_settings_for_display(){return $this->s;} function before_render(){} function after_render(){} function start_controls_section(){} function end_controls_section(){} function add_control(){} } }' ); // phpcs:ignore
	}
}, 1 );
add_action( 'init', function () {
	foreach ( array( 'class-raw-widget', 'class-element-widget', 'class-text-widget' ) as $f ) { require_once WP_PLUGIN_DIR . '/payab-sanat-asa-core/includes/elementor/' . $f . '.php'; }
}, 20 );
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'elementor-frontend', content_url( 'mu-plugins/emu/emu.css' ), array(), '4.0.8-emu' );
	wp_enqueue_style( 'elementor-frontend' );
	if ( ! empty( $_GET['nolayer'] ) ) { add_filter( 'aladdin_payab_sanat_asa_layer_elementor', '__return_false' ); }
}, 5 );
add_filter( 'body_class', function ( $c ) { $c[] = 'elementor-default'; $c[] = 'elementor-kit-5'; return $c; } );
function aladdin_emu_render( $e, $top = false ) {
	$st = $e['settings'];
	if ( 'widget' === $e['elType'] ) {
		$cls = 'aladdin_payab_sanat_asa_text' === $e['widgetType'] ? '\Aladdin\PayabSanatAsa\Text_Widget' : '\Aladdin\PayabSanatAsa\Element_Widget';
		$w = new $cls(); $w->s = $st;
		ob_start(); $m = new ReflectionMethod( $w, 'render' ); $m->setAccessible( true ); $m->invoke( $w );
		return ob_get_clean();   // Payab widgets print no wrapper on the public site (Raw_Widget), same as here
	}
	$tag = isset( $st['html_tag'] ) ? $st['html_tag'] : 'div';
	$cls = 'elementor-element elementor-element-' . $e['id'] . ' e-con e-flex e-con-full ' . ( $top ? 'e-parent ' : 'e-child ' ) . ( isset( $st['_css_classes'] ) ? $st['_css_classes'] : '' );
	$at = ' class="' . esc_attr( $cls ) . '" data-id="' . $e['id'] . '" data-element_type="container"';
	if ( ! empty( $st['_element_id'] ) ) { $at .= ' id="' . esc_attr( $st['_element_id'] ) . '"'; }
	foreach ( \Aladdin\PayabSanatAsa\Elementor_Integration::parse_attrs( isset( $st['aladdin_attrs'] ) ? $st['aladdin_attrs'] : '' ) as $k => $v ) { $at .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"'; }
	// what Elementor's per-element post CSS would set from our container settings
	$at .= ' style="--display:flex;--flex-direction:column;--padding-top:0px;--padding-right:0px;--padding-bottom:0px;--padding-left:0px;--gap:0px;--row-gap:0px;--column-gap:0px;"';
	$in = '';
	foreach ( $e['elements'] as $c ) { $in .= aladdin_emu_render( $c ); }
	return "<$tag$at>$in</$tag>";
}
add_filter( 'the_content', function ( $content ) {
	$id = get_the_ID();
	$data = json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true );
	if ( ! $data || ! in_array( get_post_type( $id ), array( 'aladdin_page_fa', 'aladdin_page_en', 'aladdin_page_ar' ), true ) ) { return $content; }
	$out = '';
	foreach ( $data as $e ) { $out .= aladdin_emu_render( $e, true ); }
	return '<div data-elementor-type="wp-post" data-elementor-id="' . $id . '" class="elementor elementor-' . $id . '">' . $out . '</div>';
}, 8 );
