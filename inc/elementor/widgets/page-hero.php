<?php
/**
 * Widget: Inner page hero (services) – pitch, buttons, metrics and dark feature card.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Page hero.
 */
class LS_Widget_Page_Hero extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-page-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS هیرو صفحات داخلی (خدمات)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-header';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن و دکمه‌ها', 'larijani' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani' ), 'دانش فنی + ماشین‌آلات سنگین + قالب‌های صادراتی' );
		$this->ctl( 'badge_icon', 'icon', __( 'آیکون برچسب', 'larijani' ), 'gear-wide-connected' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani' ), 'خدمات تخصصی، فرمولاسیون و راه‌اندازی صفر تا صد', array( 'rows' => 2 ) );
		$this->ctl( 'title_highlight', 'text', __( 'خط دوم رنگی', 'larijani' ), 'خطوط تولید بتن و سنگ مصنوعی' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'از تجهیز کارگاه در ابعاد ۵۰ تا ۱۰۰۰ مترمربع تا انتقال فرمولاسیون اختصاصی سمنت‌پلاست بدون حباب، مقاوم در برابر یخبندان منفی ۳۰ درجه و بیش از ۴۰۰ طرح قالب انحصاری نشکن.' );
		$this->ctl( 'btn1_text', 'text', __( 'دکمه اول', 'larijani' ), 'درخواست ارزیابی و مشاوره رایگان' );
		$this->ctl( 'btn1_link', 'url', __( 'لینک دکمه اول', 'larijani' ), '#consultation-form' );
		$this->ctl( 'btn1_icon', 'icon', __( 'آیکون دکمه اول', 'larijani' ), 'arrow-down' );
		$this->ctl( 'btn2_text', 'text', __( 'دکمه دوم', 'larijani' ), '۰۹۱۲ ۲۳۰ ۲۶۸۵' );
		$this->ctl( 'btn2_link', 'url', __( 'لینک دکمه دوم', 'larijani' ), 'tel:09122302685' );
		$this->ctl( 'btn2_icon', 'icon', __( 'آیکون دکمه دوم', 'larijani' ), 'telephone' );
		$this->rep(
			'metrics',
			__( 'آمار سریع', 'larijani' ),
			array(
				array( 'value', 'text', __( 'عدد', 'larijani' ), '' ),
				array( 'label', 'text', __( 'عنوان', 'larijani' ), '' ),
			),
			array(
				array( 'value' => '۴۰۰+', 'label' => 'مدل قالب نشکن ABS' ),
				array( 'value' => '۱۵+', 'label' => 'سال سابقه صنعتی' ),
				array( 'value' => '۱۰۰٪', 'label' => 'فرمولاسیون ضدخش' ),
			),
			'{{{ value }}} {{{ label }}}'
		);
		$this->end();

		$this->section( 'sec_card', __( 'کارت تصویری', 'larijani' ) );
		$this->ctl( 'show_card', 'switch', __( 'نمایش کارت', 'larijani' ), 'yes' );
		$this->ctl( 'image', 'media', __( 'تصویر', 'larijani' ), 'services_hero' );
		$this->ctl( 'card_badge', 'text', __( 'برچسب', 'larijani' ), 'سیستم تولید صنعتی پیوسته' );
		$this->ctl( 'card_code', 'text', __( 'کد / متن کوچک', 'larijani' ), 'TECH-SPEC 2026' );
		$this->ctl( 'card_title', 'text', __( 'عنوان', 'larijani' ), 'پکیج انتقال تکنولوژی و دانش فنی' );
		$this->ctl( 'card_desc', 'textarea', __( 'توضیح', 'larijani' ), 'شامل دفترچه استانداردهای اختلاط مصالح دانه‌بندی، روش تزریق پیگمنت‌های اکسید آهن، کنترل اسلامپ بتن پلیمری و ترفندهای پولیش طبیعی سطح سنگ.' );
		$this->ctl( 'card_note', 'text', __( 'یادداشت پایین', 'larijani' ), 'تضمین خروجی بدون حفره (Pinhole-Free)' );
		$this->ctl( 'card_note_right', 'text', __( 'متن سمت چپ پایین', 'larijani' ), 'ضمانت کتبی' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$card = $this->on( $s, 'show_card' );
		?>
		<section class="w-full relative overflow-hidden py-space-xl lg:py-space-2xl bg-gradient-to-b from-surface-canvas via-surface to-surface-canvas">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
					<div class="<?php echo $card ? 'lg:col-span-7' : 'lg:col-span-12'; ?> flex flex-col gap-space-md">
						<?php if ( $s['badge'] ) : ?>
						<div class="inline-flex items-center gap-2 self-start bg-primary-fixed text-on-primary-fixed px-3 py-1 rounded-full text-label-badge font-label-badge shadow-sm"><?php echo ls_icon( $s['badge_icon'], 'text-[14px]' ); // phpcs:ignore ?><span><?php echo esc_html( $s['badge'] ); ?></span></div>
						<?php endif; ?>
						<h1 class="font-black text-surface-dark tracking-tight leading-snug text-[24px] sm:text-[32px]">
							<?php echo $this->t( $s, 'title' ); // phpcs:ignore ?>
							<?php if ( $s['title_highlight'] ) : ?><span class="text-primary-container block mt-1"><?php echo esc_html( $s['title_highlight'] ); ?></span><?php endif; ?>
						</h1>
						<?php if ( $s['desc'] ) : ?><p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl leading-relaxed"><?php echo $this->t( $s, 'desc' ); // phpcs:ignore ?></p><?php endif; ?>
						<div class="flex flex-wrap items-center gap-space-md pt-space-sm">
							<?php if ( $s['btn1_text'] ) : ?>
							<a class="inline-flex items-center justify-center gap-2 bg-primary-container hover:bg-primary text-on-primary font-label-nav text-label-nav px-space-xl py-3 rounded-full shadow-md hover:shadow-lg transition-all" <?php echo ls_link_attrs( $s['btn1_link'] ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn1_text'] ); ?></span><?php echo ls_icon( $s['btn1_icon'], 'text-[16px]' ); // phpcs:ignore ?></a>
							<?php endif; ?>
							<?php if ( $s['btn2_text'] ) : ?>
							<a class="inline-flex items-center justify-center gap-2 bg-surface-card hover:bg-surface-container text-surface-dark font-label-nav text-label-nav px-space-lg py-3 rounded-full shadow-sm transition-all" <?php echo ls_link_attrs( $s['btn2_link'] ); // phpcs:ignore ?>><?php echo ls_icon( $s['btn2_icon'], 'text-[16px] text-primary' ); // phpcs:ignore ?><span dir="ltr"><?php echo esc_html( $s['btn2_text'] ); ?></span></a>
							<?php endif; ?>
						</div>
						<?php if ( $s['metrics'] ) : ?>
						<div class="grid grid-cols-3 gap-space-sm pt-space-md">
							<?php foreach ( $s['metrics'] as $m ) : ?>
							<div class="p-3 bg-surface-card rounded-xl shadow-sm flex flex-col">
								<span class="font-headline-md text-headline-md text-surface-dark leading-none" data-ls-counter><?php echo esc_html( $m['value'] ); ?></span>
								<span class="font-body-sm text-body-sm text-outline mt-1"><?php echo esc_html( $m['label'] ); ?></span>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
					<?php if ( $card ) : ?>
					<div class="lg:col-span-5 relative">
						<div class="relative rounded-2xl overflow-hidden shadow-xl bg-surface-dark text-on-tertiary">
							<div class="h-64 sm:h-72 w-full relative">
								<?php echo ls_img( $s['image'], 'w-full h-full object-cover opacity-85', '', 'large', false ); // phpcs:ignore ?>
								<div class="absolute inset-0 bg-gradient-to-t from-surface-dark via-surface-dark/40 to-transparent"></div>
							</div>
							<div class="p-space-lg flex flex-col gap-space-sm relative -mt-12 bg-surface-dark/95 backdrop-blur-md rounded-t-2xl">
								<div class="flex items-center justify-between gap-2">
									<?php if ( $s['card_badge'] ) : ?><span class="font-label-badge text-label-badge bg-primary-container text-on-primary px-2.5 py-1 rounded-full"><?php echo esc_html( $s['card_badge'] ); ?></span><?php endif; ?>
									<?php if ( $s['card_code'] ) : ?><span class="font-body-sm text-body-sm text-tertiary-fixed-dim" dir="ltr"><?php echo esc_html( $s['card_code'] ); ?></span><?php endif; ?>
								</div>
								<h2 class="font-headline-sm text-headline-sm text-on-tertiary"><?php echo esc_html( $s['card_title'] ); ?></h2>
								<p class="font-body-md text-body-md text-tertiary-fixed-dim"><?php echo esc_html( $s['card_desc'] ); ?></p>
								<?php if ( $s['card_note'] || $s['card_note_right'] ) : ?>
								<div class="pt-2 flex items-center justify-between text-body-sm font-body-sm text-on-tertiary-container gap-2">
									<span class="flex items-center gap-1.5"><i class="bi bi-patch-check-fill text-[16px] text-secondary-fixed" aria-hidden="true"></i><span><?php echo esc_html( $s['card_note'] ); ?></span></span>
									<span class="text-secondary-fixed font-semibold"><?php echo esc_html( $s['card_note_right'] ); ?></span>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
