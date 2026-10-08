<?php
/**
 * Widget: LS ابر برچسب‌ها (سایدبار).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_Sidebar_Tags.
 */
class Larijani_Widget_Sidebar_Tags extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-tags';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS ابر برچسب‌ها (سایدبار)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-tags';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani-stone' ), 'کلیدواژه‌های فنی' );
		$this->ctl( 'count', 'number', __( 'تعداد', 'larijani-stone' ), 12 );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		larijani_render_tag_cloud(
			array(
				'title' => $s['title'],
				'count' => $s['count'],
			)
		);
	}
}
