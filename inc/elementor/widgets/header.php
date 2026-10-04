<?php
/**
 * Widget: Site header (logo, mega menu, search, CTA, mobile drawer).
 * Designed for Elementor Pro Theme Builder "Header" templates or the theme's
 * free theme builder (Customizer › تم‌بیلدر).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Header widget.
 */
class LS_Widget_Header extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-header';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS هدر سایت', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-header';
	}

	/**
	 * Menu options.
	 *
	 * @return array
	 */
	protected function menu_options() {
		$opts = array(
			'primary'           => __( 'جایگاه «منوی اصلی»', 'larijani' ),
			'drawer_categories' => __( 'جایگاه «دسته‌بندی‌های منوی موبایل»', 'larijani' ),
		);
		foreach ( wp_get_nav_menus() as $m ) {
			$opts[ (string) $m->term_id ] = sprintf( /* translators: %s menu */ __( 'منو: %s', 'larijani' ), $m->name );
		}
		return $opts;
	}

	/** Controls. */
	protected function register_controls() {
		$hint = __( 'خالی = خواندن از سفارشی‌سازی › تنظیمات قالب لاریجانی', 'larijani' );

		$this->section( 'sec_topbar', __( 'نوار بالا', 'larijani' ) );
		$this->ctl( 'show_topbar', 'switch', __( 'نمایش نوار اطلاعات (دسکتاپ)', 'larijani' ), 'yes' );
		$this->ctl( 'address', 'text', __( 'آدرس', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'hours', 'text', __( 'ساعات کاری', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'phone', 'text', __( 'تلفن', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'whatsapp_label', 'text', __( 'متن لینک واتساپ', 'larijani' ), '' );
		$this->end();

		$this->section( 'sec_brand', __( 'لوگو و نام برند', 'larijani' ) );
		$this->ctl( 'logo', 'media', __( 'لوگو', 'larijani' ), array( 'url' => '' ), array( 'description' => __( 'خالی = لوگوی سایت (سفارشی‌سازی › هویت سایت)', 'larijani' ) ) );
		$this->ctl( 'brand', 'text', __( 'نام برند', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'tagline', 'text', __( 'شعار', 'larijani' ), '', array( 'description' => $hint ) );
		$this->end();

		$this->section( 'sec_nav', __( 'منو و دکمه‌ها', 'larijani' ) );
		$this->ctl( 'menu', 'select', __( 'منوی اصلی', 'larijani' ), 'primary', array( 'options' => $this->menu_options(), 'description' => __( 'زیرمنوها به صورت منوی کشویی نمایش داده می‌شوند.', 'larijani' ) ) );
		$this->ctl( 'show_search', 'switch', __( 'دکمه جستجو', 'larijani' ), 'yes' );
		$this->ctl( 'cta_text', 'text', __( 'متن دکمه اصلی', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'cta_short', 'text', __( 'متن کوتاه دکمه (تبلت)', 'larijani' ), '' );
		$this->ctl( 'cta_link', 'url', __( 'لینک دکمه اصلی', 'larijani' ), array( 'url' => '' ), array( 'description' => __( 'خالی = تماس تلفنی', 'larijani' ) ) );
		$this->ctl( 'sticky', 'switch', __( 'هدر چسبان', 'larijani' ), 'yes' );
		$this->end();

		$this->section( 'sec_drawer', __( 'منوی موبایل', 'larijani' ) );
		$this->ctl( 'drawer_main', 'text', __( 'عنوان بخش اصلی', 'larijani' ), __( 'بخش‌های اصلی', 'larijani' ) );
		$this->ctl( 'drawer_cats', 'text', __( 'عنوان دسته‌بندی‌ها', 'larijani' ), __( 'دسته‌بندی تجهیزات', 'larijani' ) );
		$this->ctl( 'drawer_cats_menu', 'select', __( 'منوی دسته‌بندی‌ها', 'larijani' ), 'drawer_categories', array( 'options' => $this->menu_options() ) );
		$this->ctl( 'drawer_whatsapp', 'text', __( 'متن دکمه واتساپ', 'larijani' ), __( 'پیام در واتساپ', 'larijani' ) );
		$this->end();

		$this->style_controls( array( 'typography' => false, 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$args = array(
			'show_topbar'      => $this->on( $s, 'show_topbar' ) ? 'yes' : 'no',
			'address'          => $s['address'],
			'hours'            => $s['hours'],
			'phone'            => $s['phone'],
			'whatsapp_label'   => $s['whatsapp_label'],
			'logo'             => ! empty( $s['logo']['url'] ) ? $s['logo'] : null,
			'brand'            => $s['brand'],
			'tagline'          => $s['tagline'],
			'menu'             => $s['menu'],
			'show_search'      => $this->on( $s, 'show_search' ) ? 'yes' : 'no',
			'cta_text'         => $s['cta_text'],
			'cta_short'        => $s['cta_short'],
			'cta_link'         => ! empty( $s['cta_link']['url'] ) ? $s['cta_link'] : '',
			'sticky'           => $this->on( $s, 'sticky' ) ? 'yes' : 'no',
			'drawer_main'      => $s['drawer_main'],
			'drawer_cats'      => $s['drawer_cats'],
			'drawer_cats_menu' => $s['drawer_cats_menu'],
			'drawer_whatsapp'  => $s['drawer_whatsapp'],
		);
		ls_render_site_header( $args );
	}
}
