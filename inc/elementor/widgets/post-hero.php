<?php
/**
 * Widget: Single post header (breadcrumb, pills, title, author, share, image, metrics).
 * For Theme Builder "Single Post" templates.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post hero widget.
 */
class LS_Widget_Post_Hero extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-post-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS سربرگ نوشته (تیتر، نویسنده، تصویر)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-post-title';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'نمایش', 'larijani' ) );
		$this->ctl( 'show_breadcrumb', 'switch', __( 'مسیر صفحه', 'larijani' ), 'yes' );
		$this->ctl( 'show_pills', 'switch', __( 'برچسب‌های دسته/زمان مطالعه/بازدید', 'larijani' ), 'yes' );
		$this->ctl( 'show_author', 'switch', __( 'نویسنده', 'larijani' ), 'yes' );
		$this->ctl( 'author_role', 'text', __( 'سمت نویسنده (خالی = بیوگرافی کاربر)', 'larijani' ), 'مدیر ارشد فنی و مهندسی مواد' );
		$this->ctl( 'show_share', 'switch', __( 'دکمه‌های اشتراک', 'larijani' ), 'yes' );
		$this->ctl( 'show_image', 'switch', __( 'تصویر شاخص', 'larijani' ), 'yes' );
		$this->ctl( 'caption', 'text', __( 'کپشن تصویر (خالی = از تنظیمات نوشته)', 'larijani' ), '' );
		$this->ctl( 'image_badge', 'text', __( 'برچسب تصویر (خالی = از تنظیمات نوشته)', 'larijani' ), '' );
		$this->end();

		$this->section( 'sec_metrics', __( 'شاخص‌های فنی (اختیاری)', 'larijani' ) );
		$this->rep(
			'metrics',
			__( 'شاخص‌ها', 'larijani' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'speedometer2' ),
				array( 'tone', 'select', __( 'رنگ آیکون', 'larijani' ), 'primary', array( 'options' => ls_tone_options() ) ),
				array( 'value', 'text', __( 'مقدار', 'larijani' ), '' ),
				array( 'value_tone', 'select', __( 'رنگ مقدار', 'larijani' ), 'dark', array( 'options' => array( 'dark' => __( 'تیره', 'larijani' ), 'emerald' => __( 'زمردی', 'larijani' ) ) ) ),
				array( 'unit', 'text', __( 'واحد', 'larijani' ), '' ),
				array( 'note', 'text', __( 'یادداشت', 'larijani' ), '' ),
				array( 'note_icon', 'icon', __( 'آیکون یادداشت', 'larijani' ), 'check2' ),
				array( 'note_tone', 'select', __( 'رنگ یادداشت', 'larijani' ), 'muted', array( 'options' => array( 'muted' => __( 'خاکستری', 'larijani' ), 'emerald' => __( 'زمردی', 'larijani' ) ) ) ),
			),
			array(),
			'{{{ label }}}'
		);
		$this->end();
		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$keys = array( 'show_breadcrumb', 'show_pills', 'show_author', 'show_share', 'show_image' );
		$args = array(
			'author_role' => $s['author_role'],
			'caption'     => $s['caption'],
			'image_badge' => $s['image_badge'],
			'metrics'     => $s['metrics'],
		);
		foreach ( $keys as $k ) {
			$args[ $k ] = $this->on( $s, $k ) ? 'yes' : 'no';
		}
		ls_render_post_hero( $args );
	}
}
