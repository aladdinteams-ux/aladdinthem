<?php
/**
 * Widget: FAQ (open cards grid or accordion) with optional FAQPage schema.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * FAQ widget.
 */
class LS_Widget_FAQ extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-faq';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS سوالات متداول', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-accordion';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani' ) );
		$this->ctl( 'heading_badge', 'text', __( 'برچسب بالای عنوان', 'larijani' ), '' );
		$this->ctl( 'heading_badge_icon', 'icon', __( 'آیکون برچسب', 'larijani' ), 'question-circle' );
		$this->heading_controls(
			array(
				'eyebrow' => 'پاسخ به سوالات پرتکرار',
				'title'   => 'پرسش‌های متداول تولیدکنندگان سنگ پلیمری',
				'align'   => 'start',
				'style'   => 'token',
			)
		);
		$this->end();

		$this->section( 'sec_items', __( 'پرسش‌ها', 'larijani' ) );
		$this->ctl( 'variant', 'select', __( 'نمایش', 'larijani' ), 'cards', array( 'options' => array( 'cards' => __( 'کارت‌های باز', 'larijani' ), 'accordion' => __( 'آکاردئون', 'larijani' ) ) ) );
		$this->ctl( 'icon_position', 'select', __( 'آیکون', 'larijani' ), 'title', array( 'options' => array( 'title' => __( 'کنار عنوان (رنگ برند)', 'larijani' ), 'header' => __( 'کنار عنوان (رنگ برند، عنوان تیره)', 'larijani' ) ) ) );
		$this->rep(
			'items',
			__( 'پرسش‌ها', 'larijani' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'question-circle-fill' ),
				array( 'question', 'text', __( 'پرسش', 'larijani' ), '' ),
				array( 'answer', 'textarea', __( 'پاسخ', 'larijani' ), '', array( 'rows' => 5 ) ),
			),
			array(
				array( 'question' => 'برای شروع به چه میزان سرمایه و چه متراژ فضایی نیاز است؟', 'answer' => 'راه‌اندازی کارگاه پایه سنگ مصنوعی از یک فضای ۶۰ تا ۸۰ متری و برق تک‌فاز امکان‌پذیر است. با تهیه یک میز ویبره، یک میکسر و حدود ۵۰ متر قالب، می‌توان تولید روزانه ۳۰ تا ۵۰ مترمربع سنگ را آغاز نمود.' ),
				array( 'question' => 'آیا دوره آموزش حضوری شامل تضمین فرمولاسیون می‌شود؟', 'answer' => 'بله، کارشناس لاریجانی استون در محل کارگاه شما حاضر شده و با آب و شن و ماسه موجود در همان شهر بچ‌های آزمایشی را ترکیب می‌کند تا فرمول نهایی کاملاً بدون حباب، زودگیر و براق تولید شود.' ),
				array( 'question' => 'تفاوت قالب ABS لاریجانی با قالب‌های ارزان‌قیمت لاستیکی چیست؟', 'answer' => 'قالب‌های ABS صیقلی سطحی با براقیت آیینه و بدون پرز تولید می‌کنند، در حالی که قالب‌های لاستیکی کدر شده و نیازمند شستشوی دائمی با اسید کلریدریک هستند که عمر قالب را به شدت می‌کاهد.' ),
				array( 'question' => 'نحوه ارسال قالب‌ها و ماشین‌آلات به شهرهای مختلف چگونه است؟', 'answer' => 'کلیه بارگیری‌ها مستقیماً از کارخانه واقع در مجتمع صنعتی پیروز آبیک با بسته‌بندی ایمن و پالت‌بندی توسط باربری‌های معتبر به کلیه استان‌های کشور و گمرکات مرزی صادراتی ارسال می‌گردد.' ),
			),
			'{{{ question }}}'
		);
		$this->ctl( 'columns', 'select', __( 'ستون‌ها', 'larijani' ), '2', array( 'options' => array( '1' => '۱', '2' => '۲' ) ) );
		$this->ctl( 'narrow', 'switch', __( 'عرض محدود (وسط‌چین)', 'larijani' ), '' );
		$this->ctl( 'schema', 'switch', __( 'افزودن اسکیمای FAQ برای گوگل', 'larijani' ), 'yes' );
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
		$acc   = 'accordion' === $s['variant'];
		$cols  = '2' === (string) $s['columns'] ? 'md:grid-cols-2' : 'md:grid-cols-1';
		$h     = ls_heading_from_settings( $s, array( 'mb' => '' ) );
		$badge = '';
		if ( $s['heading_badge'] ) {
			$badge = '<div class="inline-flex items-center gap-1.5 bg-surface-container-high px-3 py-1 rounded-full text-on-surface-variant mb-2">' . ls_icon( $s['heading_badge_icon'], 'text-[16px] text-primary' ) . '<span class="font-label-badge text-label-badge font-bold">' . esc_html( $s['heading_badge'] ) . '</span></div>';
		}
		$dark_title = 'header' === $s['icon_position'];
		?>
		<section class="w-full py-space-xl <?php echo esc_attr( ls_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin flex flex-col gap-space-lg">
				<div class="<?php echo 'center' === $h['align'] ? 'flex flex-col items-center text-center' : ''; ?>"><?php echo $badge . ls_section_heading( $h ); // phpcs:ignore ?></div>
				<div class="grid grid-cols-1 <?php echo esc_attr( $cols ); ?> gap-space-md <?php echo $this->on( $s, 'narrow' ) ? 'max-w-5xl mx-auto w-full' : ''; ?>">
					<?php foreach ( $s['items'] as $i => $it ) : ?>
						<?php if ( $acc ) : ?>
					<div class="ls-acc-item bg-surface-card rounded-2xl shadow-sm" data-open="<?php echo 0 === $i ? 'true' : 'false'; ?>">
						<button type="button" class="w-full flex items-center justify-between gap-3 p-space-lg text-right" data-ls-acc aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>">
							<span class="flex items-center gap-2 text-surface-dark"><?php echo ls_icon( $it['icon'], 'text-primary text-[20px] shrink-0' ); // phpcs:ignore ?><span class="font-headline-sm text-headline-sm"><?php echo esc_html( $it['question'] ); ?></span></span>
							<i class="ls-acc-icon bi bi-chevron-down text-outline transition-transform duration-300 shrink-0" aria-hidden="true"></i>
						</button>
						<div class="ls-acc-panel"><div class="overflow-hidden"><p class="px-space-lg pb-space-lg font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $it['answer'] ); ?></p></div></div>
					</div>
						<?php else : ?>
					<div class="bg-surface-card p-space-lg rounded-2xl shadow-sm flex flex-col gap-space-xs">
						<div class="flex items-center gap-2 <?php echo $dark_title ? 'text-primary' : 'text-surface-dark'; ?>">
							<?php echo ls_icon( $it['icon'], 'text-primary text-[20px] shrink-0' ); // phpcs:ignore ?>
							<h3 class="font-headline-sm text-headline-sm <?php echo $dark_title ? 'text-on-surface' : ''; ?>"><?php echo esc_html( $it['question'] ); ?></h3>
						</div>
						<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $it['answer'] ); ?></p>
					</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		static $printed = false; // One FAQPage node per URL.
		if ( $this->on( $s, 'schema' ) && $s['items'] && ! $printed && ls_theme_schema_enabled( 'faq' ) && ! ls_is_elementor_editor() ) {
			$printed = true;
			$schema = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => array(),
			);
			foreach ( $s['items'] as $it ) {
				$schema['mainEntity'][] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $it['question'] ),
					'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $it['answer'] ) ),
				);
			}
			ls_print_json_ld( $schema );
		}
	}
}
