<?php
/**
 * Widget: Roadmap steps + workshop gallery cards.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Steps widget.
 */
class Larijani_Widget_Steps extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-steps';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS مراحل / نقشه راه', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-number-field';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani-stone' ) );
		$this->heading_controls(
			array(
				'eyebrow' => 'مراحل گام‌به‌گام راه‌اندازی',
				'title'   => 'از سوله خالی تا اولین تولید تجاری سنگ پلیمری',
				'desc'    => 'نقشه راه روشن و استاندارد لاریجانی استون برای ورود امن سرمایه‌گذاران و تولیدکنندگان به بازار پرسود مصالح نوین ساختمانی.',
				'align'   => 'center',
				'style'   => 'token',
			)
		);
		$this->end();

		$this->section( 'sec_steps', __( 'مراحل', 'larijani-stone' ) );
		$this->rep(
			'steps',
			__( 'مراحل', 'larijani-stone' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'bar-chart-line' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), '' ),
				array( 'note', 'text', __( 'یادداشت پایین', 'larijani-stone' ), '' ),
				array( 'tone', 'select', __( 'رنگ شماره', 'larijani-stone' ), 'primary', array( 'options' => array( 'dark' => __( 'تیره', 'larijani-stone' ), 'primary' => __( 'سبز برند', 'larijani-stone' ), 'sage' => __( 'سبز ثانویه', 'larijani-stone' ) ) ) ),
			),
			array(
				array( 'icon' => 'bar-chart-line', 'tone' => 'dark', 'title' => 'مشاوره و امکان‌سنجی اولیه', 'desc' => 'بررسی متراژ فضا (برق ۳ فاز یا تک‌فاز، انبارش قالب‌ها) متناسب با بودجه اولیه و پتانسیل بازار منطقه شما.', 'note' => 'تحلیل ظرفیت تولید' ),
				array( 'icon' => 'tools', 'title' => 'انتخاب قالب و ساخت ماشین‌آلات', 'desc' => 'انتخاب پرفروش‌ترین قالب‌های منطقه و ساخت سفارشی میز ویبره و میکسر صنعتی با بالاترین متریال کارخانه‌ای.', 'note' => 'استاندارد صادراتی' ),
				array( 'icon' => 'eyedropper', 'title' => 'تأمین رزین و پیگمنت مرغوب', 'desc' => 'ارسال مستقیم رزین‌های پلی‌کربوکسیلات آلمانی، رنگدانه‌های معدنی اکسید آهن بایر و روان‌کننده‌های فوق زودگیر.', 'note' => 'مواد اولیه تضمین‌شده' ),
				array( 'icon' => 'book', 'title' => 'ارسال فرمولاسیون و آموزش', 'desc' => 'انتقال فرمول سمنت‌پلاست بدون حباب با مقاومت در برابر سرما و گرما به همراه تست عملی اولین بچ تولیدی کارگاه.', 'note' => 'تست مقاومت فشاری' ),
				array( 'icon' => 'headset', 'tone' => 'sage', 'title' => 'پشتیبانی دائم و توسعه قالب‌ها', 'desc' => 'پشتیبانی فنی بی‌پایان، رفع ایرادات خط، معرفی پروژه‌های منطقه‌ای و ارائه تخفیف‌های ویژه بر روی قالب‌های جدید.', 'note' => 'توسعه مستمر خط' ),
			)
		);
		$this->columns_controls( 5, 2, 1, 6 );
		$this->end();

		$this->section( 'sec_gallery', __( 'کارت‌های تصویری زیر مراحل', 'larijani-stone' ) );
		$this->rep(
			'gallery',
			__( 'کارت‌ها', 'larijani-stone' ),
			array(
				array( 'image', 'media', __( 'تصویر', 'larijani-stone' ), '' ),
				array( 'eyebrow', 'text', __( 'متن کوچک', 'larijani-stone' ), '' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), '' ),
			),
			array(
				array( 'image' => 'services_gallery_1', 'eyebrow' => 'تست کارگاهی', 'title' => 'تست تراکم ارتعاشی در کارخانه آبیک', 'desc' => 'تمام میزهای ویبره پیش از تحویل با بارگذاری کامل قالب‌ها تحت سنجش شتاب‌سنج دیجیتال کالیبره می‌شوند.' ),
				array( 'image' => 'services_gallery_2', 'eyebrow' => 'انبارش استاندارد', 'title' => 'دسترسی دائم به قطعات یدکی و قالب‌ها', 'desc' => 'بزرگ‌ترین بانک قالب کشور با تحویل سریع ۴۸ ساعته جهت جلوگیری از هرگونه توقف در خط تولید شما.' ),
			)
		);
		$this->bg_control( 'canvas' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$tones = array(
			'dark'    => 'bg-surface-dark text-on-tertiary',
			'primary' => 'bg-primary-container text-on-primary',
			'sage'    => 'bg-secondary text-on-secondary',
		);
		$cols  = array( 1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4', 5 => 'md:grid-cols-5', 6 => 'md:grid-cols-6' );
		?>
		<section class="w-full py-space-2xl <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin flex flex-col gap-space-2xl">
				<?php echo $this->heading( $s, array( 'mb' => '' ) ); // phpcs:ignore ?>
				<div class="grid grid-cols-1 <?php echo esc_attr( ( (int) $s['columns_tablet'] > 1 ? 'sm:grid-cols-2 ' : '' ) . ( $cols[ (int) $s['columns'] ] ?? 'md:grid-cols-5' ) ); ?> gap-space-md relative">
					<?php foreach ( $s['steps'] as $i => $st ) : ?>
					<div class="bg-surface-card p-space-md rounded-2xl shadow-sm flex flex-col justify-between gap-space-md relative overflow-hidden">
						<div class="flex items-center justify-between">
							<span class="w-9 h-9 rounded-full <?php echo esc_attr( $tones[ $st['tone'] ] ?? $tones['primary'] ); ?> flex items-center justify-center font-headline-sm text-headline-sm"><?php echo esc_html( larijani_fa_num( $i + 1 ) ); ?></span>
							<?php echo larijani_icon( $st['icon'], 'text-[22px] text-outline' ); // phpcs:ignore ?>
						</div>
						<div class="flex flex-col gap-1">
							<h3 class="font-title-card text-title-card text-surface-dark"><?php echo esc_html( $st['title'] ); ?></h3>
							<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?php echo esc_html( $st['desc'] ); ?></p>
						</div>
						<?php if ( $st['note'] ) : ?><span class="font-body-sm text-body-sm text-secondary font-medium"><?php echo esc_html( $st['note'] ); ?></span><?php endif; ?>
					</div>
					<?php endforeach; ?>
				</div>
				<?php if ( $s['gallery'] ) : ?>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
					<?php foreach ( $s['gallery'] as $g ) : ?>
					<div class="bg-surface-card p-space-lg rounded-2xl shadow-sm flex flex-col sm:flex-row items-center gap-space-md">
						<div class="w-full sm:w-44 h-36 rounded-xl overflow-hidden shrink-0"><?php echo larijani_img( $g['image'], 'w-full h-full object-cover', $g['title'], 'ls-card' ); // phpcs:ignore ?></div>
						<div class="flex flex-col gap-1">
							<span class="font-label-badge text-label-badge text-outline"><?php echo esc_html( $g['eyebrow'] ); ?></span>
							<h3 class="font-headline-sm text-headline-sm text-surface-dark"><?php echo esc_html( $g['title'] ); ?></h3>
							<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?php echo esc_html( $g['desc'] ); ?></p>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
