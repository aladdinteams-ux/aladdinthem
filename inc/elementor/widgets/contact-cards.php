<?php
/**
 * Widget: Contact department cards (phone + buttons, or opening hours).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact cards widget.
 */
class Larijani_Widget_Contact_Cards extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-contact-cards';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS کارت‌های تماس واحدها', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-envelope';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_items', __( 'کارت‌ها', 'larijani-stone' ) );
		$this->rep(
			'items',
			__( 'کارت‌ها', 'larijani-stone' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'gear-wide-connected' ),
				array( 'icon_tone', 'select', __( 'رنگ آیکون', 'larijani-stone' ), 'sage', array( 'options' => array( 'sage' => __( 'سبز ملایم', 'larijani-stone' ), 'sfixed' => __( 'سبز روشن', 'larijani-stone' ), 'high' => __( 'خاکستری-آبی', 'larijani-stone' ) ) ) ),
				array( 'badge', 'text', __( 'برچسب', 'larijani-stone' ), '' ),
				array( 'badge_tone', 'select', __( 'رنگ برچسب', 'larijani-stone' ), 'light', array( 'options' => array( 'light' => __( 'خاکستری', 'larijani-stone' ), 'sage' => __( 'سبز', 'larijani-stone' ), 'amber' => __( 'کهربایی', 'larijani-stone' ) ) ) ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), '' ),
				array( 'mode', 'select', __( 'محتوای پایین کارت', 'larijani-stone' ), 'phone', array( 'options' => array( 'phone' => __( 'شماره + دو دکمه', 'larijani-stone' ), 'hours' => __( 'ساعات کاری', 'larijani-stone' ) ) ) ),
				array( 'phone', 'text', __( 'شماره تلفن', 'larijani-stone' ), '' ),
				array( 'phone_icon', 'icon', __( 'آیکون کنار شماره', 'larijani-stone' ), 'telephone-fill' ),
				array( 'btn1_text', 'text', __( 'دکمه ۱', 'larijani-stone' ), 'تماس مستقیم' ),
				array( 'btn2_text', 'text', __( 'دکمه ۲ (واتساپ)', 'larijani-stone' ), 'واتس‌اپ واحد' ),
				array( 'btn2_dark', 'switch', __( 'دکمه ۲ تیره', 'larijani-stone' ), '' ),
				array( 'whatsapp', 'text', __( 'شماره واتساپ (98912...)', 'larijani-stone' ), '' ),
				array( 'hours', 'textarea', __( 'ساعات (هر خط: عنوان|ساعت|رنگ emerald/amber/muted)', 'larijani-stone' ), '', array( 'rows' => 4 ) ),
			),
			array(
				array( 'icon' => 'gear-wide-connected', 'icon_tone' => 'sage', 'badge' => 'خط تولید و میز ویبره', 'title' => 'مشاوره راه‌اندازی و ماشین‌آلات', 'desc' => 'مشاوره فنی راه‌اندازی خطوط سنگ پلیمری، میکسر بتن، میزهای ویبره با ضربه افقی و عمودی، و محاسبات تناژ کارگاهی.', 'mode' => 'phone', 'phone' => '09122302685', 'phone_icon' => 'telephone-fill', 'btn1_text' => 'تماس مستقیم', 'btn2_text' => 'واتس‌اپ واحد', 'whatsapp' => '989122302685' ),
				array( 'icon' => 'layers-fill', 'icon_tone' => 'sfixed', 'badge' => 'قالب نشکن و رنگ', 'badge_tone' => 'sage', 'title' => 'واحد فروش قالب، رنگ و رزین', 'desc' => 'ارسال کاتالوگ بیش از ۴۰۰ طرح قالب ABS و سیلیکونی، پیگمنت‌های معدنی بایر، و روان‌سازهای پلی‌کربوکسیلاتی.', 'mode' => 'phone', 'phone' => '09354431321', 'phone_icon' => 'cart3', 'btn1_text' => 'تماس فروش', 'btn2_text' => 'دریافت کاتالوگ', 'btn2_dark' => 'yes', 'whatsapp' => '989354431321' ),
				array( 'icon' => 'buildings', 'icon_tone' => 'high', 'badge' => 'کارخانه و انبار مرکزی', 'badge_tone' => 'amber', 'title' => 'آدرس و تقویم کاری کارخانه', 'desc' => 'قزوین، آبیک، بلوار خلیج فارس، مجتمع صنعتی پیروز، پلاک ۱۵', 'mode' => 'hours', 'hours' => "شنبه تا چهارشنبه:|۰۸:۰۰ الی ۱۸:۰۰|emerald\nپنج‌شنبه‌ها:|۰۸:۰۰ الی ۱۴:۰۰|amber\nجمعه و ایام تعطیل رسمی:|تعطیل (انبار تحویل هماهنگ)|muted" ),
			)
		);
		$this->columns_controls( 3, 1, 1, 4 );
		$this->end();
		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$badge_tones = array(
			'light' => 'bg-surface-container-high text-on-surface-variant',
			'sage'  => 'bg-secondary-fixed-dim/30 text-on-secondary-container',
			'amber' => 'bg-accent-amber/10 text-accent-amber',
		);
		$cols = array( 1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3', 4 => 'md:grid-cols-4' );
		?>
		<section class="w-full py-space-lg">
			<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
				<div class="grid grid-cols-1 <?php echo esc_attr( $cols[ (int) $s['columns'] ] ?? 'md:grid-cols-3' ); ?> gap-space-lg">
					<?php foreach ( $s['items'] as $it ) : ?>
					<div class="bg-surface-card rounded-2xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
						<div class="flex flex-col gap-space-md">
							<div class="flex items-center justify-between gap-2">
								<div class="w-14 h-14 rounded-2xl <?php echo esc_attr( larijani_tone( $it['icon_tone'], 'soft' ) ); ?> flex items-center justify-center shadow-inner text-[28px]"><?php echo larijani_icon( $it['icon'] ); // phpcs:ignore ?></div>
								<?php if ( $it['badge'] ) : ?><span class="<?php echo esc_attr( $badge_tones[ $it['badge_tone'] ] ?? $badge_tones['light'] ); ?> font-label-badge text-label-badge px-2.5 py-1 rounded-full"><?php echo esc_html( $it['badge'] ); ?></span><?php endif; ?>
							</div>
							<div class="flex flex-col gap-1">
								<h2 class="font-headline-sm text-headline-sm text-on-surface"><?php echo esc_html( $it['title'] ); ?></h2>
								<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $it['desc'] ); ?></p>
							</div>
						</div>
						<?php if ( 'hours' === $it['mode'] ) : ?>
						<div class="mt-space-lg flex flex-col gap-space-xs text-body-sm font-body-sm bg-surface-canvas p-space-sm rounded-xl">
							<?php foreach ( larijani_lines( $it['hours'] ) as $line ) : ?>
								<?php $p = array_map( 'trim', explode( '|', $line ) ); ?>
							<div class="flex items-center justify-between gap-2 py-1 <?php echo 'muted' === ( $p[2] ?? '' ) ? 'text-outline' : 'text-on-surface'; ?>">
								<span class="flex items-center gap-1 <?php echo 'muted' === ( $p[2] ?? '' ) ? '' : 'text-on-surface-variant'; ?>"><i class="bi <?php echo 'muted' === ( $p[2] ?? '' ) ? 'bi-clock-history' : 'bi-clock'; ?> text-[14px] <?php echo 'amber' === ( $p[2] ?? '' ) ? 'text-accent-amber' : ( 'emerald' === ( $p[2] ?? '' ) ? 'text-accent-emerald' : '' ); ?>" aria-hidden="true"></i><span><?php echo esc_html( $p[0] ); ?></span></span>
								<span class="<?php echo 'muted' === ( $p[2] ?? '' ) ? '' : 'font-bold'; ?>"><?php echo esc_html( $p[1] ?? '' ); ?></span>
							</div>
							<?php endforeach; ?>
						</div>
						<?php else : ?>
						<div class="pt-space-lg flex flex-col gap-space-sm">
							<a class="bg-surface-canvas rounded-xl p-space-sm flex items-center justify-between" dir="ltr" href="<?php echo esc_url( larijani_tel( $it['phone'] ) ); ?>">
								<span class="font-headline-sm text-headline-sm text-primary font-bold"><?php echo esc_html( larijani_phone_display( $it['phone'] ) ); ?></span>
								<?php echo larijani_icon( $it['phone_icon'], 'text-primary text-[18px]' ); // phpcs:ignore ?>
							</a>
							<div class="grid grid-cols-2 gap-space-xs">
								<?php if ( $it['btn1_text'] ) : ?>
								<a class="inline-flex items-center justify-center gap-1.5 bg-primary-container text-on-primary py-2.5 rounded-full font-label-nav text-label-nav hover:bg-primary transition-colors" href="<?php echo esc_url( larijani_tel( $it['phone'] ) ); ?>"><i class="bi bi-telephone text-[16px]" aria-hidden="true"></i><span><?php echo esc_html( $it['btn1_text'] ); ?></span></a>
								<?php endif; ?>
								<?php if ( $it['btn2_text'] ) : ?>
								<a class="inline-flex items-center justify-center gap-1.5 <?php echo 'yes' === $it['btn2_dark'] ? 'bg-surface-dark text-on-tertiary hover:bg-tertiary' : 'bg-accent-emerald text-white hover:opacity-95'; ?> py-2.5 rounded-full font-label-nav text-label-nav transition-colors" href="<?php echo esc_url( larijani_whatsapp_url( $it['whatsapp'] ? $it['whatsapp'] : $it['phone'] ) ); ?>" rel="noopener noreferrer" target="_blank"><i class="bi bi-whatsapp text-[16px]" aria-hidden="true"></i><span><?php echo esc_html( $it['btn2_text'] ); ?></span></a>
								<?php endif; ?>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
