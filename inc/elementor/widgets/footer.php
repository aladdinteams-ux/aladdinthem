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
class LS_Widget_Footer extends LS_Widget_Header {
	/** @return string */
	public function get_name() {
		return 'ls-footer';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS فوتر سایت', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-footer';
	}

	/** Controls. */
	protected function register_controls() {
		$hint  = __( 'خالی = خواندن از سفارشی‌سازی › تنظیمات قالب لاریجانی', 'larijani' );
		$menus = $this->menu_options();
		$menus = array_merge(
			array(
				'footer_quick'      => __( 'جایگاه «فوتر – دسترسی سریع»', 'larijani' ),
				'footer_categories' => __( 'جایگاه «فوتر – دسته‌بندی تجهیزات»', 'larijani' ),
				'footer_bottom'     => __( 'جایگاه «فوتر – لینک‌های پایین»', 'larijani' ),
			),
			$menus
		);

		$this->section( 'sec_brand', __( 'برند و معرفی', 'larijani' ) );
		$this->ctl( 'logo', 'media', __( 'لوگو', 'larijani' ), array( 'url' => '' ) );
		$this->ctl( 'brand', 'text', __( 'نام برند', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'tagline', 'text', __( 'شعار', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'about', 'textarea', __( 'متن معرفی', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'hours', 'text', __( 'ساعات کاری', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'show_social', 'switch', __( 'آیکون شبکه‌های اجتماعی', 'larijani' ), 'yes' );
		$this->end();

		$this->section( 'sec_cols', __( 'ستون‌های منو', 'larijani' ) );
		$this->ctl( 'col1_title', 'text', __( 'عنوان ستون اول', 'larijani' ), '' );
		$this->ctl( 'col1_menu', 'select', __( 'منوی ستون اول', 'larijani' ), 'footer_quick', array( 'options' => $menus ) );
		$this->ctl( 'col2_title', 'text', __( 'عنوان ستون دوم', 'larijani' ), '' );
		$this->ctl( 'col2_menu', 'select', __( 'منوی ستون دوم', 'larijani' ), 'footer_categories', array( 'options' => $menus ) );
		$this->ctl( 'col3_title', 'text', __( 'عنوان ستون تماس', 'larijani' ), '' );
		$this->end();

		$this->section( 'sec_contact', __( 'تماس و کپی‌رایت', 'larijani' ) );
		$this->ctl( 'address', 'textarea', __( 'آدرس', 'larijani' ), '', array( 'description' => $hint ) );
		$this->ctl( 'phone_1', 'text', __( 'تلفن اول', 'larijani' ), '' );
		$this->ctl( 'phone_2', 'text', __( 'تلفن دوم', 'larijani' ), '' );
		$this->ctl( 'email', 'text', __( 'ایمیل', 'larijani' ), '' );
		$this->ctl( 'copyright', 'text', __( 'کپی‌رایت', 'larijani' ), '' );
		$this->ctl( 'bottom_menu', 'select', __( 'منوی پایین', 'larijani' ), 'footer_bottom', array( 'options' => $menus ) );
		$this->ctl( 'bg_image', 'media', __( 'تصویر پس‌زمینه', 'larijani' ), array( 'url' => '' ) );
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
		ls_render_site_footer( $args );
	}
}
