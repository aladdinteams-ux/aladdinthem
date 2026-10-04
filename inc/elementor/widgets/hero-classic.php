<?php
/**
 * Widget: Classic home hero (buttons, social proof, product card) + floating filter bar (design v1).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Classic hero.
 */
class LS_Widget_Hero_Classic extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-hero-classic';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS هیرو کلاسیک + فیلتر شناور', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-banner';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن و دکمه‌ها', 'larijani' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani' ), 'تولیدکننده نمونه قالب‌های نشکن ABS و ارتعاش سنجی بتن' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani' ), 'کیفیت، دقت و نوآوری در <br>' );
		$this->ctl( 'title_highlight', 'text', __( 'بخش رنگی عنوان', 'larijani' ), 'تولید قطعات بتنی و سنگ مصنوعی' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'بیش از ۱۵ سال تجربه تخصصی در طراحی و ساخت انواع قالب‌های نشکن پلیمری، میزهای ویبره دور متغیر، رزین‌های فوق‌روان‌کننده و راه‌اندازی صفر تا صد خطوط مکانیزه در سراسر ایران و کشورهای همسایه.' );
		$this->ctl( 'btn1_text', 'text', __( 'دکمه اول', 'larijani' ), 'مشاهده محصولات و قالب‌ها' );
		$this->ctl( 'btn1_link', 'url', __( 'لینک دکمه اول', 'larijani' ), '#products' );
		$this->ctl( 'btn2_text', 'text', __( 'دکمه دوم', 'larijani' ), 'ویدیو خط تولید فعال' );
		$this->ctl( 'btn2_link', 'url', __( 'لینک دکمه دوم', 'larijani' ), '#' );
		$this->ctl( 'btn2_icon', 'icon', __( 'آیکون دکمه دوم', 'larijani' ), 'play-fill' );
		$this->end();

		$this->section( 'sec_proof', __( 'اعتماد مشتریان', 'larijani' ) );
		$this->ctl( 'show_proof', 'switch', __( 'نمایش', 'larijani' ), 'yes' );
		$this->ctl( 'avatars', 'gallery', __( 'تصاویر مشتریان', 'larijani' ), array( ls_demo_media( 'avatar_1' ), ls_demo_media( 'avatar_2' ), ls_demo_media( 'avatar_3' ) ) );
		$this->ctl( 'proof_count', 'text', __( 'عدد داخل دایره', 'larijani' ), '+۱۰k' );
		$this->ctl( 'proof_title', 'text', __( 'عنوان', 'larijani' ), '+۱۰,۰۰۰ کارگاه و پروژه موفق' );
		$this->ctl( 'proof_note', 'text', __( 'توضیح کنار ستاره‌ها', 'larijani' ), '(رضایت ۹۹٪ مشتریان صنعتی)' );
		$this->end();

		$this->section( 'sec_media', __( 'تصویر و کارت محصول', 'larijani' ) );
		$this->ctl( 'image', 'media', __( 'تصویر', 'larijani' ), 'hero_classic' );
		$this->ctl( 'card_icon', 'icon', __( 'آیکون کارت', 'larijani' ), 'gear-wide-connected' );
		$this->ctl( 'card_title', 'text', __( 'عنوان کارت', 'larijani' ), 'میز ویبره صنعتی ۲ موتوره' );
		$this->ctl( 'card_sub', 'text', __( 'زیرعنوان', 'larijani' ), 'استاندارد صادراتی - صفحه ۸ میل' );
		$this->ctl( 'card_badge', 'text', __( 'برچسب', 'larijani' ), 'آماده ارسال' );
		$this->ctl( 'card_price_label', 'text', __( 'عنوان قیمت', 'larijani' ), 'قیمت کارخانه:' );
		$this->ctl( 'card_price', 'text', __( 'قیمت', 'larijani' ), '۴۸,۰۰۰,۰۰۰' );
		$this->ctl( 'card_currency', 'text', __( 'واحد', 'larijani' ), 'تومان' );
		$this->end();

		$this->section( 'sec_filter', __( 'نوار فیلتر شناور', 'larijani' ) );
		$this->ctl( 'show_filter', 'switch', __( 'نمایش', 'larijani' ), 'yes' );
		$this->ctl( 'filter_action', 'url', __( 'آدرس صفحه نتایج', 'larijani' ), array( 'url' => '' ) );
		$this->rep(
			'filters',
			__( 'فیلدها', 'larijani' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'param', 'text', __( 'نام پارامتر', 'larijani' ), '' ),
				array( 'options', 'textarea', __( 'گزینه‌ها (هر خط: عنوان|مقدار)', 'larijani' ), '', array( 'rows' => 5 ) ),
			),
			array(
				array( 'label' => 'دسته محصول', 'param' => 'product_cat', 'options' => "همه محصولات و قالب‌ها|\nانواع قالب نشکن پلیمری|molds\nمیزهای ویبره دور متغیر|vibrator\nمیکسر سنگ مصنوعی و بتن|mixer\nرزین و پیگمنت‌های آلمانی|resin\nپکیج کامل خط تولید|line" ),
				array( 'label' => 'جنس و متریال', 'param' => 'material', 'options' => "پلیمر ABS درجه یک صادراتی|\nABS نشکن با انعطاف بالا|abs\nفایبرگلاس تقویت شده چند لایه|fiber\nورق فولادی مبارکه ضدسایش|steel" ),
				array( 'label' => 'تیراژ تولید روزانه', 'param' => 'capacity', 'options' => "۱۵۰ الی ۳۰۰ متر مربع|\nتا ۱۰۰ متر مربع (کارگاهی)|small\n۲۰۰ تا ۵۰۰ متر مربع (نیمه صنعتی)|medium\nبیش از ۱۰۰۰ متر مربع (فول اتومات)|large" ),
				array( 'label' => 'شرایط تحویل و گارانتی', 'param' => 'delivery', 'options' => "تحویل فوری (۳ الی ۵ روزه)|\nارسال فوری همان روز از انبار|immediate\nبا ضمانت تعویض ۲۴ ماهه|warranty" ),
			),
			'{{{ label }}}'
		);
		$this->ctl( 'filter_button', 'text', __( 'متن دکمه', 'larijani' ), 'جستجوی تجهیزات' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$action = ! empty( $s['filter_action']['url'] ) ? $s['filter_action']['url'] : ( ls_has_woo() ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' ) );
		?>
		<section class="relative pt-6 <?php echo $this->on( $s, 'show_filter' ) ? 'pb-16 sm:pb-20 lg:pb-32' : 'pb-12 sm:pb-16'; ?> sm:pt-8 lg:pt-14 overflow-hidden">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
					<div class="lg:col-span-6 z-10 text-right">
						<?php if ( $s['badge'] ) : ?>
						<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-container/10 text-primary text-xs font-semibold mb-4 sm:mb-6"><span class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span><?php echo esc_html( $s['badge'] ); ?></div>
						<?php endif; ?>
						<h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-gray-950 leading-[1.3] sm:leading-[1.25] tracking-tight">
							<?php echo $this->t( $s, 'title' ); // phpcs:ignore ?>
							<span class="text-primary-container"><?php echo esc_html( $s['title_highlight'] ); ?></span>
						</h1>
						<?php if ( $s['desc'] ) : ?><p class="mt-4 sm:mt-5 text-gray-600 text-sm sm:text-base lg:text-lg leading-relaxed max-w-xl"><?php echo $this->t( $s, 'desc' ); // phpcs:ignore ?></p><?php endif; ?>
						<div class="mt-6 sm:mt-8 flex flex-wrap items-center gap-3 sm:gap-4">
							<?php if ( $s['btn1_text'] ) : ?>
							<a class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl bg-primary-container hover:bg-primary text-white text-sm sm:text-base font-semibold shadow-lg shadow-primary-container/30 transition group" <?php echo ls_link_attrs( $s['btn1_link'] ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn1_text'] ); ?></span><i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1" aria-hidden="true"></i></a>
							<?php endif; ?>
							<?php if ( $s['btn2_text'] ) : ?>
							<a class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-5 py-3.5 rounded-xl bg-white border border-gray-200 text-gray-800 text-sm sm:text-base font-medium hover:border-primary-container hover:text-primary-container shadow-sm transition" <?php echo ls_link_attrs( $s['btn2_link'] ); // phpcs:ignore ?>><span class="w-7 h-7 rounded-full bg-primary-container/10 flex items-center justify-center text-primary-container"><?php echo ls_icon( $s['btn2_icon'] ); // phpcs:ignore ?></span><span><?php echo esc_html( $s['btn2_text'] ); ?></span></a>
							<?php endif; ?>
						</div>
						<?php if ( $this->on( $s, 'show_proof' ) ) : ?>
						<div class="mt-8 sm:mt-10 pt-5 sm:pt-6 border-t border-gray-200/80 flex flex-wrap items-center gap-3 sm:gap-4">
							<div class="flex -space-x-2 space-x-reverse overflow-hidden">
								<?php foreach ( (array) $s['avatars'] as $av ) : ?>
									<?php echo ls_img( $av, 'inline-block h-9 w-9 sm:h-10 sm:w-10 rounded-full ring-2 ring-white object-cover', '', 'thumbnail' ); // phpcs:ignore ?>
								<?php endforeach; ?>
								<?php if ( $s['proof_count'] ) : ?><span class="inline-flex items-center justify-center h-9 w-9 sm:h-10 sm:w-10 rounded-full bg-primary-container text-white text-xs font-bold ring-2 ring-white"><?php echo esc_html( $s['proof_count'] ); ?></span><?php endif; ?>
							</div>
							<div>
								<p class="text-xs sm:text-sm font-bold text-gray-900"><?php echo esc_html( $s['proof_title'] ); ?></p>
								<div class="flex items-center gap-1 text-amber-500 text-xs mt-0.5"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span><span class="text-gray-500 font-medium mr-1.5 text-[10px] sm:text-[11px]"><?php echo esc_html( $s['proof_note'] ); ?></span></div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<div class="lg:col-span-6 relative mt-4 lg:mt-0">
						<div class="relative mx-auto max-w-md lg:max-w-none">
							<div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-gray-100">
								<?php echo ls_img( $s['image'], 'w-full h-[320px] sm:h-[420px] lg:h-[480px] object-cover object-center', '', 'large', false ); // phpcs:ignore ?>
								<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
							</div>
							<?php if ( $s['card_title'] ) : ?>
							<div class="absolute bottom-3 right-3 left-3 sm:bottom-6 sm:right-6 sm:left-auto sm:w-80 bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xl border border-gray-100/80">
								<div class="flex items-center justify-between gap-2">
									<div class="flex items-center gap-2.5 sm:gap-3">
										<div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-primary-container/15 text-primary-container flex items-center justify-center shrink-0"><?php echo ls_icon( $s['card_icon'] ); // phpcs:ignore ?></div>
										<div>
											<h2 class="text-xs sm:text-sm font-bold text-gray-900 leading-tight"><?php echo esc_html( $s['card_title'] ); ?></h2>
											<p class="text-[10px] sm:text-[11px] text-gray-500 flex items-center gap-1 mt-0.5"><i class="bi bi-patch-check" aria-hidden="true"></i><?php echo esc_html( $s['card_sub'] ); ?></p>
										</div>
									</div>
									<?php if ( $s['card_badge'] ) : ?><span class="text-[9px] sm:text-[10px] font-semibold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md whitespace-nowrap"><?php echo esc_html( $s['card_badge'] ); ?></span><?php endif; ?>
								</div>
								<?php if ( $s['card_price'] ) : ?>
								<div class="mt-2.5 pt-2.5 sm:mt-3 sm:pt-3 border-t border-gray-100 flex items-center justify-between">
									<span class="text-[11px] sm:text-xs text-gray-500 font-medium"><?php echo esc_html( $s['card_price_label'] ); ?></span>
									<span class="text-xs sm:text-sm font-extrabold text-primary-container"><?php echo esc_html( $s['card_price'] ); ?> <span class="text-[10px] sm:text-[11px] font-normal text-gray-500"><?php echo esc_html( $s['card_currency'] ); ?></span></span>
								</div>
								<?php endif; ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php if ( $this->on( $s, 'show_filter' ) && $s['filters'] ) : ?>
		<section class="relative z-20 -mt-8 sm:-mt-12 lg:-mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="bg-white rounded-2xl lg:rounded-3xl p-4 sm:p-6 shadow-floating border border-gray-100">
				<form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-center" method="get" action="<?php echo esc_url( $action ); ?>">
					<?php foreach ( $s['filters'] as $i => $f ) : ?>
					<label class="block <?php echo $i < count( $s['filters'] ) - 1 ? 'border-b sm:border-b-0 sm:border-l border-gray-200 pb-3 sm:pb-0 sm:pl-4' : 'pb-3 sm:pb-0'; ?>">
						<span class="block text-xs font-semibold text-gray-400 mb-1"><?php echo esc_html( $f['label'] ); ?></span>
						<select name="<?php echo esc_attr( $f['param'] ? $f['param'] : sanitize_title( $f['label'] ) ); ?>" class="w-full bg-transparent font-semibold text-gray-800 text-sm p-0 pl-6 cursor-pointer">
							<?php foreach ( ls_lines( $f['options'] ) as $line ) : ?>
								<?php $o = array_map( 'trim', explode( '|', $line ) ); ?>
							<option value="<?php echo esc_attr( $o[1] ?? '' ); ?>"><?php echo esc_html( $o[0] ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<?php endforeach; ?>
					<div class="sm:col-span-2 lg:col-span-1">
						<button class="w-full py-3.5 px-6 rounded-xl bg-primary-container hover:bg-primary text-white font-bold text-sm flex items-center justify-center gap-2 shadow-md transition" type="submit"><i class="bi bi-search" aria-hidden="true"></i><span><?php echo esc_html( $s['filter_button'] ); ?></span></button>
					</div>
				</form>
			</div>
		</section>
		<?php endif; ?>
		<?php
	}
}
