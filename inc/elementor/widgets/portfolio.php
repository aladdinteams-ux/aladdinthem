<?php
/**
 * Widget: Filterable portfolio grid (manual projects or the "نمونه‌کارها" post type).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Portfolio widget.
 */
class Larijani_Widget_Portfolio extends Larijani_Widget_Base {
	/** @return string */
	public function get_name() {
		return 'ls-portfolio';
	}
	/** @return string */
	public function get_title() {
		return __( 'LS نمونه‌کارها با فیلتر', 'larijani-stone' );
	}
	/** @return string */
	public function get_icon() {
		return 'eicon-gallery-masonry';
	}

	/** Controls. */
	protected function register_controls() {
		$this->section( 'sec_source', __( 'پروژه‌ها', 'larijani-stone' ) );
		$this->ctl( 'source', 'select', __( 'منبع', 'larijani-stone' ), 'manual', array( 'options' => array( 'manual' => __( 'دستی', 'larijani-stone' ), 'cpt' => __( 'پست‌تایپ نمونه‌کارها', 'larijani-stone' ) ) ) );
		$this->ctl( 'limit', 'number', __( 'تعداد', 'larijani-stone' ), 9, array( 'condition' => array( 'source' => 'cpt' ) ) );
		$this->ctl( 'show_filters', 'switch', __( 'نوار فیلتر', 'larijani-stone' ), 'yes' );
		$this->ctl( 'all_label', 'text', __( 'عنوان «همه»', 'larijani-stone' ), 'همه پروژه‌ها' );
		$this->ctl( 'count_label', 'text', __( 'متن شمارنده', 'larijani-stone' ), 'نمایش:' );
		$this->ctl( 'count_suffix', 'text', __( 'پسوند شمارنده', 'larijani-stone' ), 'پروژه شاخص' );
		$this->rep(
			'filters',
			__( 'فیلترها (منبع دستی)', 'larijani-stone' ),
			array(
				array( 'key', 'text', __( 'کلید', 'larijani-stone' ), '' ),
				array( 'label', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
			),
			array(
				array( 'key' => 'facade', 'label' => 'نمای مدرن و سنگ سه‌بعدی' ),
				array( 'key' => 'paving', 'label' => 'موزاییک و واش‌بتن' ),
				array( 'key' => 'landscape', 'label' => 'جدول، دورباغچه و ویلایی' ),
				array( 'key' => 'industrial', 'label' => 'خطوط تولید و کارخانجات' ),
			),
			'{{{ label }}}',
			array( 'condition' => array( 'source' => 'manual' ) )
		);
		$this->rep(
			'items',
			__( 'پروژه‌ها', 'larijani-stone' ),
			array(
				array( 'image', 'media', __( 'تصویر', 'larijani-stone' ), '' ),
				array( 'filter', 'text', __( 'کلید فیلتر', 'larijani-stone' ), '' ),
				array( 'badge_1', 'text', __( 'برچسب ۱', 'larijani-stone' ), '' ),
				array( 'badge_2', 'text', __( 'برچسب ۲', 'larijani-stone' ), '' ),
				array( 'badge_2_tone', 'select', __( 'رنگ برچسب ۲', 'larijani-stone' ), 'dark', array( 'options' => array( 'dark' => __( 'تیره', 'larijani-stone' ), 'sage' => __( 'سبز ملایم', 'larijani-stone' ), 'primary' => __( 'سبز برند', 'larijani-stone' ), 'fixed' => __( 'سبز روشن', 'larijani-stone' ) ) ) ),
				array( 'location', 'text', __( 'محل اجرا', 'larijani-stone' ), '' ),
				array( 'code', 'text', __( 'کد / ظرفیت', 'larijani-stone' ), '' ),
				array( 'title', 'text', __( 'عنوان', 'larijani-stone' ), '' ),
				array( 'desc', 'textarea', __( 'توضیح', 'larijani-stone' ), '' ),
				array( 'spec1_label', 'text', __( 'مشخصه ۱ – عنوان', 'larijani-stone' ), '' ),
				array( 'spec1_value', 'text', __( 'مشخصه ۱ – مقدار', 'larijani-stone' ), '' ),
				array( 'spec2_label', 'text', __( 'مشخصه ۲ – عنوان', 'larijani-stone' ), '' ),
				array( 'spec2_value', 'text', __( 'مشخصه ۲ – مقدار', 'larijani-stone' ), '' ),
				array( 'spec3_label', 'text', __( 'مشخصه ۳ – عنوان', 'larijani-stone' ), '' ),
				array( 'spec3_value', 'text', __( 'مشخصه ۳ – مقدار', 'larijani-stone' ), '' ),
				array( 'note', 'text', __( 'یادداشت پایین', 'larijani-stone' ), '' ),
				array( 'note_icon', 'icon', __( 'آیکون یادداشت', 'larijani-stone' ), 'patch-check-fill' ),
				array( 'note_tone', 'select', __( 'رنگ یادداشت', 'larijani-stone' ), 'emerald', array( 'options' => array( 'emerald' => __( 'سبز', 'larijani-stone' ), 'muted' => __( 'خاکستری', 'larijani-stone' ) ) ) ),
				array( 'link', 'url', __( 'لینک', 'larijani-stone' ), '' ),
			),
			larijani_demo_projects(),
			'{{{ title }}}',
			array( 'condition' => array( 'source' => 'manual' ) )
		);
		$this->columns_controls( 3, 2, 1, 4 );
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
		$cards   = array();
		$filters = array( array( 'all', $s['all_label'] ) );
		if ( 'cpt' === $s['source'] ) {
			$posts = get_posts( array( 'post_type' => 'ls_project', 'posts_per_page' => (int) $s['limit'] ) );
			foreach ( $posts as $p ) {
				$cards[] = larijani_project_data( $p );
			}
			$terms = get_terms( array( 'taxonomy' => 'ls_project_cat', 'hide_empty' => true ) );
			foreach ( is_wp_error( $terms ) ? array() : $terms as $t ) {
				$filters[] = array( $t->slug, $t->name );
			}
		} else {
			foreach ( $s['items'] as $it ) {
				$specs = array();
				for ( $i = 1; $i <= 3; $i++ ) {
					if ( $it[ "spec{$i}_label" ] || $it[ "spec{$i}_value" ] ) {
						$specs[] = array( $it[ "spec{$i}_label" ], $it[ "spec{$i}_value" ] );
					}
				}
				$cards[] = array_merge( $it, array( 'specs' => $specs, 'url' => $it['link']['url'] ?? '' ) );
			}
			foreach ( $s['filters'] as $f ) {
				$filters[] = array( $f['key'], $f['label'] );
			}
		}
		$cols = array( 1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4' );
		?>
		<section class="w-full pb-space-2xl <?php echo esc_attr( larijani_section_bg( $s['section_bg'] ) ); ?>" data-ls-filter-scope data-ls-count-suffix="<?php echo esc_attr( $s['count_suffix'] ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<?php if ( $this->on( $s, 'show_filters' ) && count( $filters ) > 1 ) : ?>
				<div class="flex flex-col sm:flex-row items-center justify-between gap-space-md bg-surface-card p-space-sm rounded-2xl shadow-sm mb-space-xl">
					<div class="flex items-center gap-space-xs overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 scrollbar-none"><?php echo larijani_filter_buttons( $filters, 'pill' ); // phpcs:ignore ?></div>
					<div class="flex items-center gap-space-xs text-body-sm font-body-sm text-outline shrink-0 pr-2">
						<span><?php echo esc_html( $s['count_label'] ); ?></span>
						<span class="text-surface-dark font-bold font-label-nav" data-ls-filter-count><?php echo esc_html( larijani_fa_num( count( $cards ) ) . ' ' . $s['count_suffix'] ); ?></span>
					</div>
				</div>
				<?php endif; ?>
				<div class="grid grid-cols-1 md:grid-cols-2 <?php echo esc_attr( $cols[ (int) $s['columns'] ] ?? 'lg:grid-cols-3' ); ?> gap-space-lg">
					<?php foreach ( $cards as $c ) : ?>
						<?php echo larijani_project_card( $c ); // phpcs:ignore ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
