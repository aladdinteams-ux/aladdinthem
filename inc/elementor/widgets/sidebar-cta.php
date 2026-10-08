<?php
/**
 * Widget: LS کارت پشتیبانی (سایدبار).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_Sidebar_CTA.
 */
class Larijani_Widget_Sidebar_CTA extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-cta';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS کارت پشتیبانی (سایدبار)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-headphones';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'headset' );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani-stone' ), 'نیاز به اصلاح فرمولاسیون یا رفع حباب در خط تولید دارید؟' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'مشاوره مستقیم با مهندس مسعود لاریجانی و ارسال نمونه رایگان رزین LS-500 برای تست در کارگاه شما.' );
		$this->ctl( 'button_1', 'text', __( 'دکمه اول (خالی = شماره تلفن)', 'larijani-stone' ), '' );
		$this->ctl( 'link_1', 'url', __( 'لینک دکمه اول', 'larijani-stone' ), '' );
		$this->ctl( 'button_2', 'text', __( 'دکمه دوم', 'larijani-stone' ), 'ارسال تصاویر قطعات معیوب در واتساپ' );
		$this->ctl( 'link_2', 'url', __( 'لینک دکمه دوم', 'larijani-stone' ), '' );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		larijani_render_sidebar_cta(
			array(
				'icon' => $s['icon'],
				'title' => $s['title'],
				'desc' => $s['desc'],
				'button_1' => $s['button_1'],
				'link_1' => $s['link_1'],
				'button_2' => $s['button_2'],
				'link_2' => $s['link_2'],
			)
		);
	}
}
