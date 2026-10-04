<?php
/**
 * Widget: Sticky floating action button (bottom corner).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Floating CTA widget.
 */
class LS_Widget_Floating_CTA extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-floating-cta';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS دکمه شناور تماس', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-button';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'دکمه', 'larijani' ) );
		$this->ctl( 'text', 'text', __( 'متن', 'larijani' ), 'مشاوره راه‌اندازی خط تولید' );
		$this->ctl( 'link', 'url', __( 'لینک', 'larijani' ), 'tel:09122302685' );
		$this->ctl( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'telephone-outbound' );
		$this->ctl( 'position', 'select', __( 'موقعیت', 'larijani' ), 'left', array( 'options' => array( 'left' => __( 'پایین چپ', 'larijani' ), 'right' => __( 'پایین راست', 'larijani' ) ) ) );
		$this->ctl( 'hide_mobile_text', 'switch', __( 'فقط آیکون در موبایل', 'larijani' ), 'yes' );
		$this->end();
		$this->style_controls( array( 'typography' => false, 'layout' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$pos = 'right' === $s['position'] ? 'right-4 sm:right-6' : 'left-4 sm:left-6';
		?>
		<div class="fixed bottom-4 sm:bottom-6 <?php echo esc_attr( $pos ); ?> z-40">
			<a class="inline-flex items-center gap-space-sm px-4 sm:px-space-lg py-3 rounded-full bg-surface-dark text-on-tertiary shadow-[0_8px_30px_rgba(22,29,26,0.35)] hover:bg-primary-container transition-all group" <?php echo ls_link_attrs( $s['link'] ); // phpcs:ignore ?>>
				<span class="w-2.5 h-2.5 rounded-full bg-accent-emerald animate-ping"></span>
				<span class="text-label-nav font-label-nav <?php echo $this->on( $s, 'hide_mobile_text' ) ? 'hidden sm:inline' : ''; ?>"><?php echo esc_html( $s['text'] ); ?></span>
				<?php echo ls_icon( $s['icon'], 'text-[18px] group-hover:-translate-x-1 transition-transform' ); // phpcs:ignore ?>
			</a>
		</div>
		<?php
	}
}
