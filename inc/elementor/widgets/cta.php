<?php
/**
 * Widget: Call-to-action banners (dark card, dark strip, soft card, dark + callback form, catalog form).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * CTA widget.
 */
class Larijani_Widget_CTA extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-cta';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS بنر دعوت به اقدام (CTA)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl(
			'variant',
			'select',
			__( 'سبک', 'larijani-stone' ),
			'dark-card',
			array(
				'options' => array(
					'dark-card'    => __( 'کارت تیره با دو دکمه', 'larijani-stone' ),
					'dark-strip'   => __( 'نوار تمام‌عرض تیره', 'larijani-stone' ),
					'soft-card'    => __( 'کارت سبز ملایم', 'larijani-stone' ),
					'dark-form'    => __( 'کارت تیره + فرم درخواست تماس', 'larijani-stone' ),
					'catalog-form' => __( 'کارت تیره + فرم دریافت کاتالوگ', 'larijani-stone' ),
					'consult-band' => __( 'نوار تیره مشاوره + کارت شماره‌ها', 'larijani-stone' ),
				),
			)
		);
		$this->ctl( 'icon', 'icon', __( 'آیکون (نوار / کارت ملایم)', 'larijani-stone' ), 'people', array( 'condition' => array( 'variant' => array( 'dark-strip', 'soft-card' ) ) ) );
		$this->ctl( 'badge', 'text', __( 'برچسب بالای عنوان', 'larijani-stone' ), '' );
		$this->ctl( 'badge_icon', 'icon', __( 'آیکون برچسب', 'larijani-stone' ), '' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani-stone' ), 'آماده راه‌اندازی خط تولید سنگ مصنوعی و قطعات بتنی هستید؟', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'همین حالا با مهندسین و مشاورین ارشد لاریجانی استون تماس بگیرید و لیست قیمت و فرمولاسیون جامع را به صورت رایگان دریافت کنید.' );
		$this->ctl( 'center_mobile', 'switch', __( 'وسط‌چین در موبایل', 'larijani-stone' ), '' );
		$this->end();

		$this->section( 'sec_band', __( 'نوار مشاوره', 'larijani-stone' ), 'content', array( 'variant' => 'consult-band' ) );
		$this->ctl( 'checks', 'textarea', __( 'موارد تیک‌دار (هر خط یک مورد)', 'larijani-stone' ), "تست قالب‌ها قبل از بارگیری\nارائه طرح اختلاط اختصاصی\nآموزش ترکیب رنگ رگه‌ای و گرانیتی" );
		$this->ctl( 'phone1_label', 'text', __( 'عنوان شماره اول', 'larijani-stone' ), 'ارتباط مستقیم با مدیر فنی (مهندس لاریجانی):' );
		$this->ctl( 'phone1', 'text', __( 'شماره اول', 'larijani-stone' ), '09122302685' );
		$this->ctl( 'phone2_label', 'text', __( 'عنوان شماره دوم', 'larijani-stone' ), 'مسئول واحد فروش و توزیع قالب و رزین:' );
		$this->ctl( 'phone2', 'text', __( 'شماره دوم', 'larijani-stone' ), '09354431321' );
		$this->end();

		$this->section( 'sec_buttons', __( 'دکمه‌ها', 'larijani-stone' ) );
		$this->ctl( 'btn1_text', 'text', __( 'دکمه اول', 'larijani-stone' ), 'تماس مستقیم: ۰۹۱۲۲۳۰۲۶۸۵' );
		$this->ctl( 'btn1_sub', 'text', __( 'متن کوچک بالای دکمه اول (اختیاری)', 'larijani-stone' ), '' );
		$this->ctl( 'btn1_link', 'url', __( 'لینک دکمه اول', 'larijani-stone' ), 'tel:09122302685' );
		$this->ctl( 'btn1_icon', 'icon', __( 'آیکون دکمه اول', 'larijani-stone' ), 'telephone-fill' );
		$this->ctl( 'btn2_text', 'text', __( 'دکمه دوم', 'larijani-stone' ), 'ارسال پیام در واتساپ' );
		$this->ctl( 'btn2_link', 'url', __( 'لینک دکمه دوم', 'larijani-stone' ), array( 'url' => 'https://wa.me/989122302685', 'is_external' => true ) );
		$this->ctl( 'btn2_icon', 'icon', __( 'آیکون دکمه دوم', 'larijani-stone' ), '' );
		$this->ctl( 'btn2_style', 'select', __( 'سبک دکمه دوم', 'larijani-stone' ), 'glass', array( 'options' => array( 'glass' => __( 'شیشه‌ای', 'larijani-stone' ), 'emerald' => __( 'سبز واتساپ', 'larijani-stone' ), 'darker' => __( 'تیره با حاشیه', 'larijani-stone' ), 'white' => __( 'سفید', 'larijani-stone' ) ) ) );
		$this->end();

		$this->section( 'sec_form', __( 'فرم', 'larijani-stone' ), 'content', array( 'variant' => array( 'dark-form', 'catalog-form' ) ) );
		$this->ctl( 'form_name', 'text', __( 'نام فرم (در درخواست‌ها)', 'larijani-stone' ), 'درخواست تماس فوری' );
		$this->ctl( 'form_title', 'text', __( 'عنوان کارت فرم', 'larijani-stone' ), 'درخواست مشاوره اختصاصی' );
		$this->ctl( 'form_desc', 'textarea', __( 'توضیح کارت فرم', 'larijani-stone' ), 'شماره همراه خود را وارد کنید تا کارشناس خط تولید حداکثر ظرف ۱۵ دقیقه با شما تماس بگیرد.' );
		$this->ctl( 'form_placeholder', 'text', __( 'متن راهنمای فیلد', 'larijani-stone' ), '0912...' );
		$this->ctl( 'form_button', 'text', __( 'متن دکمه', 'larijani-stone' ), 'ثبت درخواست تماس فوری' );
		$this->ctl( 'form_note', 'text', __( 'یادداشت زیر فرم', 'larijani-stone' ), '' );
		$this->ctl( 'form_success', 'text', __( 'پیام موفقیت', 'larijani-stone' ), 'درخواست شما ثبت شد؛ به زودی تماس خواهیم گرفت.' );
		$this->end();

		$this->section( 'sec_layout', __( 'چیدمان', 'larijani-stone' ) );
		$this->bg_control( 'none' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * A button.
	 *
	 * @param array  $s Settings.
	 * @param int    $n Button index.
	 * @param string $class Classes.
	 * @return string
	 */
	protected function button( $s, $n, $class ) {
		$text = $s[ "btn{$n}_text" ];
		if ( ! $text ) {
			return '';
		}
		$sub  = 1 === $n ? $s['btn1_sub'] : '';
		$icon = larijani_icon( $s[ "btn{$n}_icon" ], 'text-lg' );
		$body = $sub ? '<div class="flex flex-col text-right"><span class="text-xs opacity-80 leading-none">' . esc_html( $sub ) . '</span><span class="font-black mt-1" dir="ltr">' . esc_html( $text ) . '</span></div>' : '<span class="text-center">' . esc_html( $text ) . '</span>';
		return '<a class="' . esc_attr( $class ) . '" ' . larijani_link_attrs( $s[ "btn{$n}_link" ] ) . '>' . $icon . $body . '</a>';
	}

	/**
	 * Mini phone form.
	 *
	 * @param array  $s Settings.
	 * @param string $layout stack|inline.
	 * @return string
	 */
	protected function form( $s, $layout = 'stack' ) {
		ob_start();
		?>
		<form class="flex <?php echo 'inline' === $layout ? 'flex-col sm:flex-row' : 'flex-col'; ?> gap-2" data-ls-form>
			<?php
			echo larijani_form_hidden_fields( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
				$s['form_name'],
				array(
					'phone' => array(
						'label'    => __( 'شماره تماس', 'larijani-stone' ),
						// The inline variant is a free text box: accept a phone number or an e-mail.
						'type'     => 'inline' === $layout ? 'contact' : 'tel',
						'required' => true,
					),
				)
			);
			?>
			<?php if ( 'inline' === $layout ) : ?>
			<input class="flex-1 px-4 py-3 sm:py-3.5 rounded-xl bg-white text-gray-900 placeholder:text-gray-400 text-xs sm:text-sm focus:ring-2 focus:ring-primary-container border-0" name="fields[phone]" placeholder="<?php echo esc_attr( $s['form_placeholder'] ); ?>" aria-label="<?php echo esc_attr( $s['form_placeholder'] ? $s['form_placeholder'] : __( 'شماره تماس', 'larijani-stone' ) ); ?>" type="text" required>
			<button class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-xl bg-primary-container hover:bg-primary text-white font-bold text-xs sm:text-sm shadow-md transition whitespace-nowrap" type="submit"><?php echo esc_html( $s['form_button'] ); ?></button>
			<?php else : ?>
			<input class="w-full px-4 py-2.5 rounded-xl bg-white/10 text-white placeholder:text-white/50 font-body-md text-body-md focus:bg-white/15 text-left" dir="ltr" name="fields[phone]" placeholder="<?php echo esc_attr( $s['form_placeholder'] ); ?>" aria-label="<?php esc_attr_e( 'شماره تماس', 'larijani-stone' ); ?>" type="tel" required>
			<button class="w-full py-2.5 rounded-xl bg-primary-container text-on-primary font-label-nav text-label-nav hover:bg-primary transition-colors flex items-center justify-center gap-space-xs" type="submit"><span><?php echo esc_html( $s['form_button'] ); ?></span><i class="bi bi-send-fill text-sm" aria-hidden="true"></i></button>
			<?php endif; ?>
			<div class="hidden font-body-sm text-body-sm text-accent-emerald text-center w-full" data-ls-success><?php echo esc_html( $s['form_success'] ); ?></div>
			<div class="hidden ls-form-error text-center w-full" data-ls-error></div>
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$v       = $s['variant'];
		$center  = $this->on( $s, 'center_mobile' ) ? ' text-center lg:text-right' : ' text-right';
		$bg      = larijani_section_bg( $s['section_bg'] );
		$badge   = '';
		if ( $s['badge'] ) {
			$badge_class = 'soft-card' === $v ? 'bg-white text-primary' : ( 'catalog-form' === $v ? 'bg-primary-container text-white rounded-md' : 'bg-white/10 text-secondary-fixed' );
			$badge       = '<div class="inline-flex w-fit items-center gap-2 px-3 py-1 ' . ( 'catalog-form' === $v ? '' : 'rounded-full ' ) . esc_attr( $badge_class ) . ' font-label-badge text-label-badge mb-3">' . larijani_icon( $s['badge_icon'], 'text-accent-emerald' ) . esc_html( $s['badge'] ) . '</div>';
		}
		$btn1_cls = 'bg-primary-container hover:bg-primary text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-full shadow-lg transition-all flex items-center justify-center gap-2 max-w-full min-w-0';
		$btn2_map = array(
			'glass'   => 'bg-white/10 hover:bg-white/20 text-white font-medium text-xs sm:text-sm px-6 py-3.5 rounded-full border border-white/20 transition-all flex items-center justify-center gap-2 max-w-full min-w-0',
			'emerald' => 'bg-accent-emerald hover:opacity-90 text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-full shadow-lg transition-all flex items-center justify-center gap-2 max-w-full min-w-0',
			'darker'  => 'bg-surface-footer/80 hover:bg-surface-footer text-white font-label-nav text-label-nav px-6 py-3.5 rounded-full border border-white/10 transition-colors flex items-center justify-center gap-2 max-w-full min-w-0',
			'white'   => 'bg-surface-card text-on-surface hover:bg-surface-canvas px-space-md py-3 rounded-full font-label-nav text-label-nav shadow-sm transition-all flex items-center justify-center gap-2 max-w-full min-w-0',
		);
		$btn2_cls = $btn2_map[ $s['btn2_style'] ] ?? $btn2_map['glass'];

		if ( 'consult-band' === $v ) :
			?>
			<section class="w-full bg-surface-dark text-on-tertiary py-space-2xl my-space-lg relative overflow-hidden">
				<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin relative z-10">
					<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
						<div class="lg:col-span-8 flex flex-col gap-space-md">
							<?php if ( $s['badge'] ) : ?>
							<div class="inline-flex items-center gap-space-xs text-primary-fixed font-label-badge text-label-badge bg-primary-container/40 px-space-md py-1 rounded-full w-fit"><?php echo larijani_icon( $s['badge_icon'], 'text-[16px]' ); // phpcs:ignore ?><span><?php echo esc_html( $s['badge'] ); ?></span></div>
							<?php endif; ?>
							<h2 class="font-headline-lg text-headline-lg text-on-tertiary"><?php echo larijani_kses( $s['title'] ); // phpcs:ignore ?></h2>
							<?php if ( $s['desc'] ) : ?><p class="font-body-lg text-body-lg text-on-tertiary-container leading-relaxed"><?php echo esc_html( $s['desc'] ); ?></p><?php endif; ?>
							<?php $checks = larijani_lines( $s['checks'] ?? '' ); ?>
							<?php if ( $checks ) : ?>
							<div class="flex flex-wrap items-center gap-space-lg pt-space-xs font-body-sm text-body-sm text-outline-variant">
								<?php foreach ( $checks as $c ) : ?><div class="flex items-center gap-1"><i class="bi bi-check-lg text-primary-fixed text-[18px]" aria-hidden="true"></i><span><?php echo esc_html( $c ); ?></span></div><?php endforeach; ?>
							</div>
							<?php endif; ?>
						</div>
						<div class="lg:col-span-4 flex flex-col gap-space-md bg-surface-footer/80 p-space-lg rounded-2xl">
							<?php if ( ! empty( $s['phone1'] ) ) : ?>
							<div class="flex flex-col gap-1">
								<span class="text-body-sm font-body-sm text-outline-variant"><?php echo esc_html( $s['phone1_label'] ); ?></span>
								<a class="font-display-hero text-headline-md text-primary-fixed font-bold hover:text-white transition-colors flex items-center justify-end gap-2" dir="ltr" href="<?php echo esc_url( larijani_tel( $s['phone1'] ) ); ?>"><i class="bi bi-telephone text-[22px]" aria-hidden="true"></i><?php echo esc_html( larijani_phone_display( $s['phone1'] ) ); ?></a>
							</div>
							<?php endif; ?>
							<?php if ( ! empty( $s['phone2'] ) ) : ?>
							<div class="flex flex-col gap-1">
								<span class="text-body-sm font-body-sm text-outline-variant"><?php echo esc_html( $s['phone2_label'] ); ?></span>
								<a class="font-headline-sm text-headline-sm text-on-tertiary hover:text-primary-fixed transition-colors flex items-center justify-end gap-2" dir="ltr" href="<?php echo esc_url( larijani_tel( $s['phone2'] ) ); ?>"><i class="bi bi-phone text-[20px]" aria-hidden="true"></i><?php echo esc_html( larijani_phone_display( $s['phone2'] ) ); ?></a>
							</div>
							<?php endif; ?>
							<?php echo $this->button( $s, 1, 'w-full text-center py-3 rounded-full bg-primary-container hover:bg-primary text-on-primary font-label-nav text-label-nav transition-all shadow-md flex items-center justify-center gap-2' ); // phpcs:ignore ?>
						</div>
					</div>
				</div>
			</section>
			<?php
			return;
		endif;

		if ( 'dark-strip' === $v ) :
			?>
			<section class="w-full bg-surface-dark text-on-tertiary py-space-xl">
				<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin flex flex-col sm:flex-row items-center justify-between gap-space-md">
					<div class="flex items-center gap-space-md">
						<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shrink-0 text-2xl"><?php echo larijani_icon( $s['icon'] ); // phpcs:ignore ?></div>
						<div class="flex flex-col">
							<span class="font-headline-sm text-headline-sm text-on-tertiary"><?php echo larijani_kses( $s['title'] ); // phpcs:ignore ?></span>
							<?php if ( $s['desc'] ) : ?><span class="font-body-sm text-body-sm text-tertiary-fixed-dim"><?php echo esc_html( $s['desc'] ); ?></span><?php endif; ?>
						</div>
					</div>
					<div class="flex items-center gap-space-sm shrink-0">
						<?php echo $this->button( $s, 1, 'inline-flex items-center gap-2 bg-secondary-fixed text-on-secondary-fixed font-label-nav text-label-nav px-space-lg py-2.5 rounded-full hover:bg-secondary-fixed-dim transition-colors' ); // phpcs:ignore ?>
						<?php echo $this->button( $s, 2, 'inline-flex items-center gap-2 bg-white/10 text-white font-label-nav text-label-nav px-space-lg py-2.5 rounded-full hover:bg-white/20 transition-colors' ); // phpcs:ignore ?>
					</div>
				</div>
			</section>
			<?php
			return;
		endif;

		if ( 'soft-card' === $v ) :
			?>
			<section class="w-full py-space-xl <?php echo esc_attr( $bg ); ?>">
				<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
					<div class="bg-secondary-container/60 rounded-3xl p-space-lg lg:p-space-xl flex flex-col md:flex-row items-center justify-between gap-space-lg">
						<div class="flex items-center gap-space-md">
							<div class="w-14 h-14 rounded-2xl bg-surface-card flex items-center justify-center text-primary shrink-0 shadow-sm text-[28px]"><?php echo larijani_icon( $s['icon'] ); // phpcs:ignore ?></div>
							<div class="flex flex-col">
								<?php echo $badge; // phpcs:ignore ?>
								<span class="font-headline-sm text-headline-sm text-on-secondary-container"><?php echo larijani_kses( $s['title'] ); // phpcs:ignore ?></span>
								<?php if ( $s['desc'] ) : ?><span class="text-body-md font-body-md text-on-secondary-fixed-variant"><?php echo esc_html( $s['desc'] ); ?></span><?php endif; ?>
							</div>
						</div>
						<div class="flex flex-wrap items-center gap-space-sm shrink-0">
							<?php echo $this->button( $s, 1, 'inline-flex items-center gap-2 bg-primary text-on-primary px-space-lg py-3 rounded-full font-label-nav text-label-nav shadow-sm hover:bg-primary-container transition-all' ); // phpcs:ignore ?>
							<?php echo $this->button( $s, 2, $btn2_map['white'] ); // phpcs:ignore ?>
						</div>
					</div>
				</div>
			</section>
			<?php
			return;
		endif;

		$is_form    = in_array( $v, array( 'dark-form', 'catalog-form' ), true );
		$card_bg    = 'catalog-form' === $v ? 'bg-[#262E23]' : 'bg-surface-dark';
		?>
		<section class="py-10 sm:py-14 <?php echo esc_attr( $bg ); ?>">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="<?php echo esc_attr( $card_bg ); ?> rounded-2xl sm:rounded-3xl p-6 sm:p-10 lg:p-12 relative overflow-hidden text-white shadow-xl">
					<div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary-container/30 rounded-full blur-3xl pointer-events-none"></div>
					<?php if ( 'dark-card' === $v ) : ?><div class="absolute top-0 left-0 w-64 h-64 rounded-full bg-accent-emerald/10 blur-2xl pointer-events-none"></div><?php endif; ?>
					<?php if ( $is_form ) : ?>
					<div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center">
						<div class="<?php echo 'dark-form' === $v ? 'lg:col-span-8' : 'lg:col-span-7'; ?> flex flex-col gap-3<?php echo esc_attr( $center ); ?>">
							<?php echo $badge; // phpcs:ignore ?>
							<h3 class="font-black tracking-tight text-xl sm:text-2xl lg:text-3xl leading-snug"><?php echo larijani_kses( $s['title'] ); // phpcs:ignore ?></h3>
							<?php if ( $s['desc'] ) : ?><p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed"><?php echo esc_html( $s['desc'] ); ?></p><?php endif; ?>
							<?php if ( 'dark-form' === $v ) : ?>
							<div class="flex flex-wrap items-center gap-3 pt-2">
								<?php echo $this->button( $s, 1, $btn1_cls . ' hover:-translate-y-0.5' ); // phpcs:ignore ?>
								<?php echo $this->button( $s, 2, $btn2_cls . ' hover:-translate-y-0.5' ); // phpcs:ignore ?>
							</div>
							<?php endif; ?>
						</div>
						<div class="<?php echo 'dark-form' === $v ? 'lg:col-span-4 bg-white/5 rounded-2xl p-space-lg flex flex-col gap-space-md backdrop-blur-md' : 'lg:col-span-5'; ?>">
							<?php if ( 'dark-form' === $v ) : ?>
							<div class="flex items-center gap-space-xs"><i class="bi bi-clock-history text-accent-amber text-lg" aria-hidden="true"></i><span class="font-title-card text-title-card text-white"><?php echo esc_html( $s['form_title'] ); ?></span></div>
							<?php if ( $s['form_desc'] ) : ?><p class="font-body-sm text-body-sm text-white/70"><?php echo esc_html( $s['form_desc'] ); ?></p><?php endif; ?>
							<?php echo $this->form( $s, 'stack' ); // phpcs:ignore ?>
							<?php else : ?>
							<?php echo $this->form( $s, 'inline' ); // phpcs:ignore ?>
							<?php if ( $s['form_note'] ) : ?><p class="text-[10px] sm:text-[11px] text-gray-400 mt-2"><?php echo esc_html( $s['form_note'] ); ?></p><?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
					<?php else : ?>
					<div class="relative z-10 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6">
						<div class="max-w-2xl<?php echo esc_attr( $center ); ?>">
							<?php echo $badge; // phpcs:ignore ?>
							<h3 class="font-black tracking-tight mb-2 text-lg sm:text-xl lg:text-2xl leading-snug"><?php echo larijani_kses( $s['title'] ); // phpcs:ignore ?></h3>
							<?php if ( $s['desc'] ) : ?><p class="text-slate-300 text-xs sm:text-sm max-w-xl leading-relaxed"><?php echo esc_html( $s['desc'] ); ?></p><?php endif; ?>
						</div>
						<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
							<?php echo $this->button( $s, 1, $btn1_cls ); // phpcs:ignore ?>
							<?php echo $this->button( $s, 2, $btn2_cls ); // phpcs:ignore ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
