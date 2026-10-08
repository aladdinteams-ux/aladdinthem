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
class Larijani_Widget_Post_Hero extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-post-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS سربرگ نوشته (تیتر، نویسنده، تصویر)', 'larijani-stone' );
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
		$this->section( 'sec_main', __( 'نمایش', 'larijani-stone' ) );
		$this->ctl( 'show_breadcrumb', 'switch', __( 'مسیر صفحه', 'larijani-stone' ), 'yes' );
		$this->ctl( 'show_pills', 'switch', __( 'برچسب‌های دسته/زمان مطالعه/بازدید', 'larijani-stone' ), 'yes' );
		$this->ctl( 'show_author', 'switch', __( 'نویسنده', 'larijani-stone' ), 'yes' );
		$this->ctl( 'author_role', 'text', __( 'سمت نویسنده (خالی = بیوگرافی کاربر)', 'larijani-stone' ), 'مدیر ارشد فنی و مهندسی مواد' );
		$this->ctl( 'show_share', 'switch', __( 'دکمه‌های اشتراک', 'larijani-stone' ), 'yes' );
		$this->ctl( 'show_image', 'switch', __( 'تصویر شاخص', 'larijani-stone' ), 'yes' );
		$this->ctl( 'caption', 'text', __( 'کپشن تصویر (خالی = از تنظیمات نوشته)', 'larijani-stone' ), '' );
		$this->ctl( 'image_badge', 'text', __( 'برچسب تصویر (خالی = از تنظیمات نوشته)', 'larijani-stone' ), '' );
		$this->end();

		$this->section( 'sec_metrics', __( 'شاخص‌های فنی (اختیاری)', 'larijani-stone' ) );
		$this->rep(
			'metrics',
			__( 'شاخص‌ها', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'speedometer2' ),
				array( 'tone', 'select', __( 'رنگ آیکون', 'larijani-stone' ), 'primary', array( 'options' => larijani_tone_options() ) ),
				array( 'value', 'text', __( 'مقدار', 'larijani-stone' ), '' ),
				array( 'value_tone', 'select', __( 'رنگ مقدار', 'larijani-stone' ), 'dark', array( 'options' => array( 'dark' => __( 'تیره', 'larijani-stone' ), 'emerald' => __( 'زمردی', 'larijani-stone' ) ) ) ),
				array( 'unit', 'text', __( 'واحد', 'larijani-stone' ), '' ),
				array( 'note', 'text', __( 'یادداشت', 'larijani-stone' ), '' ),
				array( 'note_icon', 'icon', __( 'آیکون یادداشت', 'larijani-stone' ), 'check2' ),
				array( 'note_tone', 'select', __( 'رنگ یادداشت', 'larijani-stone' ), 'muted', array( 'options' => array( 'muted' => __( 'خاکستری', 'larijani-stone' ), 'emerald' => __( 'زمردی', 'larijani-stone' ) ) ) ),
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
		larijani_render_post_hero( $args );
	}
}
