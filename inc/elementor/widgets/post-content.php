<?php
/**
 * Widget: Post content (section cards, tags, author box, prev/next) with optional sidebar.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post content widget.
 */
class LS_Widget_Post_Content extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-post-content';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS محتوای نوشته + سایدبار', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-post-content';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'نمایش', 'larijani' ) );
		$this->ctl( 'split_sections', 'switch', __( 'تقسیم محتوا به کارت‌های شماره‌دار (بر اساس H2)', 'larijani' ), 'yes' );
		$this->ctl( 'show_tags', 'switch', __( 'برچسب‌ها', 'larijani' ), 'yes' );
		$this->ctl( 'tags_label', 'text', __( 'عنوان برچسب‌ها', 'larijani' ), 'برچسب‌های تخصصی:' );
		$this->ctl( 'show_author', 'switch', __( 'باکس نویسنده', 'larijani' ), 'yes' );
		$this->ctl( 'author_prefix', 'text', __( 'پیشوند نام نویسنده', 'larijani' ), 'درباره نویسنده:' );
		$this->ctl( 'author_cta', 'text', __( 'لینک تماس با نویسنده', 'larijani' ), 'گفتگوی مستقیم با نویسنده' );
		$this->ctl( 'show_nav', 'switch', __( 'نوشته قبلی/بعدی', 'larijani' ), 'yes' );
		$this->ctl( 'show_related', 'switch', __( 'مقالات مرتبط', 'larijani' ), 'yes' );
		$this->ctl( 'related_title', 'text', __( 'عنوان مقالات مرتبط', 'larijani' ), 'مقالات و فرمولاسیون‌های مرتبط کارگاهی' );
		$this->ctl( 'show_comments', 'switch', __( 'دیدگاه‌ها', 'larijani' ), 'yes' );
		$this->ctl( 'sidebar', 'select', __( 'سایدبار', 'larijani' ), 'default', array( 'options' => array( 'default' => __( 'سایدبار پیش‌فرض قالب (فهرست، پشتیبانی، پربازدیدها)', 'larijani' ), 'widgets' => __( 'فقط ابزارک‌های «سایدبار وبلاگ»', 'larijani' ), 'none' => __( 'بدون سایدبار', 'larijani' ) ) ) );
		$this->end();
		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		if ( ls_is_elementor_editor() && ! is_singular( 'post' ) ) {
			$latest = get_posts( array( 'posts_per_page' => 1 ) );
			if ( $latest ) {
				$GLOBALS['post'] = $latest[0]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				setup_postdata( $latest[0] );
			}
		}
		$side = $s['sidebar'];
		echo '<section class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-12 py-10"><div class="grid grid-cols-1 lg:grid-cols-12 gap-10">';
		echo '<article class="' . ( 'none' === $side ? 'lg:col-span-12' : 'lg:col-span-8' ) . ' flex flex-col gap-10">';
		ls_render_post_body(
			array(
				'split_sections' => $this->on( $s, 'split_sections' ) ? 'yes' : 'no',
				'show_tags'      => $this->on( $s, 'show_tags' ) ? 'yes' : 'no',
				'tags_label'     => $s['tags_label'],
				'show_author'    => $this->on( $s, 'show_author' ) ? 'yes' : 'no',
				'author_prefix'  => $s['author_prefix'],
				'author_cta'     => $s['author_cta'],
				'show_nav'       => $this->on( $s, 'show_nav' ) ? 'yes' : 'no',
			)
		);
		if ( $this->on( $s, 'show_related' ) ) {
			ls_render_related_posts( array( 'title' => $s['related_title'] ) );
		}
		if ( $this->on( $s, 'show_comments' ) && ! ls_is_elementor_editor() ) {
			ls_render_post_comments();
		}
		echo '</article>';
		if ( 'default' === $side ) {
			echo '<aside class="lg:col-span-4 space-y-6">';
			ls_render_single_sidebar();
			echo '</aside>';
		} elseif ( 'widgets' === $side ) {
			echo '<aside class="lg:col-span-4"><div class="sticky top-28 ls-widget-area">';
			dynamic_sidebar( 'blog-sidebar' );
			echo '</div></aside>';
		}
		echo '</div></section>';
		if ( ls_is_elementor_editor() ) {
			wp_reset_postdata();
		}
	}
}
