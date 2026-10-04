<?php
/**
 * Elementor dynamic tags: site contact information & post meta.
 *
 * Use them in any Elementor text / link field (the "stack" icon) so phone
 * numbers, address etc. are edited once in Customizer › تنظیمات قالب لاریجانی.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

/**
 * Contact info options.
 *
 * @return array
 */
function ls_dynamic_contact_options() {
	return array(
		'brand_name'     => __( 'نام برند', 'larijani' ),
		'brand_tagline'  => __( 'شعار', 'larijani' ),
		'phone_1'        => __( 'تلفن اول', 'larijani' ),
		'phone_1_fa'     => __( 'تلفن اول (فارسی، فاصله‌دار)', 'larijani' ),
		'phone_2'        => __( 'تلفن دوم', 'larijani' ),
		'phone_2_fa'     => __( 'تلفن دوم (فارسی، فاصله‌دار)', 'larijani' ),
		'email'          => __( 'ایمیل', 'larijani' ),
		'address'        => __( 'آدرس کامل', 'larijani' ),
		'address_short'  => __( 'آدرس کوتاه', 'larijani' ),
		'hours'          => __( 'ساعات کاری', 'larijani' ),
		'hours_full'     => __( 'ساعات کاری کامل', 'larijani' ),
		'footer_about'   => __( 'متن درباره ما', 'larijani' ),
	);
}

/**
 * Text tag: contact info.
 */
class LS_Tag_Contact_Text extends Tag {
	/** @return string */
	public function get_name() {
		return 'ls-contact-text';
	}
	/** @return string */
	public function get_title() {
		return __( 'اطلاعات تماس سایت', 'larijani' );
	}
	/** @return string */
	public function get_group() {
		return 'larijani';
	}
	/** @return array */
	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}
	/** Controls. */
	protected function register_controls() {
		$this->add_control(
			'field',
			array(
				'label'   => __( 'فیلد', 'larijani' ),
				'type'    => Controls_Manager::SELECT,
				'options' => ls_dynamic_contact_options(),
				'default' => 'phone_1_fa',
			)
		);
	}
	/** Render. */
	public function render() {
		$field = $this->get_settings( 'field' );
		if ( 'phone_1_fa' === $field || 'phone_2_fa' === $field ) {
			echo esc_html( ls_phone_display( ls_opt( str_replace( '_fa', '', $field ) ) ) );
			return;
		}
		echo esc_html( ls_opt( $field ) );
	}
}

/**
 * URL tag: tel / WhatsApp / e-mail / social links.
 */
class LS_Tag_Contact_URL extends Data_Tag {
	/** @return string */
	public function get_name() {
		return 'ls-contact-url';
	}
	/** @return string */
	public function get_title() {
		return __( 'لینک تماس سایت', 'larijani' );
	}
	/** @return string */
	public function get_group() {
		return 'larijani';
	}
	/** @return array */
	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}
	/** Controls. */
	protected function register_controls() {
		$this->add_control(
			'field',
			array(
				'label'   => __( 'لینک', 'larijani' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'tel_1'     => __( 'تماس با تلفن اول', 'larijani' ),
					'tel_2'     => __( 'تماس با تلفن دوم', 'larijani' ),
					'whatsapp'  => __( 'واتساپ', 'larijani' ),
					'email'     => __( 'ایمیل', 'larijani' ),
					'instagram' => __( 'اینستاگرام', 'larijani' ),
					'telegram'  => __( 'تلگرام', 'larijani' ),
					'eitaa'     => __( 'ایتا', 'larijani' ),
					'aparat'    => __( 'آپارات', 'larijani' ),
					'linkedin'  => __( 'لینکدین', 'larijani' ),
				),
				'default' => 'tel_1',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'     => __( 'متن پیش‌فرض واتساپ', 'larijani' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array( 'field' => 'whatsapp' ),
			)
		);
	}
	/**
	 * Value.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	public function get_value( array $options = array() ) {
		$field = $this->get_settings( 'field' );
		switch ( $field ) {
			case 'tel_1':
				return ls_tel( ls_opt( 'phone_1' ) );
			case 'tel_2':
				return ls_tel( ls_opt( 'phone_2' ) );
			case 'whatsapp':
				return ls_whatsapp_url( '', (string) $this->get_settings( 'text' ) );
			case 'email':
				return 'mailto:' . ls_opt( 'email' );
			default:
				return (string) ls_opt( $field );
		}
	}
}

/**
 * Text tag: post info (reading time, views, primary category, author role).
 */
class LS_Tag_Post_Meta_Text extends Tag {
	/** @return string */
	public function get_name() {
		return 'ls-post-meta';
	}
	/** @return string */
	public function get_title() {
		return __( 'اطلاعات نوشته (لاریجانی)', 'larijani' );
	}
	/** @return string */
	public function get_group() {
		return 'larijani';
	}
	/** @return array */
	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}
	/** Controls. */
	protected function register_controls() {
		$this->add_control(
			'field',
			array(
				'label'   => __( 'فیلد', 'larijani' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'reading'  => __( 'زمان مطالعه', 'larijani' ),
					'views'    => __( 'تعداد بازدید', 'larijani' ),
					'category' => __( 'دسته اصلی', 'larijani' ),
					'date'     => __( 'تاریخ انتشار', 'larijani' ),
					'comments' => __( 'تعداد دیدگاه', 'larijani' ),
				),
				'default' => 'reading',
			)
		);
	}
	/** Render. */
	public function render() {
		switch ( $this->get_settings( 'field' ) ) {
			case 'reading':
				/* translators: %s minutes */
				echo esc_html( sprintf( __( '%s دقیقه مطالعه', 'larijani' ), ls_fa_num( ls_reading_time() ) ) );
				break;
			case 'views':
				echo esc_html( ls_fa_number_format( ls_post_views() ) );
				break;
			case 'category':
				$c = ls_primary_category();
				echo esc_html( $c ? $c->name : '' );
				break;
			case 'date':
				echo esc_html( ls_post_date() );
				break;
			case 'comments':
				echo esc_html( ls_fa_num( get_comments_number() ) );
				break;
		}
	}
}
