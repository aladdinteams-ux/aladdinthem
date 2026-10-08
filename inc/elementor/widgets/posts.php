<?php
/**
 * Widget: Latest articles (home style cards).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Posts widget.
 */
class Larijani_Widget_Posts extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-posts';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS آخرین مقالات', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani-stone' ) );
		$this->heading_controls(
			array(
				'eyebrow'   => 'دانش فنی و تخصصی',
				'title'     => 'آخرین مقالات و راهنماهای کاربردی',
				'link_text' => 'همه مقالات',
			)
		);
		$this->end();

		$this->section( 'sec_query', __( 'نوشته‌ها', 'larijani-stone' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani-stone' ), 'posts', array( 'options' => array( 'posts' => __( 'نوشته‌های وبلاگ', 'larijani-stone' ), 'manual' => __( 'دستی', 'larijani-stone' ) ) ) );
		$this->ctl( 'posts_per_page', 'number', __( 'تعداد', 'larijani-stone' ), 3, array( 'condition' => array( 'source' => 'posts' ) ) );
		$this->ctl( 'category', 'select', __( 'دسته', 'larijani-stone' ), '', array( 'options' => larijani_term_options( 'category' ), 'condition' => array( 'source' => 'posts' ) ) );
		$this->ctl( 'orderby', 'select', __( 'مرتب‌سازی', 'larijani-stone' ), 'date', array( 'options' => array( 'date' => __( 'جدیدترین', 'larijani-stone' ), 'views' => __( 'پربازدیدترین', 'larijani-stone' ), 'comments' => __( 'پربحث‌ترین', 'larijani-stone' ), 'rand' => __( 'تصادفی', 'larijani-stone' ) ), 'condition' => array( 'source' => 'posts' ) ) );
		$this->rep(
			'items',
			__( 'کارت‌ها', 'larijani-stone' ),
			array(
				array( 'image', 'media', __( 'تصویر', 'larijani-stone' ), '' ),
				array( 'category', 'text', __( 'دسته', 'larijani-stone' ), '' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'excerpt', 'textarea', __( 'خلاصه', 'larijani-stone' ), '' ),
				array( 'date', 'text', __( 'تاریخ', 'larijani-stone' ), '' ),
				array( 'link', 'url', __( 'لینک', 'larijani-stone' ), '#' ),
			),
			array(
				array( 'image' => 'blog_1', 'category' => 'آموزش و فرمولاسیون', 'title' => 'فرمول استاندارد تولید سنگ مصنوعی پلیمری با رزین پلی‌کربوکسیلات', 'excerpt' => 'بررسی درصد دقیق سنگ‌دانه‌ها، میکروسیلیس و روان‌کننده بتن جهت رسیدن به حداکثر مقاومت خمشی و جلوگیری از ترک.', 'date' => '۱۴ اسفند ۱۴۰۳' ),
				array( 'image' => 'blog_2', 'category' => 'ماشین‌آلات', 'title' => 'راهنمای خرید میز ویبره استاندارد؛ از قدرت موتور تا ضخامت شاسی', 'excerpt' => 'چگونه مانع حباب‌زدگی بتن شویم و فنرهای فولادی مناسب با فرکانس کاری چطور عمر دستگاه را چند برابر می‌کنند.', 'date' => '۰۸ اسفند ۱۴۰۳' ),
				array( 'image' => 'blog_3', 'category' => 'نگهداری تجهیزات', 'title' => 'ترفندهای طلایی افزایش طول عمر قالب‌های نشکن ABS تا ۱۰۰۰ مرتبه', 'excerpt' => 'روغن‌کاری صحیح، شرایط نگهداری در برابر آفتاب مستقیم و روش‌های اصولی دپوی قالب‌ها در فصول سرد و گرم سال.', 'date' => '۲۵ بهمن ۱۴۰۳' ),
			),
			'{{{ title }}}',
			array( 'condition' => array( 'source' => 'manual' ) )
		);
		$this->ctl( 'card_style', 'select', __( 'سبک کارت', 'larijani-stone' ), 'home', array( 'options' => array( 'home' => __( 'صفحه اصلی', 'larijani-stone' ), 'archive' => __( 'آرشیو', 'larijani-stone' ), 'related' => __( 'مرتبط', 'larijani-stone' ) ) ) );
		$this->ctl( 'read_more', 'text', __( 'متن ادامه مطلب', 'larijani-stone' ), 'ادامه مطلب' );
		$this->columns_controls( 3, 3, 1, 4 );
		$this->bg_control( 'white-bordered' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$cards = array();
		if ( 'posts' === $s['source'] ) {
			$posts = get_posts( larijani_posts_query_args( array( 'posts_per_page' => $s['posts_per_page'], 'category' => $s['category'], 'orderby' => $s['orderby'], 'exclude_current' => true ) ) );
			foreach ( $posts as $p ) {
				$cards[] = larijani_post_data( $p );
			}
		}
		if ( ! $cards ) {
			foreach ( $s['items'] as $it ) {
				$cards[] = array(
					'image'    => $it['image'],
					'title'    => $it['title'],
					'excerpt'  => $it['excerpt'],
					'category' => $it['category'],
					'date'     => $it['date'],
					'url'      => $it['link']['url'] ?? '#',
				);
			}
		}
		?>
		<section class="py-12 sm:py-16 <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<?php echo $this->heading( $s ); // phpcs:ignore ?>
				<div class="grid <?php echo esc_attr( larijani_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) ); ?> gap-5 sm:gap-6">
					<?php foreach ( $cards as $c ) : ?>
						<?php echo larijani_post_card( $c, $s['card_style'], array( 'read_more' => $s['read_more'] ) ); // phpcs:ignore ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Section-heading link fallback.
	 *
	 * @return string
	 */
	protected function heading_fallback_url() {
		return larijani_blog_url();
	}
}
