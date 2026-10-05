<?php
/**
 * Widget: Category tiles (manual or WooCommerce product categories).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Categories widget.
 */
class LS_Widget_Categories extends LS_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-categories';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS دسته‌بندی‌های اصلی', 'larijani' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_heading', __( 'عنوان بخش', 'larijani' ) );
		$this->heading_controls(
			array(
				'title'     => 'دسته‌بندی‌های اصلی لاریجانی استون',
				'desc'      => 'تجهیزات صنعتی و قالب‌های تخصصی بتن پلیمری و سنگ مصنوعی',
				'link_text' => 'مشاهده همه دسته‌ها',
			)
		);
		$this->end();

		$this->section( 'sec_items', __( 'دسته‌ها', 'larijani' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani' ), 'manual', array( 'options' => array( 'manual' => __( 'دستی', 'larijani' ), 'product_cat' => __( 'دسته‌های محصول ووکامرس', 'larijani' ) ) ) );
		$this->ctl( 'limit', 'number', __( 'تعداد دسته‌ها', 'larijani' ), 6, array( 'condition' => array( 'source' => 'product_cat' ) ) );
		$this->rep(
			'items',
			__( 'دسته‌ها', 'larijani' ),
			array(
				array( 'icon', 'icon', __( 'آیکون', 'larijani' ), 'grid-3x3-gap' ),
				array( 'title', 'text', __( 'عنوان', 'larijani' ), '' ),
				array( 'sub', 'text', __( 'زیرعنوان', 'larijani' ), '' ),
				array( 'link', 'url', __( 'لینک', 'larijani' ), '#' ),
			),
			array(
				array( 'icon' => 'grid-3x3-gap', 'title' => 'قالب کفپوش و واش‌بتن', 'sub' => 'بیش از ۱۲۰ طرح' ),
				array( 'icon' => 'bank', 'title' => 'قالب نما و صراحی', 'sub' => 'بیش از ۸۰ مدل' ),
				array( 'icon' => 'bricks', 'title' => 'جدول و دورباغچه', 'sub' => 'طرح‌های مدرن و سنتی' ),
				array( 'icon' => 'activity', 'title' => 'میزهای ویبره صنعتی', 'sub' => 'تک و دو موتوره' ),
				array( 'icon' => 'arrow-repeat', 'title' => 'میکسر و ناریساز بتن', 'sub' => 'ظرفیت ۳۰۰ الی ۸۰۰ کیلو' ),
				array( 'icon' => 'droplet-half', 'title' => 'رزین و رنگدانه‌ها', 'sub' => 'پلی‌کربوکسیلات و اکسید' ),
			),
			'{{{ title }}}',
			array( 'condition' => array( 'source' => 'manual' ) )
		);
		$this->columns_controls( 6, 3, 2, 6 );
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
		$items = array();
		if ( 'product_cat' === $s['source'] && taxonomy_exists( 'product_cat' ) ) {
			$terms = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => false, 'number' => (int) $s['limit'], 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
			foreach ( is_wp_error( $terms ) ? array() : $terms as $t ) {
				$icon    = get_term_meta( $t->term_id, 'ls_icon', true );
				$items[] = array(
					'icon'  => $icon ? $icon : 'bi bi-grid-3x3-gap',
					'title' => $t->name,
					/* translators: %s count */
					'sub'   => sprintf( __( '%s محصول', 'larijani' ), ls_fa_num( $t->count ) ),
					'link'  => get_term_link( $t ),
				);
			}
		} else {
			$items = $s['items'];
		}
		?>
		<section class="py-16 sm:py-20 <?php echo esc_attr( ls_section_bg( $s['section_bg'] ) ); ?>">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<?php echo $this->heading( $s ); // phpcs:ignore ?>
				<div class="grid <?php echo esc_attr( ls_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) ); ?> gap-3 sm:gap-4">
					<?php foreach ( $items as $it ) : ?>
					<a class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 hover:border-primary-container hover:shadow-soft transition text-center flex flex-col items-center group" <?php echo ls_link_attrs( ( empty( $it['link']['url'] ) || '#' === $it['link']['url'] ) ? $this->heading_fallback_url() : $it['link'] ); // phpcs:ignore ?>>
						<div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-surface-canvas group-hover:bg-primary-container text-primary-container group-hover:text-white flex items-center justify-center mb-3 transition text-2xl"><?php echo ls_icon( $it['icon'] ); // phpcs:ignore ?></div>
						<h3 class="font-bold text-gray-900 text-xs sm:text-sm group-hover:text-primary-container transition"><?php echo esc_html( $it['title'] ); ?></h3>
						<?php if ( $it['sub'] ) : ?><span class="text-[11px] sm:text-xs text-gray-400 mt-1 font-medium"><?php echo esc_html( $it['sub'] ); ?></span><?php endif; ?>
					</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Section-heading link fallback.
	 *
	 * @return string
	 */
	protected function heading_fallback_url() {
		return ls_page_url( 'shop', function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' ) );
	}
}
