<?php
/**
 * Widget: Dark page title banner with side metric box (contact page).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Page banner widget.
 */
class Larijani_Widget_Page_Banner extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-page-banner';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS بنر عنوان صفحه (تیره)', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-site-title';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_main', __( 'محتوا', 'larijani-stone' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani-stone' ), 'پاسخگویی فنی کارخانجات و خطوط صنعتی سنگ مصنوعی' );
		$this->ctl( 'badge_icon', 'icon', __( 'آیکون برچسب', 'larijani-stone' ), 'gear-wide-connected' );
		$this->ctl( 'title', 'textarea', __( 'عنوان (خالی = عنوان صفحه)', 'larijani-stone' ), 'تماس با گروه صنعتی لاریجانی استون', array( 'rows' => 2 ) );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'ارتباط مستقیم با مدیریت مهندسی، کارشناسان متالورژی و قالب‌های نشکن بتن، تأمین‌کنندگان رزین‌های پلی‌کربوکسیلاتی و واحد پشتیبانی ماشین‌آلات سنگین مستقر در مجتمع صنعتی پیروز آبیک.' );
		$this->end();

		$this->section( 'sec_side', __( 'باکس شاخص', 'larijani-stone' ) );
		$this->ctl( 'show_side', 'switch', __( 'نمایش', 'larijani-stone' ), 'yes' );
		$this->ctl( 'side_label', 'text', __( 'عنوان', 'larijani-stone' ), 'زمان پاسخگویی میانگین' );
		$this->ctl( 'side_value', 'text', __( 'مقدار', 'larijani-stone' ), 'کمتر از ۱۵ دقیقه' );
		$this->ctl( 'side_percent', 'number', __( 'درصد نوار', 'larijani-stone' ), 94, array( 'min' => 0, 'max' => 100 ) );
		$this->ctl( 'side_note_1', 'text', __( 'یادداشت راست', 'larijani-stone' ), 'ترافیک استعلام: متراکم' );
		$this->ctl( 'side_note_2', 'text', __( 'یادداشت چپ', 'larijani-stone' ), '۹۴٪ رضایت مشتریان صنعتی' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$title = $s['title'] ? $s['title'] : get_the_title();
		$side  = $this->on( $s, 'show_side' );
		?>
		<section class="w-full pb-space-lg">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<div class="bg-surface-dark text-on-tertiary rounded-3xl p-space-lg lg:p-space-2xl relative overflow-hidden shadow-xl">
					<div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-primary-container/20 blur-3xl pointer-events-none"></div>
					<div class="absolute bottom-0 right-1/4 w-64 h-64 rounded-full bg-secondary-container/10 blur-2xl pointer-events-none"></div>
					<div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-end">
						<div class="<?php echo $side ? 'lg:col-span-8' : 'lg:col-span-12'; ?> flex flex-col gap-space-md">
							<?php if ( $s['badge'] ) : ?>
							<div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full w-max max-w-full text-primary-fixed"><?php echo larijani_icon( $s['badge_icon'], 'text-[16px]' ); // phpcs:ignore ?><span class="font-label-badge text-label-badge font-bold"><?php echo esc_html( $s['badge'] ); ?></span></div>
							<?php endif; ?>
							<h1 class="font-headline-lg text-headline-lg lg:font-display-hero lg:text-display-hero text-on-tertiary tracking-tight leading-snug"><?php echo larijani_kses( $title ); // phpcs:ignore ?></h1>
							<?php if ( $s['desc'] ) : ?><p class="font-body-lg text-body-lg text-outline-variant max-w-2xl leading-relaxed"><?php echo esc_html( $s['desc'] ); ?></p><?php endif; ?>
						</div>
						<?php if ( $side ) : ?>
						<div class="lg:col-span-4 flex flex-col gap-space-sm bg-white/5 backdrop-blur-sm p-space-md rounded-2xl">
							<div class="flex items-center justify-between gap-3 pb-space-xs text-on-tertiary">
								<span class="font-label-badge text-label-badge text-secondary-fixed"><?php echo esc_html( $s['side_label'] ); ?></span>
								<span class="font-headline-sm text-headline-sm text-primary-fixed"><?php echo esc_html( $s['side_value'] ); ?></span>
							</div>
							<div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden"><div class="bg-accent-emerald h-full rounded-full" style="width: <?php echo esc_attr( max( 0, min( 100, (int) $s['side_percent'] ) ) ); ?>%"></div></div>
							<div class="flex items-center justify-between text-body-sm font-body-sm text-outline-variant pt-1 gap-2">
								<span><?php echo esc_html( $s['side_note_1'] ); ?></span>
								<span class="text-on-tertiary"><?php echo esc_html( $s['side_note_2'] ); ?></span>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
