<?php
/**
 * Widget: Posts grid for archives (supports the current query → Theme Builder "Archive").
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Posts grid widget.
 */
class LS_Widget_Posts_Grid extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-posts-grid';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS شبکه مقالات (آرشیو)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-archive-posts';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_query', __( 'کوئری', 'larijani' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani' ), 'auto', array( 'options' => array( 'auto' => __( 'خودکار (کوئری جاری در آرشیو، وگرنه سفارشی)', 'larijani' ), 'current' => __( 'کوئری جاری صفحه (آرشیو)', 'larijani' ), 'custom' => __( 'سفارشی', 'larijani' ), 'related' => __( 'مرتبط با نوشته جاری', 'larijani' ) ) ) );
		$this->ctl( 'posts_per_page', 'number', __( 'تعداد', 'larijani' ), 6 );
		$this->ctl( 'category', 'select', __( 'دسته', 'larijani' ), '', array( 'options' => ls_term_options( 'category' ) ) );
		$this->ctl( 'orderby', 'select', __( 'مرتب‌سازی', 'larijani' ), 'date', array( 'options' => array( 'date' => __( 'جدیدترین', 'larijani' ), 'views' => __( 'پربازدیدترین', 'larijani' ), 'comments' => __( 'پربحث‌ترین', 'larijani' ), 'rand' => __( 'تصادفی', 'larijani' ) ) ) );
		$this->ctl( 'exclude_featured', 'switch', __( 'حذف مقاله ویژه از لیست', 'larijani' ), 'yes' );
		$this->ctl( 'pagination', 'switch', __( 'صفحه‌بندی', 'larijani' ), 'yes' );
		$this->end();

		$this->section( 'sec_layout', __( 'نمایش', 'larijani' ) );
		$this->ctl( 'bar_title', 'text', __( 'عنوان بالای لیست', 'larijani' ), 'جدیدترین مقالات و راهنماهای کارگاهی' );
		$this->ctl( 'show_count', 'switch', __( 'نمایش شمارنده', 'larijani' ), 'yes' );
		$this->ctl( 'card_style', 'select', __( 'سبک کارت', 'larijani' ), 'archive', array( 'options' => array( 'archive' => __( 'آرشیو', 'larijani' ), 'home' => __( 'صفحه اصلی', 'larijani' ), 'related' => __( 'مرتبط', 'larijani' ) ) ) );
		$this->ctl( 'read_more', 'text', __( 'متن ادامه مطلب', 'larijani' ), 'ادامه مطلب تخصصی' );
		$this->columns_controls( 2, 2, 1, 4 );
		$this->ctl( 'with_sidebar', 'switch', __( 'سایدبار پیش‌فرض قالب در کنار لیست', 'larijani' ), 'yes' );
		$this->end();
		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$source = $s['source'];
		if ( 'auto' === $source ) {
			$source = ( is_home() || is_archive() || is_search() ) && ! ls_is_elementor_editor() ? 'current' : 'custom';
		}
		$args = array(
			'source'           => $source,
			'posts_per_page'   => (int) $s['posts_per_page'],
			'category'         => $s['category'],
			'orderby'          => $s['orderby'],
			'exclude_featured' => 'current' !== $source && $this->on( $s, 'exclude_featured' ) ? 'yes' : 'no',
			'card_style'       => $s['card_style'],
			'columns'          => $s['columns'],
			'columns_tablet'   => $s['columns_tablet'],
			'columns_mobile'   => $s['columns_mobile'],
			'bar_title'        => $s['bar_title'],
			'show_count'       => $this->on( $s, 'show_count' ) ? 'yes' : 'no',
			'pagination'       => $this->on( $s, 'pagination' ) ? 'yes' : 'no',
			'read_more'        => $s['read_more'],
		);
		echo '<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20 w-full">';
		if ( $this->on( $s, 'with_sidebar' ) ) {
			echo '<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10"><div class="lg:col-span-8 flex flex-col">';
			ls_render_posts_grid( $args );
			echo '</div><aside class="lg:col-span-4 flex flex-col gap-6">';
			ls_render_archive_sidebar();
			echo '</aside></div>';
		} else {
			ls_render_posts_grid( $args );
		}
		echo '</section>';
	}
}
