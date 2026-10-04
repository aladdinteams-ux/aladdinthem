<?php
/**
 * Widget: Single product showcase (gallery, specs, price calculator, add to cart).
 * Use in Theme Builder "Single Product" templates (WooCommerce) or on any page with manual content.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product detail widget.
 */
class LS_Widget_Product_Detail extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-product-detail';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS معرفی محصول (گالری + خرید)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-product-images';
	}
	/** @return array */
	public function get_keywords() {
		return array( 'product', 'single', 'woocommerce', 'محصول' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_source', __( 'منبع', 'larijani' ) );
		$this->ctl( 'source', 'select', __( 'منبع اطلاعات', 'larijani' ), 'auto', array( 'options' => array( 'auto' => __( 'خودکار (محصول جاری ووکامرس)', 'larijani' ), 'manual' => __( 'دستی', 'larijani' ) ), 'description' => __( 'در حالت خودکار، فیلدهای اختصاصی را از تب «اطلاعات لاریجانی» در ویرایش محصول تکمیل کنید.', 'larijani' ) ) );
		$this->ctl( 'show_breadcrumb', 'switch', __( 'نمایش مسیر صفحه', 'larijani' ), 'yes' );
		$this->end();

		$this->section( 'sec_manual', __( 'اطلاعات محصول (دستی)', 'larijani' ) );
		$this->ctl( 'gallery', 'gallery', __( 'گالری تصاویر', 'larijani' ), array( ls_demo_media( 'single_main' ), ls_demo_media( 'single_thumb_2' ), ls_demo_media( 'single_thumb_3' ), ls_demo_media( 'single_thumb_4' ) ) );
		$this->rep(
			'image_badges',
			__( 'برچسب‌های روی تصویر', 'larijani' ),
			array(
				array( 'text', 'text', __( 'متن', 'larijani' ), '' ),
				array( 'tone', 'select', __( 'رنگ', 'larijani' ), 'primary', array( 'options' => array( 'primary' => __( 'سبز', 'larijani' ), 'amber' => __( 'کهربایی', 'larijani' ) ) ) ),
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'shield-check' ),
			),
			array(
				array( 'text' => 'ضمانت مادام‌العمر عدم شکستن ABS', 'tone' => 'primary', 'icon' => 'shield-check' ),
				array( 'text' => 'گرید صادراتی A++', 'tone' => 'amber', 'icon' => 'award-fill' ),
			),
			'{{{ text }}}'
		);
		$this->ctl( 'stock_text', 'text', __( 'وضعیت موجودی', 'larijani' ), 'موجود در انبار مرکزی آبیک (ارسال فوری ۲۴ ساعته)' );
		$this->ctl( 'code', 'text', __( 'کد فنی', 'larijani' ), 'DS-904' );
		$this->ctl( 'hot_text', 'text', __( 'برچسب پرفروش', 'larijani' ), 'پرفروش‌ترین طرح سال' );
		$this->ctl( 'title', 'text', __( 'نام محصول', 'larijani' ), 'قالب سنگ پلیمری طرح سه‌بعدی صخره‌ای و کریستالی' );
		$this->ctl( 'subtitle', 'textarea', __( 'توضیح کوتاه', 'larijani' ), 'تولید شده از ورق کاملاً نو ABS سامسونگ کره با انعطاف‌پذیری فوق‌العاده و مقاومت سایشی بالا برای انواع بتن اکسپوز و سنگ آنتیک' );
		$this->ctl( 'rating', 'number', __( 'امتیاز (۰ تا ۵)', 'larijani' ), 4.9, array( 'min' => 0, 'max' => 5, 'step' => 0.1 ) );
		$this->ctl( 'review_count', 'number', __( 'تعداد نظرات', 'larijani' ), 24 );
		$this->ctl( 'quality_note', 'text', __( 'تأییدیه کیفی', 'larijani' ), 'تأییدیه کنترل کیفی آزمایشگاه بتن لاریجانی' );
		$this->rep(
			'highlights',
			__( 'مشخصات کلیدی', 'larijani' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'value', 'text', __( 'مقدار', 'larijani' ), '' ),
			),
			array(
				array( 'label' => 'ابعاد مفید قطعه', 'value' => '۳۰ × ۵۰ cm' ),
				array( 'label' => 'ضخامت ورق خام', 'value' => '۴ میلی‌متر خالص' ),
				array( 'label' => 'پوشش هر قالب', 'value' => '۰.۱۵ مترمربع' ),
				array( 'label' => 'جنس بدنه', 'value' => 'ABS کره نشکن' ),
			),
			'{{{ label }}}'
		);
		$this->rep(
			'trust',
			__( 'نشان‌های زیر گالری', 'larijani' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'arrow-repeat' ),
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'text', 'text', __( 'متن', 'larijani' ), '' ),
			),
			array(
				array( 'icon' => 'arrow-repeat', 'title' => '۵۰۰+ سیکل بتن', 'text' => 'ماندگاری فرم تضمینی' ),
				array( 'icon' => 'moisture', 'title' => 'بدون نیاز به اسید', 'text' => 'صیقلی و ضدرسوب' ),
				array( 'icon' => 'truck', 'title' => 'ارسال روزانه', 'text' => 'از انبار کارخانه آبیک' ),
			)
		);
		$this->ctl( 'price_label', 'text', __( 'عنوان قیمت', 'larijani' ), 'قیمت هر عدد (تک‌فروشی):' );
		$this->ctl( 'unit_price', 'number', __( 'قیمت واحد (عدد)', 'larijani' ), 125000 );
		$this->ctl( 'bulk_price', 'number', __( 'قیمت عمده', 'larijani' ), 115000 );
		$this->ctl( 'bulk_min', 'number', __( 'حداقل تعداد عمده', 'larijani' ), 50 );
		$this->ctl( 'area_per_unit', 'number', __( 'سطح تولید هر عدد (مترمربع)', 'larijani' ), 0.15, array( 'step' => 0.01 ) );
		$this->ctl( 'currency', 'text', __( 'واحد پول', 'larijani' ), 'تومان' );
		$this->ctl( 'qty_default', 'number', __( 'تعداد پیش‌فرض', 'larijani' ), 10 );
		$this->ctl( 'cart_link', 'url', __( 'لینک دکمه خرید (حالت دستی)', 'larijani' ), '' );
		$this->rep(
			'guarantees',
			__( 'تضمین‌ها', 'larijani' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'shield-fill-check' ),
				array( 'tone', 'select', __( 'رنگ', 'larijani' ), 'emerald', array( 'options' => array( 'emerald' => __( 'سبز', 'larijani' ), 'amber' => __( 'کهربایی', 'larijani' ) ) ) ),
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'text', 'textarea', __( 'متن', 'larijani' ), '' ),
			),
			array(
				array( 'icon' => 'shield-fill-check', 'tone' => 'emerald', 'title' => 'تضمین تعویض بی‌قیدوشرط:', 'text' => 'در صورت هرگونه تغییر فرم، ترکیدگی در ارتعاش ویبره یا دفرمه شدن در ۶ ماه اول، قالب‌ها فوراً مرجوع و تعویض می‌گردند.' ),
				array( 'icon' => 'journal-bookmark-fill', 'tone' => 'amber', 'title' => 'فرمولاسیون رایگان همراه فاکتور:', 'text' => 'جدول دقیق نسبت‌های اختلاط رزین، پودر سنگ سیلیسی، پیگمنت و سیمان سفید/سیاه همراه بار ارسال می‌شود.' ),
			)
		);
		$this->end();

		$this->section( 'sec_labels', __( 'برچسب‌ها و دکمه‌ها', 'larijani' ) );
		$this->ctl( 'qty_label', 'text', __( 'برچسب تعداد', 'larijani' ), 'تعداد قالب:' );
		$this->ctl( 'area_label', 'text', __( 'برچسب سطح', 'larijani' ), 'سطح تولید:' );
		$this->ctl( 'total_label', 'text', __( 'برچسب مجموع', 'larijani' ), 'مجموع:' );
		$this->ctl( 'cart_text', 'text', __( 'متن دکمه خرید', 'larijani' ), 'افزودن به سبد خرید کارگاهی' );
		$this->ctl( 'consult_text', 'text', __( 'متن دکمه مشاوره', 'larijani' ), 'مشاوره تیراژ و خط تولید' );
		$this->ctl( 'consult_link', 'url', __( 'لینک دکمه مشاوره', 'larijani' ), 'tel:09122302685' );
		$this->ctl( 'show_wishlist', 'switch', __( 'دکمه علاقه‌مندی', 'larijani' ), 'yes' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$labels = array(
			'qty_label'     => $s['qty_label'],
			'area_label'    => $s['area_label'],
			'total_label'   => $s['total_label'],
			'cart_text'     => $s['cart_text'],
			'consult_text'  => $s['consult_text'],
			'consult_link'  => $s['consult_link'],
			'show_wishlist' => $this->on( $s, 'show_wishlist' ) ? 'yes' : 'no',
		);
		$product = null;
		if ( 'auto' === $s['source'] && ls_has_woo() ) {
			$product = wc_get_product( get_the_ID() );
			if ( ! $product && ls_is_elementor_editor() ) {
				$ids     = wc_get_products( array( 'limit' => 1, 'return' => 'ids' ) );
				$product = $ids ? wc_get_product( $ids[0] ) : null;
			}
		}
		if ( $product ) {
			$data = array_merge( ls_wc_detail_data( $product ), $labels );
			if ( ! $data['area_per_unit'] ) {
				$data['area_label'] = '';
			}
		} else {
			$gallery = array();
			foreach ( (array) $s['gallery'] as $g ) {
				$gallery[] = $g;
			}
			$data = array_merge(
				array(
					'gallery'       => $gallery,
					'image_badges'  => array_map(
						static function ( $b ) {
							return array( 'text' => $b['text'], 'tone' => $b['tone'], 'icon' => $b['icon'] );
						},
						(array) $s['image_badges']
					),
					'stock_text'    => $s['stock_text'],
					'code'          => $s['code'],
					'hot_text'      => $s['hot_text'],
					'title'         => $s['title'],
					'subtitle'      => $s['subtitle'],
					'rating'        => (float) $s['rating'],
					'review_count'  => (int) $s['review_count'],
					'quality_note'  => $s['quality_note'],
					'highlights'    => $s['highlights'],
					'trust'         => $s['trust'],
					'price_label'   => $s['price_label'],
					'unit_price'    => (float) $s['unit_price'],
					'bulk_price'    => (float) $s['bulk_price'],
					'bulk_min'      => (int) $s['bulk_min'],
					'area_per_unit' => (float) $s['area_per_unit'],
					'currency'      => $s['currency'],
					'guarantees'    => $s['guarantees'],
					'qty_default'   => max( 1, (int) $s['qty_default'] ),
					'cart_link'     => $s['cart_link'],
				),
				$labels
			);
		}
		if ( $product && function_exists( 'wc_print_notices' ) && ! ls_is_elementor_editor() ) {
			echo '<div class="max-w-7xl mx-auto px-4 sm:px-gutter pt-4 ls-wc-notices">';
			wc_print_notices();
			echo '</div>';
		}
		?>
		<?php if ( $this->on( $s, 'show_breadcrumb' ) ) : ?>
		<section class="w-full bg-surface-canvas py-space-sm">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter">
				<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'larijani' ); ?>" class="flex items-center flex-wrap gap-space-xs text-on-surface-variant font-body-sm text-body-sm"><?php echo ls_breadcrumb_html( $product ? array() : array( 'items' => array( array( __( 'صفحه اصلی', 'larijani' ), home_url( '/' ) ), array( __( 'فروشگاه و کاتالوگ', 'larijani' ), ls_page_url( 'shop' ) ), array( $data['title'], null ) ) ) ); // phpcs:ignore ?></nav>
			</div>
		</section>
		<?php endif; ?>
		<section class="w-full py-space-md lg:py-space-xl">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter">
				<?php ls_render_product_detail( $data ); ?>
			</div>
		</section>
		<?php
	}
}
