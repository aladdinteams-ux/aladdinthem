<?php
/**
 * Widget: Product cards grid (manual cards or live WooCommerce products).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Products widget.
 */
class LS_Widget_Products extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-products';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS محصولات (کارت محصول)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-products';
	}
	/** @return array */
	public function get_keywords() {
		return array( 'product', 'woocommerce', 'محصول', 'فروشگاه' );
	}

	/**
	 * Product card repeater fields (shared with the catalog widget).
	 *
	 * @param LS_Widget_Base $w Widget.
	 * @return array
	 */
	public static function card_fields() {
		return array(
			array( 'image', 'media', __( 'تصویر', 'larijani' ), '' ),
			array( 'badge', 'text', __( 'برچسب روی تصویر', 'larijani' ), '' ),
			array( 'badge_tone', 'select', __( 'رنگ برچسب', 'larijani' ), 'primary', array( 'options' => ls_tone_options() ) ),
			array( 'stock_badge', 'text', __( 'برچسب موجودی پایین تصویر (سبک فروشگاه)', 'larijani' ), '' ),
			array( 'stock_tone', 'select', __( 'رنگ برچسب موجودی', 'larijani' ), 'emerald', array( 'options' => array( 'emerald' => __( 'سبز با تیک', 'larijani' ), 'dark' => __( 'تیره', 'larijani' ) ) ) ),
			array( 'category', 'text', __( 'دسته (بالای عنوان)', 'larijani' ), '' ),
			array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
			array( 'subtitle', 'text', __( 'ویژگی کوتاه (سبک کلاسیک)', 'larijani' ), '' ),
			array( 'desc', 'textarea', __( 'توضیح کوتاه (سبک کاتالوگ)', 'larijani' ), '' ),
			array( 'code', 'text', __( 'کد محصول', 'larijani' ), '' ),
			array( 'spec1_label', 'text', __( 'مشخصه ۱ – عنوان', 'larijani' ), '' ),
			array( 'spec1_value', 'text', __( 'مشخصه ۱ – مقدار', 'larijani' ), '' ),
			array( 'spec2_label', 'text', __( 'مشخصه ۲ – عنوان', 'larijani' ), '' ),
			array( 'spec2_value', 'text', __( 'مشخصه ۲ – مقدار', 'larijani' ), '' ),
			array( 'spec3_label', 'text', __( 'مشخصه ۳ – عنوان', 'larijani' ), '' ),
			array( 'spec3_value', 'text', __( 'مشخصه ۳ – مقدار (سبز)', 'larijani' ), '' ),
			array( 'price_label', 'text', __( 'عنوان قیمت', 'larijani' ), 'قیمت واحد' ),
			array( 'price', 'text', __( 'قیمت', 'larijani' ), '' ),
			array( 'currency', 'text', __( 'واحد پول', 'larijani' ), 'تومان' ),
			array( 'price_raw', 'number', __( 'قیمت عددی (برای مرتب‌سازی)', 'larijani' ), 0 ),
			array( 'link', 'url', __( 'لینک محصول', 'larijani' ), '#' ),
			array( 'button_text', 'text', __( 'متن دکمه (سبک کاتالوگ)', 'larijani' ), '' ),
			array( 'button_style', 'select', __( 'سبک دکمه', 'larijani' ), 'primary', array( 'options' => array( 'primary' => __( 'سبز', 'larijani' ), 'dark' => __( 'تیره', 'larijani' ) ) ) ),
			array( 'button_icon', 'icon', __( 'آیکون دکمه', 'larijani' ), '' ),
			array( 'button_link', 'url', __( 'لینک دکمه', 'larijani' ), '' ),
			array( 'chat_button', 'switch', __( 'دکمه استعلام واتساپ', 'larijani' ), '' ),
			array( 'filter', 'text', __( 'کلید فیلتر (دسته برای فیلتر کاتالوگ)', 'larijani' ), '' ),
		);
	}

	/**
	 * Convert a repeater row to card data.
	 *
	 * @param array $r Row.
	 * @return array
	 */
	public static function row_to_card( $r ) {
		$specs = array();
		for ( $i = 1; $i <= 3; $i++ ) {
			if ( ! empty( $r[ "spec{$i}_label" ] ) || ! empty( $r[ "spec{$i}_value" ] ) ) {
				$specs[] = array( $r[ "spec{$i}_label" ] ?? '', $r[ "spec{$i}_value" ] ?? '', 3 === $i );
			}
		}
		return array(
			'image'        => $r['image'] ?? '',
			'badge'        => $r['badge'] ?? '',
			'badge_tone'   => $r['badge_tone'] ?? 'primary',
			'stock_badge'  => $r['stock_badge'] ?? '',
			'stock_tone'   => $r['stock_tone'] ?? 'emerald',
			'category'     => $r['category'] ?? '',
			'title'        => $r['title'] ?? '',
			'subtitle'     => $r['subtitle'] ?? '',
			'desc'         => $r['desc'] ?? '',
			'code'         => $r['code'] ?? '',
			'specs'        => $specs,
			'price_label'  => $r['price_label'] ?? '',
			'price'        => $r['price'] ?? '',
			'currency'     => $r['currency'] ?? '',
			'price_raw'    => $r['price_raw'] ?? 0,
			'url'          => $r['link']['url'] ?? '#',
			'button_text'  => $r['button_text'] ?? '',
			'button_style' => $r['button_style'] ?? 'primary',
			'button_icon'  => ! empty( $r['button_icon']['value'] ) ? $r['button_icon'] : '',
			'button_url'   => $r['button_link']['url'] ?? '',
			'chat_button'  => 'yes' === ( $r['chat_button'] ?? '' ),
			'filter'       => $r['filter'] ?? '',
		);
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani' ) );
		$this->heading_controls(
			array(
				'eyebrow'   => 'محصولات برگزیده کارگاهی',
				'title'     => 'تجهیزات و قالب‌های پرفروش صنعتی',
				'desc'      => 'انتخابی ایده‌آل برای ارتقای کیفیت و مقاومت سطحی قطعات بتنی شما',
				'link_text' => 'مشاهده همه محصولات',
			)
		);
		$this->end();

		$this->section( 'sec_source', __( 'محصولات', 'larijani' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani' ), 'manual', array( 'options' => array( 'manual' => __( 'کارت‌های دستی', 'larijani' ), 'woocommerce' => __( 'محصولات ووکامرس', 'larijani' ) ) ) );
		$this->ctl( 'wc_query', 'select', __( 'نوع کوئری', 'larijani' ), 'recent', array( 'options' => array( 'recent' => __( 'جدیدترین', 'larijani' ), 'featured' => __( 'ویژه', 'larijani' ), 'best_selling' => __( 'پرفروش‌ترین', 'larijani' ), 'sale' => __( 'تخفیف‌دار', 'larijani' ), 'related' => __( 'مرتبط با محصول جاری', 'larijani' ), 'ids' => __( 'شناسه‌های مشخص', 'larijani' ) ), 'condition' => array( 'source' => 'woocommerce' ) ) );
		$this->ctl( 'wc_ids', 'text', __( 'شناسه محصولات (با کاما)', 'larijani' ), '', array( 'condition' => array( 'source' => 'woocommerce', 'wc_query' => 'ids' ) ) );
		$this->ctl( 'wc_cat', 'select', __( 'دسته محصول', 'larijani' ), '', array( 'options' => taxonomy_exists( 'product_cat' ) ? ls_term_options( 'product_cat' ) : array( '' => __( 'همه', 'larijani' ) ), 'condition' => array( 'source' => 'woocommerce' ) ) );
		$this->ctl( 'wc_limit', 'number', __( 'تعداد', 'larijani' ), 4, array( 'condition' => array( 'source' => 'woocommerce' ) ) );
		$this->rep(
			'items',
			__( 'کارت‌ها', 'larijani' ),
			self::card_fields(),
			array(
				array( 'image' => 'product_1', 'badge' => 'موتور ایتالیایی', 'category' => 'ماشین‌آلات بتن', 'title' => 'میز ویبره صنعتی دور متغیر', 'subtitle' => 'ورق ۸ میل ضد ارتعاش معکوس', 'spec1_label' => 'ابعاد', 'spec1_value' => '۲×۱ متر', 'spec2_label' => 'وزن', 'spec2_value' => '۳۵۰ kg', 'spec3_label' => 'گارانتی', 'spec3_value' => '۲ سال', 'price_label' => 'شروع قیمت از', 'price' => '۴۸,۰۰۰,۰۰۰', 'price_raw' => 48000000 ),
				array( 'image' => 'product_2', 'badge' => 'پرفروش‌ترین', 'badge_tone' => 'amber', 'category' => 'قالب پلیمری نشکن', 'title' => 'قالب کفپوش واش‌بتن طرح شیاردار', 'subtitle' => 'جنس ABS کریستال ۱۰۰٪ نو', 'spec1_label' => 'ابعاد', 'spec1_value' => '۴۰×۴۰', 'spec2_label' => 'ضخامت', 'spec2_value' => '۴ cm', 'spec3_label' => 'خروج', 'spec3_value' => 'آسان', 'price_label' => 'قیمت هر عدد', 'price' => '۱۱۰,۰۰۰', 'price_raw' => 110000 ),
				array( 'image' => 'product_3', 'badge' => 'طرح لوکس رومی', 'badge_tone' => 'emerald', 'category' => 'قالب نما و نرده', 'title' => 'قالب صراحی گرد و چهارگوش رومی', 'subtitle' => 'سطح شیشه‌ای بدون نیاز به اسیدشور', 'spec1_label' => 'ارتفاع', 'spec1_value' => '۷۰ cm', 'spec2_label' => 'براقیت', 'spec2_value' => 'عالی', 'spec3_label' => 'تنوع', 'spec3_value' => '۲۰ مدل', 'price_label' => 'قیمت جفتی', 'price' => '۲۹۰,۰۰۰', 'price_raw' => 290000 ),
				array( 'image' => 'product_4', 'badge' => 'گرید ویژه A+', 'badge_tone' => 'cobalt', 'category' => 'مواد شیمیایی ساختمان', 'title' => 'فوق روان‌کننده پلی‌کربوکسیلات', 'subtitle' => 'افزایش چشمگیر مقاومت فشاری ۴۰٪', 'spec1_label' => 'بسته‌بندی', 'spec1_value' => '۲۰ لیتری', 'spec2_label' => 'غلظت', 'spec2_value' => '۵۰٪', 'spec3_label' => 'حباب‌زدا', 'spec3_value' => 'دارد', 'price_label' => 'قیمت هر گالن', 'price' => '۱,۴۵۰,۰۰۰', 'price_raw' => 1450000 ),
			),
			'{{{ title }}}',
			array( 'condition' => array( 'source' => 'manual' ) )
		);
		$this->end();

		$this->section( 'sec_layout', __( 'چیدمان', 'larijani' ) );
		$this->ctl( 'card_style', 'select', __( 'سبک کارت', 'larijani' ), 'classic', array( 'options' => array( 'classic' => __( 'کلاسیک (صفحه اصلی)', 'larijani' ), 'catalog' => __( 'کاتالوگ (فروشگاه)', 'larijani' ), 'compact' => __( 'فشرده (محصولات مکمل)', 'larijani' ), 'showcase' => __( 'ویترین (صفحه اصلی کلاسیک)', 'larijani' ), 'store' => __( 'فروشگاه (دکمه تمام‌عرض)', 'larijani' ) ) ) );
		$this->columns_controls( 4, 2, 1, 4 );
		$this->bg_control( 'none' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Cards from WooCommerce.
	 *
	 * @param array $s Settings.
	 * @return array
	 */
	protected function wc_cards( $s ) {
		if ( ! ls_has_woo() ) {
			return array();
		}
		$args = array(
			'limit'  => max( 1, (int) $s['wc_limit'] ),
			'status' => 'publish',
		);
		switch ( $s['wc_query'] ) {
			case 'featured':
				$args['featured'] = true;
				break;
			case 'best_selling':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['order']    = 'DESC';
				break;
			case 'sale':
				$args['include'] = wc_get_product_ids_on_sale() ? wc_get_product_ids_on_sale() : array( 0 );
				break;
			case 'related':
				$args['include'] = is_singular( 'product' ) ? wc_get_related_products( get_the_ID(), $args['limit'] ) : array();
				if ( ! $args['include'] ) {
					unset( $args['include'] );
				}
				break;
			case 'ids':
				$args['include'] = array_map( 'absint', explode( ',', (string) $s['wc_ids'] ) );
				$args['orderby'] = 'include';
				break;
		}
		if ( $s['wc_cat'] ) {
			$term = get_term( (int) $s['wc_cat'], 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				$args['category'] = array( $term->slug );
			}
		}
		$cards = array();
		foreach ( wc_get_products( $args ) as $product ) {
			$cards[] = ls_wc_card_data( $product );
		}
		return $cards;
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$cards = 'woocommerce' === $s['source'] ? $this->wc_cards( $s ) : array_map( array( __CLASS__, 'row_to_card' ), $s['items'] );
		?>
		<section class="py-12 sm:py-16 <?php echo esc_attr( ls_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<?php echo $this->heading( $s ); // phpcs:ignore ?>
				<?php if ( $cards ) : ?>
				<div class="grid <?php echo esc_attr( ls_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) ); ?> gap-5 sm:gap-6">
					<?php foreach ( $cards as $card ) : ?>
						<?php echo ls_product_card( $card, $s['card_style'] ); // phpcs:ignore ?>
					<?php endforeach; ?>
				</div>
				<?php elseif ( ls_is_elementor_editor() ) : ?>
				<div class="p-6 rounded-2xl bg-white text-center text-sm text-slate-500"><?php esc_html_e( 'محصولی برای نمایش یافت نشد.', 'larijani' ); ?></div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
