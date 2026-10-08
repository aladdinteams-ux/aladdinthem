<?php
/**
 * Widget: Related posts.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Related posts widget.
 */
class Larijani_Widget_Related_Posts extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-related-posts';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS مقالات مرتبط', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-post-list';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani-stone' ), 'مقالات و فرمولاسیون‌های مرتبط کارگاهی' );
		$this->ctl( 'count', 'number', __( 'تعداد', 'larijani-stone' ), 3 );
		$this->ctl( 'link_text', 'text', __( 'متن لینک همه', 'larijani-stone' ), 'مشاهده همه' );
		$this->end();
		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		echo '<section class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-12 py-6">';
		larijani_render_related_posts( array( 'title' => $s['title'], 'count' => (int) $s['count'], 'link_text' => $s['link_text'] ) );
		echo '</section>';
	}
}
