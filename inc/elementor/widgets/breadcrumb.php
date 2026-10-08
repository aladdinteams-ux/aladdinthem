<?php
/**
 * Widget: Breadcrumb / context bar (automatic trail + status pill or badge).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Breadcrumb widget.
 */
class Larijani_Widget_Breadcrumb extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-breadcrumb';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS مسیر صفحه (بردکرامب)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-product-breadcrumbs';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'مسیر صفحه', 'larijani-stone' ) );
		$this->ctl( 'separator', 'select', __( 'جداکننده', 'larijani-stone' ), 'chevron', array( 'options' => array( 'chevron' => '‹', 'slash' => '/' ) ) );
		$this->ctl( 'custom_current', 'text', __( 'عنوان صفحه جاری (اختیاری)', 'larijani-stone' ), '' );
		$this->ctl(
			'right_style',
			'select',
			__( 'المان سمت چپ', 'larijani-stone' ),
			'status',
			array(
				'options' => array(
					'none'   => __( 'هیچ', 'larijani-stone' ),
					'status' => __( 'وضعیت با نقطه چشمک‌زن', 'larijani-stone' ),
					'badge'  => __( 'متن تیک‌دار + برچسب', 'larijani-stone' ),
				),
			)
		);
		$this->ctl( 'status_text', 'text', __( 'متن وضعیت', 'larijani-stone' ), __( 'خطوط پاسخگویی مستقیم و انبار آبیک فعال است', 'larijani-stone' ), array( 'condition' => array( 'right_style' => 'status' ) ) );
		$this->ctl( 'check_text', 'text', __( 'متن تیک‌دار', 'larijani-stone' ), __( 'پشتیبانی فنی و فرمولاسیون فعال در سراسر ایران', 'larijani-stone' ), array( 'condition' => array( 'right_style' => 'badge' ) ) );
		$this->ctl( 'badge_text', 'text', __( 'برچسب', 'larijani-stone' ), __( 'نسخه صنعتی ۱۴۰۳', 'larijani-stone' ), array( 'condition' => array( 'right_style' => 'badge' ) ) );
		$this->ctl( 'bordered', 'switch', __( 'خط زیرین', 'larijani-stone' ), '' );
		$this->bg_control( 'canvas' );
		$this->end();
		$this->style_controls( array( 'typography' => false ) );
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$items = null;
		if ( $s['custom_current'] ) {
			$items = larijani_breadcrumb_trail();
			$items[ count( $items ) - 1 ][0] = $s['custom_current'];
		}
		?>
		<section class="w-full <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?> py-space-md <?php echo $this->on( $s, 'bordered' ) ? 'border-b border-border-subtle' : ''; ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<div class="flex flex-wrap items-center justify-between gap-space-sm text-body-sm font-body-sm text-on-surface-variant">
					<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'larijani-stone' ); ?>" class="flex items-center flex-wrap gap-space-xs"><?php echo larijani_breadcrumb_html( array( 'separator' => $s['separator'], 'items' => $items, 'current_class' => 'font-bold text-on-surface truncate max-w-xs sm:max-w-md' ) ); // phpcs:ignore ?></nav>
					<?php if ( 'status' === $s['right_style'] && $s['status_text'] ) : ?>
					<div class="inline-flex items-center gap-space-xs bg-surface-card px-space-md py-1 rounded-full shadow-sm text-primary">
						<span class="w-2 h-2 rounded-full bg-accent-emerald animate-pulse"></span>
						<span class="font-label-badge text-label-badge font-bold tracking-wide"><?php echo esc_html( $s['status_text'] ); ?></span>
					</div>
					<?php elseif ( 'badge' === $s['right_style'] ) : ?>
					<div class="hidden sm:flex items-center gap-space-md text-body-sm">
						<?php if ( $s['check_text'] ) : ?><span class="flex items-center gap-1.5 text-accent-emerald"><i class="bi bi-check-circle-fill text-[15px]" aria-hidden="true"></i><span><?php echo esc_html( $s['check_text'] ); ?></span></span><?php endif; ?>
						<?php if ( $s['check_text'] && $s['badge_text'] ) : ?><span class="text-outline-variant">|</span><?php endif; ?>
						<?php if ( $s['badge_text'] ) : ?><span class="font-label-badge text-label-badge bg-secondary-container text-on-secondary-container px-2 py-0.5 rounded-full"><?php echo esc_html( $s['badge_text'] ); ?></span><?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
