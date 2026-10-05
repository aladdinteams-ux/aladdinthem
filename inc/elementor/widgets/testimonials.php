<?php
/**
 * Widget: Testimonials (cards grid or horizontal slider).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Testimonials widget.
 */
class LS_Widget_Testimonials extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-testimonials';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS نظرات مشتریان', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani' ) );
		$this->heading_controls(
			array(
				'eyebrow' => 'رضایت همکاران صنعتی',
				'title'   => 'نظرات تولیدکنندگان قطعات بتنی',
			)
		);
		$this->ctl( 'rating_text', 'text', __( 'متن امتیاز کنار عنوان', 'larijani' ), '۴.۹ از ۵ رضایت مشتری' );
		$this->end();

		$this->section( 'sec_items', __( 'نظرات', 'larijani' ) );
		$this->rep(
			'items',
			__( 'نظرات', 'larijani' ),
			array(
				array( 'text', 'textarea', __( 'متن نظر', 'larijani' ), '' ),
				array( 'name', 'text', __( 'نام', 'larijani' ), '' ),
				array( 'role', 'text', __( 'سمت / شرکت', 'larijani' ), '' ),
				array( 'avatar', 'media', __( 'تصویر (اختیاری)', 'larijani' ), '' ),
				array( 'stars', 'number', __( 'ستاره (۰ = مخفی)', 'larijani' ), 0, array( 'min' => 0, 'max' => 5 ) ),
			),
			array(
				array( 'text' => 'قالب‌های واش‌بتن لاریجانی استون واقعاً نشکن هستند؛ ما بیش از ۵۰۰ مرتبه قالب‌ریزی انجام دادیم بدون حتی یک مورد شکستگی یا انحراف لبه. خروج سنگ هم بدون نیاز به اسید راحت انجام می‌شه.', 'name' => 'مهندس حسینی', 'role' => 'کارخانه موزاییک اصفهان' ),
				array( 'text' => 'میز ویبره ۲ متری که پارسال خریداری کردیم بدون کوچک‌ترین لرزش معکوس به بدنه، ویبره یکدست و بی‌نقصی به ملات میده. تراکم قطعات نهایی با فرمول رزین خودشون فوق‌العاده بالاست.', 'name' => 'علیرضا رادپور', 'role' => 'مدیر تولید سنگ مدرن تبریز' ),
				array( 'text' => 'پشتیبانی فنی و آموزش ترکیب رزین‌ها نقطه تمایز لاریجانی استون هست. از صفر کارگاه راه‌اندازی کردیم و در کمتر از یک هفته به راندمان تولید روزانه مطلوب رسیدیم.', 'name' => 'کامران مهدوی', 'role' => 'صنایع سنگ مصنوعی شیراز' ),
			),
			'{{{ name }}}'
		);
		$this->ctl( 'layout', 'select', __( 'نمایش', 'larijani' ), 'grid', array( 'options' => array( 'grid' => __( 'شبکه', 'larijani' ), 'slider' => __( 'اسلایدر افقی', 'larijani' ) ) ) );
		$this->ctl( 'card_style', 'select', __( 'سبک کارت', 'larijani' ), 'default', array( 'options' => array( 'default' => __( 'پیش‌فرض (ستاره بالا)', 'larijani' ), 'classic' => __( 'کلاسیک (عکس + ستاره پایین)', 'larijani' ) ) ) );
		$this->columns_controls( 3, 3, 1, 4 );
		$this->bg_control( 'none' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$slider = 'slider' === $s['layout'];
		$h      = ls_heading_from_settings( $s );
		?>
		<section class="py-12 sm:py-16 <?php echo esc_attr( ls_section_bg( $s['section_bg'] ) ); ?>" <?php echo $slider ? 'data-ls-slider' : ''; ?>>
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 sm:mb-10 gap-2">
					<div>
						<?php if ( $h['eyebrow'] ) : ?><span class="text-xs font-bold text-primary-container uppercase tracking-wider"><?php echo esc_html( $h['eyebrow'] ); ?></span><?php endif; ?>
						<h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-surface-dark mt-1"><?php echo ls_kses( $h['title'] ); // phpcs:ignore ?></h2>
						<?php if ( $h['desc'] ) : ?><p class="text-xs sm:text-sm text-slate-500 mt-1"><?php echo esc_html( $h['desc'] ); ?></p><?php endif; ?>
					</div>
					<div class="flex items-center gap-3 self-start sm:self-auto">
						<?php if ( $s['rating_text'] ) : ?>
						<div class="flex items-center gap-1.5 text-primary-container"><i class="bi bi-star-fill text-amber-500" aria-hidden="true"></i><span class="text-xs sm:text-sm font-bold text-slate-700"><?php echo esc_html( $s['rating_text'] ); ?></span></div>
						<?php endif; ?>
						<?php if ( $slider ) : ?>
						<button type="button" class="w-9 h-9 rounded-full border border-slate-200 bg-white flex items-center justify-center hover:border-primary-container hover:text-primary-container" data-ls-slide="prev" aria-label="<?php esc_attr_e( 'قبلی', 'larijani' ); ?>"><i class="bi bi-chevron-right" aria-hidden="true"></i></button>
						<button type="button" class="w-9 h-9 rounded-full border border-slate-200 bg-white flex items-center justify-center hover:border-primary-container hover:text-primary-container" data-ls-slide="next" aria-label="<?php esc_attr_e( 'بعدی', 'larijani' ); ?>"><i class="bi bi-chevron-left" aria-hidden="true"></i></button>
						<?php endif; ?>
					</div>
				</div>
				<div class="<?php echo esc_attr( $slider ? 'flex overflow-x-auto snap-x snap-mandatory scrollbar-none gap-5 sm:gap-6 [&>*]:snap-start [&>*]:shrink-0 [&>*]:w-[85%] sm:[&>*]:w-[48%] lg:[&>*]:w-[31.5%]' : 'grid ' . ls_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) . ' gap-5 sm:gap-6' ); ?>" data-ls-track>
					<?php foreach ( $s['items'] as $it ) : ?>
					<?php if ( 'classic' === ( $s['card_style'] ?? '' ) ) : ?>
					<div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-200/90 shadow-sm flex flex-col justify-between">
						<div>
							<span class="text-3xl sm:text-4xl text-primary-container/40 font-serif leading-none block mb-2">“</span>
							<p class="text-gray-700 text-xs sm:text-sm leading-relaxed"><?php echo esc_html( $it['text'] ); ?></p>
						</div>
						<div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
							<div class="flex items-center gap-3">
								<?php if ( ! empty( $it['avatar']['url'] ) ) : ?>
									<?php echo ls_img( $it['avatar'], 'w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover', $it['name'], 'thumbnail' ); // phpcs:ignore ?>
								<?php else : ?>
								<div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-xs text-slate-700"><?php echo esc_html( ls_initials( $it['name'] ) ); ?></div>
								<?php endif; ?>
								<div>
									<p class="font-bold text-gray-900 text-xs sm:text-sm"><?php echo esc_html( $it['name'] ); ?></p>
									<p class="text-[10px] sm:text-[11px] text-gray-500"><?php echo esc_html( $it['role'] ); ?></p>
								</div>
							</div>
							<?php if ( (int) $it['stars'] > 0 ) : ?><div class="flex text-amber-400 text-xs" aria-label="<?php echo esc_attr( sprintf( /* translators: %d stars */ __( '%d ستاره', 'larijani' ), (int) $it['stars'] ) ); ?>"><?php echo esc_html( str_repeat( '★', min( 5, (int) $it['stars'] ) ) ); ?></div><?php endif; ?>
						</div>
					</div>
					<?php continue; endif; ?>
					<div class="bg-white rounded-2xl p-5 sm:p-6 border border-border-subtle card-shadow flex flex-col justify-between">
						<div>
							<div class="flex items-center justify-between">
								<div class="text-3xl font-serif text-primary-container mb-2 leading-none">“</div>
								<?php if ( (int) $it['stars'] > 0 ) : ?><div class="text-amber-500 text-xs"><?php echo str_repeat( '★', min( 5, (int) $it['stars'] ) ); // phpcs:ignore ?></div><?php endif; ?>
							</div>
							<p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6"><?php echo esc_html( $it['text'] ); ?></p>
						</div>
						<div class="flex items-center gap-3 pt-4 border-t border-slate-100">
							<?php if ( ! empty( $it['avatar']['url'] ) ) : ?>
								<?php echo ls_img( $it['avatar'], 'w-10 h-10 rounded-full object-cover', $it['name'], 'thumbnail' ); // phpcs:ignore ?>
							<?php else : ?>
							<div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-xs text-slate-700"><?php echo esc_html( ls_initials( $it['name'] ) ); ?></div>
							<?php endif; ?>
							<div>
								<p class="text-xs font-black text-surface-dark"><?php echo esc_html( $it['name'] ); ?></p>
								<div class="text-[11px] text-slate-500"><?php echo esc_html( $it['role'] ); ?></div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
