<?php
/**
 * Widget: Blog archive hero (title, search, category chips).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Blog hero widget.
 */
class Larijani_Widget_Blog_Hero extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-blog-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS سربرگ وبلاگ / آرشیو', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-archive-title';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone', 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani-stone' ), 'مرجع مهندسی سنگ مصنوعی و افزودنی‌های بتن' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani-stone' ), 'آرشیو جامع مقالات، دانشنامه و راهنمای فنی سنگ مصنوعی و بتن پلیمری', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'مجموعه پژوهش‌های میدانی، دستورالعمل‌های اختلاط کارگاهی، عیب‌یابی خطوط تولید سمنت‌پلاست و راهکارهای ارتقای دوام قالب‌های پلیمری صنعتی به قلم مهندسان لاریجانی استون.' );
		$this->ctl( 'auto_title', 'switch', __( 'عنوان خودکار در صفحات دسته/برچسب/جستجو', 'larijani-stone' ), 'yes' );
		$this->ctl( 'show_search', 'switch', __( 'جستجو', 'larijani-stone' ), 'yes' );
		$this->ctl( 'search_placeholder', 'text', __( 'راهنمای جستجو', 'larijani-stone' ), 'جستجو در بین مقالات، فرمول‌ها، عیوب بتن، رزین LS و تجهیزات...' );
		$this->ctl( 'search_button', 'text', __( 'دکمه جستجو', 'larijani-stone' ), 'جستجو' );
		$this->ctl( 'show_cats', 'switch', __( 'دسته‌بندی‌ها', 'larijani-stone' ), 'yes' );
		$this->ctl( 'all_label', 'text', __( 'عنوان «همه»', 'larijani-stone' ), 'همه مقالات' );
		$this->ctl( 'cats_limit', 'number', __( 'حداکثر دسته‌ها', 'larijani-stone' ), 6 );
		$this->end();
		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		larijani_render_blog_hero(
			array(
				'badge'              => $s['badge'],
				'title'              => $s['title'],
				'desc'               => $s['desc'],
				'auto_title'         => $this->on( $s, 'auto_title' ) ? 'yes' : 'no',
				'show_search'        => $this->on( $s, 'show_search' ) ? 'yes' : 'no',
				'search_placeholder' => $s['search_placeholder'],
				'search_button'      => $s['search_button'],
				'show_cats'          => $this->on( $s, 'show_cats' ) ? 'yes' : 'no',
				'all_label'          => $s['all_label'],
				'cats_limit'         => (int) $s['cats_limit'],
			)
		);
	}
}
