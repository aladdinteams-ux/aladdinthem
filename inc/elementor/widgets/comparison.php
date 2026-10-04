<?php
/**
 * Widget: Comparison table (us vs market).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Comparison widget.
 */
class LS_Widget_Comparison extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-comparison';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS جدول مقایسه', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-table';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani' ) );
		$this->heading_controls(
			array(
				'eyebrow' => 'مقایسه فنی و اقتصادی',
				'title'   => 'چرا قالب‌های نشکن لاریجانی استون؟',
				'desc'    => 'سرمایه‌گذاری روی قالب باکیفیت یعنی کاهش دورریز، سرعت خروج قطعه و کاهش هزینه‌های استهلاک خط تولید.',
				'align'   => 'center',
				'style'   => 'token',
			)
		);
		$this->end();

		$this->section( 'sec_table', __( 'جدول', 'larijani' ) );
		$this->ctl( 'col1', 'text', __( 'ستون اول', 'larijani' ), 'شاخص فنی و کارکردی' );
		$this->ctl( 'col2', 'text', __( 'ستون برجسته (ما)', 'larijani' ), 'قالب‌های نشکن ABS لاریجانی استون' );
		$this->ctl( 'col3', 'text', __( 'ستون رقیب', 'larijani' ), 'قالب‌های متفرقه و بازیافتی بازار' );
		$this->rep(
			'rows',
			__( 'ردیف‌ها', 'larijani' ),
			array(
				array( 'label', 'text', __( 'شاخص', 'larijani' ), '' ),
				array( 'ours', 'text', __( 'مقدار ما', 'larijani' ), '' ),
				array( 'ours_icon', 'icon', __( 'آیکون مقدار ما', 'larijani' ), '' ),
				array( 'ours_tone', 'select', __( 'رنگ مقدار ما', 'larijani' ), 'dark', array( 'options' => array( 'dark' => __( 'تیره', 'larijani' ), 'emerald' => __( 'زمردی', 'larijani' ), 'primary' => __( 'سبز برند', 'larijani' ) ) ) ),
				array( 'theirs', 'text', __( 'مقدار رقیب', 'larijani' ), '' ),
				array( 'theirs_bad', 'switch', __( 'نمایش قرمز (نقطه ضعف)', 'larijani' ), '' ),
			),
			array(
				array( 'label' => 'جنس و خلوص مواد اولیه', 'ours' => 'گرانول ABS درجه یک وارداتی (کره‌ای)', 'ours_icon' => 'patch-check-fill', 'ours_tone' => 'emerald', 'theirs' => 'مواد ضایعاتی بازیافتی و خشک (شکننده)' ),
				array( 'label' => 'طول عمر و دفعات قالب‌ریزی', 'ours' => 'بیش از ۵۰۰ الی ۸۰۰ مرتبه تولید مداوم', 'theirs' => '۵۰ تا ۱۰۰ مرتبه (سریعاً تاب برمی‌دارد)' ),
				array( 'label' => 'کیفیت سطح و صیقلی بودن خروجی', 'ours' => 'سطح شیشه‌ای صیقلی بدون نیاز به اسیدشویی', 'ours_icon' => 'star-fill', 'theirs' => 'مات، متخلخل با چسبندگی مکرر سیمان' ),
				array( 'label' => 'مقاومت در برابر شکستگی لبه‌ها', 'ours' => '۱۰۰٪ انعطاف‌پذیر و مقاوم به ضربه چکش', 'ours_tone' => 'emerald', 'theirs' => 'ترک خوردگی سریع گوشه‌ها هنگام دکف کردن', 'theirs_bad' => 'yes' ),
				array( 'label' => 'گونیا بودن و ثبات ابعادی', 'ours' => 'دقت خطای کمتر از ۰.۲ میلی‌متر (CNC تاییدشده)', 'theirs' => 'شکم دادن، عدم تراز در هنگام نصب پروژه' ),
				array( 'label' => 'پشتیبانی و طرح‌های جدید', 'ours' => 'بیش از ۴۰۰ طرح مدرن با جایگزینی دوره‌ای', 'ours_tone' => 'primary', 'theirs' => 'طرح‌های قدیمی محدود بدون گارانتی تعویض' ),
			),
			'{{{ label }}}'
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
		$tones = array( 'dark' => 'text-surface-dark', 'emerald' => 'text-accent-emerald', 'primary' => 'text-primary' );
		?>
		<section class="w-full py-space-2xl <?php echo esc_attr( ls_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin flex flex-col gap-space-xl">
				<?php echo $this->heading( $s, array( 'mb' => '' ) ); // phpcs:ignore ?>
				<div class="w-full overflow-x-auto bg-surface-card rounded-2xl shadow-sm">
					<table class="w-full text-right border-collapse min-w-[640px]">
						<thead>
							<tr class="bg-surface-dark text-on-tertiary">
								<th class="p-space-md font-headline-sm text-headline-sm" scope="col"><?php echo esc_html( $s['col1'] ); ?></th>
								<th class="p-space-md font-headline-sm text-headline-sm bg-primary-container text-on-primary" scope="col"><?php echo esc_html( $s['col2'] ); ?></th>
								<th class="p-space-md font-headline-sm text-headline-sm text-tertiary-fixed-dim" scope="col"><?php echo esc_html( $s['col3'] ); ?></th>
							</tr>
						</thead>
						<tbody class="divide-y divide-border-subtle font-body-md text-body-md">
							<?php foreach ( $s['rows'] as $r ) : ?>
							<tr class="hover:bg-surface-canvas/60 transition-colors">
								<th class="p-space-md font-semibold text-surface-dark text-right" scope="row"><?php echo esc_html( $r['label'] ); ?></th>
								<td class="p-space-md font-medium bg-secondary-container/20">
									<span class="flex items-center gap-1.5 font-bold <?php echo esc_attr( $tones[ $r['ours_tone'] ] ?? $tones['dark'] ); ?>"><?php echo ls_icon( $r['ours_icon'], 'text-[16px] text-accent-emerald' ); // phpcs:ignore ?><span><?php echo esc_html( $r['ours'] ); ?></span></span>
								</td>
								<td class="p-space-md <?php echo 'yes' === $r['theirs_bad'] ? 'text-error font-medium' : 'text-outline'; ?>"><?php echo esc_html( $r['theirs'] ); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>
		<?php
	}
}
