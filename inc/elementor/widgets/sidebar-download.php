<?php
/**
 * Widget: LS کارت دانلود تیره (سایدبار).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_Sidebar_Download.
 */
class Larijani_Widget_Sidebar_Download extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-download';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS کارت دانلود تیره (سایدبار)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-download-button';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'file-earmark-pdf-fill' );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani-stone' ), 'ویرایش زمستان ۱۴۰۴' );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani-stone' ), 'هندبوک جامع جداول اختلاط بتن سمنت‌پلاست' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'شامل ۱۲ فرمول آزمون‌شده آزمایشگاهی بر اساس نوع سیمان، فصول سرد و گرم، و جداول عیار پیگمنت‌های معدنی اکسید آهن.' );
		$this->ctl( 'button', 'text', __( 'متن دکمه', 'larijani-stone' ), 'دانلود مستقیم فایل PDF (۱۴ مگابایت)' );
		$this->ctl( 'link', 'url', __( 'فایل / لینک', 'larijani-stone' ), '#' );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		larijani_render_download_card(
			array(
				'icon' => $s['icon'],
				'badge' => $s['badge'],
				'title' => $s['title'],
				'desc' => $s['desc'],
				'button' => $s['button'],
				'link' => $s['link'],
			)
		);
	}
}
