<?php
/**
 * Widget: Portfolio hero (headline, mini stats, highlights bar).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Portfolio hero widget.
 */
class LS_Widget_Portfolio_Hero extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-portfolio-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS هیرو نمونه‌کارها + آمار', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-number-field';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن', 'larijani' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani' ), 'پورتفولیو پروژه‌های کشوری و بین‌المللی' );
		$this->ctl( 'badge_note', 'text', __( 'متن کنار برچسب', 'larijani' ), 'خروجی ماشین‌آلات و قالب‌های فوق مهندسی لاریجانی استون' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani' ), 'نمونه‌کارها و پروژه‌های اجرا شده با قالب‌ها و فرمولاسیون لاریجانی استون', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'ثمره اعتماد بیش از ۱۰,۰۰۰ کارگاه مستقل، شهرداری‌های کلان‌شهرها و معماران پیشرو. از هندسه‌های سه‌بعدی صخره‌ای تا کفپوش‌های فوق مقاوم صنعتی با پیگمنت‌های پایدار و رزین‌های اصلاح‌شده پلیمری.' );
		$this->end();

		$this->section( 'sec_mini', __( 'کارت‌های کناری', 'larijani' ) );
		$this->rep(
			'mini',
			__( 'کارت‌ها', 'larijani' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'patch-check-fill' ),
				array( 'icon_tone', 'select', __( 'رنگ آیکون', 'larijani' ), 'dark', array( 'options' => array( 'dark' => __( 'تیره', 'larijani' ), 'sage' => __( 'سبز ملایم', 'larijani' ) ) ) ),
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'text', 'text', __( 'متن', 'larijani' ), '' ),
				array( 'value', 'text', __( 'مقدار سمت چپ', 'larijani' ), '' ),
				array( 'value_tone', 'select', __( 'رنگ مقدار', 'larijani' ), 'emerald', array( 'options' => array( 'emerald' => __( 'زمردی', 'larijani' ), 'primary' => __( 'سبز برند', 'larijani' ) ) ) ),
			),
			array(
				array( 'icon' => 'patch-check-fill', 'icon_tone' => 'dark', 'title' => 'استاندارد ملی و بین‌المللی', 'text' => 'تست مقاومت سایشی و یخ‌زدگی', 'value' => 'A+', 'value_tone' => 'emerald' ),
				array( 'icon' => 'geo-alt-fill', 'icon_tone' => 'sage', 'title' => 'گستره کشوری', 'text' => '۳۱ استان + صادرات به ۴ کشور منطقه', 'value' => '۱۰۰٪', 'value_tone' => 'primary' ),
			)
		);
		$this->end();

		$this->section( 'sec_stats', __( 'نوار آمار', 'larijani' ) );
		$this->rep(
			'stats',
			__( 'آمار', 'larijani' ),
			array(
				array( 'value', 'text', __( 'عدد', 'larijani' ), '' ),
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'text', 'text', __( 'توضیح', 'larijani' ), '' ),
			),
			array(
				array( 'value' => '۱۰,۰۰۰+', 'title' => 'پروژه موفق شهری و ویلایی', 'text' => 'اجرا شده توسط تولیدکنندگان و پیمانکاران' ),
				array( 'value' => '۴۰۰+', 'title' => 'طرح و قالب انحصاری', 'text' => 'ABS نشکن، سیلیکونی و تزریقی صنعتی' ),
				array( 'value' => '۱۵ سال', 'title' => 'سابقه پیوسته ماشین‌سازی', 'text' => 'طراحی میزهای ارتعاش و پان میکسر' ),
				array( 'value' => '۱۰۰٪', 'title' => 'فرمولاسیون پایدار', 'text' => 'بدون خردشدگی در دمای منفی ۳۰ درجه' ),
			),
			'{{{ value }}} {{{ title }}}'
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
		$icon_tones = array( 'dark' => 'bg-surface-dark text-primary-fixed', 'sage' => 'bg-secondary-container text-on-secondary-container' );
		?>
		<section class="relative w-full bg-surface-canvas overflow-hidden pt-space-xl pb-space-2xl">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<?php if ( $s['badge'] ) : ?>
				<div class="flex items-center flex-wrap gap-space-sm mb-space-md">
					<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-fixed text-label-badge font-label-badge tracking-wider"><span class="w-2 h-2 rounded-full bg-accent-emerald animate-pulse"></span><?php echo esc_html( $s['badge'] ); ?></span>
					<?php if ( $s['badge_note'] ) : ?><span class="text-outline text-body-sm font-body-sm hidden sm:inline">•</span><span class="text-outline text-body-sm font-body-sm hidden sm:inline"><?php echo esc_html( $s['badge_note'] ); ?></span><?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-end">
					<div class="<?php echo $s['mini'] ? 'lg:col-span-8' : 'lg:col-span-12'; ?> flex flex-col gap-space-sm">
						<h1 class="text-headline-lg lg:text-display-hero font-headline-lg lg:font-display-hero text-surface-dark tracking-tight leading-tight"><?php echo $this->t( $s, 'title' ); // phpcs:ignore ?></h1>
						<?php if ( $s['desc'] ) : ?><p class="text-body-lg font-body-lg text-tertiary max-w-3xl leading-relaxed"><?php echo $this->t( $s, 'desc' ); // phpcs:ignore ?></p><?php endif; ?>
					</div>
					<?php if ( $s['mini'] ) : ?>
					<div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-space-sm">
						<?php foreach ( $s['mini'] as $m ) : ?>
						<div class="bg-surface-card p-space-md rounded-xl shadow-sm flex items-center justify-between gap-3 flex-1">
							<div class="flex items-center gap-space-sm">
								<div class="w-10 h-10 rounded-lg <?php echo esc_attr( $icon_tones[ $m['icon_tone'] ] ?? $icon_tones['dark'] ); ?> flex items-center justify-center text-[20px]"><?php echo ls_icon( $m['icon'] ); // phpcs:ignore ?></div>
								<div class="flex flex-col"><span class="text-title-card font-title-card text-surface-dark"><?php echo esc_html( $m['title'] ); ?></span><span class="text-body-sm font-body-sm text-outline"><?php echo esc_html( $m['text'] ); ?></span></div>
							</div>
							<span class="<?php echo 'primary' === $m['value_tone'] ? 'text-primary' : 'text-accent-emerald'; ?> text-headline-sm font-headline-sm"><?php echo esc_html( $m['value'] ); ?></span>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php if ( $s['stats'] ) : ?>
				<div class="grid grid-cols-2 md:grid-cols-4 gap-space-md mt-space-xl">
					<?php foreach ( $s['stats'] as $st ) : ?>
					<div class="bg-surface-card p-space-lg rounded-xl shadow-sm flex flex-col gap-1 transition-all hover:shadow-md">
						<span class="text-headline-lg font-headline-lg text-primary tracking-tight" data-ls-counter><?php echo esc_html( $st['value'] ); ?></span>
						<span class="text-body-md font-body-md text-surface-dark font-bold"><?php echo esc_html( $st['title'] ); ?></span>
						<span class="text-body-sm font-body-sm text-outline"><?php echo esc_html( $st['text'] ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
