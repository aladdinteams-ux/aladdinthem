<?php
/**
 * Widget: Site footer.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Footer widget.
 */
class Larijani_Widget_Footer extends Larijani_Widget_Header {
	/** @return string */
	public function get_name() {
		return 'ls-footer';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS فوتر سایت', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-footer';
	}

	/** Controls. */
	protected function register_controls() {
		$hint  = __( 'خالی = خواندن از سفارشی‌سازی › تنظیمات قالب لاریجانی', 'larijani-stone' );
		$menus = $this->menu_options();
		$menus = array_merge(
			array(
				'footer_quick'      => __( 'جایگاه «فوتر – دسترسی سریع»', 'larijani-stone' ),
				'footer_categories' => __( 'جایگاه «فوتر – دسته‌بندی تجهیزات»', 'larijani-stone' ),
				'footer_bottom'     => __( 'جایگاه «فوتر – لینک‌های پایین»', 'larijani-stone' ),
			),
			$menus
		);

		$this->section( 'sec_brand', __( 'برند و معرفی', 'larijani-stone' ) );
		$this->ctl( 'logo', 'media', __( 'لوگو', 'larijani-stone' ), array( 'url' => '' ) );
		$this->ctl( 'brand', 'text', __( 'نام برند', 'larijani-stone' ), '', array( 'description' => $hint ) );
		$this->ctl( 'tagline', 'text', __( 'شعار', 'larijani-stone' ), '', array( 'description' => $hint ) );
		$this->ctl( 'about', 'textarea', __( 'متن معرفی', 'larijani-stone' ), '', array( 'description' => $hint ) );
		$this->ctl( 'hours', 'text', __( 'ساعات کاری', 'larijani-stone' ), '', array( 'description' => $hint ) );
		$this->ctl( 'show_social', 'switch', __( 'آیکون شبکه‌های اجتماعی', 'larijani-stone' ), 'yes' );
		$this->end();

		$this->section( 'sec_cols', __( 'ستون‌های منو', 'larijani-stone' ) );
		$this->ctl( 'col1_title', 'text', __( 'عنوان ستون اول', 'larijani-stone' ), '' );
		$this->ctl( 'col1_menu', 'select', __( 'منوی ستون اول', 'larijani-stone' ), 'footer_quick', array( 'options' => $menus ) );
		$this->ctl( 'col2_title', 'text', __( 'عنوان ستون دوم', 'larijani-stone' ), '' );
		$this->ctl( 'col2_menu', 'select', __( 'منوی ستون دوم', 'larijani-stone' ), 'footer_categories', array( 'options' => $menus ) );
		$this->ctl( 'col3_title', 'text', __( 'عنوان ستون تماس', 'larijani-stone' ), '' );
		$this->end();

		$this->section( 'sec_contact', __( 'تماس و کپی‌رایت', 'larijani-stone' ) );
		$this->ctl( 'address', 'textarea', __( 'آدرس', 'larijani-stone' ), '', array( 'description' => $hint ) );
		$this->ctl( 'phone_1', 'text', __( 'تلفن اول', 'larijani-stone' ), '' );
		$this->ctl( 'phone_2', 'text', __( 'تلفن دوم', 'larijani-stone' ), '' );
		$this->ctl( 'email', 'text', __( 'ایمیل', 'larijani-stone' ), '' );
		$this->ctl( 'copyright', 'text', __( 'کپی‌رایت', 'larijani-stone' ), '' );
		$this->ctl( 'bottom_menu', 'select', __( 'منوی پایین', 'larijani-stone' ), 'footer_bottom', array( 'options' => $menus ) );
		$this->ctl( 'bg_image', 'media', __( 'تصویر پس‌زمینه', 'larijani-stone' ), array( 'url' => '' ) );
		$this->end();

		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$keys = array( 'brand', 'tagline', 'about', 'hours', 'col1_title', 'col1_menu', 'col2_title', 'col2_menu', 'col3_title', 'address', 'phone_1', 'phone_2', 'email', 'copyright', 'bottom_menu' );
		$args = array();
		foreach ( $keys as $k ) {
			$args[ $k ] = $s[ $k ] ?? '';
		}
		$args['logo']        = ! empty( $s['logo']['url'] ) ? $s['logo'] : null;
		$args['bg_image']    = ! empty( $s['bg_image']['url'] ) ? $s['bg_image'] : '';
		$args['show_social'] = $this->on( $s, 'show_social' ) ? 'yes' : 'no';
		larijani_render_site_footer( $args );
	}
}
