<?php
/**
 * Widget: LS خبرنامه پیامکی (سایدبار).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_Sidebar_Newsletter.
 */
class Larijani_Widget_Sidebar_Newsletter extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-newsletter';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS خبرنامه پیامکی (سایدبار)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-mail';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani-stone' ), 'پیامک و خبرنامه عیب‌یابی فرمولاسیون' );
		$this->ctl( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'bell-fill' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'نکات هفتگی حل مسائل کارگاهی (ترک‌خوردگی، چسبیدن به قالب، دیرگیر شدن بتن در سرما) مستقیماً به موبایل شما ارسال می‌شود.' );
		$this->ctl( 'placeholder', 'text', __( 'راهنمای فیلد', 'larijani-stone' ), 'شماره تماس همراه (مثال: ۰۹۱۲۳۴۵۶۷۸۹)' );
		$this->ctl( 'button', 'text', __( 'متن دکمه', 'larijani-stone' ), 'عضویت رایگان در شبکه کارگاهی' );
		$this->ctl( 'note', 'text', __( 'یادداشت', 'larijani-stone' ), 'بدون ارسال پیام‌های تبلیغاتی تکراری' );
		$this->ctl( 'success', 'text', __( 'پیام موفقیت', 'larijani-stone' ), 'شماره شما با موفقیت برای دریافت پیامک‌های فنی ثبت شد.' );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		larijani_render_newsletter(
			array(
				'title' => $s['title'],
				'icon' => $s['icon'],
				'desc' => $s['desc'],
				'placeholder' => $s['placeholder'],
				'button' => $s['button'],
				'note' => $s['note'],
				'success' => $s['success'],
			)
		);
	}
}
