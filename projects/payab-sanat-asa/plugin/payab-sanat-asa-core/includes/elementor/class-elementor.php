<?php
/**
 * Elementor (free) integration: widget category, "Payab Element" widget, container attribute control.
 * Nothing here uses Elementor Pro.
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Elementor_Integration {

	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'widgets' ) );
		add_action( 'elementor/element/container/section_layout/after_section_end', array( __CLASS__, 'container_controls' ), 10, 2 );
		add_action( 'elementor/frontend/container/before_render', array( __CLASS__, 'container_render' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notice_missing' ) );
		// Keep imported design text exactly as approved: no typographic replacement inside our text widgets.
		add_action( 'elementor/frontend/widget/before_render', array( __CLASS__, 'before_widget' ) );
		add_action( 'elementor/frontend/widget/after_render', array( __CLASS__, 'after_widget' ) );
	}

	public static function category( $mgr ) {
		$mgr->add_category( 'aladdin-payab', array( 'title' => esc_html__( 'Payab Sanat Asa', 'payab-sanat-asa-core' ), 'icon' => 'eicon-code' ) );
	}

	public static function widgets( $widgets_manager ) {
		require_once __DIR__ . '/class-element-widget.php';
		$widgets_manager->register( new Element_Widget() );
	}

	public static function notice_missing() {
		if ( did_action( 'elementor/loaded' ) || ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		$s = get_current_screen();
		if ( ! $s || ! in_array( $s->id, array( 'dashboard', 'settings_page_aladdin-payab', 'plugins' ), true ) ) {
			return;
		}
		echo '<div class="notice notice-info"><p>' . esc_html__( 'Payab Sanat Asa: install and activate the free Elementor plugin to edit pages visually. Without it the site still shows the saved page HTML.', 'payab-sanat-asa-core' ) . '</p></div>';
	}

	/** Allowed attribute names for the free-text attribute controls. */
	public static function attr_allowed( $name ) {
		return (bool) preg_match( '/^(aria-[a-z-]+|data-[a-z0-9_-]+|role|tabindex|title|lang|dir|hidden|hreflang|rel|target|type|name|value|placeholder|for|min|max|rows|cols|required|autocomplete|inputmode|open|loading|decoding|fetchpriority|width|height|srcset|sizes|allowfullscreen|referrerpolicy|href|action|method|novalidate|multiple|selected|checked|disabled|viewBox|viewbox|xmlns|fill|stroke|focusable|style|src|sizes|stroke-[a-z]+|fill-[a-z]+|aria-[a-z]+|data-[a-z0-9_-]+|enctype|maxlength|minlength|pattern|step|list|form|accept|autofocus|readonly)$/i', $name );
	}

	/** Parse "key|value" lines into a safe array. */
	public static function parse_attrs( $text ) {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
			if ( '' === trim( $line ) || false === strpos( $line, '|' ) ) {
				continue;
			}
			list( $k, $v ) = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( self::attr_allowed( $k ) && ! preg_match( '/^\s*(javascript|vbscript):/i', $v ) ) {
				$out[ $k ] = $v;
			}
		}
		return $out;
	}

	public static function container_controls( $element, $args ) {
		$element->start_controls_section( 'aladdin_payab_attrs', array( 'label' => esc_html__( 'Payab attributes', 'payab-sanat-asa-core' ), 'tab' => \Elementor\Controls_Manager::TAB_ADVANCED ) );
		$element->add_control(
			'aladdin_attrs',
			array(
				'label'       => esc_html__( 'HTML attributes', 'payab-sanat-asa-core' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'description' => esc_html__( 'One per line: key|value (aria-*, data-*, role, title…). Needed for the approved design scripts.', 'payab-sanat-asa-core' ),
			)
		);
		$element->end_controls_section();
	}

	public static function before_widget( $widget ) {
		if ( 'text-editor' === $widget->get_name() && 'yes' === $widget->get_settings( 'aladdin_raw' ) ) {
			add_filter( 'run_wptexturize', '__return_false', 99 );
		}
	}

	public static function after_widget( $widget ) {
		if ( 'text-editor' === $widget->get_name() && 'yes' === $widget->get_settings( 'aladdin_raw' ) ) {
			remove_filter( 'run_wptexturize', '__return_false', 99 );
		}
	}

	public static function container_render( $element ) {
		$s = $element->get_settings_for_display( 'aladdin_attrs' );
		if ( $s ) {
			foreach ( self::parse_attrs( $s ) as $k => $v ) {
				$element->add_render_attribute( '_wrapper', $k, $v );
			}
		}
	}
}
