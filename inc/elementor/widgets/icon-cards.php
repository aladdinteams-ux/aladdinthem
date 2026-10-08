<?php
/**
 * Widget: Icon cards grid (trust badges, "why us", feature bento, service pillars).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon cards.
 */
class Larijani_Widget_Icon_Cards extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-icon-cards';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS کارت‌های آیکون‌دار / مزایا / خدمات', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-icon-box';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_layout', __( 'سبک و چیدمان', 'larijani-stone' ) );
		$this->ctl(
			'variant',
			'select',
			__( 'سبک کارت', 'larijani-stone' ),
			'badge',
			array(
				'options' => array(
					'badge'      => __( 'نشان اعتماد فشرده (صفحه اصلی)', 'larijani-stone' ),
					'horizontal' => __( 'افقی با توضیح (چرا ما؟)', 'larijani-stone' ),
					'simple'     => __( 'ستونی ساده (مزایای فنی)', 'larijani-stone' ),
					'bento'      => __( 'بنتو با برچسب‌ها (محورهای خدمات)', 'larijani-stone' ),
					'guide'      => __( 'کادر راهنما با ستون‌های ساده (فروشگاه)', 'larijani-stone' ),
				),
			)
		);
		$this->columns_controls( 4, 2, 1, 6 );
		$this->bg_control( 'white-bordered' );
		$this->end();

		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani-stone' ) );
		$this->heading_controls( array( 'align' => 'center' ) );
		$this->end();

		$this->section( 'sec_items', __( 'کارت‌ها', 'larijani-stone' ) );
		$this->rep(
			'items',
			__( 'کارت‌ها', 'larijani-stone' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'shield-check' ),
				array( 'tone', 'select', __( 'رنگ آیکون', 'larijani-stone' ), 'primary', array( 'options' => array( 'primary' => __( 'سبز برند', 'larijani-stone' ), 'emerald' => __( 'زمردی', 'larijani-stone' ), 'amber' => __( 'کهربایی', 'larijani-stone' ), 'cobalt' => __( 'آبی', 'larijani-stone' ), 'sage' => __( 'سبز ملایم', 'larijani-stone' ), 'fixed' => __( 'سبز کم‌رنگ', 'larijani-stone' ), 'light' => __( 'خاکستری-آبی', 'larijani-stone' ), 'dark' => __( 'تیره', 'larijani-stone' ) ) ) ),
				array( 'eyebrow', 'text', __( 'متن کوچک بالا (سبک بنتو)', 'larijani-stone' ), '' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), '' ),
				array( 'chips', 'textarea', __( 'برچسب‌ها (هر خط یکی – سبک بنتو)', 'larijani-stone' ), '' ),
				array( 'footer', 'text', __( 'متن پایین کارت (سبک بنتو)', 'larijani-stone' ), '' ),
				array( 'link', 'url', __( 'لینک', 'larijani-stone' ), '' ),
			),
			array(
				array( 'icon' => 'shield-check', 'title' => 'قالب‌های نشکن پلیمری', 'desc' => 'مواد درجه یک ABS بدون تغییر شکل' ),
				array( 'icon' => 'award', 'title' => '۲ سال ضمانت ماشین‌آلات', 'desc' => 'موتورهای ویبره با سیم‌پیچی مس' ),
				array( 'icon' => 'headset', 'title' => 'مشاوره رایگان تولید', 'desc' => 'فرمولاسیون اختصاصی سنگ و بتن' ),
				array( 'icon' => 'truck', 'title' => 'ارسال سریع به کل کشور', 'desc' => 'بارگیری روزانه از انبار مرکزی' ),
			)
		);
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$v      = $s['variant'];
		$pad    = 'badge' === $v ? 'py-6 sm:py-8' : 'py-12 sm:py-16';
		$gap    = 'badge' === $v ? 'gap-3 sm:gap-4 lg:gap-6' : 'gap-4 sm:gap-6';
		$token  = in_array( $v, array( 'simple', 'bento' ), true );
		if ( 'guide' === $v ) {
			?>
			<section class="w-full py-space-xl <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?>">
				<div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin">
					<div class="bg-surface-card rounded-2xl p-space-xl shadow-sm">
						<?php if ( $s['heading_eyebrow'] ) : ?>
						<div class="flex items-center gap-space-xs text-primary mb-space-xs"><i class="bi bi-journal-bookmark text-[20px]" aria-hidden="true"></i><span class="font-label-nav text-label-nav"><?php echo esc_html( $s['heading_eyebrow'] ); ?></span></div>
						<?php endif; ?>
						<?php if ( $s['heading_title'] ) : ?><h2 class="font-headline-md text-headline-md text-on-surface mb-space-md"><?php echo larijani_kses( $s['heading_title'] ); // phpcs:ignore ?></h2><?php endif; ?>
						<div class="grid <?php echo esc_attr( larijani_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) ); ?> gap-space-lg font-body-md text-body-md text-on-surface-variant">
							<?php foreach ( $s['items'] as $it ) : ?>
							<div class="flex flex-col gap-space-xs bg-surface-canvas p-space-md rounded-xl">
								<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-1"><?php echo larijani_icon( $it['icon'], 'text-primary text-[20px]' ); // phpcs:ignore ?><?php echo esc_html( $it['title'] ); ?></h3>
								<?php if ( $it['desc'] ) : ?><p class="font-body-sm text-body-sm leading-relaxed"><?php echo esc_html( $it['desc'] ); ?></p><?php endif; ?>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</section>
			<?php
			return;
		}
		?>
		<section class="<?php echo esc_attr( $pad . ' ' . larijani_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<?php echo $this->heading( $s, array( 'style' => $s['heading_style'] ?? ( $token ? 'token' : 'classic' ) ) ); // phpcs:ignore ?>
				<div class="grid <?php echo esc_attr( larijani_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) . ' ' . $gap ); ?>">
					<?php foreach ( $s['items'] as $it ) : ?>
						<?php
						$has_link = ! empty( $it['link']['url'] );
						$tag      = $has_link ? 'a' : 'div';
						$attrs    = $has_link ? ' ' . larijani_link_attrs( $it['link'] ) : '';
						?>
						<?php if ( 'badge' === $v ) : ?>
						<<?php echo $tag . $attrs; // phpcs:ignore ?> class="flex items-center gap-3.5 p-3.5 sm:p-4 rounded-2xl bg-surface-canvas border border-border-subtle transition-transform hover:-translate-y-1">
							<div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white <?php echo esc_attr( larijani_tone( $it['tone'], 'text' ) ); ?> border border-slate-200 flex items-center justify-center flex-shrink-0 shadow-sm text-xl"><?php echo larijani_icon( $it['icon'] ); // phpcs:ignore ?></div>
							<div>
								<p class="text-xs sm:text-sm font-black text-surface-dark"><?php echo esc_html( $it['title'] ); ?></p>
								<?php if ( $it['desc'] ) : ?><p class="text-[11px] sm:text-xs text-slate-500 mt-0.5"><?php echo esc_html( $it['desc'] ); ?></p><?php endif; ?>
							</div>
						</<?php echo $tag; // phpcs:ignore ?>>
						<?php elseif ( 'horizontal' === $v ) : ?>
						<<?php echo $tag . $attrs; // phpcs:ignore ?> class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-200/80 shadow-sm flex items-start gap-4 hover:shadow-md transition-shadow">
							<div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl <?php echo esc_attr( larijani_tone( $it['tone'], 'soft' ) ); ?> shrink-0 flex items-center justify-center text-xl"><?php echo larijani_icon( $it['icon'] ); // phpcs:ignore ?></div>
							<div>
								<h3 class="font-bold text-gray-900 text-sm sm:text-base"><?php echo esc_html( $it['title'] ); ?></h3>
								<?php if ( $it['desc'] ) : ?><p class="text-xs text-gray-500 mt-1 leading-relaxed"><?php echo esc_html( $it['desc'] ); ?></p><?php endif; ?>
							</div>
						</<?php echo $tag; // phpcs:ignore ?>>
						<?php elseif ( 'simple' === $v ) : ?>
						<<?php echo $tag . $attrs; // phpcs:ignore ?> class="p-space-lg rounded-2xl bg-surface-card shadow-sm flex flex-col gap-space-sm hover:shadow-md transition-shadow">
							<div class="w-12 h-12 rounded-xl <?php echo esc_attr( larijani_tone( $it['tone'], 'soft' ) ); ?> flex items-center justify-center text-2xl"><?php echo larijani_icon( $it['icon'] ); // phpcs:ignore ?></div>
							<h3 class="font-title-card text-title-card text-on-surface"><?php echo esc_html( $it['title'] ); ?></h3>
							<?php if ( $it['desc'] ) : ?><p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $it['desc'] ); ?></p><?php endif; ?>
						</<?php echo $tag; // phpcs:ignore ?>>
						<?php else : ?>
						<<?php echo $tag . $attrs; // phpcs:ignore ?> class="bg-surface-card rounded-2xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between group">
							<div class="flex flex-col gap-space-md">
								<div class="w-14 h-14 rounded-xl <?php echo esc_attr( larijani_tone( $it['tone'], 'soft' ) ); ?> flex items-center justify-center text-[28px]"><?php echo larijani_icon( $it['icon'] ); // phpcs:ignore ?></div>
								<div class="flex flex-col gap-space-xs">
									<?php if ( $it['eyebrow'] ) : ?><span class="font-label-badge text-label-badge text-outline"><?php echo esc_html( $it['eyebrow'] ); ?></span><?php endif; ?>
									<h3 class="font-headline-sm text-headline-sm text-surface-dark group-hover:text-primary transition-colors"><?php echo esc_html( $it['title'] ); ?></h3>
								</div>
								<?php if ( $it['desc'] ) : ?><p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $it['desc'] ); ?></p><?php endif; ?>
								<?php $chips = larijani_lines( $it['chips'] ); ?>
								<?php if ( $chips ) : ?>
								<div class="flex flex-wrap gap-1.5 pt-space-xs">
									<?php foreach ( $chips as $chip ) : ?><span class="font-body-sm text-body-sm px-2.5 py-1 bg-surface-canvas rounded-lg text-on-surface-variant"><?php echo esc_html( $chip ); ?></span><?php endforeach; ?>
								</div>
								<?php endif; ?>
							</div>
							<?php if ( $it['footer'] ) : ?>
							<div class="pt-space-lg mt-space-md flex items-center justify-between text-body-sm font-body-sm text-primary">
								<span class="font-semibold"><?php echo esc_html( $it['footer'] ); ?></span>
								<i class="bi bi-arrow-left text-[18px] group-hover:-translate-x-1 transition-transform" aria-hidden="true"></i>
							</div>
							<?php endif; ?>
						</<?php echo $tag; // phpcs:ignore ?>>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
