<?php
/**
 * Base class of the Payab widgets. On the public site they print exactly their own markup, without Elementor's
 * wrapper divs (".elementor-element.elementor-widget > .elementor-widget-container"), so the DOM equals the approved HTML and
 * child/sibling selectors (">", "+", ":first-child") of the design CSS keep working. Inside the Elementor editor and its
 * preview the standard wrappers are printed (selection outlines, hover toolbars need them).
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

abstract class Raw_Widget extends Widget_Base {

	/** True while Elementor draws the editor or its preview iframe. */
	protected function in_editor() {
		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance ) ) {
			return false;
		}
		$p = \Elementor\Plugin::$instance;
		return ( isset( $p->editor ) && $p->editor->is_edit_mode() ) || ( isset( $p->preview ) && $p->preview->is_preview_mode() );
	}

	public function before_render() {
		if ( $this->in_editor() ) {
			parent::before_render();
		}
	}

	public function after_render() {
		if ( $this->in_editor() ) {
			parent::after_render();
		}
	}

	public function has_widget_inner_wrapper(): bool {
		return $this->in_editor();
	}

	public function get_categories() {
		return array( 'aladdin-payab' );
	}
}
