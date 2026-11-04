<?php
/**
 * "Payab Text": a headline / paragraph / list of the approved design, edited in the visual editor.
 * Prints its markup unwrapped on the public site (see Raw_Widget).
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Text_Widget extends Raw_Widget {

	public function get_name() {
		return 'aladdin_payab_sanat_asa_text';
	}

	public function get_title() {
		return esc_html__( 'Payab Text', 'payab-sanat-asa-core' );
	}

	public function get_icon() {
		return 'eicon-text';
	}

	public function get_keywords() {
		return array( 'payab', 'text', 'heading', 'paragraph', 'aladdin' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Content', 'payab-sanat-asa-core' ) ) );
		$this->add_control( 'editor', array( 'label' => esc_html__( 'Text', 'payab-sanat-asa-core' ), 'type' => Controls_Manager::WYSIWYG, 'default' => '', 'dynamic' => array( 'active' => false ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$c = isset( $s['editor'] ) ? (string) $s['editor'] : '';
		echo wp_kses( Languages::resolve_tokens( $c ), Element_Widget::allowed_html() ); // phpcs:ignore WordPress.Security.EscapeOutput -- kses'd.
	}
}
