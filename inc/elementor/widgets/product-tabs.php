<?php
/**
 * Widget: Product tabs – technical specs, formulation guide, reviews.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product tabs widget.
 */
class Larijani_Widget_Product_Tabs extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-product-tabs';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS تب‌های محصول (مشخصات، فرمولاسیون، نظرات)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-product-tabs';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_source', __( 'منبع', 'larijani-stone' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani-stone' ), 'auto', array( 'options' => array( 'auto' => __( 'خودکار (محصول جاری ووکامرس)', 'larijani-stone' ), 'manual' => __( 'دستی', 'larijani-stone' ) ) ) );
		$this->end();

		$this->section( 'sec_specs', __( 'تب مشخصات', 'larijani-stone' ) );
		$this->ctl( 'tab1_label', 'text', __( 'عنوان تب', 'larijani-stone' ), 'مشخصات فنی و متریال ورق' );
		$this->ctl( 'spec_title', 'text', __( 'عنوان', 'larijani-stone' ), 'آنالیز متالورژی و ساختار فیزیکی قالب DS-904' );
		$this->ctl( 'spec_text', 'textarea', __( 'متن (حالت دستی)', 'larijani-stone' ), 'تمامی قالب‌های سری کریستالی لاریجانی استون به روش وکیوم‌فرمینگ دقیق CNC تحت حرارت یکنواخت مادون قرمز تولید می‌شوند. لبه‌های تقویت‌شده دوبل با زاویه خروج ۱.۵ درجه تضمین می‌کند که بلوک بتنی سنگین بدون نیاز به چکش‌کاری یا ضربه‌های مخرب به راحتی پس از ۲۴ ساعت از قالب جدا شود.' );
		$this->rep(
			'spec_rows',
			__( 'ردیف‌های مشخصات', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'value', 'text', __( 'مقدار', 'larijani-stone' ), '' ),
			),
			array(
				array( 'label' => 'ضخامت اولیه ورق', 'value' => '۴.۰۰ میلی‌متر ± ۰.۱' ),
				array( 'label' => 'مقاومت دمایی کاری', 'value' => '-۲۰°C تا +۶۵°C' ),
				array( 'label' => 'وزن خالص هر قالب', 'value' => '۸۵۰ گرم' ),
				array( 'label' => 'روغن قالب موردنیاز', 'value' => 'پایه‌گیاهی (بسیار رقیق)' ),
				array( 'label' => 'استحکام ضربه ایزود', 'value' => '32 kJ/m²' ),
				array( 'label' => 'روش تمیزکاری دوره‌ای', 'value' => 'شستشو با آب گرم و صابون' ),
			),
			'{{{ label }}}'
		);
		$this->ctl( 'use_attributes', 'switch', __( 'در حالت ووکامرس از ویژگی‌های محصول استفاده شود', 'larijani-stone' ), 'yes' );
		$this->ctl( 'highlight_title', 'text', __( 'عنوان کارت ویژه', 'larijani-stone' ), 'چرا ABS نو به جای ورق بازیافتی؟' );
		$this->ctl( 'highlight_text', 'textarea', __( 'متن کارت ویژه', 'larijani-stone' ), 'ورق‌های ضایعاتی یا گرانولی در بازار پس از ۲۰ بار ویبره دچار شکستگی گوشه‌ها و کدر شدن سطح سنگ می‌شوند. پلیمرهای ویرجین لاریجانی استون بدون افت براقیت سطح، سطح سنگ بتنی را شبیه به سنگ گرانیت صیقلی طبیعی خارج می‌سازند.' );
		$this->ctl( 'datasheet_text', 'text', __( 'متن دکمه دیتاشیت', 'larijani-stone' ), 'دانلود برگه مشخصات فنی PDF' );
		$this->ctl( 'datasheet_link', 'url', __( 'فایل دیتاشیت', 'larijani-stone' ), '#' );
		$this->end();

		$this->section( 'sec_formula', __( 'تب فرمولاسیون', 'larijani-stone' ) );
		$this->ctl( 'tab2_label', 'text', __( 'عنوان تب', 'larijani-stone' ), 'دستورالعمل و فرمولاسیون اختصاصی سنگ' );
		$this->ctl( 'formula_title', 'text', __( 'عنوان', 'larijani-stone' ), 'فرمولاسیون طلایی سنگ دکوراتیو کریستالی (ویژه هر ۱ مترمربع = ۶.۶ قالب)' );
		$this->ctl( 'formula_subtitle', 'text', __( 'زیرعنوان', 'larijani-stone' ), 'طراحی شده بر مبنای استاندارد ملی ایران و تست‌های آزمایشگاه پلیمر لاریجانی استون' );
		$this->rep(
			'formula_items',
			__( 'اقلام فرمول', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'ماده', 'larijani-stone' ), '' ),
				array( 'value', 'text', __( 'مقدار', 'larijani-stone' ), '' ),
				array( 'note', 'text', __( 'یادداشت', 'larijani-stone' ), '' ),
				array( 'tone', 'select', __( 'رنگ مقدار', 'larijani-stone' ), 'dark', array( 'options' => array( 'dark' => __( 'تیره', 'larijani-stone' ), 'primary' => __( 'سبز برند', 'larijani-stone' ), 'emerald' => __( 'زمردی', 'larijani-stone' ) ) ) ),
			),
			array(
				array( 'label' => 'سیمان سفید یا خاکستری عیار ۴۰۰', 'value' => '۱۸ کیلوگرم', 'note' => 'تیپ ۲ یا سیمان سفید ساوه' ),
				array( 'label' => 'ماسه سیلیسی دانه‌بندی ۰ تا ۳', 'value' => '۳۵ کیلوگرم', 'note' => 'شسته‌شده و بدون خاک رس' ),
				array( 'label' => 'فوق روان‌کننده پلی‌کربوکسیلات', 'value' => '۱۴۰ گرم', 'note' => '۰.۸٪ وزن سیمان مصرفی', 'tone' => 'primary' ),
				array( 'label' => 'نسبت آب به سیمان (W/C)', 'value' => '۰.۲۸ تا ۰.۳۰', 'note' => 'حداکثر ۵.۵ لیتر آب کل', 'tone' => 'emerald' ),
			),
			'{{{ label }}}'
		);
		$this->rep(
			'formula_steps',
			__( 'مراحل', 'larijani-stone' ),
			array(
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'text', 'textarea', __( 'متن', 'larijani-stone' ), '' ),
			),
			array(
				array( 'title' => 'میکس خشک', 'text' => 'سیلیس و سیمان را به مدت ۲ دقیقه در میکسر طرح پن به صورت خشک مخلوط کنید تا کاملاً یکنواخت شوند.' ),
				array( 'title' => 'تزریق رزین روان‌کننده', 'text' => 'رزین روان‌کننده را داخل آب حل کرده و آهسته اضافه نمایید تا ملاتی روان، خمیری و بدون آب‌انداختگی شکل گیرد.' ),
				array( 'title' => 'ویبره و خروج هوا', 'text' => 'قالب‌ها را روی میز ویبره قرار داده و حداکثر ۳۰ الی ۴۵ ثانیه ارتعاش دهید تا حباب‌های ریز کاملاً تخلیه شوند.' ),
			)
		);
		$this->end();

		$this->section( 'sec_reviews', __( 'تب نظرات', 'larijani-stone' ) );
		$this->ctl( 'tab3_label', 'text', __( 'عنوان تب', 'larijani-stone' ), 'دیدگاه‌ها و تجربیات کارگاه‌ها' );
		$this->ctl( 'rating', 'number', __( 'امتیاز (حالت دستی)', 'larijani-stone' ), 4.9, array( 'step' => 0.1 ) );
		$this->ctl( 'review_count', 'number', __( 'تعداد نظرات (حالت دستی)', 'larijani-stone' ), 24 );
		$this->rep(
			'rating_bars',
			__( 'نوار امتیازها', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'percent', 'number', __( 'درصد', 'larijani-stone' ), 0 ),
			),
			array(
				array( 'label' => '۵ ستاره', 'percent' => 92 ),
				array( 'label' => '۴ ستاره', 'percent' => 8 ),
				array( 'label' => '۳ ستاره', 'percent' => 0 ),
			),
			'{{{ label }}}'
		);
		$this->rep(
			'reviews',
			__( 'نظرات (حالت دستی)', 'larijani-stone' ),
			array(
				array( 'name', 'text', __( 'نام', 'larijani-stone' ), '' ),
				array( 'meta', 'text', __( 'توضیح', 'larijani-stone' ), '' ),
				array( 'stars', 'number', __( 'ستاره', 'larijani-stone' ), 5 ),
				array( 'text', 'textarea', __( 'متن', 'larijani-stone' ), '' ),
			),
			array(
				array( 'name' => 'مهندس کاویانی (کارگاه بتن اکسپوز اصفهان)', 'meta' => 'خریدار ۱۲۰ عدد قالب کد DS-904', 'stars' => 5, 'text' => '«حدود ۶ ماه است که با این قالب‌ها روزانه ۱۲۰ قطعه می‌ریزیم. با اینکه میز ویبره ما ارتعاش بالایی دارد، لبه‌های قالب ذره‌ای تاب برنداشته و گوشه‌های سنگ کاملاً ۹۰ درجه و گونیا درمی‌آید. به نسبت قالب‌های لاستیکی خیلی سبک‌تر و شستشوی آن هم سریع‌تر است.»' ),
				array( 'name' => 'حاج حسین رضایی (مجتمع سنگ البرز، کرج)', 'meta' => 'خریدار ۶۰ عدد برای پروژه ویلایی کردان', 'stars' => 5, 'text' => '«زاویه‌های شکست نور این طرح در نمای شب فوق‌العاده زیباست. مشاوره آقای مهندس لاریجانی در مورد استفاده از پیگمنت دوده مشکی باعث شد نمایی شبیه به سنگ بازالت آتشفشانی خلق کنیم که معمار پروژه بسیار راضی بود.»' ),
			),
			'{{{ name }}}'
		);
		$this->ctl( 'review_button', 'text', __( 'دکمه ثبت تجربه', 'larijani-stone' ), 'ثبت تجربه و عکس تولیدی شما' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$product = null;
		if ( 'auto' === $s['source'] && larijani_has_woo() ) {
			$product = wc_get_product( get_the_ID() );
		}
		$d = array(
			'product'          => $product,
			'tab1_label'       => $s['tab1_label'],
			'spec_title'       => $s['spec_title'],
			'spec_text'        => $s['spec_text'],
			'spec_rows'        => $s['spec_rows'],
			'highlight_title'  => $s['highlight_title'],
			'highlight_text'   => $s['highlight_text'],
			'datasheet_text'   => $s['datasheet_text'],
			'datasheet_link'   => $s['datasheet_link'],
			'tab2_label'       => $s['tab2_label'],
			'formula_title'    => $s['formula_title'],
			'formula_subtitle' => $s['formula_subtitle'],
			'formula_items'    => $s['formula_items'],
			'formula_steps'    => $s['formula_steps'],
			'tab3_label'       => $s['tab3_label'],
			'rating'           => (float) $s['rating'],
			'review_count'     => (int) $s['review_count'],
			'rating_bars'      => $s['rating_bars'],
			'reviews'          => $s['reviews'],
			'review_button'    => $s['review_button'],
		);
		if ( $product ) {
			$id = $product->get_id();
			if ( $this->on( $s, 'use_attributes' ) ) {
				$rows = array();
				foreach ( $product->get_attributes() as $attr ) {
					if ( $attr->get_visible() ) {
						$values = $attr->is_taxonomy() ? wc_get_product_terms( $id, $attr->get_name(), array( 'fields' => 'names' ) ) : $attr->get_options();
						$rows[] = array( 'label' => wc_attribute_label( $attr->get_name() ), 'value' => implode( '، ', $values ) );
					}
				}
				if ( $product->get_weight() ) {
					$rows[] = array( 'label' => __( 'وزن', 'larijani-stone' ), 'value' => wc_format_weight( $product->get_weight() ) );
				}
				$d['spec_rows'] = $rows;
			}
			$d['spec_title'] = get_post_meta( $id, '_ls_spec_title', true ) ? get_post_meta( $id, '_ls_spec_title', true ) : '';
			foreach ( array( 'highlight_title', 'highlight_text' ) as $k ) {
				$meta = get_post_meta( $id, '_ls_' . $k, true );
				$d[ $k ] = $meta ? $meta : '';
			}
			$sheet = get_post_meta( $id, '_ls_datasheet', true );
			$d['datasheet_link'] = $sheet ? array( 'url' => $sheet, 'is_external' => true ) : '';
			$formula            = get_post_meta( $id, '_ls_formulation', true );
			$d['formula_items'] = array();
			$d['formula_steps'] = array();
			$d['formula_title'] = $formula ? $s['tab2_label'] : '';
			$d['formula_html']  = $formula;
		}
		echo '<section class="w-full py-space-xl bg-surface-card shadow-sm"><div class="max-w-7xl mx-auto px-4 sm:px-gutter">';
		larijani_render_product_tabs( $d );
		echo '</div></section>';
	}
}
