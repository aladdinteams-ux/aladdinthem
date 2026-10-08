<?php
/**
 * Widget: Featured article card.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Featured post widget.
 */
class Larijani_Widget_Featured_Post extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-featured-post';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS مقاله ویژه', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-post-content';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'مقاله', 'larijani-stone' ) );
		$this->ctl( 'post_id', 'number', __( 'شناسه نوشته (خالی = نوشته «ویژه تحریریه» یا آخرین نوشته)', 'larijani-stone' ), '' );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani-stone' ), 'مقاله ویژه تحریریه' );
		$this->ctl( 'author_role', 'text', __( 'سمت نویسنده', 'larijani-stone' ), 'سرپرست دپارتمان تحقیق و توسعه' );
		$this->ctl( 'button_text', 'text', __( 'متن دکمه', 'larijani-stone' ), 'مطالعه مقاله کامل' );
		$this->rep(
			'metrics',
			__( 'شاخص‌های فنی', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'value', 'text', __( 'مقدار', 'larijani-stone' ), '' ),
				array( 'unit', 'text', __( 'واحد', 'larijani-stone' ), '' ),
				array( 'tone', 'select', __( 'رنگ', 'larijani-stone' ), 'primary', array( 'options' => array( 'primary' => __( 'سبز برند', 'larijani-stone' ), 'emerald' => __( 'زمردی', 'larijani-stone' ), 'dark' => __( 'تیره', 'larijani-stone' ) ) ) ),
			),
			array(
				array( 'label' => 'مقاومت فشاری ۲۸ روزه', 'value' => '75+', 'unit' => 'مگاپاسکال (MPa)', 'tone' => 'primary' ),
				array( 'label' => 'میزان جذب آب نهایی', 'value' => '< 0.5', 'unit' => 'درصد وزنی', 'tone' => 'emerald' ),
			),
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
		echo '<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 mb-16 w-full">';
		larijani_render_featured_post(
			array(
				'post_id'     => (int) $s['post_id'],
				'badge'       => $s['badge'],
				'metrics'     => $s['metrics'],
				'author_role' => $s['author_role'],
				'button_text' => $s['button_text'],
				'fallback'    => array(
					'image'       => larijani_demo_image( 'archive_featured' ),
					'title'       => 'راهنمای جامع فرمولاسیون سنگ مصنوعی با رزین پلی‌کربوکسیلاتی LS-500 و روش‌های حذف کامل حباب‌های میکروسکوپی',
					'excerpt'     => 'بررسی اثر پیوند زنجیره‌های اتر جانبی بر روانی دوغاب سیمانی، زمان‌بندی دقیق ویبراسیون جهت خروج حباب‌های به تله افتاده پشت قالب‌های ABS، و روش‌های بهینه‌سازی نسبت آب به سیمان تا زیر ۰.۲۸ با حفظ اسلامپ استاندارد.',
					'category'    => 'دانشنامه فرمولاسیون',
					'date'        => '۱۸ بهمن ۱۴۰۳',
					'reading'     => 14,
					'views'       => 3420,
					'url'         => '#',
					'author'      => 'مهندس مسعود لاریجانی',
					'author_role' => $s['author_role'],
				),
			)
		);
		echo '</section>';
	}
}
