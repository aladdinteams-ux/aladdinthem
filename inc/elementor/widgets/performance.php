<?php
/**
 * Widget: Dark technical section with feature list and comparison bars.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Performance widget.
 */
class Larijani_Widget_Performance extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-performance';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS نمودار میله‌ای مقایسه (تیره)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-skill-bar';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن و ویژگی‌ها', 'larijani-stone' ) );
		$this->ctl( 'eyebrow', 'text', __( 'متن بالای عنوان', 'larijani-stone' ), 'راز ماندگاری پروژه‌ها' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani-stone' ), 'چرا سنگ‌های ساخته‌شده با قالب و فرمول ما هرگز پوسته و خرد نمی‌شوند؟', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'تفاوت اصلی یک قطعه بتنی معمولی با سنگ پلیمری مهندسی لاریجانی استون، کنترل دقیق منحنی ارتعاش، خروج کامل حباب‌های ریز میکروسکوپی و نسبت آب به سیمان کمتر از ۰.۲۸ با رزین‌های نسل جدید است.' );
		$this->rep(
			'features',
			__( 'ویژگی‌ها', 'larijani-stone' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'speedometer2' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'text', 'text', __( 'توضیح', 'larijani-stone' ), '' ),
			),
			array(
				array( 'icon' => 'speedometer2', 'title' => 'ارتعاش فرکانس متغیر (Variable Frequency)', 'text' => 'جداسازی کامل ریزحباب‌ها بدون ته‌نشین شدن سنگدانه‌های سنگین' ),
				array( 'icon' => 'eyedropper', 'title' => 'پلیمرها و روان‌سازهای فوق کاهنده آب', 'text' => 'روان‌کنندگی عالی بدون افزودن آب اضافه، منجر به مقاومت فشاری ۴ برابر بتن عادی' ),
				array( 'icon' => 'layers', 'title' => 'قالب‌های ABS با کشش حرارتی دقیق', 'text' => 'ضد چسبندگی به ملات، بدون نیاز به اسیدشویی و شکستگی لبه‌ها حین دکفره' ),
			)
		);
		$this->end();

		$this->section( 'sec_chart', __( 'نمودار', 'larijani-stone' ) );
		$this->ctl( 'chart_title', 'text', __( 'عنوان نمودار', 'larijani-stone' ), 'نمودار آزمایشگاهی مقاومت سایشی و یخبندان' );
		$this->ctl( 'chart_badge', 'text', __( 'برچسب', 'larijani-stone' ), 'گزارش آزمایشگاه مقاومت مصالح' );
		$this->rep(
			'bars',
			__( 'میله‌ها', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'value', 'text', __( 'مقدار نوشتاری', 'larijani-stone' ), '' ),
				array( 'percent', 'number', __( 'درصد طول', 'larijani-stone' ), 50, array( 'min' => 0, 'max' => 100 ) ),
				array( 'style', 'select', __( 'رنگ', 'larijani-stone' ), 'gradient', array( 'options' => array( 'gradient' => __( 'گرادیان سبز', 'larijani-stone' ), 'grey' => __( 'خاکستری', 'larijani-stone' ), 'dark' => __( 'تیره', 'larijani-stone' ) ) ) ),
			),
			array(
				array( 'label' => 'سنگ پلیمری لاریجانی استون (با رزین فرموله)', 'value' => '۵۲ مگاپاسکال (فشاری)', 'percent' => 95, 'style' => 'gradient' ),
				array( 'label' => 'تراورتن طبیعی و سنگ‌های رگه‌دار آهکی', 'value' => '۳۲ مگاپاسکال (حساس به تخلخل)', 'percent' => 62, 'style' => 'grey' ),
				array( 'label' => 'موزاییک پرسی بتنی سنتی (کارگاهی)', 'value' => '۱۸ مگاپاسکال (جذب آب بالا)', 'percent' => 35, 'style' => 'dark' ),
			),
			'{{{ label }}}'
		);
		$this->rep(
			'chips',
			__( 'کارت‌های کوچک زیر نمودار', 'larijani-stone' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'thermometer-half' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'text', 'text', __( 'مقدار', 'larijani-stone' ), '' ),
			),
			array(
				array( 'icon' => 'thermometer-half', 'title' => 'تحمل حرارتی', 'text' => '-۳۰°C تا +۱۵۰°C' ),
				array( 'icon' => 'brush', 'title' => 'ثبات پیگمنت', 'text' => 'اکسید آهن بایر آلمان' ),
				array( 'icon' => 'recycle', 'title' => 'قابلیت بازیافت', 'text' => '۱۰۰٪ پلیمر دوستدار محیط' ),
			)
		);
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$bar = array(
			'gradient' => 'bg-gradient-to-l from-primary-container to-accent-emerald',
			'grey'     => 'bg-outline',
			'dark'     => 'bg-tertiary',
		);
		?>
		<section class="w-full bg-surface-dark text-on-tertiary py-space-2xl relative overflow-hidden">
			<div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-primary-container/20 blur-3xl pointer-events-none"></div>
			<div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-secondary-container/10 blur-3xl pointer-events-none"></div>
			<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin relative z-10">
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
					<div class="lg:col-span-5 flex flex-col gap-space-md">
						<?php if ( $s['eyebrow'] ) : ?><span class="text-secondary-fixed text-label-badge font-label-badge tracking-wider"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
						<h2 class="text-headline-lg font-headline-lg text-on-tertiary tracking-tight"><?php echo $this->t( $s, 'title' ); // phpcs:ignore ?></h2>
						<?php if ( $s['desc'] ) : ?><p class="text-body-md font-body-md text-on-tertiary-container leading-relaxed"><?php echo $this->t( $s, 'desc' ); // phpcs:ignore ?></p><?php endif; ?>
						<div class="flex flex-col gap-space-sm pt-space-xs">
							<?php foreach ( $s['features'] as $f ) : ?>
							<div class="flex items-start gap-space-sm">
								<div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-primary-fixed shrink-0 text-[16px]"><?php echo larijani_icon( $f['icon'] ); // phpcs:ignore ?></div>
								<div class="flex flex-col"><span class="text-title-card font-title-card text-on-tertiary"><?php echo esc_html( $f['title'] ); ?></span><span class="text-body-sm font-body-sm text-outline-variant"><?php echo esc_html( $f['text'] ); ?></span></div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="lg:col-span-7 bg-white/5 backdrop-blur-md p-space-lg rounded-2xl">
						<div class="flex items-center justify-between flex-wrap gap-2 pb-space-md">
							<div class="flex items-center gap-space-xs"><i class="bi bi-graph-up-arrow text-primary-fixed text-[18px]" aria-hidden="true"></i><span class="text-title-card font-title-card text-on-tertiary"><?php echo esc_html( $s['chart_title'] ); ?></span></div>
							<?php if ( $s['chart_badge'] ) : ?><span class="text-label-badge font-label-badge bg-primary-container text-on-primary px-2.5 py-1 rounded-full"><?php echo esc_html( $s['chart_badge'] ); ?></span><?php endif; ?>
						</div>
						<div class="space-y-space-md py-space-sm">
							<?php foreach ( $s['bars'] as $i => $b ) : ?>
							<div>
								<div class="flex justify-between gap-3 text-body-sm font-body-sm mb-1 text-on-tertiary-container">
									<span><?php echo esc_html( $b['label'] ); ?></span>
									<span class="font-bold <?php echo 0 === $i ? 'text-secondary-fixed' : 'text-outline-variant'; ?> shrink-0"><?php echo esc_html( $b['value'] ); ?></span>
								</div>
								<div class="w-full bg-surface-dark h-3 rounded-full overflow-hidden"><div class="<?php echo esc_attr( $bar[ $b['style'] ] ?? $bar['grey'] ); ?> h-full rounded-full transition-all duration-1000" style="width: <?php echo esc_attr( max( 0, min( 100, (int) $b['percent'] ) ) ); ?>%" data-ls-bar></div></div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php if ( $s['chips'] ) : ?>
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm mt-space-md pt-space-md">
							<?php foreach ( $s['chips'] as $c ) : ?>
							<div class="bg-surface-dark p-3 rounded-xl flex items-center gap-2">
								<?php echo larijani_icon( $c['icon'], 'text-secondary-fixed text-[18px]' ); // phpcs:ignore ?>
								<div class="flex flex-col"><span class="text-body-sm font-body-sm text-on-tertiary"><?php echo esc_html( $c['title'] ); ?></span><span class="text-label-badge font-label-badge text-outline-variant"><?php echo esc_html( $c['text'] ); ?></span></div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
