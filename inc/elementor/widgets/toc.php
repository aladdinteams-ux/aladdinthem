<?php
/**
 * Widget: LS فهرست مطالب مقاله.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_TOC.
 */
class LS_Widget_TOC extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-toc';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS فهرست مطالب مقاله', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-table-of-contents';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani' ), 'فهرست عناوین مقاله' );
		$this->ctl( 'selector', 'text', __( 'انتخابگر سرتیترها (CSS)', 'larijani' ), '.ls-article h2' );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		ls_render_toc(
			array(
				'title' => $s['title'],
				'selector' => $s['selector'],
			)
		);
	}
}
