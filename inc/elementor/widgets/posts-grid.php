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
class Larijani_Widget_Posts_Grid extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-posts-grid';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS شبکه مقالات (آرشیو)', 'larijani-stone' );
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
		$this->section( 'sec_query', __( 'کوئری', 'larijani-stone' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani-stone' ), 'auto', array( 'options' => array( 'auto' => __( 'خودکار (کوئری جاری در آرشیو، وگرنه سفارشی)', 'larijani-stone' ), 'current' => __( 'کوئری جاری صفحه (آرشیو)', 'larijani-stone' ), 'custom' => __( 'سفارشی', 'larijani-stone' ), 'related' => __( 'مرتبط با نوشته جاری', 'larijani-stone' ) ) ) );
		$this->ctl( 'posts_per_page', 'number', __( 'تعداد', 'larijani-stone' ), 6 );
		$this->ctl( 'category', 'select', __( 'دسته', 'larijani-stone' ), '', array( 'options' => larijani_term_options( 'category' ) ) );
		$this->ctl( 'orderby', 'select', __( 'مرتب‌سازی', 'larijani-stone' ), 'date', array( 'options' => array( 'date' => __( 'جدیدترین', 'larijani-stone' ), 'views' => __( 'پربازدیدترین', 'larijani-stone' ), 'comments' => __( 'پربحث‌ترین', 'larijani-stone' ), 'rand' => __( 'تصادفی', 'larijani-stone' ) ) ) );
		$this->ctl( 'exclude_featured', 'switch', __( 'حذف مقاله ویژه از لیست', 'larijani-stone' ), 'yes' );
		$this->ctl( 'pagination', 'switch', __( 'صفحه‌بندی', 'larijani-stone' ), 'yes' );
		$this->end();

		$this->section( 'sec_layout', __( 'نمایش', 'larijani-stone' ) );
		$this->ctl( 'bar_title', 'text', __( 'عنوان بالای لیست', 'larijani-stone' ), 'جدیدترین مقالات و راهنماهای کارگاهی' );
		$this->ctl( 'show_count', 'switch', __( 'نمایش شمارنده', 'larijani-stone' ), 'yes' );
		$this->ctl( 'card_style', 'select', __( 'سبک کارت', 'larijani-stone' ), 'archive', array( 'options' => array( 'archive' => __( 'آرشیو', 'larijani-stone' ), 'home' => __( 'صفحه اصلی', 'larijani-stone' ), 'related' => __( 'مرتبط', 'larijani-stone' ) ) ) );
		$this->ctl( 'read_more', 'text', __( 'متن ادامه مطلب', 'larijani-stone' ), 'ادامه مطلب تخصصی' );
		$this->columns_controls( 2, 2, 1, 4 );
		$this->ctl( 'with_sidebar', 'switch', __( 'سایدبار پیش‌فرض قالب در کنار لیست', 'larijani-stone' ), 'yes' );
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
			$source = ( is_home() || is_archive() || is_search() ) && ! larijani_is_elementor_editor() ? 'current' : 'custom';
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
			larijani_render_posts_grid( $args );
			echo '</div><aside class="lg:col-span-4 flex flex-col gap-6">';
			larijani_render_archive_sidebar();
			echo '</aside></div>';
		} else {
			larijani_render_posts_grid( $args );
		}
		echo '</section>';
	}
}
