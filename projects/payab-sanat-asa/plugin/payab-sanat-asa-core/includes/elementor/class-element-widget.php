<?php
/**
 * "Payab Element": renders one HTML element of the approved design with real controls
 * (tag, classes, id, link, text, image + alt, inner markup, attributes).
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

class Element_Widget extends Widget_Base {

	const TAGS = array( 'div', 'span', 'a', 'button', 'img', 'small', 'strong', 'em', 'i', 'b', 'label', 'nav', 'ul', 'ol', 'li', 'details', 'summary', 'table', 'form', 'iframe', 'svg', 'dialog', 'figcaption', 'figure', 'select', 'textarea', 'p', 'section', 'aside', 'header', 'footer' );

	public function get_name() {
		return 'aladdin_payab_sanat_asa_element';
	}

	public function get_title() {
		return esc_html__( 'Payab Element', 'payab-sanat-asa-core' );
	}

	public function get_icon() {
		return 'eicon-code-highlight';
	}

	public function get_categories() {
		return array( 'aladdin-payab' );
	}

	public function get_keywords() {
		return array( 'payab', 'aladdin', 'link', 'button', 'image', 'icon' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'payab-sanat-asa-core' ) ) );
		$this->add_control( 'tag', array( 'label' => esc_html__( 'HTML tag', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::SELECT, 'default' => 'div', 'options' => array_combine( self::TAGS, self::TAGS ) ) );
		$this->add_control( 'text', array( 'label' => esc_html__( 'Text', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXT, 'label_block' => true, 'dynamic' => array( 'active' => false ) ) );
		$this->add_control( 'href', array( 'label' => esc_html__( 'Link', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXT, 'label_block' => true, 'description' => esc_html__( 'URL, #anchor, tel:… or a route token such as {{route:fa:shop-2}}.', 'payab-sanat-asa-core' ), 'condition' => array( 'tag' => 'a' ) ) );
		$this->add_control( 'image', array( 'label' => esc_html__( 'Image', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::MEDIA, 'condition' => array( 'tag' => 'img' ) ) );
		$this->add_control( 'src', array( 'label' => esc_html__( 'Image URL (when no media chosen)', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXT, 'label_block' => true, 'condition' => array( 'tag' => 'img' ) ) );
		$this->add_control( 'alt', array( 'label' => esc_html__( 'Alt text', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXT, 'label_block' => true, 'condition' => array( 'tag' => 'img' ) ) );
		$this->add_control( 'inner_html', array( 'label' => esc_html__( 'Inner markup (icons, nested elements)', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 8, 'dynamic' => array( 'active' => false ), 'description' => esc_html__( 'Used instead of Text when filled. Scripts and unsafe attributes are removed.', 'payab-sanat-asa-core' ), 'condition' => array( 'tag!' => 'img' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'advanced_attrs', array( 'label' => esc_html__( 'Class & attributes', 'payab-sanat-asa-core' ) ) );
		$this->add_control( 'css_classes', array( 'label' => esc_html__( 'CSS classes of the element', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXT, 'label_block' => true, 'dynamic' => array( 'active' => false ) ) );
		$this->add_control( 'element_id', array( 'label' => esc_html__( 'Element ID', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => false ) ) );
		$this->add_control( 'attrs', array( 'label' => esc_html__( 'Attributes (key|value per line)', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'dynamic' => array( 'active' => false ) ) );
		$this->end_controls_section();
	}

	/** Allowed markup for inner HTML: post content + SVG + form controls + details/summary/dialog/iframe. */
	public static function allowed_html() {
		$a   = wp_kses_allowed_html( 'post' );
		$any = array( 'class' => true, 'id' => true, 'style' => true, 'title' => true, 'role' => true, 'lang' => true, 'dir' => true, 'hidden' => true, 'tabindex' => true, 'aria-label' => true, 'aria-hidden' => true, 'aria-expanded' => true, 'aria-controls' => true, 'aria-labelledby' => true, 'aria-describedby' => true, 'aria-live' => true, 'aria-pressed' => true, 'aria-current' => true, 'data-*' => true );
		$extra = array(
			'svg' => array( 'xmlns' => true, 'viewbox' => true, 'viewBox' => true, 'width' => true, 'height' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'focusable' => true ),
			'g' => array( 'transform' => true, 'fill' => true, 'stroke' => true ),
			'path' => array( 'd' => true, 'fill' => true, 'stroke' => true, 'opacity' => true, 'transform' => true ),
			'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ),
			'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true, 'stroke' => true ),
			'line' => array( 'x1' => true, 'x2' => true, 'y1' => true, 'y2' => true ),
			'details' => array( 'open' => true ), 'summary' => array(), 'dialog' => array( 'open' => true ),
			'form' => array( 'action' => true, 'method' => true, 'novalidate' => true, 'data-inquiry-form' => true, 'data-msg-title' => true, 'data-msg-usage' => true, 'data-msg-topic' => true, 'data-msg-note' => true, 'data-copy-success' => true, 'data-copy-fail' => true, 'role' => true ),
			'input' => array( 'maxlength' => true, 'minlength' => true, 'pattern' => true, 'inputmode' => true, 'step' => true, 'type' => true, 'name' => true, 'value' => true, 'placeholder' => true, 'required' => true, 'autocomplete' => true, 'checked' => true, 'min' => true, 'max' => true ),
			'select' => array( 'name' => true, 'required' => true, 'multiple' => true ), 'option' => array( 'value' => true, 'selected' => true ), 'optgroup' => array( 'label' => true ),
			'textarea' => array( 'name' => true, 'maxlength' => true, 'minlength' => true, 'rows' => true, 'cols' => true, 'placeholder' => true, 'required' => true, 'readonly' => true ),
			'button' => array( 'type' => true, 'name' => true, 'value' => true, 'disabled' => true ),
			'label' => array( 'for' => true ),
			'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'loading' => true, 'allowfullscreen' => true, 'referrerpolicy' => true, 'title' => true ),
			'img' => array( 'decoding' => true, 'fetchpriority' => true, 'srcset' => true, 'sizes' => true, 'loading' => true, 'width' => true, 'height' => true, 'alt' => true, 'src' => true ),
			'i' => array(), 'em' => array(), 'small' => array(), 'figure' => array(), 'figcaption' => array(), 'nav' => array(), 'time' => array( 'datetime' => true ),
		);
		foreach ( $extra as $tag => $attrs ) {
			$a[ $tag ] = array_merge( isset( $a[ $tag ] ) ? $a[ $tag ] : array(), $attrs, $any );
		}
		return $a;
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$tag = in_array( $s['tag'], self::TAGS, true ) ? $s['tag'] : 'div';
		$at  = array();
		if ( ! empty( $s['css_classes'] ) ) {
			$at['class'] = $s['css_classes'];
		}
		if ( ! empty( $s['element_id'] ) ) {
			$at['id'] = $s['element_id'];
		}
		foreach ( Elementor_Integration::parse_attrs( isset( $s['attrs'] ) ? $s['attrs'] : '' ) as $k => $v ) {
			$at[ $k ] = $v;
		}
		if ( 'a' === $tag && ! empty( $s['href'] ) ) {
			$at['href'] = $s['href'];
		}
		if ( 'img' === $tag ) {
			$at['src'] = ! empty( $s['image']['url'] ) ? $s['image']['url'] : ( isset( $s['src'] ) ? $s['src'] : '' );
			$at['alt'] = isset( $s['alt'] ) ? $s['alt'] : '';
		}
		$html = '<' . $tag;
		foreach ( $at as $k => $v ) {
			$v = in_array( $k, array( 'href', 'src', 'action' ), true ) ? esc_url( aladdin_payab_sanat_asa_resolve_tokens( $v ), null, 'raw' ) : $v;
			if ( in_array( $k, array( 'href', 'src', 'action' ), true ) ) {
				$v = esc_url( $v );
			}
			$html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
		if ( 'img' === $tag ) {
			echo $html . '/>'; // phpcs:ignore WordPress.Security.EscapeOutput -- attributes escaped above.
			return;
		}
		$inner = '';
		if ( ! empty( $s['inner_html'] ) ) {
			$inner = wp_kses( aladdin_payab_sanat_asa_resolve_tokens( $s['inner_html'] ), self::allowed_html() );
		} elseif ( isset( $s['text'] ) && '' !== $s['text'] ) {
			$inner = esc_html( $s['text'] );
		}
		echo $html . '>' . $inner . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped/kses'd above.
	}
}
