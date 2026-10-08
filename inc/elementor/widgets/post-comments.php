<?php
/**
 * Widget: Post comments (styled WordPress comments).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Comments widget.
 */
class Larijani_Widget_Post_Comments extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-post-comments';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS دیدگاه‌ها و پرسش و پاسخ', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-comments';
	}
	/** @return array */
	public function get_categories() {
		return array( 'larijani-stone-single' );
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب کنار عنوان', 'larijani-stone' ), 'پاسخگویی مستقیم توسط تیم فنی' );
		$this->end();
		$this->style_controls( array( 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		if ( larijani_is_elementor_editor() ) {
			echo '<div class="max-w-7xl mx-auto px-4 py-6"><div class="bg-white rounded-3xl p-8 shadow-sm text-center text-sm text-slate-500">' . esc_html__( 'بخش دیدگاه‌ها در نمای سایت نمایش داده می‌شود.', 'larijani-stone' ) . '</div></div>';
			return;
		}
		echo '<section class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-12 py-6">';
		larijani_render_post_comments( array( 'badge' => $s['badge'] ) );
		echo '</section>';
	}
}
