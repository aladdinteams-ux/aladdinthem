<?php
/**
 * Widget: Full catalog (search, sorting, category chips, sidebar filters, product grid, pagination).
 * In WooCommerce mode it drives the real shop query (use it in Theme Builder "Product Archive").
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/products.php';

/**
 * Catalog widget.
 */
class LS_Widget_Catalog extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-catalog';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS کاتالوگ محصولات (فیلتر + جستجو)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-archive-posts';
	}
	/** @return array */
	public function get_keywords() {
		return array( 'shop', 'archive', 'catalog', 'woocommerce', 'فروشگاه' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_source', __( 'منبع محصولات', 'larijani' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani' ), 'auto', array( 'options' => array( 'auto' => __( 'خودکار (ووکامرس اگر فعال باشد)', 'larijani' ), 'woocommerce' => __( 'ووکامرس', 'larijani' ), 'manual' => __( 'کارت‌های دستی', 'larijani' ) ) ) );
		$this->ctl( 'use_main_query', 'select', __( 'کوئری ووکامرس', 'larijani' ), 'auto', array( 'options' => array( 'auto' => __( 'خودکار (در صفحه فروشگاه از کوئری اصلی)', 'larijani' ), 'yes' => __( 'همیشه کوئری اصلی صفحه', 'larijani' ), 'no' => __( 'کوئری سفارشی', 'larijani' ) ) ) );
		$this->ctl( 'posts_per_page', 'number', __( 'تعداد در صفحه (کوئری سفارشی)', 'larijani' ), 12 );
		$this->ctl( 'category', 'select', __( 'دسته (کوئری سفارشی)', 'larijani' ), '', array( 'options' => taxonomy_exists( 'product_cat' ) ? ls_term_options( 'product_cat' ) : array( '' => __( 'همه', 'larijani' ) ) ) );
		$this->ctl( 'columns', 'select', __( 'ستون‌ها در دسکتاپ', 'larijani' ), '3', array( 'options' => array( '2' => '۲', '3' => '۳', '4' => '۴' ) ) );
		$this->end();

		$this->section( 'sec_manual', __( 'کارت‌های دستی', 'larijani' ), 'content', array( 'source' => 'manual' ) );
		$this->rep(
			'chips',
			__( 'دسته‌های فیلتر', 'larijani' ),
			array(
				array( 'key', 'text', __( 'کلید (همان «کلید فیلتر» کارت‌ها)', 'larijani' ), '' ),
				array( 'label', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'bounding-box-circles' ),
			),
			array(
				array( 'key' => 'mold', 'label' => 'قالب‌های ABS و کامپوزیت', 'icon' => 'bounding-box-circles' ),
				array( 'key' => 'machinery', 'label' => 'ماشین‌آلات و میکسرها', 'icon' => 'cpu-fill' ),
				array( 'key' => 'chemical', 'label' => 'رزین و رنگدانه‌های صنعتی', 'icon' => 'droplet-half' ),
				array( 'key' => 'mortar', 'label' => 'ملات و روان‌کننده‌ها', 'icon' => 'layer-forward' ),
			),
			'{{{ label }}}'
		);
		$this->rep( 'items', __( 'محصولات', 'larijani' ), LS_Widget_Products::card_fields(), ls_demo_catalog(), '{{{ title }}}' );
		$this->rep(
			'filter_boxes',
			__( 'باکس‌های فیلتر سایدبار', 'larijani' ),
			array(
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'layers-half' ),
				array( 'options', 'textarea', __( 'گزینه‌ها (هر خط: عنوان|تعداد|کلید فیلتر)', 'larijani' ), '', array( 'rows' => 5 ) ),
			),
			array(
				array( 'title' => 'نوع متریال ساخت', 'icon' => 'layers-half', 'options' => "ABS نو کره‌ای درجه یک|۴|mold\nفایبرگلاس نشکن مقاوم|۱|mold\nفولاد صنعتی و ضدسایش ST52|۲|machinery\nپلیمر مایع و پودر صنعتی|۲|chemical" ),
				array( 'title' => 'کاربرد اجرایی', 'icon' => 'hammer', 'options' => "کف‌پوش و واش‌بتن|۲|mold\nنمای سه‌بعدی و دکوراتیو|۱|mold\nجدول و دورباغچه شهری|۱|mold\nصراحی و المان ویلایی|۱|mold\nتجهیز کامل کارگاه سنگ|۳|machinery" ),
			),
			'{{{ title }}}'
		);
		$this->end();

		$this->section( 'sec_labels', __( 'متن‌ها و سایدبار', 'larijani' ) );
		$this->ctl( 'search_placeholder', 'text', __( 'راهنمای جستجو', 'larijani' ), 'جستجو در نام قالب، ابعاد، میکسر یا مواد...' );
		$this->ctl( 'all_label', 'text', __( 'عنوان «همه»', 'larijani' ), 'همه محصولات' );
		$this->ctl(
			'layout',
			'select',
			__( 'طرح فروشگاه', 'larijani' ),
			'sidebar',
			array(
				'options' => array(
					'sidebar' => __( 'کاتالوگ با سایدبار فیلتر (۳ ستون)', 'larijani' ),
					'store'   => __( 'فروشگاه تمام‌عرض (۴ ستون، مرتب‌سازی کشویی)', 'larijani' ),
				),
			)
		);
		$this->ctl( 'show_sidebar', 'switch', __( 'نمایش سایدبار', 'larijani' ), 'yes', array( 'condition' => array( 'layout' => 'sidebar' ) ) );
		$this->ctl( 'footer_note', 'text', __( 'متن زیر محصولات (کارت‌های دستی)', 'larijani' ), '' );
		$this->ctl( 'show_price', 'switch', __( 'فیلتر قیمت (ووکامرس)', 'larijani' ), 'yes' );
		$this->ctl( 'advisory_title', 'text', __( 'عنوان باکس پیشنهاد', 'larijani' ), 'پیشنهاد راه‌اندازی' );
		$this->ctl( 'advisory_text', 'textarea', __( 'متن باکس پیشنهاد', 'larijani' ), 'برای راه‌اندازی کارگاه سنگ مصنوعی در متراژ ۱۵۰ متر، بسته شامل ۲۵۰ قالب ABS، میز ویبره ۲×۱ و میکسر ۵۰۰ کیلویی اقتصادی‌ترین گزینه تولید است.' );
		$this->ctl( 'advisory_link_text', 'text', __( 'متن لینک باکس', 'larijani' ), 'دریافت پکیج جامع خط تولید' );
		$this->ctl( 'advisory_link', 'url', __( 'لینک باکس', 'larijani' ), 'tel:09122302685' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$source = 'auto' === $s['source'] ? ( ls_has_woo() ? 'woocommerce' : 'manual' ) : $s['source'];
		$args   = array(
			'source'             => $source,
			'items'              => array_map( array( 'LS_Widget_Products', 'row_to_card' ), (array) $s['items'] ),
			'chips'              => $s['chips'],
			'use_main_query'     => $s['use_main_query'],
			'posts_per_page'     => $s['posts_per_page'],
			'category'           => $s['category'],
			'search_placeholder' => $s['search_placeholder'],
			'all_label'          => $s['all_label'],
			'show_sidebar'       => $this->on( $s, 'show_sidebar' ) ? 'yes' : 'no',
			'filter_boxes'       => $s['filter_boxes'],
			'show_price'         => $this->on( $s, 'show_price' ) ? 'yes' : 'no',
			'advisory_title'     => $s['advisory_title'],
			'advisory_text'      => $s['advisory_text'],
			'advisory_link_text' => $s['advisory_link_text'],
			'advisory_link'      => $s['advisory_link'],
			'columns'            => (int) $s['columns'],
			'layout'             => $s['layout'] ?? 'sidebar',
			'footer_note'        => $s['footer_note'] ?? '',
		);
		echo '<section class="w-full py-space-lg"><div class="max-w-7xl mx-auto px-4 sm:px-gutter">';
		ls_render_catalog( $args );
		echo '</div></section>';
	}
}
