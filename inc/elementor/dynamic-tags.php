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
function larijani_dynamic_contact_options() {
	return array(
		'brand_name'     => __( 'نام برند', 'larijani-stone' ),
		'brand_tagline'  => __( 'شعار', 'larijani-stone' ),
		'phone_1'        => __( 'تلفن اول', 'larijani-stone' ),
		'phone_1_fa'     => __( 'تلفن اول (فارسی، فاصله‌دار)', 'larijani-stone' ),
		'phone_2'        => __( 'تلفن دوم', 'larijani-stone' ),
		'phone_2_fa'     => __( 'تلفن دوم (فارسی، فاصله‌دار)', 'larijani-stone' ),
		'email'          => __( 'ایمیل', 'larijani-stone' ),
		'address'        => __( 'آدرس کامل', 'larijani-stone' ),
		'address_short'  => __( 'آدرس کوتاه', 'larijani-stone' ),
		'hours'          => __( 'ساعات کاری', 'larijani-stone' ),
		'hours_full'     => __( 'ساعات کاری کامل', 'larijani-stone' ),
		'footer_about'   => __( 'متن درباره ما', 'larijani-stone' ),
	);
}

/**
 * Text tag: contact info.
 */
class Larijani_Tag_Contact_Text extends Tag {
	/** @return string */
	public function get_name() {
		return 'ls-contact-text';
	}
	/** @return string */
	public function get_title() {
		return __( 'اطلاعات تماس سایت', 'larijani-stone' );
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
				'label'   => __( 'فیلد', 'larijani-stone' ),
				'type'    => Controls_Manager::SELECT,
				'options' => larijani_dynamic_contact_options(),
				'default' => 'phone_1_fa',
			)
		);
	}
	/** Render. */
	public function render() {
		$field = $this->get_settings( 'field' );
		if ( 'phone_1_fa' === $field || 'phone_2_fa' === $field ) {
			echo esc_html( larijani_phone_display( larijani_opt( str_replace( '_fa', '', $field ) ) ) );
			return;
		}
		echo esc_html( larijani_opt( $field ) );
	}
}

/**
 * URL tag: tel / WhatsApp / e-mail / social links.
 */
class Larijani_Tag_Contact_URL extends Data_Tag {
	/** @return string */
	public function get_name() {
		return 'ls-contact-url';
	}
	/** @return string */
	public function get_title() {
		return __( 'لینک تماس سایت', 'larijani-stone' );
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
				'label'   => __( 'لینک', 'larijani-stone' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'tel_1'     => __( 'تماس با تلفن اول', 'larijani-stone' ),
					'tel_2'     => __( 'تماس با تلفن دوم', 'larijani-stone' ),
					'whatsapp'  => __( 'واتساپ', 'larijani-stone' ),
					'email'     => __( 'ایمیل', 'larijani-stone' ),
					'instagram' => __( 'اینستاگرام', 'larijani-stone' ),
					'telegram'  => __( 'تلگرام', 'larijani-stone' ),
					'eitaa'     => __( 'ایتا', 'larijani-stone' ),
					'aparat'    => __( 'آپارات', 'larijani-stone' ),
					'linkedin'  => __( 'لینکدین', 'larijani-stone' ),
				),
				'default' => 'tel_1',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'     => __( 'متن پیش‌فرض واتساپ', 'larijani-stone' ),
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
				return larijani_tel( larijani_opt( 'phone_1' ) );
			case 'tel_2':
				return larijani_tel( larijani_opt( 'phone_2' ) );
			case 'whatsapp':
				return larijani_whatsapp_url( '', (string) $this->get_settings( 'text' ) );
			case 'email':
				return 'mailto:' . larijani_opt( 'email' );
			default:
				return (string) larijani_opt( $field );
		}
	}
}

/**
 * Text tag: post info (reading time, views, primary category, author role).
 */
class Larijani_Tag_Post_Meta_Text extends Tag {
	/** @return string */
	public function get_name() {
		return 'ls-post-meta';
	}
	/** @return string */
	public function get_title() {
		return __( 'اطلاعات نوشته (لاریجانی)', 'larijani-stone' );
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
				'label'   => __( 'فیلد', 'larijani-stone' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'reading'  => __( 'زمان مطالعه', 'larijani-stone' ),
					'views'    => __( 'تعداد بازدید', 'larijani-stone' ),
					'category' => __( 'دسته اصلی', 'larijani-stone' ),
					'date'     => __( 'تاریخ انتشار', 'larijani-stone' ),
					'comments' => __( 'تعداد دیدگاه', 'larijani-stone' ),
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
				echo esc_html( sprintf( __( '%s دقیقه مطالعه', 'larijani-stone' ), larijani_fa_num( larijani_reading_time() ) ) );
				break;
			case 'views':
				echo esc_html( larijani_fa_number_format( larijani_post_views() ) );
				break;
			case 'category':
				$c = larijani_primary_category();
				echo esc_html( $c ? $c->name : '' );
				break;
			case 'date':
				echo esc_html( larijani_post_date() );
				break;
			case 'comments':
				echo esc_html( larijani_fa_num( get_comments_number() ) );
				break;
		}
	}
}
