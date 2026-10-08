<?php
/**
 * Widget: About section (text + image + statistics).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * About widget.
 */
class Larijani_Widget_About extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-about';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS درباره ما + آمار', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-info-box';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن', 'larijani-stone' ) );
		$this->ctl( 'eyebrow', 'text', __( 'متن بالای عنوان', 'larijani-stone' ), 'درباره مجموعه صنعتی ما' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani-stone' ), 'پیشگام در صنعت قالب‌سازی و ماشین‌آلات سنگ مصنوعی' );
		$this->ctl( 'content', 'wysiwyg', __( 'متن', 'larijani-stone' ), '<p>مجموعه <strong>لاریجانی استون</strong> با بیش از ۱۵ سال تجربه تخصصی و مستمر، با در اختیار داشتن خط تولید مجهز تزریق پلاستیک، ورق‌کاری صنعتی و آزمایشگاه کنترل کیفیت بتن، صفر تا صد ملزومات تولیدکنندگان سنگ‌های پلیمری و بتنی را با بالاترین استانداردهای روز دنیا تأمین می‌کند.</p><p>هدف ما ارتقای بهره‌وری کارگاه‌ها با تجهیزاتی با دوام، خروج آسان قطعه از قالب، سطح نهایی صیقلی و پشتیبانی فنی دائمی است.</p>' );
		$this->ctl( 'button_text', 'text', __( 'متن دکمه', 'larijani-stone' ), 'اطلاعات بیشتر و درخواست نمایندگی' );
		$this->ctl( 'button_link', 'url', __( 'لینک دکمه', 'larijani-stone' ), '#contact' );
		$this->ctl( 'image', 'media', __( 'تصویر', 'larijani-stone' ), 'about' );
		$this->bg_control( 'white-bordered' );
		$this->end();

		$this->section( 'sec_stats', __( 'آمار', 'larijani-stone' ) );
		$this->rep(
			'stats',
			__( 'آمار', 'larijani-stone' ),
			array(
				array( 'value', 'text', __( 'عدد', 'larijani-stone' ), '' ),
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'accent', 'switch', __( 'رنگ سبز', 'larijani-stone' ), '' ),
			),
			array(
				array( 'value' => '+۱۵', 'label' => 'سال سابقه تخصصی درخشان', 'accent' => 'yes' ),
				array( 'value' => '+۲,۵۰۰', 'label' => 'پروژه موفق تجهیز کارگاه' ),
				array( 'value' => '+۱,۲۰۰', 'label' => 'تنوع قالب‌های انحصاری' ),
				array( 'value' => '+۲۵', 'label' => 'مهندس و کارشناس تولید', 'accent' => 'yes' ),
			),
			'{{{ value }}} – {{{ label }}}'
		);
		$this->ctl( 'counter', 'switch', __( 'انیمیشن شمارنده هنگام اسکرول', 'larijani-stone' ), 'yes' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		?>
		<section class="py-12 sm:py-16 <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
					<div class="lg:col-span-5 text-right order-1">
						<?php if ( $s['eyebrow'] ) : ?><span class="text-xs font-bold text-primary-container uppercase tracking-wider"><?php echo esc_html( $s['eyebrow'] ); ?></span><?php endif; ?>
						<h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-surface-dark mt-2 mb-3 sm:mb-4 leading-snug"><?php echo $this->t( $s, 'title' ); // phpcs:ignore ?></h2>
						<div class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6 space-y-3 sm:space-y-4 [&_strong]:text-surface-dark"><?php echo wp_kses_post( $s['content'] ); ?></div>
						<?php if ( $s['button_text'] ) : ?>
						<a class="inline-flex items-center gap-2 bg-primary-container hover:bg-primary text-white text-xs sm:text-sm font-bold px-5 sm:px-6 py-3 rounded-full transition-colors group" <?php echo larijani_link_attrs( $s['button_link'] ); // phpcs:ignore ?>><span><?php echo esc_html( $s['button_text'] ); ?></span><i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1" aria-hidden="true"></i></a>
						<?php endif; ?>
					</div>
					<div class="lg:col-span-4 order-2">
						<div class="rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg border border-slate-200">
							<?php echo larijani_img( $s['image'], 'w-full h-64 sm:h-80 lg:h-96 object-cover' ); // phpcs:ignore ?>
						</div>
					</div>
					<div class="lg:col-span-3 grid grid-cols-2 lg:grid-cols-1 gap-3 sm:gap-4 order-3">
						<?php foreach ( $s['stats'] as $st ) : ?>
						<div class="bg-surface-canvas p-4 sm:p-5 rounded-2xl border border-border-subtle text-right">
							<div class="text-2xl sm:text-3xl font-black <?php echo 'yes' === $st['accent'] ? 'text-primary-container' : 'text-surface-dark'; ?>"<?php echo $this->on( $s, 'counter' ) ? ' data-ls-counter' : ''; ?>><?php echo esc_html( $st['value'] ); ?></div>
							<div class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-1"><?php echo esc_html( $st['label'] ); ?></div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
