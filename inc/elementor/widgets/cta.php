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
class LS_Widget_CTA extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-cta';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS بنر دعوت به اقدام (CTA)', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani' ) );
		$this->ctl(
			'variant',
			'select',
			__( 'سبک', 'larijani' ),
			'dark-card',
			array(
				'options' => array(
					'dark-card'    => __( 'کارت تیره با دو دکمه', 'larijani' ),
					'dark-strip'   => __( 'نوار تمام‌عرض تیره', 'larijani' ),
					'soft-card'    => __( 'کارت سبز ملایم', 'larijani' ),
					'dark-form'    => __( 'کارت تیره + فرم درخواست تماس', 'larijani' ),
					'catalog-form' => __( 'کارت تیره + فرم دریافت کاتالوگ', 'larijani' ),
				),
			)
		);
		$this->ctl( 'icon', 'icon', __( 'آیکون (نوار / کارت ملایم)', 'larijani' ), 'people', array( 'condition' => array( 'variant' => array( 'dark-strip', 'soft-card' ) ) ) );
		$this->ctl( 'badge', 'text', __( 'برچسب بالای عنوان', 'larijani' ), '' );
		$this->ctl( 'badge_icon', 'icon', __( 'آیکون برچسب', 'larijani' ), '' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani' ), 'آماده راه‌اندازی خط تولید سنگ مصنوعی و قطعات بتنی هستید؟', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'همین حالا با مهندسین و مشاورین ارشد لاریجانی استون تماس بگیرید و لیست قیمت و فرمولاسیون جامع را به صورت رایگان دریافت کنید.' );
		$this->ctl( 'center_mobile', 'switch', __( 'وسط‌چین در موبایل', 'larijani' ), '' );
		$this->end();

		$this->section( 'sec_buttons', __( 'دکمه‌ها', 'larijani' ) );
		$this->ctl( 'btn1_text', 'text', __( 'دکمه اول', 'larijani' ), 'تماس مستقیم: ۰۹۱۲۲۳۰۲۶۸۵' );
		$this->ctl( 'btn1_sub', 'text', __( 'متن کوچک بالای دکمه اول (اختیاری)', 'larijani' ), '' );
		$this->ctl( 'btn1_link', 'url', __( 'لینک دکمه اول', 'larijani' ), 'tel:09122302685' );
		$this->ctl( 'btn1_icon', 'icon', __( 'آیکون دکمه اول', 'larijani' ), 'telephone-fill' );
		$this->ctl( 'btn2_text', 'text', __( 'دکمه دوم', 'larijani' ), 'ارسال پیام در واتساپ' );
		$this->ctl( 'btn2_link', 'url', __( 'لینک دکمه دوم', 'larijani' ), array( 'url' => 'https://wa.me/989122302685', 'is_external' => true ) );
		$this->ctl( 'btn2_icon', 'icon', __( 'آیکون دکمه دوم', 'larijani' ), '' );
		$this->ctl( 'btn2_style', 'select', __( 'سبک دکمه دوم', 'larijani' ), 'glass', array( 'options' => array( 'glass' => __( 'شیشه‌ای', 'larijani' ), 'emerald' => __( 'سبز واتساپ', 'larijani' ), 'darker' => __( 'تیره با حاشیه', 'larijani' ), 'white' => __( 'سفید', 'larijani' ) ) ) );
		$this->end();

		$this->section( 'sec_form', __( 'فرم', 'larijani' ), 'content', array( 'variant' => array( 'dark-form', 'catalog-form' ) ) );
		$this->ctl( 'form_name', 'text', __( 'نام فرم (در درخواست‌ها)', 'larijani' ), 'درخواست تماس فوری' );
		$this->ctl( 'form_title', 'text', __( 'عنوان کارت فرم', 'larijani' ), 'درخواست مشاوره اختصاصی' );
		$this->ctl( 'form_desc', 'textarea', __( 'توضیح کارت فرم', 'larijani' ), 'شماره همراه خود را وارد کنید تا کارشناس خط تولید حداکثر ظرف ۱۵ دقیقه با شما تماس بگیرد.' );
		$this->ctl( 'form_placeholder', 'text', __( 'متن راهنمای فیلد', 'larijani' ), '0912...' );
		$this->ctl( 'form_button', 'text', __( 'متن دکمه', 'larijani' ), 'ثبت درخواست تماس فوری' );
		$this->ctl( 'form_note', 'text', __( 'یادداشت زیر فرم', 'larijani' ), '' );
		$this->ctl( 'form_success', 'text', __( 'پیام موفقیت', 'larijani' ), 'درخواست شما ثبت شد؛ به زودی تماس خواهیم گرفت.' );
		$this->end();

		$this->section( 'sec_layout', __( 'چیدمان', 'larijani' ) );
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
		$icon = ls_icon( $s[ "btn{$n}_icon" ], 'text-lg' );
		$body = $sub ? '<div class="flex flex-col text-right"><span class="text-xs opacity-80 leading-none">' . esc_html( $sub ) . '</span><span class="font-black mt-1" dir="ltr">' . esc_html( $text ) . '</span></div>' : '<span class="whitespace-nowrap">' . esc_html( $text ) . '</span>';
		return '<a class="' . esc_attr( $class ) . '" ' . ls_link_attrs( $s[ "btn{$n}_link" ] ) . '>' . $icon . $body . '</a>';
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
			<?php echo ls_form_hidden_fields( $s['form_name'] ); // phpcs:ignore ?>
			<input type="hidden" name="labels[phone]" value="<?php esc_attr_e( 'شماره تماس', 'larijani' ); ?>"><input type="hidden" name="types[phone]" value="tel">
			<?php if ( 'inline' === $layout ) : ?>
			<input class="flex-1 px-4 py-3 sm:py-3.5 rounded-xl bg-white text-gray-900 placeholder:text-gray-400 text-xs sm:text-sm focus:ring-2 focus:ring-primary-container" name="fields[phone]" placeholder="<?php echo esc_attr( $s['form_placeholder'] ); ?>" type="text" required>
			<button class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-xl bg-primary-container hover:bg-primary text-white font-bold text-xs sm:text-sm shadow-md transition whitespace-nowrap" type="submit"><?php echo esc_html( $s['form_button'] ); ?></button>
			<?php else : ?>
			<input class="w-full px-4 py-2.5 rounded-xl bg-white/10 text-white placeholder:text-white/50 font-body-md text-body-md focus:bg-white/15 text-left" dir="ltr" name="fields[phone]" placeholder="<?php echo esc_attr( $s['form_placeholder'] ); ?>" type="tel" required>
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
		$bg      = ls_section_bg( $s['section_bg'] );
		$badge   = '';
		if ( $s['badge'] ) {
			$badge_class = 'soft-card' === $v ? 'bg-white text-primary' : ( 'catalog-form' === $v ? 'bg-primary-container text-white rounded-md' : 'bg-white/10 text-secondary-fixed' );
			$badge       = '<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full ' . esc_attr( $badge_class ) . ' font-label-badge text-label-badge mb-3">' . ls_icon( $s['badge_icon'], 'text-accent-emerald' ) . esc_html( $s['badge'] ) . '</div>';
		}
		$btn1_cls = 'bg-primary-container hover:bg-primary text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-full shadow-lg transition-all flex items-center justify-center gap-2 flex-shrink-0';
		$btn2_map = array(
			'glass'   => 'bg-white/10 hover:bg-white/20 text-white font-medium text-xs sm:text-sm px-6 py-3.5 rounded-full border border-white/20 transition-all flex items-center justify-center gap-2 flex-shrink-0',
			'emerald' => 'bg-accent-emerald hover:opacity-90 text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-full shadow-lg transition-all flex items-center justify-center gap-2 flex-shrink-0',
			'darker'  => 'bg-surface-footer/80 hover:bg-surface-footer text-white font-label-nav text-label-nav px-6 py-3.5 rounded-full border border-white/10 transition-colors flex items-center justify-center gap-2 flex-shrink-0',
			'white'   => 'bg-surface-card text-on-surface hover:bg-surface-canvas px-space-md py-3 rounded-full font-label-nav text-label-nav shadow-sm transition-all flex items-center justify-center gap-2 flex-shrink-0',
		);
		$btn2_cls = $btn2_map[ $s['btn2_style'] ] ?? $btn2_map['glass'];

		if ( 'dark-strip' === $v ) :
			?>
			<section class="w-full bg-surface-dark text-on-tertiary py-space-xl">
				<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin flex flex-col sm:flex-row items-center justify-between gap-space-md">
					<div class="flex items-center gap-space-md">
						<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shrink-0 text-2xl"><?php echo ls_icon( $s['icon'] ); // phpcs:ignore ?></div>
						<div class="flex flex-col">
							<span class="font-headline-sm text-headline-sm text-on-tertiary"><?php echo ls_kses( $s['title'] ); // phpcs:ignore ?></span>
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
				<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
					<div class="bg-secondary-container/60 rounded-3xl p-space-lg lg:p-space-xl flex flex-col md:flex-row items-center justify-between gap-space-lg">
						<div class="flex items-center gap-space-md">
							<div class="w-14 h-14 rounded-2xl bg-surface-card flex items-center justify-center text-primary shrink-0 shadow-sm text-[28px]"><?php echo ls_icon( $s['icon'] ); // phpcs:ignore ?></div>
							<div class="flex flex-col">
								<?php echo $badge; // phpcs:ignore ?>
								<span class="font-headline-sm text-headline-sm text-on-secondary-container"><?php echo ls_kses( $s['title'] ); // phpcs:ignore ?></span>
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
		$card_bg    = 'catalog-form' === $v ? 'bg-surface-dark' : 'bg-surface-dark';
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
							<h3 class="font-black tracking-tight text-xl sm:text-2xl lg:text-3xl leading-snug"><?php echo ls_kses( $s['title'] ); // phpcs:ignore ?></h3>
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
							<h3 class="font-black tracking-tight mb-2 text-lg sm:text-xl lg:text-2xl leading-snug"><?php echo ls_kses( $s['title'] ); // phpcs:ignore ?></h3>
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
