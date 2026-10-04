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
class LS_Widget_Sidebar_Tags extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-tags';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS ابر برچسب‌ها (سایدبار)', 'larijani' );
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
		$this->section( 'sec_main', __( 'محتوا', 'larijani' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani' ), 'کلیدواژه‌های فنی' );
		$this->ctl( 'count', 'number', __( 'تعداد', 'larijani' ), 12 );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		ls_render_tag_cloud(
			array(
				'title' => $s['title'],
				'count' => $s['count'],
			)
		);
	}
}
