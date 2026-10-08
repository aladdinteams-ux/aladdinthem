<?php
/**
 * Widget: Map & transit directions (embedded map or image, overlays, route cards).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Map widget.
 */
class Larijani_Widget_Map extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-map';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS نقشه و راهنمای دسترسی', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-google-maps';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani-stone' ) );
		$this->heading_controls(
			array(
				'eyebrow' => 'موقعیت لجستیکی کارخانه',
				'title'   => 'دسترسی ترانزیتی و نقشه کارخانه آبیک',
				'desc'    => 'موقعیت کارخانه در هاب مواصلاتی آزادراه کرج - قزوین دسترسی آسان برای بازدید حضوری کارگاه نمونه، تست ماشین‌آلات و بارگیری سریع را فراهم کرده است.',
				'style'   => 'token',
			)
		);
		$this->ctl( 'btn1_text', 'text', __( 'دکمه مسیریابی ۱', 'larijani-stone' ), 'مسیریابی در نشان و بلد' );
		$this->ctl( 'btn1_link', 'url', __( 'لینک ۱', 'larijani-stone' ), array( 'url' => 'https://neshan.org/maps/search/آبیک', 'is_external' => true ) );
		$this->ctl( 'btn2_text', 'text', __( 'دکمه مسیریابی ۲', 'larijani-stone' ), 'گوگل مپ' );
		$this->ctl( 'btn2_link', 'url', __( 'لینک ۲', 'larijani-stone' ), array( 'url' => 'https://maps.google.com/?q=Abyek+Qazvin', 'is_external' => true ) );
		$this->end();

		$this->section( 'sec_map', __( 'نقشه', 'larijani-stone' ) );
		$this->ctl( 'map_type', 'select', __( 'نوع نقشه', 'larijani-stone' ), 'image', array( 'options' => array( 'image' => __( 'تصویر', 'larijani-stone' ), 'embed' => __( 'کد iframe (گوگل/نشان/بلد)', 'larijani-stone' ), 'osm' => __( 'OpenStreetMap با مختصات', 'larijani-stone' ) ) ) );
		$this->ctl( 'map_image', 'media', __( 'تصویر نقشه', 'larijani-stone' ), 'contact_map', array( 'condition' => array( 'map_type' => 'image' ) ) );
		$this->ctl( 'embed_url', 'text', __( 'آدرس src کد embed', 'larijani-stone' ), '', array( 'condition' => array( 'map_type' => 'embed' ) ) );
		$this->ctl( 'lat', 'text', __( 'عرض جغرافیایی', 'larijani-stone' ), '36.0528', array( 'condition' => array( 'map_type' => 'osm' ) ) );
		$this->ctl( 'lng', 'text', __( 'طول جغرافیایی', 'larijani-stone' ), '50.5367', array( 'condition' => array( 'map_type' => 'osm' ) ) );
		$this->ctl( 'pin_title', 'text', __( 'عنوان پین', 'larijani-stone' ), 'کارخانه لاریجانی استون' );
		$this->ctl( 'pin_sub', 'text', __( 'زیرعنوان پین', 'larijani-stone' ), 'مجتمع صنعتی پیروز، پلاک ۱۵' );
		$this->ctl( 'coords', 'text', __( 'متن مختصات', 'larijani-stone' ), '36.0528° N, 50.5367° E' );
		$this->end();

		$this->section( 'sec_routes', __( 'راهنمای دسترسی', 'larijani-stone' ) );
		$this->ctl( 'routes_title', 'text', __( 'عنوان', 'larijani-stone' ), 'راهنمای دسترسی جاده‌ای' );
		$this->rep(
			'routes',
			__( 'مسیرها', 'larijani-stone' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani-stone' ), 'pin-map-fill' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'text', 'textarea', __( 'متن', 'larijani-stone' ), '' ),
				array( 'highlight', 'switch', __( 'پس‌زمینه سبز', 'larijani-stone' ), '' ),
			),
			array(
				array( 'icon' => 'pin-map-fill', 'title' => 'مسیر از تهران و کرج:', 'text' => 'آزادراه تهران-قزوین، پس از عوارضی آبیک، خروجی بلوار خلیج فارس، مجتمع صنعتی پیروز، پلاک ۱۵. (فاصله زمانی از میدان آزادی: ۵۵ دقیقه)' ),
				array( 'icon' => 'pin-map-fill', 'title' => 'مسیر از سمت شمال و قزوین:', 'text' => 'کیلومتر ۴۵ جاده قدیم قزوین به آبیک، ورودی شرقی شهرک صنعتی پیروز، خیابان صنعتگران دوم.' ),
				array( 'icon' => 'box-seam-fill', 'title' => 'بارگیری ماشین‌آلات سنگین:', 'text' => 'دارای باسکول دیجیتال ۵۰ تنی و رمپ استاندارد جهت بارگیری تریلی کفی و جرثقیل سقفی ۱۰ تن.', 'highlight' => 'yes' ),
			)
		);
		$this->ctl( 'bottom_text', 'text', __( 'دکمه پایین', 'larijani-stone' ), 'هماهنگی پیش از بازدید حضوری' );
		$this->ctl( 'bottom_link', 'url', __( 'لینک دکمه پایین', 'larijani-stone' ), 'tel:09122302685' );
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
		$h = larijani_heading_from_settings( $s, array( 'mb' => '' ) );
		?>
		<section class="w-full py-space-xl <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<div class="flex flex-col gap-space-lg">
					<div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md">
						<div class="flex flex-col gap-space-xs max-w-xl">
							<?php if ( $h['eyebrow'] ) : ?><span class="font-label-badge text-label-badge text-primary uppercase font-bold tracking-wider"><?php echo esc_html( $h['eyebrow'] ); ?></span><?php endif; ?>
							<h2 class="font-headline-lg text-headline-lg text-on-surface"><?php echo larijani_kses( $h['title'] ); // phpcs:ignore ?></h2>
							<?php if ( $h['desc'] ) : ?><p class="font-body-md text-body-md text-on-surface-variant"><?php echo esc_html( $h['desc'] ); ?></p><?php endif; ?>
						</div>
						<div class="flex flex-wrap items-center gap-space-sm">
							<?php if ( $s['btn1_text'] ) : ?><a class="inline-flex items-center gap-2 bg-surface-card hover:bg-surface-container-high text-on-surface px-space-md py-2.5 rounded-full font-label-nav text-label-nav shadow-sm transition-all" <?php echo larijani_link_attrs( $s['btn1_link'] ); // phpcs:ignore ?>><i class="bi bi-compass text-[16px] text-accent-cobalt" aria-hidden="true"></i><span><?php echo esc_html( $s['btn1_text'] ); ?></span></a><?php endif; ?>
							<?php if ( $s['btn2_text'] ) : ?><a class="inline-flex items-center gap-2 bg-primary-container text-on-primary px-space-md py-2.5 rounded-full font-label-nav text-label-nav shadow-sm hover:bg-primary transition-all" <?php echo larijani_link_attrs( $s['btn2_link'] ); // phpcs:ignore ?>><i class="bi bi-geo-alt-fill text-[16px]" aria-hidden="true"></i><span><?php echo esc_html( $s['btn2_text'] ); ?></span></a><?php endif; ?>
						</div>
					</div>
					<div class="bg-surface-card rounded-3xl overflow-hidden shadow-md">
						<div class="grid grid-cols-1 lg:grid-cols-12">
							<div class="lg:col-span-8 min-h-[380px] lg:min-h-[460px] relative">
								<?php if ( 'embed' === $s['map_type'] && $s['embed_url'] ) : ?>
								<iframe class="absolute inset-0 w-full h-full" src="<?php echo esc_url( $s['embed_url'] ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title="<?php echo esc_attr( $s['pin_title'] ); ?>"></iframe>
								<?php elseif ( 'osm' === $s['map_type'] ) : ?>
									<?php
									$lat  = (float) $s['lat'];
									$lng  = (float) $s['lng'];
									$bbox = ( $lng - 0.02 ) . ',' . ( $lat - 0.012 ) . ',' . ( $lng + 0.02 ) . ',' . ( $lat + 0.012 );
									?>
								<iframe class="absolute inset-0 w-full h-full" src="<?php echo esc_url( 'https://www.openstreetmap.org/export/embed.html?bbox=' . rawurlencode( $bbox ) . '&layer=mapnik&marker=' . $lat . ',' . $lng ); ?>" loading="lazy" title="<?php echo esc_attr( $s['pin_title'] ); ?>"></iframe>
								<?php else : ?>
								<div class="absolute inset-0 bg-cover bg-center" style="background-image:url('<?php echo esc_url( larijani_img_url( $s['map_image'], 'large' ) ); ?>')" role="img" aria-label="<?php echo esc_attr( $s['pin_title'] ); ?>"></div>
								<?php endif; ?>
								<?php if ( $s['pin_title'] ) : ?>
								<div class="absolute top-4 right-4 bg-surface-card/90 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-md flex items-center gap-3 pointer-events-none">
									<div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-on-primary"><i class="bi bi-buildings-fill text-[16px]" aria-hidden="true"></i></div>
									<div class="flex flex-col text-right"><span class="font-headline-sm text-body-md text-on-surface"><?php echo esc_html( $s['pin_title'] ); ?></span><span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $s['pin_sub'] ); ?></span></div>
								</div>
								<?php endif; ?>
								<?php if ( $s['coords'] ) : ?>
								<div class="absolute bottom-4 left-4 bg-surface-dark/85 backdrop-blur-md text-on-tertiary px-3.5 py-2 rounded-xl text-body-sm font-body-sm flex items-center gap-2 pointer-events-none" dir="ltr"><i class="bi bi-geo-fill text-[14px] text-accent-emerald" aria-hidden="true"></i><span><?php echo esc_html( $s['coords'] ); ?></span></div>
								<?php endif; ?>
							</div>
							<div class="lg:col-span-4 p-space-lg lg:p-space-xl flex flex-col justify-between bg-surface-card">
								<div class="flex flex-col gap-space-md">
									<h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2"><i class="bi bi-car-front text-primary text-[20px]" aria-hidden="true"></i><span><?php echo esc_html( $s['routes_title'] ); ?></span></h3>
									<div class="flex flex-col gap-space-sm text-body-md font-body-md">
										<?php foreach ( $s['routes'] as $r ) : ?>
										<div class="p-space-sm <?php echo 'yes' === $r['highlight'] ? 'bg-secondary-container/40' : 'bg-surface-canvas'; ?> rounded-xl flex items-start gap-2.5">
											<?php echo larijani_icon( $r['icon'], ( 'yes' === $r['highlight'] ? 'text-on-secondary-container' : 'text-primary' ) . ' text-[18px] shrink-0 mt-0.5' ); // phpcs:ignore ?>
											<div><span class="font-bold text-on-surface block"><?php echo esc_html( $r['title'] ); ?></span><span class="text-on-surface-variant text-body-sm"><?php echo esc_html( $r['text'] ); ?></span></div>
										</div>
										<?php endforeach; ?>
									</div>
								</div>
								<?php if ( $s['bottom_text'] ) : ?>
								<div class="pt-space-md"><a class="w-full inline-flex items-center justify-center gap-2 bg-surface-dark hover:bg-tertiary text-on-tertiary py-3 rounded-full font-label-nav text-label-nav transition-colors" <?php echo larijani_link_attrs( $s['bottom_link'] ); // phpcs:ignore ?>><i class="bi bi-headset text-[18px]" aria-hidden="true"></i><span><?php echo esc_html( $s['bottom_text'] ); ?></span></a></div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
