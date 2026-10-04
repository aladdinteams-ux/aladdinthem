<?php
/**
 * Widget: LS باکس معرفی (سایدبار).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * LS_Widget_Sidebar_Promo.
 */
class LS_Widget_Sidebar_Promo extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-sidebar-promo';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS باکس معرفی (سایدبار)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-info-box';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani' ) );
		$this->ctl( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'journal-bookmark' );
		$this->ctl( 'title', 'text', __( 'عنوان', 'larijani' ), 'هندبوک جامع ۳۲ فرمول تست شده' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'شامل کاتالوگ جامع اختلاط برای تولید سنگ پله، جدول، کفپوش‌های پرتردد و سنگ‌های آنتیک دکوراتیو داخلی.' );
		$this->ctl( 'link_text', 'text', __( 'متن لینک', 'larijani' ), 'درخواست نسخه چاپی یا PDF' );
		$this->ctl( 'link', 'url', __( 'لینک', 'larijani' ), '#' );
		$this->ctl( 'tone', 'select', __( 'رنگ', 'larijani' ), 'sage', array( 'options' => array( 'sage' => __( 'سبز ملایم', 'larijani' ), 'light' => __( 'آبی-خاکستری', 'larijani' ) ) ) );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		ls_render_promo_box(
			array(
				'icon' => $s['icon'],
				'title' => $s['title'],
				'desc' => $s['desc'],
				'link_text' => $s['link_text'],
				'link' => $s['link'],
				'tone' => $s['tone'],
			)
		);
	}
}
