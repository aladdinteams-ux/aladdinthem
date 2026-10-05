<?php
/**
 * Widget: Home hero with product search panel and image card (design v2).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hero widget.
 */
class LS_Widget_Hero extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS هیرو با جستجوی محصولات', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن‌ها', 'larijani' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani' ), 'تجهیزات مدرن و قالب‌های نشکن بتنی و سنگ مصنوعی' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani' ), 'کیفیت، دقت و نوآوری در تولید <br class="hidden sm:inline">' );
		$this->ctl( 'title_highlight', 'text', __( 'بخش رنگی عنوان', 'larijani' ), 'قطعات بتنی با لاریجانی استون' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani' ), 'بیش از ۱۵ سال تجربه مستمر در طراحی و ساخت انواع قالب‌های پلیمری، میزهای ویبره صنعتی سنگین، ملات‌سازها، افزودنی‌های پلیمری بتن و راه‌اندازی صفر تا صد خطوط تولید در سراسر کشور.' );
		$this->ctl( 'title_tag', 'select', __( 'تگ عنوان', 'larijani' ), 'h1', array( 'options' => array( 'h1' => 'H1', 'h2' => 'H2' ) ) );
		$this->end();

		$this->section( 'sec_search', __( 'پنل جستجو', 'larijani' ) );
		$this->ctl( 'show_search', 'switch', __( 'نمایش پنل جستجو', 'larijani' ), 'yes' );
		$this->ctl( 'search_action', 'url', __( 'آدرس صفحه نتایج', 'larijani' ), array( 'url' => '' ), array( 'description' => __( 'خالی = صفحه فروشگاه ووکامرس یا جستجوی سایت. فیلدها به صورت پارامتر GET ارسال می‌شوند.', 'larijani' ) ) );
		$this->rep(
			'tabs',
			__( 'تب‌ها', 'larijani' ),
			array(
				array( 'title', 'text', __( 'عنوان تب', 'larijani' ), '' ),
				array( 'value', 'text', __( 'مقدار (مثلاً اسلاگ دسته محصول)', 'larijani' ), '' ),
			),
			array(
				array( 'title' => 'قالب‌های پلیمری و نشکن', 'value' => 'molds' ),
				array( 'title' => 'ماشین‌آلات و میز ویبره', 'value' => 'machinery' ),
				array( 'title' => 'رزین و رنگدانه‌ها', 'value' => 'chemicals' ),
			)
		);
		$this->ctl( 'tab_param', 'text', __( 'نام پارامتر تب', 'larijani' ), 'product_cat' );
		$this->rep(
			'fields',
			__( 'فیلدهای انتخابی', 'larijani' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'param', 'text', __( 'نام پارامتر', 'larijani' ), '' ),
				array( 'options', 'textarea', __( 'گزینه‌ها (هر خط: عنوان|مقدار)', 'larijani' ), '', array( 'rows' => 5 ) ),
			),
			array(
				array( 'label' => 'دسته‌بندی محصول', 'param' => 'type', 'options' => "قالب کفپوش و واش‌بتن|paving\nقالب سنگ نما و آنتیک|facade\nقالب جدول و دورباغچه‌ای|curb\nقالب پله و زیرپله|stairs\nمیز ویبره و میکسر صنعتی|machinery" ),
				array( 'label' => 'نوع مواد مصرفی', 'param' => 'material', 'options' => "ABS درجه یک نشکن|abs\nپلیمر مهندسی مقاوم|polymer\nلاستیکی انعطاف‌پذیر|rubber\nآلیاژ فولادی مقاوم|alloy" ),
				array( 'label' => 'حجم و رده سفارش', 'param' => 'scale', 'options' => "تک و کارگاهی|single\nتیراژ متوسط (کارگاهی)|medium\nپروژه‌ای و صنعتی (عمده)|bulk" ),
			),
			'{{{ label }}}'
		);
		$this->ctl( 'search_button', 'text', __( 'متن دکمه جستجو', 'larijani' ), 'جستجو و مشاهده قیمت محصولات' );
		$this->end();

		$this->section( 'sec_media', __( 'تصویر و کارت‌های شناور', 'larijani' ) );
		$this->ctl( 'image', 'media', __( 'تصویر', 'larijani' ), 'hero' );
		$this->ctl( 'stat_icon', 'icon', __( 'آیکون کارت بالا', 'larijani' ), 'people-fill' );
		$this->ctl( 'stat_label', 'text', __( 'عنوان کارت بالا', 'larijani' ), 'مشتریان راضی' );
		$this->ctl( 'stat_value', 'text', __( 'مقدار کارت بالا', 'larijani' ), '۱۰,۰۰۰+ خریدار موفق' );
		$this->ctl( 'card_label', 'text', __( 'متن کوچک کارت پایین', 'larijani' ), 'خط تولید استاندارد و صادراتی' );
		$this->ctl( 'card_title', 'text', __( 'عنوان کارت پایین', 'larijani' ), 'تجهیز بیش از ۲۰۰ کارگاه در سال گذشته' );
		$this->ctl( 'card_link', 'url', __( 'لینک کارت پایین', 'larijani' ), '#' );
		$this->end();

		$this->style_controls();
	}

	/**
	 * Render.
	 *
	 * @param array $s Settings.
	 */
	protected function render_widget( $s ) {
		$tag    = in_array( $s['title_tag'], array( 'h1', 'h2' ), true ) ? $s['title_tag'] : 'h1';
		$action = ! empty( $s['search_action']['url'] ) ? $s['search_action']['url'] : ( ls_has_woo() ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' ) );
		?>
		<section class="pt-6 sm:pt-10 pb-12 sm:pb-16 lg:pt-14 lg:pb-20">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="flex flex-col-reverse lg:grid lg:grid-cols-12 gap-8 lg:gap-8 items-center">
					<div class="w-full lg:col-span-7 flex flex-col items-start text-right">
						<?php if ( $s['badge'] ) : ?>
						<div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200/80 px-3.5 py-1.5 rounded-full text-xs font-semibold text-primary-container mb-3 sm:mb-4">
							<span class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
							<span class="text-[11px] sm:text-xs"><?php echo esc_html( $s['badge'] ); ?></span>
						</div>
						<?php endif; ?>
						<<?php echo esc_attr( $tag ); ?> class="text-2xl sm:text-4xl lg:text-5xl font-black text-surface-dark leading-[1.3] sm:leading-[1.25] tracking-tight mb-3 sm:mb-4">
							<?php echo $this->t( $s, 'title' ); // phpcs:ignore ?>
							<?php if ( $s['title_highlight'] ) : ?><span class="text-primary-container"><?php echo esc_html( $s['title_highlight'] ); ?></span><?php endif; ?>
						</<?php echo esc_attr( $tag ); ?>>
						<?php if ( $s['desc'] ) : ?>
						<p class="text-slate-600 text-xs sm:text-sm lg:text-base leading-relaxed max-w-xl mb-6 sm:mb-8"><?php echo $this->t( $s, 'desc' ); // phpcs:ignore ?></p>
						<?php endif; ?>

						<?php if ( $this->on( $s, 'show_search' ) ) : ?>
						<form class="w-full bg-white rounded-2xl p-4 sm:p-5 border border-border-subtle card-shadow relative z-10" method="get" action="<?php echo esc_url( $action ); ?>" data-ls-hero-search>
							<?php if ( $s['tabs'] ) : ?>
							<input type="hidden" name="<?php echo esc_attr( $s['tab_param'] ? $s['tab_param'] : 'tab' ); ?>" value="<?php echo esc_attr( $s['tabs'][0]['value'] ); ?>" data-ls-tab-input>
							<div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3 overflow-x-auto custom-scroll -mx-2 px-2" role="group" aria-label="<?php esc_attr_e( 'نوع جستجو', 'larijani' ); ?>">
								<?php foreach ( $s['tabs'] as $i => $tab ) : ?>
								<button class="flex-shrink-0 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm <?php echo 0 === $i ? 'font-bold bg-primary-container text-white' : 'font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200'; ?> transition-colors" type="button" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>" data-ls-search-tab="<?php echo esc_attr( $tab['value'] ); ?>" data-on="font-bold bg-primary-container text-white" data-off="font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200"><?php echo esc_html( $tab['title'] ); ?></button>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
							<?php if ( $s['fields'] ) : ?>
							<div class="grid grid-cols-1 <?php echo count( $s['fields'] ) >= 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-' . count( $s['fields'] ); ?> gap-3 mb-4">
								<?php foreach ( $s['fields'] as $f ) : ?>
								<label class="flex flex-col">
									<span class="text-[11px] font-semibold text-slate-500 mb-1"><?php echo esc_html( $f['label'] ); ?></span>
									<select name="<?php echo esc_attr( $f['param'] ? $f['param'] : sanitize_title( $f['label'] ) ); ?>" class="w-full h-11 bg-surface-canvas hover:bg-slate-50 text-xs sm:text-sm text-slate-800 rounded-xl border border-slate-200 focus:border-primary-container py-2 pr-3 pl-8 cursor-pointer transition-colors block text-right font-medium">
										<?php foreach ( ls_lines( $f['options'] ) as $line ) : ?>
											<?php $o = array_map( 'trim', explode( '|', $line ) ); ?>
										<option value="<?php echo esc_attr( $o[1] ?? $o[0] ); ?>"><?php echo esc_html( $o[0] ); ?></option>
										<?php endforeach; ?>
									</select>
								</label>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
							<div class="flex items-center justify-end">
								<button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-primary-container hover:bg-primary text-white text-xs sm:text-sm font-bold px-6 sm:px-7 py-3 rounded-xl transition-all shadow-sm" type="submit">
									<i class="bi bi-search" aria-hidden="true"></i><span><?php echo esc_html( $s['search_button'] ); ?></span>
								</button>
							</div>
						</form>
						<?php endif; ?>
					</div>

					<div class="w-full lg:col-span-5 relative">
						<div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl sm:shadow-2xl border-2 sm:border-4 border-white max-w-lg mx-auto lg:max-w-none">
							<?php echo ls_img( $s['image'], 'w-full h-[320px] sm:h-[420px] lg:h-[480px] object-cover hover:scale-105 transition-transform duration-700', '', 'large', false ); // phpcs:ignore ?>
							<div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
							<?php if ( $s['stat_value'] ) : ?>
							<div class="absolute top-3.5 right-3.5 sm:top-5 sm:right-5 bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl p-2.5 sm:p-3 sm:px-4 shadow-lg border border-white/40 flex items-center gap-2.5 sm:gap-3 max-w-[85%]">
								<div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-primary-container/15 flex items-center justify-center text-primary-container flex-shrink-0 text-lg"><?php echo ls_icon( $s['stat_icon'] ); // phpcs:ignore ?></div>
								<div>
									<div class="text-[10px] sm:text-xs font-semibold text-slate-500"><?php echo esc_html( $s['stat_label'] ); ?></div>
									<div class="text-xs sm:text-base font-black text-surface-dark"><?php echo esc_html( $s['stat_value'] ); ?></div>
								</div>
							</div>
							<?php endif; ?>
							<?php if ( $s['card_title'] ) : ?>
							<a class="absolute bottom-3.5 left-3.5 right-3.5 sm:bottom-5 sm:left-5 sm:right-5 bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-white/50 flex items-center justify-between gap-2 group" <?php echo ls_link_attrs( $s['card_link'] ); // phpcs:ignore ?>>
								<div class="min-w-0">
									<div class="text-[11px] sm:text-xs text-slate-600 font-medium truncate"><?php echo esc_html( $s['card_label'] ); ?></div>
									<div class="text-xs sm:text-sm font-black text-surface-dark truncate"><?php echo esc_html( $s['card_title'] ); ?></div>
								</div>
								<span class="inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-primary-container text-white flex-shrink-0 transition-transform group-hover:-translate-x-1"><i class="bi bi-arrow-left" aria-hidden="true"></i></span>
							</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
