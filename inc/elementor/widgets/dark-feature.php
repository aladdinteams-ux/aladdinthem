<?php
/**
 * Widget: Dark spotlight with feature list and circular metric (formulation).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Dark feature widget.
 */
class LS_Widget_Dark_Feature extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-dark-feature';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS باکس تیره + نمودار دایره‌ای', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-counter-circle';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'محتوا', 'larijani' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani' ), 'دانش فنی سمنت‌پلاست انحصاری' );
		$this->ctl( 'badge_icon', 'icon', __( 'آیکون برچسب', 'larijani' ), 'shield-check' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani' ), 'فرمولاسیون سنگ مصنوعی لاریجانی استون چه تفاوتی ایجاد می‌کند؟', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'سنگ پلیمری غیراستاندارد معمولاً پس از اولین زمستان دچار پوسته شدن، شوره و مات‌شدگی رنگ می‌شود. فرمولاسیون اختصاصی لاریجانی با اصلاح زنجیره پلیمری و توازن نسبت آب به سیمان، مقاومت مکانیکی را تا ۳ برابر بتن سنتی افزایش می‌دهد.' );
		$this->rep(
			'features',
			__( 'ویژگی‌ها', 'larijani' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'check2-circle' ),
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'text', 'text', __( 'توضیح', 'larijani' ), '' ),
			),
			array(
				array( 'icon' => 'snow', 'title' => 'تحمل یخبندان تا ۳۰- درجه', 'text' => 'جذب آب کمتر از ۳ درصد مانع یخ‌زدگی درونی می‌شود.' ),
				array( 'icon' => 'sun', 'title' => 'ثبات رنگ در برابر UV', 'text' => 'استفاده از پیگمنت‌های معدنی نسوز ضد تغییر رنگ.' ),
				array( 'icon' => 'droplet-half', 'title' => 'سطح آینه‌ای بدون تخلخل', 'text' => 'تخلیه کامل هوای محبوس با روان‌ساز نسل سوم.' ),
				array( 'icon' => 'shield-fill', 'title' => 'مقاومت خمشی ۶۰ مگاپاسکال', 'text' => 'مناسب برای تردد خودروهای سنگین و باربر شهری.' ),
			)
		);
		$this->end();

		$this->section( 'sec_ring', __( 'نمودار دایره‌ای', 'larijani' ) );
		$this->ctl( 'show_ring', 'switch', __( 'نمایش', 'larijani' ), 'yes' );
		$this->ctl( 'ring_percent', 'number', __( 'درصد پر شدن', 'larijani' ), 87, array( 'min' => 0, 'max' => 100 ) );
		$this->ctl( 'ring_value', 'text', __( 'عدد وسط', 'larijani' ), '۸۷٪' );
		$this->ctl( 'ring_label', 'text', __( 'متن وسط', 'larijani' ), 'تراکم بالاتر از بتن معمولی' );
		$this->ctl( 'stat1_value', 'text', __( 'آمار ۱ – عدد', 'larijani' ), '۰.۲۸' );
		$this->ctl( 'stat1_label', 'text', __( 'آمار ۱ – عنوان', 'larijani' ), 'نسبت آب به سیمان W/C' );
		$this->ctl( 'stat2_value', 'text', __( 'آمار ۲ – عدد', 'larijani' ), '۲۴ ساعت' );
		$this->ctl( 'stat2_label', 'text', __( 'آمار ۲ – عنوان', 'larijani' ), 'زمان خروج کامل از قالب' );
		$this->bg_control( 'surface' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$pct  = max( 0, min( 100, (float) $s['ring_percent'] ) );
		$circ = 2 * M_PI * 70;
		$ring = $this->on( $s, 'show_ring' );
		?>
		<section class="w-full py-space-2xl <?php echo esc_attr( ls_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<div class="bg-surface-dark rounded-2xl p-space-lg lg:p-space-xl text-on-tertiary shadow-xl relative overflow-hidden">
					<div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-primary-container/20 blur-3xl pointer-events-none"></div>
					<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center relative z-10">
						<div class="<?php echo $ring ? 'lg:col-span-7' : 'lg:col-span-12'; ?> flex flex-col gap-space-md">
							<?php if ( $s['badge'] ) : ?><div class="inline-flex items-center gap-2 self-start bg-secondary-fixed text-on-secondary-fixed px-3 py-1 rounded-full text-label-badge font-label-badge"><?php echo ls_icon( $s['badge_icon'], 'text-[14px]' ); // phpcs:ignore ?><span><?php echo esc_html( $s['badge'] ); ?></span></div><?php endif; ?>
							<h2 class="font-headline-lg text-headline-lg text-on-tertiary"><?php echo $this->t( $s, 'title' ); // phpcs:ignore ?></h2>
							<?php if ( $s['desc'] ) : ?><p class="font-body-md text-body-md text-tertiary-fixed-dim leading-relaxed"><?php echo $this->t( $s, 'desc' ); // phpcs:ignore ?></p><?php endif; ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm pt-space-xs">
								<?php foreach ( $s['features'] as $f ) : ?>
								<div class="flex items-start gap-2.5">
									<?php echo ls_icon( $f['icon'], 'text-secondary-fixed text-[18px] shrink-0 mt-0.5' ); // phpcs:ignore ?>
									<div class="flex flex-col">
										<span class="font-title-card text-title-card text-on-tertiary"><?php echo esc_html( $f['title'] ); ?></span>
										<span class="font-body-sm text-body-sm text-tertiary-fixed-dim"><?php echo esc_html( $f['text'] ); ?></span>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
						<?php if ( $ring ) : ?>
						<div class="lg:col-span-5 flex flex-col items-center justify-center p-space-md bg-surface-dark/60 rounded-xl">
							<div class="relative w-48 h-48 flex items-center justify-center">
								<svg class="w-48 h-48 -rotate-90" viewBox="0 0 160 160" aria-hidden="true">
									<circle cx="80" cy="80" r="70" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="12"></circle>
									<circle cx="80" cy="80" r="70" fill="none" stroke="#b8cdae" stroke-width="12" stroke-linecap="round" stroke-dasharray="<?php echo esc_attr( round( $circ, 2 ) ); ?>" stroke-dashoffset="<?php echo esc_attr( round( $circ * ( 1 - $pct / 100 ), 2 ) ); ?>" data-ls-ring="<?php echo esc_attr( round( $circ * ( 1 - $pct / 100 ), 2 ) ); ?>" style="transition: stroke-dashoffset 1.4s ease"></circle>
								</svg>
								<div class="absolute flex flex-col items-center text-center px-6">
									<span class="font-headline-lg text-headline-lg text-on-tertiary leading-none"><?php echo esc_html( $s['ring_value'] ); ?></span>
									<span class="font-body-sm text-body-sm text-tertiary-fixed-dim mt-1"><?php echo esc_html( $s['ring_label'] ); ?></span>
								</div>
							</div>
							<div class="w-full mt-space-md pt-space-sm flex justify-around text-center text-body-sm font-body-sm gap-3">
								<div><span class="block font-headline-sm text-headline-sm text-on-tertiary"><?php echo esc_html( $s['stat1_value'] ); ?></span><span class="text-tertiary-fixed-dim"><?php echo esc_html( $s['stat1_label'] ); ?></span></div>
								<div><span class="block font-headline-sm text-headline-sm text-secondary-fixed"><?php echo esc_html( $s['stat2_value'] ); ?></span><span class="text-tertiary-fixed-dim"><?php echo esc_html( $s['stat2_label'] ); ?></span></div>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
