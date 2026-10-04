<?php
/**
 * Widget: Shop / catalog header with quick metrics and price-list download card.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shop hero widget.
 */
class LS_Widget_Shop_Hero extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-shop-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS سربرگ فروشگاه / کاتالوگ', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-woo-settings';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن', 'larijani' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani' ), 'تضمین اصالت متریال و مواد پتروشیمی نو کره' );
		$this->ctl( 'badge_icon', 'icon', __( 'آیکون برچسب', 'larijani' ), 'shield-check' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani' ), 'کاتالوگ جامع تجهیزات، قالب‌ها و متریال سنگ مصنوعی', array( 'rows' => 2 ) );
		$this->ctl( 'auto_title', 'switch', __( 'در صفحات دسته محصول، عنوان دسته نمایش داده شود', 'larijani' ), 'yes' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'تأمین بی‌واسطه بیش از ۴۰۰ مدل قالب‌های تخصصی ABS نشکن، ماشین‌آلات لرزشی و اختلاط با گارانتی تعویض شرکتی، به همراه افزودنی‌های پلیمری و رنگدانه‌های ضد فرابنفش برای کارگاه‌ها و انبوه‌سازان سراسر کشور.' );
		$this->rep(
			'metrics',
			__( 'شاخص‌ها', 'larijani' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'grid-3x3-gap-fill' ),
				array( 'tone', 'select', __( 'رنگ', 'larijani' ), 'primary', array( 'options' => ls_tone_options() ) ),
				array( 'value', 'text', __( 'مقدار', 'larijani' ), '' ),
				array( 'label', 'text', __( 'عنوان', 'larijani' ), '' ),
			),
			array(
				array( 'icon' => 'grid-3x3-gap-fill', 'tone' => 'primary', 'value' => '۴۰۰+', 'label' => 'طرح قالب سنگ و کف' ),
				array( 'icon' => 'gear-wide-connected', 'tone' => 'emerald', 'value' => '۲۴ ماه', 'label' => 'گارانتی تعویض ماشین‌آلات' ),
				array( 'icon' => 'truck', 'tone' => 'amber', 'value' => '۴۸ ساعته', 'label' => 'ارسال مستقیم از کارخانه' ),
			),
			'{{{ value }}} {{{ label }}}'
		);
		$this->end();

		$this->section( 'sec_card', __( 'کارت دانلود', 'larijani' ) );
		$this->ctl( 'show_card', 'switch', __( 'نمایش', 'larijani' ), 'yes' );
		$this->ctl( 'card_label', 'text', __( 'عنوان کوچک', 'larijani' ), 'لیست قیمت روز' );
		$this->ctl( 'card_chip', 'text', __( 'برچسب تاریخ', 'larijani' ), 'بهمن ۱۴۰۴' );
		$this->ctl( 'card_title', 'text', __( 'عنوان', 'larijani' ), 'دانلود مستقیم کاتالوگ و جداول فنی' );
		$this->ctl( 'card_desc', 'textarea', __( 'توضیح', 'larijani' ), 'شامل تمامی ابعاد هندسی، متراژ لازم بر متر مربع، ضخامت ورق ABS و مشخصات الکتروموتورهای میز ویبره.' );
		$this->ctl( 'card_btn1', 'text', __( 'دکمه دانلود', 'larijani' ), 'دانلود PDF لیست قیمت' );
		$this->ctl( 'card_btn1_link', 'url', __( 'فایل / لینک دانلود', 'larijani' ), '#' );
		$this->ctl( 'card_btn2', 'text', __( 'دکمه دوم', 'larijani' ), 'استعلام تیراژ' );
		$this->ctl( 'card_btn2_link', 'url', __( 'لینک دکمه دوم', 'larijani' ), 'tel:09122302685' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$title = $s['title'];
		$desc  = $s['desc'];
		if ( $this->on( $s, 'auto_title' ) && function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
			$title = single_term_title( '', false );
			$desc  = term_description() ? wp_strip_all_tags( term_description() ) : $desc;
		} elseif ( $this->on( $s, 'auto_title' ) && is_search() ) {
			/* translators: %s query */
			$title = sprintf( __( 'نتایج جستجو برای «%s»', 'larijani' ), get_search_query() );
		}
		$card = $this->on( $s, 'show_card' );
		?>
		<section class="w-full bg-gradient-to-b from-surface-container-high/40 via-surface-canvas to-surface-canvas py-space-xl">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter">
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-center">
					<div class="<?php echo $card ? 'lg:col-span-8' : 'lg:col-span-12'; ?> flex flex-col gap-space-sm">
						<?php if ( $s['badge'] ) : ?>
						<div class="inline-flex items-center gap-space-xs self-start px-space-md py-1 rounded-full bg-secondary-container text-on-secondary-fixed text-body-sm font-semibold"><?php echo ls_icon( $s['badge_icon'], 'text-accent-emerald text-base' ); // phpcs:ignore ?><span><?php echo esc_html( $s['badge'] ); ?></span></div>
						<?php endif; ?>
						<h1 class="font-headline-lg text-headline-lg text-on-surface"><?php echo ls_kses( $title ); // phpcs:ignore ?></h1>
						<?php if ( $desc ) : ?><p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed"><?php echo esc_html( $desc ); ?></p><?php endif; ?>
						<?php if ( $s['metrics'] ) : ?>
						<div class="grid grid-cols-2 sm:grid-cols-3 gap-space-md pt-space-sm">
							<?php foreach ( $s['metrics'] as $i => $m ) : ?>
							<div class="p-space-md rounded-xl bg-surface-card shadow-sm flex items-center gap-space-sm <?php echo 2 === $i ? 'col-span-2 sm:col-span-1' : ''; ?>">
								<div class="w-10 h-10 rounded-lg <?php echo esc_attr( ls_tone( $m['tone'], 'soft' ) ); ?> flex items-center justify-center text-xl shrink-0"><?php echo ls_icon( $m['icon'] ); // phpcs:ignore ?></div>
								<div class="flex flex-col"><span class="font-headline-sm text-headline-sm text-on-surface"><?php echo esc_html( $m['value'] ); ?></span><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $m['label'] ); ?></span></div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
					<?php if ( $card ) : ?>
					<div class="lg:col-span-4 flex flex-col gap-space-md p-space-lg rounded-2xl bg-surface-dark text-inverse-on-surface shadow-xl relative overflow-hidden">
						<div class="absolute -top-12 -left-12 w-40 h-40 rounded-full bg-primary-container/20 blur-3xl pointer-events-none"></div>
						<div class="flex items-center justify-between relative">
							<div class="flex items-center gap-space-xs text-secondary-fixed"><i class="bi bi-file-earmark-pdf-fill text-xl" aria-hidden="true"></i><span class="font-label-nav text-label-nav"><?php echo esc_html( $s['card_label'] ); ?></span></div>
							<?php if ( $s['card_chip'] ) : ?><span class="font-body-sm text-body-sm px-2 py-0.5 rounded-full bg-white/10 text-surface-container-highest"><?php echo esc_html( $s['card_chip'] ); ?></span><?php endif; ?>
						</div>
						<h2 class="font-headline-sm text-headline-sm text-surface-bright relative"><?php echo esc_html( $s['card_title'] ); ?></h2>
						<p class="font-body-sm text-body-sm text-surface-container-highest/80 leading-relaxed relative"><?php echo esc_html( $s['card_desc'] ); ?></p>
						<div class="flex flex-col sm:flex-row gap-space-sm pt-space-xs relative">
							<?php if ( $s['card_btn1'] ) : ?><a class="flex-1 inline-flex items-center justify-center gap-space-xs px-space-md py-space-sm rounded-full bg-primary-container hover:bg-primary text-on-primary font-label-nav text-label-nav transition-all shadow-md" <?php echo ls_link_attrs( $s['card_btn1_link'] ); // phpcs:ignore ?>><i class="bi bi-cloud-arrow-down-fill text-lg" aria-hidden="true"></i><span><?php echo esc_html( $s['card_btn1'] ); ?></span></a><?php endif; ?>
							<?php if ( $s['card_btn2'] ) : ?><a class="inline-flex items-center justify-center gap-space-xs px-space-md py-space-sm rounded-full bg-white/10 hover:bg-white/20 text-surface-bright font-label-nav text-label-nav transition-all" <?php echo ls_link_attrs( $s['card_btn2_link'] ); // phpcs:ignore ?>><i class="bi bi-telephone-fill" aria-hidden="true"></i><span><?php echo esc_html( $s['card_btn2'] ); ?></span></a><?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
