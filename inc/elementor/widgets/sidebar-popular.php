<?php
/**
 * Widget: LS مقالات پرطرفدار (سایدبار).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_Sidebar_Popular.
 */
class LS_Widget_Sidebar_Popular extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-popular';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS مقالات پرطرفدار (سایدبار)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-bullet-list';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani' ), 'مباحث پرطرفدار کارگاه‌ها' );
		$this->ctl( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'fire' );
		$this->ctl( 'count', 'number', __( 'تعداد', 'larijani' ), 4 );
		$this->ctl( 'orderby', 'select', __( 'مرتب‌سازی', 'larijani' ), 'views', array( 'options' => array( 'views' => __( 'پربازدیدترین', 'larijani' ), 'comments' => __( 'پربحث‌ترین', 'larijani' ), 'date' => __( 'جدیدترین', 'larijani' ) ) ) );
		$this->ctl( 'style', 'select', __( 'سبک', 'larijani' ), 'card', array( 'options' => array( 'card' => __( 'با تعداد بازدید', 'larijani' ), 'compact' => __( 'فشرده', 'larijani' ) ) ) );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		ls_render_popular_posts(
			array(
				'title' => $s['title'],
				'icon' => $s['icon'],
				'count' => $s['count'],
				'orderby' => $s['orderby'],
				'style' => $s['style'],
			)
		);
	}
}
