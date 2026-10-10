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
class Larijani_Widget_Hero extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-hero';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS هیرو با جستجوی محصولات', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_text', __( 'متن‌ها', 'larijani-stone' ) );
		$this->ctl( 'badge', 'text', __( 'برچسب', 'larijani-stone' ), 'تجهیزات مدرن و قالب‌های نشکن بتنی و سنگ مصنوعی' );
		$this->ctl( 'title', 'textarea', __( 'عنوان', 'larijani-stone' ), 'کیفیت، دقت و نوآوری در تولید <br class="hidden sm:inline">' );
		$this->ctl( 'title_highlight', 'text', __( 'بخش رنگی عنوان', 'larijani-stone' ), 'قطعات بتنی با لاریجانی استون' );
		$this->ctl( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), 'بیش از ۱۵ سال تجربه مستمر در طراحی و ساخت انواع قالب‌های پلیمری، میزهای ویبره صنعتی سنگین، ملات‌سازها، افزودنی‌های پلیمری بتن و راه‌اندازی صفر تا صد خطوط تولید در سراسر کشور.' );
		$this->ctl( 'title_tag', 'select', __( 'تگ عنوان', 'larijani-stone' ), 'h1', array( 'options' => array( 'h1' => 'H1', 'h2' => 'H2' ) ) );
		$this->end();

		$this->section( 'sec_search', __( 'پنل جستجو', 'larijani-stone' ) );
		$this->ctl( 'show_search', 'switch', __( 'نمایش پنل جستجو', 'larijani-stone' ), 'yes' );
		$this->ctl( 'search_action', 'url', __( 'آدرس صفحه نتایج', 'larijani-stone' ), array( 'url' => '' ), array( 'description' => __( 'خالی = صفحه فروشگاه ووکامرس یا جستجوی سایت. فیلدها به صورت پارامتر GET ارسال می‌شوند.', 'larijani-stone' ) ) );
		$this->rep(
			'tabs',
			__( 'تب‌ها', 'larijani-stone' ),
			array(
				array( 'title', 'text', __( 'عنوان تب', 'larijani-stone' ), '' ),
				array( 'value', 'text', __( 'مقدار (نامک دسته محصول، مثل mold)', 'larijani-stone' ), '' ),
			),
			array(
				array( 'title' => 'قالب‌های پلیمری و نشکن', 'value' => 'mold' ),
				array( 'title' => 'ماشین‌آلات و میز ویبره', 'value' => 'machinery' ),
				array( 'title' => 'رزین و رنگدانه‌ها', 'value' => 'chemical' ),
			)
		);
		$this->ctl( 'tab_param', 'text', __( 'نام پارامتر تب', 'larijani-stone' ), 'product_cat' );
		$this->rep(
			'fields',
			__( 'فیلدهای انتخابی', 'larijani-stone' ),
			array(
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'param', 'text', __( 'نام پارامتر', 'larijani-stone' ), '' ),
				array(
					'mode',
					'select',
					__( 'کاربرد', 'larijani-stone' ),
					'filter',
					array(
						'options' => array(
							'filter' => __( 'فیلتر محصولات', 'larijani-stone' ),
							'info'   => __( 'فقط اطلاعات (مثل حجم سفارش)', 'larijani-stone' ),
						),
					),
				),
				array( 'options', 'textarea', __( 'گزینه‌ها', 'larijani-stone' ), '', array( 'rows' => 7, 'description' => __( 'هر خط: عنوان|کلمات جستجو یا نامک برچسب محصول|تب‌ها. کلمات با کاما جدا می‌شوند و در عنوان، توضیح یا شناسه محصول جستجو می‌شوند؛ اگر برچسب یا ویژگی محصولی با همین نامک وجود داشته باشد همان استفاده می‌شود. «تب‌ها» (اختیاری) مقدار تب‌هایی است که گزینه در آن‌ها نمایش داده شود. مقدار خالی = همه.', 'larijani-stone' ) ) ),
			),
			array(
				array( 'label' => 'دسته‌بندی محصول', 'param' => 'type', 'mode' => 'filter', 'options' => "همه دسته‌ها|\nقالب کفپوش و واش‌بتن|کفپوش,واش|mold\nقالب سنگ نما و آنتیک|نما,سه‌بعدی,صخره,آنتیک|mold\nقالب جدول و دورباغچه‌ای|جدول,دورباغچه|mold\nقالب صراحی، ستون و نرده|صراحی,ستون,نرده|mold\nمیز ویبره صنعتی|ویبره|machinery\nمیکسر و همزن صنعتی|میکسر,همزن|machinery\nروان‌کننده و رزین بتن|روان‌کننده,رزین,کربوکسیلات|chemical\nرنگدانه و پیگمنت|رنگدانه,اکسید آهن,پیگمنت|chemical" ),
				array( 'label' => 'نوع مواد مصرفی', 'param' => 'material', 'mode' => 'filter', 'options' => "همه مواد|\nABS درجه یک نشکن|ABS|mold\nفایبرگلاس و کامپوزیت|فایبرگلاس,کامپوزیت|mold\nفولاد و ورق صنعتی|ST52,ST-37,فولاد,ورق,تیرآهن|machinery\nپایه پلی‌کربوکسیلات|کربوکسیلات|chemical\nپیگمنت معدنی|اکسید آهن,معدنی|chemical" ),
				array( 'label' => 'حجم و رده سفارش', 'param' => 'scale', 'mode' => 'info', 'options' => "تک و کارگاهی|single\nتیراژ متوسط (کارگاهی)|medium\nپروژه‌ای و صنعتی (عمده)|bulk" ),
			),
			'{{{ label }}}'
		);
		$this->ctl( 'search_button', 'text', __( 'متن دکمه جستجو', 'larijani-stone' ), 'جستجو و مشاهده قیمت محصولات' );
		$this->end();

		$this->section( 'sec_media', __( 'تصویر و کارت‌های شناور', 'larijani-stone' ) );
		$this->ctl( 'image', 'media', __( 'تصویر', 'larijani-stone' ), 'hero' );
		$this->ctl( 'stat_icon', 'icon', __( 'آیکون کارت بالا', 'larijani-stone' ), 'people-fill' );
		$this->ctl( 'stat_label', 'text', __( 'عنوان کارت بالا', 'larijani-stone' ), 'مشتریان راضی' );
		$this->ctl( 'stat_value', 'text', __( 'مقدار کارت بالا', 'larijani-stone' ), '۱۰,۰۰۰+ خریدار موفق' );
		$this->ctl( 'card_label', 'text', __( 'متن کوچک کارت پایین', 'larijani-stone' ), 'خط تولید استاندارد و صادراتی' );
		$this->ctl( 'card_title', 'text', __( 'عنوان کارت پایین', 'larijani-stone' ), 'تجهیز بیش از ۲۰۰ کارگاه در سال گذشته' );
		$this->ctl( 'card_link', 'url', __( 'لینک کارت پایین', 'larijani-stone' ), '#' );
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
		$woo       = larijani_has_woo();
		$action    = ! empty( $s['search_action']['url'] ) ? $s['search_action']['url'] : ( $woo ? get_permalink( wc_get_page_id( 'shop' ) ) : larijani_page_url( 'shop', home_url( '/' ) ) );
		$tab_param = $woo ? ( $s['tab_param'] ? $s['tab_param'] : 'product_cat' ) : 'ls_cat'; // The theme catalog (no WooCommerce) filters itself from ?ls_cat=.
		$filters   = array();
		foreach ( (array) $s['fields'] as $f ) {
			if ( 'info' !== ( $f['mode'] ?? 'filter' ) ) {
				$filters[] = $f['param'] ? sanitize_key( $f['param'] ) : sanitize_title( $f['label'] );
			}
		}
		$uid = $this->get_id();
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
							<input type="hidden" name="<?php echo esc_attr( $tab_param ); ?>" value="<?php echo esc_attr( $s['tabs'][0]['value'] ); ?>" data-ls-tab-input>
							<div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3 overflow-x-auto custom-scroll -mx-2 px-2" role="group" aria-label="<?php esc_attr_e( 'نوع جستجو', 'larijani-stone' ); ?>">
								<?php foreach ( $s['tabs'] as $i => $tab ) : ?>
								<button class="flex-shrink-0 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm <?php echo 0 === $i ? 'font-bold bg-primary-container text-white' : 'font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200'; ?> transition-colors" type="button" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>" data-ls-search-tab="<?php echo esc_attr( $tab['value'] ); ?>" data-on="font-bold bg-primary-container text-white" data-off="font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200"><?php echo esc_html( $tab['title'] ); ?></button>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
							<input type="hidden" name="ls_hs" value="<?php echo esc_attr( implode( ',', $filters ) ); ?>">
							<?php if ( $s['fields'] ) : ?>
							<div class="grid grid-cols-1 <?php echo count( $s['fields'] ) >= 3 ? 'sm:grid-cols-3' : 'sm:grid-cols-' . count( $s['fields'] ); ?> gap-3 mb-4">
								<?php foreach ( $s['fields'] as $fi => $f ) : ?>
									<?php $fid = 'ls-hs-' . $uid . '-' . $fi; ?>
								<div class="flex flex-col" data-ls-select-wrap>
									<label class="text-[11px] font-semibold text-slate-500 mb-1" for="<?php echo esc_attr( $fid ); ?>"><?php echo esc_html( $f['label'] ); ?></label>
									<select id="<?php echo esc_attr( $fid ); ?>" name="<?php echo esc_attr( $f['param'] ? $f['param'] : sanitize_title( $f['label'] ) ); ?>" class="w-full h-11 bg-surface-canvas hover:bg-slate-50 text-xs sm:text-sm text-slate-800 rounded-xl border border-slate-200 focus:border-primary-container py-2 pr-3 pl-8 cursor-pointer transition-colors block text-right font-medium" data-ls-select>
										<?php foreach ( larijani_lines( $f['options'] ) as $line ) : ?>
											<?php $o = larijani_search_option( $line ); ?>
										<option value="<?php echo esc_attr( $o['value'] ); ?>"<?php echo $o['tabs'] ? ' data-tabs="' . esc_attr( implode( ',', $o['tabs'] ) ) . '"' : ''; ?>><?php echo esc_html( $o['label'] ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
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
							<?php echo larijani_img( $s['image'], 'w-full h-[320px] sm:h-[420px] lg:h-[480px] object-cover hover:scale-105 transition-transform duration-700', '', 'large', 'high' ); // phpcs:ignore ?>
							<div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
							<?php if ( $s['stat_value'] ) : ?>
							<div class="absolute top-3.5 right-3.5 sm:top-5 sm:right-5 bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl p-2.5 sm:p-3 sm:px-4 shadow-lg border border-white/40 flex items-center gap-2.5 sm:gap-3 max-w-[85%]">
								<div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-primary-container/15 flex items-center justify-center text-primary-container flex-shrink-0 text-lg"><?php echo larijani_icon( $s['stat_icon'] ); // phpcs:ignore ?></div>
								<div>
									<div class="text-[10px] sm:text-xs font-semibold text-slate-500"><?php echo esc_html( $s['stat_label'] ); ?></div>
									<div class="text-xs sm:text-base font-black text-surface-dark"><?php echo esc_html( $s['stat_value'] ); ?></div>
								</div>
							</div>
							<?php endif; ?>
							<?php if ( $s['card_title'] ) : ?>
							<a class="absolute bottom-3.5 left-3.5 right-3.5 sm:bottom-5 sm:left-5 sm:right-5 bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-white/50 flex items-center justify-between gap-2 group" <?php echo larijani_link_attrs( $s['card_link'] ); // phpcs:ignore ?>>
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
