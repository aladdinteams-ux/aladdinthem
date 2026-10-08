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
class Larijani_Widget_Sidebar_Popular extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-popular';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS مقالات پرطرفدار (سایدبار)', 'larijani-stone' );
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
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani-stone' ), 'مباحث پرطرفدار کارگاه‌ها' );
		$this->ctl( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'fire' );
		$this->ctl( 'count', 'number', __( 'تعداد', 'larijani-stone' ), 4 );
		$this->ctl( 'orderby', 'select', __( 'مرتب‌سازی', 'larijani-stone' ), 'views', array( 'options' => array( 'views' => __( 'پربازدیدترین', 'larijani-stone' ), 'comments' => __( 'پربحث‌ترین', 'larijani-stone' ), 'date' => __( 'جدیدترین', 'larijani-stone' ) ) ) );
		$this->ctl( 'style', 'select', __( 'سبک', 'larijani-stone' ), 'card', array( 'options' => array( 'card' => __( 'با تعداد بازدید', 'larijani-stone' ), 'compact' => __( 'فشرده', 'larijani-stone' ) ) ) );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		larijani_render_popular_posts(
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
