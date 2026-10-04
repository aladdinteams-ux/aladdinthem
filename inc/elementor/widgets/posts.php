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
class LS_Widget_Posts extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-posts';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS آخرین مقالات', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani' ) );
		$this->heading_controls(
			array(
				'eyebrow'   => 'دانش فنی و تخصصی',
				'title'     => 'آخرین مقالات و راهنماهای کاربردی',
				'link_text' => 'همه مقالات',
			)
		);
		$this->end();

		$this->section( 'sec_query', __( 'نوشته‌ها', 'larijani' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani' ), 'posts', array( 'options' => array( 'posts' => __( 'نوشته‌های وبلاگ', 'larijani' ), 'manual' => __( 'دستی', 'larijani' ) ) ) );
		$this->ctl( 'posts_per_page', 'number', __( 'تعداد', 'larijani' ), 3, array( 'condition' => array( 'source' => 'posts' ) ) );
		$this->ctl( 'category', 'select', __( 'دسته', 'larijani' ), '', array( 'options' => ls_term_options( 'category' ), 'condition' => array( 'source' => 'posts' ) ) );
		$this->ctl( 'orderby', 'select', __( 'مرتب‌سازی', 'larijani' ), 'date', array( 'options' => array( 'date' => __( 'جدیدترین', 'larijani' ), 'views' => __( 'پربازدیدترین', 'larijani' ), 'comments' => __( 'پربحث‌ترین', 'larijani' ), 'rand' => __( 'تصادفی', 'larijani' ) ), 'condition' => array( 'source' => 'posts' ) ) );
		$this->rep(
			'items',
			__( 'کارت‌ها', 'larijani' ),
			array(
				array( 'image', 'media', __( 'تصویر', 'larijani' ), '' ),
				array( 'category', 'text', __( 'دسته', 'larijani' ), '' ),
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'excerpt', 'textarea', __( 'خلاصه', 'larijani' ), '' ),
				array( 'date', 'text', __( 'تاریخ', 'larijani' ), '' ),
				array( 'link', 'url', __( 'لینک', 'larijani' ), '#' ),
			),
			array(
				array( 'image' => 'blog_1', 'category' => 'آموزش و فرمولاسیون', 'title' => 'فرمول استاندارد تولید سنگ مصنوعی پلیمری با رزین پلی‌کربوکسیلات', 'excerpt' => 'بررسی درصد دقیق سنگ‌دانه‌ها، میکروسیلیس و روان‌کننده بتن جهت رسیدن به حداکثر مقاومت خمشی و جلوگیری از ترک.', 'date' => '۱۴ اسفند ۱۴۰۳' ),
				array( 'image' => 'blog_2', 'category' => 'ماشین‌آلات', 'title' => 'راهنمای خرید میز ویبره استاندارد؛ از قدرت موتور تا ضخامت شاسی', 'excerpt' => 'چگونه مانع حباب‌زدگی بتن شویم و فنرهای فولادی مناسب با فرکانس کاری چطور عمر دستگاه را چند برابر می‌کنند.', 'date' => '۰۸ اسفند ۱۴۰۳' ),
				array( 'image' => 'blog_3', 'category' => 'نگهداری تجهیزات', 'title' => 'ترفندهای طلایی افزایش طول عمر قالب‌های نشکن ABS تا ۱۰۰۰ مرتبه', 'excerpt' => 'روغن‌کاری صحیح، شرایط نگهداری در برابر آفتاب مستقیم و روش‌های اصولی دپوی قالب‌ها در فصول سرد و گرم سال.', 'date' => '۲۵ بهمن ۱۴۰۳' ),
			),
			'{{{ title }}}',
			array( 'condition' => array( 'source' => 'manual' ) )
		);
		$this->ctl( 'card_style', 'select', __( 'سبک کارت', 'larijani' ), 'home', array( 'options' => array( 'home' => __( 'صفحه اصلی', 'larijani' ), 'archive' => __( 'آرشیو', 'larijani' ), 'related' => __( 'مرتبط', 'larijani' ) ) ) );
		$this->ctl( 'read_more', 'text', __( 'متن ادامه مطلب', 'larijani' ), 'ادامه مطلب' );
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
			$posts = get_posts( ls_posts_query_args( array( 'posts_per_page' => $s['posts_per_page'], 'category' => $s['category'], 'orderby' => $s['orderby'], 'exclude_current' => true ) ) );
			foreach ( $posts as $p ) {
				$cards[] = ls_post_data( $p );
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
		<section class="py-12 sm:py-16 <?php echo esc_attr( ls_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<?php echo $this->heading( $s ); // phpcs:ignore ?>
				<div class="grid <?php echo esc_attr( ls_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) ); ?> gap-5 sm:gap-6">
					<?php foreach ( $cards as $c ) : ?>
						<?php echo ls_post_card( $c, $s['card_style'], array( 'read_more' => $s['read_more'] ) ); // phpcs:ignore ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
