<?php
/**
 * Widget: Factory highlight card (image, stats, checklist) + dispatch badge.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plant card widget.
 */
class Larijani_Widget_Plant_Card extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-plant-card';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS کارت کارخانه + آمار', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-image-box';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_card', __( 'کارت تصویری', 'larijani-stone' ) );
		$this->ctl( 'bare', 'switch', __( 'بدون کانتینر بخش (برای ستون‌های المنتور)', 'larijani-stone' ), 'yes' );
		$this->ctl( 'image', 'media', __( 'تصویر', 'larijani-stone' ), 'contact_plant' );
		$this->ctl( 'title', 'text', __( 'عنوان روی تصویر', 'larijani-stone' ), 'کارخانه ماشین‌سازی لاریجانی' );
		$this->ctl( 'subtitle', 'text', __( 'زیرعنوان', 'larijani-stone' ), 'آبیک، مجتمع صنعتی پیروز' );
		$this->ctl( 'image_badge', 'text', __( 'برچسب روی تصویر', 'larijani-stone' ), 'فعال ۱۵+ سال' );
		$this->rep(
			'stats',
			__( 'آمار', 'larijani-stone' ),
			array(
				array( 'value', 'text', __( 'عدد', 'larijani-stone' ), '' ),
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
			),
			array(
				array( 'value' => '۴۰۰+', 'label' => 'مدل قالب نشکن موجود' ),
				array( 'value' => '۱,۲۰۰+', 'label' => 'خط تولید فعال در سراسر ایران' ),
			),
			'{{{ value }}}'
		);
		$this->ctl( 'checks', 'textarea', __( 'لیست تیک‌دار (هر خط یک مورد)', 'larijani-stone' ), "طراحی و ساخت میزهای ویبره با ورق ۱۰ میلی‌متر و شاسی ناودانی سنگین اروپایی\nقالب‌های تزریق پلاستیک ABS با مواد صددرصد نو و فرمول ضد شکست در سرما\nتأمین مستقیم رزین‌های کربوکسیلاتی با درصد جامد بالا برای ایجاد مقاومت فشاری ۹۰ مگاپاسکال", array( 'rows' => 5 ) );
		$this->end();

		$this->section( 'sec_badge', __( 'کارت ارسال', 'larijani-stone' ) );
		$this->ctl( 'show_dispatch', 'switch', __( 'نمایش', 'larijani-stone' ), 'yes' );
		$this->ctl( 'dispatch_icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'truck' );
		$this->ctl( 'dispatch_title', 'text', __( 'عنوان', 'larijani-stone' ), 'ارسال سریع تجهیزات به کل کشور' );
		$this->ctl( 'dispatch_text', 'textarea', __( 'متن', 'larijani-stone' ), 'ارسال محموله‌های قالب و رزین ظرف ۲۴ ساعت از طریق باربری، و حمل دستگاه‌های صنعتی با تریلی اختصاصی به مقصد کلیه استان‌ها و مرزهای صادراتی.' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		ob_start();
		?>
		<div class="flex flex-col gap-space-lg">
			<div class="bg-surface-card rounded-3xl overflow-hidden shadow-sm flex flex-col">
				<div class="relative h-64 w-full bg-surface-dark overflow-hidden">
					<?php echo larijani_img( $s['image'], 'w-full h-full object-cover' ); // phpcs:ignore ?>
					<div class="absolute inset-0 bg-gradient-to-t from-surface-dark via-surface-dark/40 to-transparent"></div>
					<div class="absolute bottom-4 right-4 left-4 flex items-center justify-between gap-2 text-on-tertiary">
						<div class="flex flex-col">
							<span class="font-headline-sm text-headline-sm"><?php echo esc_html( $s['title'] ); ?></span>
							<span class="text-body-sm font-body-sm text-outline-variant"><?php echo esc_html( $s['subtitle'] ); ?></span>
						</div>
						<?php if ( $s['image_badge'] ) : ?><div class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-label-badge font-label-badge text-primary-fixed whitespace-nowrap"><?php echo esc_html( $s['image_badge'] ); ?></div><?php endif; ?>
					</div>
				</div>
				<div class="p-space-lg flex flex-col gap-space-md">
					<?php if ( $s['stats'] ) : ?>
					<div class="grid grid-cols-2 gap-space-sm">
						<?php foreach ( $s['stats'] as $st ) : ?>
						<div class="bg-surface-canvas p-space-sm rounded-xl flex flex-col"><span class="font-headline-md text-headline-md text-primary font-bold"><?php echo esc_html( $st['value'] ); ?></span><span class="text-body-sm font-body-sm text-on-surface-variant"><?php echo esc_html( $st['label'] ); ?></span></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					<div class="flex flex-col gap-space-sm text-body-md font-body-md text-on-surface">
						<?php foreach ( larijani_lines( $s['checks'] ) as $line ) : ?>
						<div class="flex items-start gap-2.5"><i class="bi bi-patch-check-fill text-accent-emerald text-[18px] shrink-0 mt-0.5" aria-hidden="true"></i><span><?php echo esc_html( $line ); ?></span></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php if ( $this->on( $s, 'show_dispatch' ) ) : ?>
			<div class="bg-primary text-on-primary rounded-3xl p-space-lg shadow-md flex items-center gap-space-md">
				<div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 text-[32px] text-secondary-fixed"><?php echo larijani_icon( $s['dispatch_icon'] ); // phpcs:ignore ?></div>
				<div class="flex flex-col gap-1">
					<h3 class="font-headline-sm text-headline-sm text-on-primary"><?php echo esc_html( $s['dispatch_title'] ); ?></h3>
					<p class="font-body-sm text-body-sm text-primary-fixed-dim leading-relaxed"><?php echo esc_html( $s['dispatch_text'] ); ?></p>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php
		$inner = ob_get_clean();
		if ( $this->on( $s, 'bare' ) ) {
			echo $inner; // phpcs:ignore
			return;
		}
		echo '<section class="w-full py-space-xl"><div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">' . $inner . '</div></section>'; // phpcs:ignore
	}
}
