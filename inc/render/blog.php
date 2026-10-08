<?php
/**
 * Blog renderers: archive hero, featured post, posts grid, sidebars, single post parts.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Blog index URL.
 *
 * @return string
 */
function larijani_blog_url() {
	$id = (int) get_option( 'page_for_posts' );
	return $id ? get_permalink( $id ) : home_url( '/' );
}

/**
 * Archive hero (title + search + category chips).
 *
 * @param array $s Settings.
 */
function larijani_render_blog_hero( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'badge'          => __( 'مرجع مهندسی سنگ مصنوعی و افزودنی‌های بتن', 'larijani-stone' ),
			'title'          => '',
			'desc'           => '',
			'show_search'    => 'yes',
			'search_placeholder' => __( 'جستجو در بین مقالات، فرمول‌ها، عیوب بتن، رزین LS و تجهیزات...', 'larijani-stone' ),
			'search_button'  => __( 'جستجو', 'larijani-stone' ),
			'show_cats'      => 'yes',
			'all_label'      => __( 'همه مقالات', 'larijani-stone' ),
			'cats_limit'     => 6,
			'auto_title'     => 'yes',
		)
	);
	$title = $s['title'];
	$desc  = $s['desc'];
	if ( 'yes' === $s['auto_title'] && ( is_category() || is_tag() || is_tax() || is_author() || is_date() ) ) {
		$title = wp_strip_all_tags( get_the_archive_title() );
		$desc  = get_the_archive_description() ? wp_strip_all_tags( get_the_archive_description() ) : $desc;
	} elseif ( 'yes' === $s['auto_title'] && is_search() ) {
		/* translators: %s query */
		$title = sprintf( __( 'نتایج جستجو برای «%s»', 'larijani-stone' ), get_search_query() );
	}
	if ( ! $title ) {
		$title = __( 'آرشیو جامع مقالات، دانشنامه و راهنمای فنی سنگ مصنوعی و بتن پلیمری', 'larijani-stone' );
	}
	$current_cat = is_category() ? get_queried_object_id() : 0;
	$cats        = get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => (int) $s['cats_limit'], 'hide_empty' => true ) );
	?>
	<section class="relative w-full bg-surface-canvas overflow-hidden py-12 lg:py-16">
		<div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-primary-container/10 blur-3xl pointer-events-none"></div>
		<div class="absolute -bottom-24 right-0 w-80 h-80 rounded-full bg-secondary-container/20 blur-2xl pointer-events-none"></div>
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
			<div class="flex flex-col items-center text-center max-w-4xl mx-auto">
				<?php if ( $s['badge'] ) : ?>
				<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white shadow-sm mb-6">
					<span class="w-2 h-2 rounded-full bg-accent-emerald animate-pulse"></span>
					<span class="font-label-badge text-label-badge text-primary uppercase tracking-wider"><?php echo esc_html( $s['badge'] ); ?></span>
				</div>
				<?php endif; ?>
				<h1 class="font-headline-lg text-headline-lg sm:text-[2.25rem] text-on-surface leading-tight mb-5"><?php echo larijani_kses( $title ); // phpcs:ignore ?></h1>
				<?php if ( $desc ) : ?>
				<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl leading-relaxed mb-8"><?php echo esc_html( $desc ); ?></p>
				<?php endif; ?>
				<?php if ( 'yes' === $s['show_search'] ) : ?>
				<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="w-full max-w-2xl bg-surface-card rounded-2xl shadow-sm p-2 flex flex-col sm:flex-row items-center gap-2 mb-8">
					<input type="hidden" name="post_type" value="post">
					<label class="flex items-center gap-3 w-full px-3 py-2 flex-1">
						<i class="bi bi-search text-outline text-lg" aria-hidden="true"></i>
						<span class="screen-reader-text"><?php esc_html_e( 'جستجو', 'larijani-stone' ); ?></span>
						<input class="w-full bg-transparent border-0 p-0 focus:ring-0 text-on-surface placeholder:text-outline font-body-md text-body-md" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" type="search">
					</label>
					<button class="w-full sm:w-auto px-6 py-3 rounded-xl bg-primary-container text-on-primary font-headline-sm text-headline-sm hover:bg-primary transition-all flex items-center justify-center gap-2 shadow-sm flex-shrink-0" type="submit">
						<span><?php echo esc_html( $s['search_button'] ); ?></span><i class="bi bi-arrow-left" aria-hidden="true"></i>
					</button>
				</form>
				<?php endif; ?>
				<?php if ( 'yes' === $s['show_cats'] && $cats ) : ?>
				<div class="w-full flex items-center justify-center gap-2 flex-wrap">
					<a href="<?php echo esc_url( larijani_blog_url() ); ?>" class="px-4 py-2 rounded-full <?php echo $current_cat ? 'bg-surface-card text-on-surface-variant hover:text-on-surface hover:bg-white font-label-nav text-label-nav' : 'bg-primary-container text-on-primary font-headline-sm text-headline-sm'; ?> shadow-sm transition-all"><?php echo esc_html( $s['all_label'] ); ?></a>
					<?php foreach ( $cats as $cat ) : ?>
					<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="px-4 py-2 rounded-full <?php echo $current_cat === $cat->term_id ? 'bg-primary-container text-on-primary font-headline-sm text-headline-sm' : 'bg-surface-card text-on-surface-variant hover:text-on-surface hover:bg-white font-label-nav text-label-nav'; ?> shadow-sm transition-all"><?php echo esc_html( $cat->name ); ?></a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Find the featured post.
 *
 * @param int $post_id Explicit post id.
 * @return WP_Post|null
 */
function larijani_get_featured_post( $post_id = 0 ) {
	if ( $post_id ) {
		return get_post( $post_id );
	}
	$q = get_posts(
		array(
			'posts_per_page' => 1,
			'meta_key'       => '_ls_featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	if ( ! $q ) {
		$sticky = get_option( 'sticky_posts' );
		$q      = $sticky ? get_posts( array( 'post__in' => $sticky, 'posts_per_page' => 1 ) ) : get_posts( array( 'posts_per_page' => 1 ) );
	}
	return $q ? $q[0] : null;
}

/**
 * Featured post card.
 *
 * @param array $s Settings.
 */
function larijani_render_featured_post( $s = array() ) {
	$s    = wp_parse_args(
		$s,
		array(
			'post_id'       => 0,
			'badge'         => __( 'مقاله ویژه تحریریه', 'larijani-stone' ),
			'metrics'       => array(),
			'author_role'   => '',
			'button_text'   => __( 'مطالعه مقاله کامل', 'larijani-stone' ),
			'fallback'      => array(),
		)
	);
	$post = larijani_get_featured_post( (int) $s['post_id'] );
	if ( $post ) {
		$d      = larijani_post_data( $post );
		$image  = larijani_post_image_url( $post, 'large' );
		$author = get_post_meta( $post->ID, '_ls_author_name', true );
		$author = $author ? $author : get_the_author_meta( 'display_name', $post->post_author );
		$role   = $s['author_role'] ? $s['author_role'] : (string) get_post_meta( $post->ID, '_ls_author_role', true );
		$role   = $role ? $role : get_the_author_meta( 'description', $post->post_author );
		$views  = larijani_post_views( $post->ID );
	} elseif ( $s['fallback'] ) {
		$d      = $s['fallback'];
		$image  = $d['image'];
		$author = $d['author'];
		$role   = $d['author_role'];
		$views  = $d['views'] ?? 0;
	} else {
		return;
	}
	$role = wp_trim_words( $role, 8 );
	?>
	<div class="bg-surface-card rounded-3xl shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-0">
		<a href="<?php echo esc_url( $d['url'] ); ?>" class="lg:col-span-7 relative min-h-[300px] lg:min-h-[460px] overflow-hidden group block">
			<div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url('<?php echo esc_url( $image ? $image : LARIJANI_URI . '/assets/images/placeholder.svg' ); ?>')"></div>
			<div class="absolute inset-0 bg-gradient-to-t from-surface-dark/80 via-surface-dark/20 to-transparent lg:hidden"></div>
			<div class="absolute top-4 right-4 flex items-center gap-2">
				<?php if ( $s['badge'] ) : ?>
				<span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-accent-amber text-white font-label-badge text-label-badge shadow-md"><i class="bi bi-star-fill text-[10px]" aria-hidden="true"></i><?php echo esc_html( $s['badge'] ); ?></span>
				<?php endif; ?>
				<?php if ( $d['category'] ) : ?>
				<span class="px-3 py-1.5 rounded-full bg-surface-dark/70 backdrop-blur-md text-white font-label-badge text-label-badge"><?php echo esc_html( $d['category'] ); ?></span>
				<?php endif; ?>
			</div>
		</a>
		<div class="lg:col-span-5 p-6 sm:p-8 lg:p-10 flex flex-col justify-between bg-surface-card">
			<div>
				<div class="flex items-center flex-wrap gap-3 text-outline text-xs mb-4">
					<span class="flex items-center gap-1"><i class="bi bi-calendar3" aria-hidden="true"></i><?php echo esc_html( $d['date'] ); ?></span>
					<span>•</span>
					<span class="flex items-center gap-1"><i class="bi bi-clock" aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %s minutes */ __( '%s دقیقه مطالعه', 'larijani-stone' ), larijani_fa_num( $d['reading'] ) ) ); ?></span>
					<?php if ( $views && larijani_opt( 'blog_show_views' ) ) : ?>
					<span>•</span>
					<span class="flex items-center gap-1"><i class="bi bi-eye" aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %s views */ __( '%s بازدید', 'larijani-stone' ), larijani_fa_number_format( $views ) ) ); ?></span>
					<?php endif; ?>
				</div>
				<h2 class="font-headline-md text-headline-md text-on-surface leading-snug mb-4 hover:text-primary transition-colors"><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['title'] ); ?></a></h2>
				<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-6"><?php echo esc_html( $d['excerpt'] ); ?></p>
				<?php if ( $s['metrics'] ) : ?>
				<div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-surface-canvas mb-6">
					<?php foreach ( array_slice( $s['metrics'], 0, 4 ) as $m ) : ?>
					<div class="flex flex-col">
						<span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $m['label'] ?? '' ); ?></span>
						<div class="flex items-baseline gap-1 mt-0.5">
							<span class="font-headline-md text-headline-md <?php echo esc_attr( larijani_tone( $m['tone'] ?? 'primary', 'text' ) ); ?> font-bold" dir="ltr"><?php echo esc_html( $m['value'] ?? '' ); ?></span>
							<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $m['unit'] ?? '' ); ?></span>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
			<div class="flex items-center justify-between gap-3 pt-4 flex-wrap">
				<div class="flex items-center gap-3">
					<div class="w-10 h-10 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm"><?php echo esc_html( larijani_initials( $author ) ); ?></div>
					<div class="flex flex-col">
						<span class="text-xs font-bold text-on-surface"><?php echo esc_html( $author ); ?></span>
						<?php if ( $role ) : ?><span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $role ); ?></span><?php endif; ?>
					</div>
				</div>
				<a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-container text-on-primary font-headline-sm text-headline-sm hover:bg-primary transition-all shadow-sm" href="<?php echo esc_url( $d['url'] ); ?>">
					<span><?php echo esc_html( $s['button_text'] ); ?></span><i class="bi bi-arrow-left" aria-hidden="true"></i>
				</a>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Posts grid (main query or custom query).
 *
 * @param array $s Settings.
 */
function larijani_render_posts_grid( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'source'          => 'custom', // custom | current | related.
			'posts_per_page'  => 6,
			'category'        => '',
			'orderby'         => 'date',
			'offset'          => 0,
			'exclude_featured' => '',
			'card_style'      => 'archive',
			'columns'         => 2,
			'columns_tablet'  => 2,
			'columns_mobile'  => 1,
			'bar_title'       => '',
			'show_count'      => 'yes',
			'pagination'      => 'yes',
			'read_more'       => __( 'ادامه مطلب تخصصی', 'larijani-stone' ),
			'empty_text'      => __( 'مطلبی یافت نشد.', 'larijani-stone' ),
			'gap'             => 'gap-6',
		)
	);
	$own_query = 'current' !== $s['source'];
	if ( $own_query ) {
		$args = larijani_posts_query_args(
			array(
				'posts_per_page'  => $s['posts_per_page'],
				'category'        => $s['category'],
				'orderby'         => $s['orderby'],
				'offset'          => $s['offset'],
				'exclude_current' => true,
				'related'         => 'related' === $s['source'],
			)
		);
		if ( 'yes' === $s['pagination'] && ! $s['offset'] ) {
			$args['paged'] = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		}
		if ( 'yes' === $s['exclude_featured'] ) {
			$featured = larijani_get_featured_post();
			if ( $featured ) {
				$args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), array( $featured->ID ) );
			}
		}
		$query = new WP_Query( $args );
	} else {
		global $wp_query;
		$query = $wp_query;
	}

	if ( $s['bar_title'] ) :
		$total = (int) $query->found_posts;
		$from  = $total ? ( ( max( 1, (int) $query->get( 'paged' ) ) - 1 ) * (int) $query->get( 'posts_per_page' ) + 1 ) : 0;
		$to    = min( $total, $from + (int) $query->post_count - 1 );
		?>
		<div class="flex items-center justify-between flex-wrap gap-2 pb-2 mb-6">
			<div class="flex items-center gap-2.5">
				<div class="w-2.5 h-6 rounded-full bg-primary-container"></div>
				<h3 class="font-headline-md text-headline-md text-on-surface"><?php echo esc_html( $s['bar_title'] ); ?></h3>
			</div>
			<?php if ( 'yes' === $s['show_count'] && $total ) : ?>
			<span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( sprintf( /* translators: 1 from 2 to 3 total */ __( 'نمایش %1$s تا %2$s از %3$s نوشتار تخصصی', 'larijani-stone' ), larijani_fa_num( $from ), larijani_fa_num( $to ), larijani_fa_num( $total ) ) ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	endif;

	if ( $query->have_posts() ) {
		echo '<div class="grid ' . esc_attr( larijani_grid_cols( $s['columns'], $s['columns_tablet'], $s['columns_mobile'] ) . ' ' . $s['gap'] ) . '">';
		while ( $query->have_posts() ) {
			$query->the_post();
			echo larijani_post_card( larijani_post_data( get_post() ), $s['card_style'], array( 'read_more' => $s['read_more'] ) ); // phpcs:ignore
		}
		echo '</div>';
		if ( 'yes' === $s['pagination'] ) {
			echo '<div class="pt-8">' . larijani_pagination( $query ) . '</div>'; // phpcs:ignore
		}
	} else {
		echo '<div class="bg-surface-card rounded-2xl p-8 text-center text-on-surface-variant shadow-sm">' . esc_html( $s['empty_text'] ) . '</div>';
	}
	if ( $own_query ) {
		wp_reset_postdata();
	}
}

/* -------------------------------------------------------------------------
 * Sidebar blocks.
 * ---------------------------------------------------------------------- */

/**
 * Dark download card.
 *
 * @param array $s Settings.
 */
function larijani_render_download_card( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'icon'   => 'bi bi-file-earmark-pdf-fill',
			'badge'  => __( 'ویرایش زمستان ۱۴۰۴', 'larijani-stone' ),
			'title'  => __( 'هندبوک جامع جداول اختلاط بتن سمنت‌پلاست', 'larijani-stone' ),
			'desc'   => __( 'شامل ۱۲ فرمول آزمون‌شده آزمایشگاهی بر اساس نوع سیمان، فصول سرد و گرم، و جداول عیار پیگمنت‌های معدنی اکسید آهن.', 'larijani-stone' ),
			'button' => __( 'دانلود مستقیم فایل PDF (۱۴ مگابایت)', 'larijani-stone' ),
			'link'   => '#',
		)
	);
	?>
	<div class="relative bg-surface-dark text-white rounded-3xl p-6 shadow-md overflow-hidden">
		<div class="absolute -top-12 -left-12 w-40 h-40 rounded-full bg-primary-container/30 blur-2xl"></div>
		<div class="relative z-10">
			<div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-2xl mb-4"><?php echo larijani_icon( $s['icon'], 'text-accent-amber' ); // phpcs:ignore ?></div>
			<?php if ( $s['badge'] ) : ?><span class="px-2.5 py-1 rounded-full bg-white/10 text-white/80 font-label-badge text-label-badge inline-block mb-2"><?php echo esc_html( $s['badge'] ); ?></span><?php endif; ?>
			<h2 class="font-headline-sm text-headline-sm text-white mb-2 leading-snug"><?php echo esc_html( $s['title'] ); ?></h2>
			<p class="font-body-sm text-body-sm text-slate-300 leading-relaxed mb-5"><?php echo esc_html( $s['desc'] ); ?></p>
			<a class="w-full py-3 rounded-xl bg-primary-container hover:bg-primary text-white font-headline-sm text-headline-sm transition-all flex items-center justify-center gap-2 shadow-sm" <?php echo larijani_link_attrs( $s['link'] ); // phpcs:ignore ?>>
				<i class="bi bi-cloud-arrow-down-fill" aria-hidden="true"></i><span><?php echo esc_html( $s['button'] ); ?></span>
			</a>
		</div>
	</div>
	<?php
}

/**
 * Numbered popular posts list.
 *
 * @param array $s Settings { title, icon, count, orderby, style (card|compact), suffix }.
 */
function larijani_render_popular_posts( $s = array() ) {
	$s     = wp_parse_args(
		$s,
		array(
			'title'   => __( 'مباحث پرطرفدار کارگاه‌ها', 'larijani-stone' ),
			'icon'    => 'bi bi-fire',
			'count'   => 4,
			'orderby' => 'views',
			'style'   => 'card',
			'items'   => array(),
		)
	);
	$items = array();
	if ( $s['items'] ) {
		$items = $s['items'];
	} else {
		$posts = get_posts( larijani_posts_query_args( array( 'posts_per_page' => $s['count'], 'orderby' => $s['orderby'] ) ) );
		if ( ! $posts && 'views' === $s['orderby'] ) {
			$posts = get_posts( array( 'posts_per_page' => $s['count'] ) );
		}
		foreach ( $posts as $p ) {
			$v       = larijani_post_views( $p->ID );
			$items[] = array(
				'title' => get_the_title( $p ),
				'url'   => get_permalink( $p ),
				/* translators: %s views */
				'meta'  => $v ? sprintf( __( '%s مطالعه', 'larijani-stone' ), larijani_fa_number_format( $v ) ) : larijani_post_date( $p->ID ),
			);
		}
	}
	if ( ! $items ) {
		return;
	}
	?>
	<div class="bg-surface-card rounded-<?php echo 'compact' === $s['style'] ? '3xl' : '2xl'; ?> p-6 shadow-sm">
		<div class="flex items-center gap-2 mb-4 pb-2">
			<?php echo larijani_icon( $s['icon'], 'text-accent-amber text-lg' ); // phpcs:ignore ?>
			<h2 class="font-headline-sm text-headline-sm text-on-surface font-black"><?php echo esc_html( $s['title'] ); ?></h2>
		</div>
		<div class="flex flex-col gap-<?php echo 'compact' === $s['style'] ? '3' : '4'; ?>">
			<?php foreach ( $items as $i => $it ) : ?>
			<a class="flex items-start gap-3 group" href="<?php echo esc_url( is_array( $it['url'] ) ? ( $it['url']['url'] ?? '#' ) : $it['url'] ); ?>">
				<span class="<?php echo 'compact' === $s['style'] ? 'w-6 h-6' : 'w-7 h-7'; ?> rounded-lg bg-surface-canvas text-primary font-bold flex items-center justify-center flex-shrink-0 text-sm group-hover:bg-primary-container group-hover:text-white transition-colors"><?php echo esc_html( larijani_fa_num( $i + 1 ) ); ?></span>
				<div class="flex flex-col">
					<span class="<?php echo 'compact' === $s['style'] ? 'font-body-sm text-body-sm text-on-surface' : 'font-body-md text-body-md text-on-surface font-semibold'; ?> group-hover:text-primary transition-colors line-clamp-2"><?php echo esc_html( $it['title'] ); ?></span>
					<?php if ( ! empty( $it['meta'] ) && 'compact' !== $s['style'] ) : ?><span class="font-body-sm text-body-sm text-outline mt-1"><?php echo esc_html( $it['meta'] ); ?></span><?php endif; ?>
				</div>
			</a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Newsletter / SMS mini form.
 *
 * @param array $s Settings.
 */
function larijani_render_newsletter( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'title'       => __( 'پیامک و خبرنامه عیب‌یابی فرمولاسیون', 'larijani-stone' ),
			'icon'        => 'bi bi-bell-fill',
			'desc'        => __( 'نکات هفتگی حل مسائل کارگاهی (ترک‌خوردگی، چسبیدن به قالب، دیرگیر شدن بتن در سرما) مستقیماً به موبایل شما ارسال می‌شود.', 'larijani-stone' ),
			'placeholder' => __( 'شماره تماس همراه (مثال: ۰۹۱۲۳۴۵۶۷۸۹)', 'larijani-stone' ),
			'button'      => __( 'عضویت رایگان در شبکه کارگاهی', 'larijani-stone' ),
			'note'        => __( 'بدون ارسال پیام‌های تبلیغاتی تکراری', 'larijani-stone' ),
			'success'     => __( 'شماره شما با موفقیت برای دریافت پیامک‌های فنی ثبت شد.', 'larijani-stone' ),
		)
	);
	?>
	<div class="bg-surface-card rounded-2xl p-6 shadow-sm">
		<div class="flex items-center gap-2 mb-2"><?php echo larijani_icon( $s['icon'], 'text-primary text-base' ); // phpcs:ignore ?><h2 class="font-headline-sm text-headline-sm text-on-surface"><?php echo esc_html( $s['title'] ); ?></h2></div>
		<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed mb-4"><?php echo esc_html( $s['desc'] ); ?></p>
		<form class="flex flex-col gap-3" data-ls-form>
			<?php
			echo larijani_form_hidden_fields( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
				$s['title'],
				array(
					'phone' => array(
						'label'    => __( 'شماره همراه', 'larijani-stone' ),
						'type'     => 'tel',
						'required' => true,
					),
				)
			);
			?>
			<input class="w-full px-4 py-2.5 rounded-xl bg-surface-canvas text-on-surface placeholder:text-outline font-body-md text-body-md focus:bg-white shadow-inner" name="fields[phone]" aria-label="<?php esc_attr_e( 'شماره همراه', 'larijani-stone' ); ?>" placeholder="<?php echo esc_attr( $s['placeholder'] ); ?>" required type="tel">
			<button class="w-full py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white font-headline-sm text-headline-sm transition-all shadow-sm" type="submit"><?php echo esc_html( $s['button'] ); ?></button>
			<div class="hidden text-center font-body-sm text-body-sm text-accent-emerald" data-ls-success><?php echo esc_html( $s['success'] ); ?></div>
			<div class="hidden ls-form-error text-center" data-ls-error></div>
		</form>
		<?php if ( $s['note'] ) : ?><span class="text-[11px] text-outline text-center block mt-2"><?php echo esc_html( $s['note'] ); ?></span><?php endif; ?>
	</div>
	<?php
}

/**
 * Tag cloud.
 *
 * @param array $s Settings { title, count, taxonomy }.
 */
function larijani_render_tag_cloud( $s = array() ) {
	$s    = wp_parse_args( $s, array( 'title' => __( 'کلیدواژه‌های فنی', 'larijani-stone' ), 'count' => 12, 'taxonomy' => 'post_tag', 'items' => array() ) );
	$tags = $s['items'];
	if ( ! $tags ) {
		$terms = get_terms( array( 'taxonomy' => $s['taxonomy'], 'orderby' => 'count', 'order' => 'DESC', 'number' => (int) $s['count'] ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$tags[] = array( 'title' => $t->name, 'url' => get_term_link( $t ) );
			}
		}
	}
	if ( ! $tags ) {
		return;
	}
	?>
	<div class="bg-surface-card rounded-2xl p-6 shadow-sm">
		<div class="flex items-center gap-2 mb-4 pb-2"><i class="bi bi-tags-fill text-outline text-base" aria-hidden="true"></i><h2 class="font-headline-sm text-headline-sm text-on-surface"><?php echo esc_html( $s['title'] ); ?></h2></div>
		<div class="flex flex-wrap gap-2">
			<?php foreach ( $tags as $t ) : ?>
			<a class="px-3 py-1.5 rounded-lg bg-surface-canvas hover:bg-primary-container hover:text-white text-on-surface-variant text-xs font-medium transition-all" href="<?php echo esc_url( is_array( $t['url'] ) ? ( $t['url']['url'] ?? '#' ) : $t['url'] ); ?>"><?php echo esc_html( $t['title'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Table of contents (filled client side from the article headings).
 *
 * @param array $s Settings.
 */
function larijani_render_toc( $s = array() ) {
	$s = wp_parse_args( $s, array( 'title' => __( 'فهرست عناوین مقاله', 'larijani-stone' ), 'selector' => '.ls-article h2', 'items' => array() ) );
	?>
	<div class="bg-surface-card rounded-3xl p-6 shadow-sm space-y-4" data-ls-toc="<?php echo esc_attr( $s['selector'] ); ?>">
		<div class="flex items-center gap-2 text-surface-dark"><i class="bi bi-book text-[20px] text-primary-container" aria-hidden="true"></i><h3 class="font-headline-sm text-headline-sm font-black"><?php echo esc_html( $s['title'] ); ?></h3></div>
		<nav class="space-y-2 font-body-md text-body-md" data-ls-toc-list>
			<?php foreach ( $s['items'] as $it ) : ?>
			<a class="flex items-center gap-2 p-2 rounded-xl text-on-surface-variant hover:text-surface-dark hover:bg-surface-canvas transition-colors" href="<?php echo esc_url( '#' . ltrim( $it['anchor'] ?? '', '#' ) ); ?>"><span class="w-1.5 h-1.5 rounded-full bg-primary-container shrink-0"></span><span><?php echo esc_html( $it['title'] ?? '' ); ?></span></a>
			<?php endforeach; ?>
		</nav>
	</div>
	<?php
}

/**
 * Resin dosage calculator.
 *
 * @param array $s Settings.
 */
function larijani_render_resin_calculator( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'title'        => __( 'محاسبه‌گر مصرف رزین', 'larijani-stone' ),
			'badge'        => __( 'فرمول LS-500', 'larijani-stone' ),
			'desc'         => __( 'وزن سیمان مصرفی در هر بچ اختلاط میکسر را وارد کنید:', 'larijani-stone' ),
			'input_label'  => __( 'سیمان مصرفی در میکسر:', 'larijani-stone' ),
			'min'          => 50,
			'max'          => 600,
			'step'         => 25,
			'value'        => 100,
			'unit'         => __( 'کیلوگرم', 'larijani-stone' ),
			'out1_label'   => __( 'رزین پیشنهادی:', 'larijani-stone' ),
			'out1_factor'  => 0.009,
			'out1_unit'    => __( 'کیلوگرم', 'larijani-stone' ),
			'out2_label'   => __( 'حداکثر آب مجاز:', 'larijani-stone' ),
			'out2_factor'  => 0.28,
			'out2_unit'    => __( 'لیتر', 'larijani-stone' ),
		)
	);
	?>
	<div class="bg-surface-card rounded-3xl p-6 shadow-sm space-y-4" data-ls-calc data-f1="<?php echo esc_attr( (float) $s['out1_factor'] ); ?>" data-f2="<?php echo esc_attr( (float) $s['out2_factor'] ); ?>" data-unit="<?php echo esc_attr( $s['unit'] ); ?>">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-2"><i class="bi bi-calculator text-[20px] text-accent-emerald" aria-hidden="true"></i><h3 class="font-headline-sm text-headline-sm text-surface-dark font-black"><?php echo esc_html( $s['title'] ); ?></h3></div>
			<?php if ( $s['badge'] ) : ?><span class="text-label-badge font-label-badge px-2 py-0.5 rounded bg-secondary-container text-on-secondary-container"><?php echo esc_html( $s['badge'] ); ?></span><?php endif; ?>
		</div>
		<p class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $s['desc'] ); ?></p>
		<div class="space-y-3">
			<div class="space-y-1.5">
				<div class="flex justify-between font-body-sm text-body-sm">
					<span class="text-on-surface-variant"><?php echo esc_html( $s['input_label'] ); ?></span>
					<span class="font-bold text-surface-dark" data-calc-label><?php echo esc_html( larijani_fa_num( $s['value'] ) . ' ' . $s['unit'] ); ?></span>
				</div>
				<input class="w-full cursor-pointer" aria-label="<?php echo esc_attr( $s['label'] ?? __( 'میزان مصرف', 'larijani-stone' ) ); ?>" data-calc-input max="<?php echo esc_attr( $s['max'] ); ?>" min="<?php echo esc_attr( $s['min'] ); ?>" step="<?php echo esc_attr( $s['step'] ); ?>" type="range" value="<?php echo esc_attr( $s['value'] ); ?>">
			</div>
			<div class="grid grid-cols-2 gap-2 pt-2">
				<div class="p-3 rounded-2xl bg-surface-canvas flex flex-col justify-between">
					<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $s['out1_label'] ); ?></span>
					<div class="mt-1 flex items-baseline gap-1"><span class="font-headline-sm text-headline-sm text-accent-emerald font-black" data-calc-out1><?php echo esc_html( larijani_fa_num( round( $s['value'] * $s['out1_factor'], 2 ) ) ); ?></span><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $s['out1_unit'] ); ?></span></div>
				</div>
				<div class="p-3 rounded-2xl bg-surface-canvas flex flex-col justify-between">
					<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $s['out2_label'] ); ?></span>
					<div class="mt-1 flex items-baseline gap-1"><span class="font-headline-sm text-headline-sm text-accent-cobalt font-black" data-calc-out2><?php echo esc_html( larijani_fa_num( round( $s['value'] * $s['out2_factor'], 1 ) ) ); ?></span><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $s['out2_unit'] ); ?></span></div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Gradient support CTA card (sidebar).
 *
 * @param array $s Settings.
 */
function larijani_render_sidebar_cta( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'icon'     => 'bi bi-headset',
			'title'    => __( 'نیاز به اصلاح فرمولاسیون یا رفع حباب در خط تولید دارید؟', 'larijani-stone' ),
			'desc'     => __( 'مشاوره مستقیم با مهندس مسعود لاریجانی و ارسال نمونه رایگان رزین LS-500 برای تست در کارگاه شما.', 'larijani-stone' ),
			'button_1' => '',
			'link_1'   => '',
			'button_2' => __( 'ارسال تصاویر قطعات معیوب در واتساپ', 'larijani-stone' ),
			'link_2'   => '',
		)
	);
	$phone = larijani_opt( 'phone_1' );
	$b1    = $s['button_1'] ? $s['button_1'] : sprintf( /* translators: %s phone */ __( 'تماس مستقیم: %s', 'larijani-stone' ), larijani_fa_num( $phone ) );
	?>
	<div class="bg-gradient-to-br from-surface-dark to-surface-footer rounded-3xl p-6 text-on-tertiary-container shadow-xl space-y-4">
		<div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-primary-fixed text-[26px]"><?php echo larijani_icon( $s['icon'] ); // phpcs:ignore ?></div>
		<div class="space-y-1">
			<h2 class="font-headline-sm text-headline-sm text-white font-black"><?php echo esc_html( $s['title'] ); ?></h2>
			<p class="font-body-sm text-body-sm text-tertiary-fixed leading-relaxed"><?php echo esc_html( $s['desc'] ); ?></p>
		</div>
		<div class="pt-2 flex flex-col gap-2">
			<a class="w-full py-3 px-4 rounded-full bg-primary-container hover:bg-primary text-on-primary font-label-nav text-label-nav text-center flex items-center justify-center gap-2 shadow-lg transition-all" <?php echo larijani_link_attrs( ! empty( $s['link_1']['url'] ) || ( is_string( $s['link_1'] ) && $s['link_1'] ) ? $s['link_1'] : larijani_tel( $phone ) ); // phpcs:ignore ?>><i class="bi bi-telephone" aria-hidden="true"></i><?php echo esc_html( $b1 ); ?></a>
			<?php if ( $s['button_2'] ) : ?>
			<a class="w-full py-2.5 px-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-body-sm text-body-sm text-center flex items-center justify-center gap-2 transition-all" <?php echo larijani_link_attrs( ! empty( $s['link_2']['url'] ) || ( is_string( $s['link_2'] ) && $s['link_2'] ) ? $s['link_2'] : array( 'url' => larijani_whatsapp_url(), 'is_external' => true ) ); // phpcs:ignore ?>><i class="bi bi-whatsapp" aria-hidden="true"></i><?php echo esc_html( $s['button_2'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Soft promo box (sidebar).
 *
 * @param array $s Settings.
 */
function larijani_render_promo_box( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'icon'  => 'bi bi-journal-bookmark',
			'title' => __( 'هندبوک جامع ۳۲ فرمول تست شده', 'larijani-stone' ),
			'desc'  => __( 'شامل کاتالوگ جامع اختلاط برای تولید سنگ پله، جدول، کفپوش‌های پرتردد و سنگ‌های آنتیک دکوراتیو داخلی.', 'larijani-stone' ),
			'link_text' => __( 'درخواست نسخه چاپی یا PDF', 'larijani-stone' ),
			'link'  => '#',
			'tone'  => 'sage',
		)
	);
	$bg = 'light' === $s['tone'] ? 'bg-surface-container-low' : 'bg-secondary-container/60';
	?>
	<div class="<?php echo esc_attr( $bg ); ?> rounded-3xl p-6 shadow-sm space-y-3">
		<div class="flex items-center gap-2 text-on-secondary-container"><?php echo larijani_icon( $s['icon'], 'text-[22px]' ); // phpcs:ignore ?><span class="font-headline-sm text-title-card font-black"><?php echo esc_html( $s['title'] ); ?></span></div>
		<p class="font-body-sm text-body-sm text-on-secondary-container leading-relaxed"><?php echo esc_html( $s['desc'] ); ?></p>
		<?php if ( $s['link_text'] ) : ?>
		<a class="inline-flex items-center gap-1.5 text-primary-container font-label-nav text-label-nav hover:underline pt-1" <?php echo larijani_link_attrs( $s['link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['link_text'] ); ?><i class="bi bi-arrow-left" aria-hidden="true"></i></a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Default archive sidebar.
 */
function larijani_render_archive_sidebar() {
	echo '<div class="sticky top-24 flex flex-col gap-6">';
	larijani_render_download_card();
	larijani_render_popular_posts();
	larijani_render_newsletter();
	larijani_render_tag_cloud();
	if ( is_active_sidebar( 'blog-sidebar' ) ) {
		echo '<div class="ls-widget-area">';
		dynamic_sidebar( 'blog-sidebar' );
		echo '</div>';
	}
	echo '</div>';
}

/* -------------------------------------------------------------------------
 * Single post.
 * ---------------------------------------------------------------------- */

/**
 * Single post hero: pills, title, author bar, share, featured image, metrics.
 *
 * @param array $s Settings.
 */
function larijani_render_post_hero( $s = array() ) {
	$s       = wp_parse_args(
		$s,
		array(
			'show_breadcrumb' => 'yes',
			'show_pills'      => 'yes',
			'show_author'     => 'yes',
			'show_share'      => 'yes',
			'show_image'      => 'yes',
			'author_role'     => '',
			'metrics'         => array(),
			'caption'         => '',
			'image_badge'     => '',
		)
	);
	$post_id = get_the_ID();
	$cat     = larijani_primary_category( $post_id );
	$views   = larijani_post_views( $post_id );
	$author_id = (int) get_post_field( 'post_author', $post_id );
	$author  = get_the_author_meta( 'display_name', $author_id );
	$role    = $s['author_role'] ? $s['author_role'] : wp_trim_words( get_the_author_meta( 'description', $author_id ), 8 );
	$caption = $s['caption'] ? $s['caption'] : get_post_meta( $post_id, '_ls_image_caption', true );
	$ibadge  = $s['image_badge'] ? $s['image_badge'] : get_post_meta( $post_id, '_ls_image_badge', true );
	$share   = larijani_share_links();
	if ( ! $s['metrics'] ) {
		$s['metrics'] = larijani_post_metrics( $post_id );
	}
	$author_meta = get_post_meta( $post_id, '_ls_author_name', true );
	if ( $author_meta ) {
		$author = $author_meta;
		$role   = $s['author_role'] ? $s['author_role'] : (string) get_post_meta( $post_id, '_ls_author_role', true );
	}
	?>
	<?php if ( 'yes' === $s['show_breadcrumb'] ) : ?>
	<section class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-12 pt-6 pb-4">
		<nav class="flex items-center flex-wrap gap-2 text-on-surface-variant font-body-sm text-body-sm" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'larijani-stone' ); ?>"><?php echo larijani_breadcrumb_html(); // phpcs:ignore ?></nav>
	</section>
	<?php endif; ?>
	<section class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-12 py-6">
		<div class="space-y-6">
			<?php if ( 'yes' === $s['show_pills'] ) : ?>
			<div class="flex flex-wrap items-center gap-3">
				<?php if ( $cat ) : ?>
				<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-badge text-label-badge"><i class="bi bi-bookmark-star" aria-hidden="true"></i><?php echo esc_html( $cat->name ); ?></a>
				<?php endif; ?>
				<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-card text-on-surface-variant font-label-badge text-label-badge shadow-sm"><i class="bi bi-stopwatch" aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %s minutes */ __( '%s دقیقه زمان مطالعه', 'larijani-stone' ), larijani_fa_num( larijani_reading_time() ) ) ); ?></span>
				<?php if ( $views && larijani_opt( 'blog_show_views' ) ) : ?>
				<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-card text-on-surface-variant font-label-badge text-label-badge shadow-sm"><i class="bi bi-eye" aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %s views */ __( '%s بازدید تخصصی', 'larijani-stone' ), larijani_fa_number_format( $views ) ) ); ?></span>
				<?php endif; ?>
				<?php if ( comments_open() || get_comments_number() ) : ?>
				<a href="#comments" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-card text-on-surface-variant font-label-badge text-label-badge shadow-sm"><i class="bi bi-chat" aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %s comments */ __( '%s پرسش و پاسخ کارگاهی', 'larijani-stone' ), larijani_fa_num( get_comments_number() ) ) ); ?></a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<h1 class="font-display-hero text-surface-dark font-black leading-tight max-w-5xl text-[22px] sm:text-[28px]"><?php the_title(); ?></h1>
			<?php if ( 'yes' === $s['show_author'] || 'yes' === $s['show_share'] ) : ?>
			<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pt-4 pb-2">
				<?php if ( 'yes' === $s['show_author'] ) : ?>
				<div class="flex items-center gap-3.5">
					<div class="w-13 h-13 rounded-2xl bg-surface-card shadow-sm p-1 flex items-center justify-center shrink-0">
						<?php echo get_avatar( $author_id, 88, '', $author, array( 'class' => 'w-11 h-11 rounded-xl object-cover' ) ); ?>
					</div>
					<div class="flex flex-col">
						<div class="flex items-center gap-2"><span class="font-title-card text-title-card text-surface-dark font-black"><?php echo esc_html( $author ); ?></span><i class="bi bi-patch-check-fill text-[15px] text-accent-emerald" title="<?php esc_attr_e( 'تایید هویت فنی', 'larijani-stone' ); ?>" aria-hidden="true"></i></div>
						<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( trim( $role . ( $role ? ' | ' : '' ) . larijani_post_date() ) ); ?></span>
					</div>
				</div>
				<?php endif; ?>
				<?php if ( 'yes' === $s['show_share'] ) : ?>
				<div class="flex items-center gap-2">
					<button class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-surface-card hover:bg-surface-container text-on-surface font-body-sm text-body-sm shadow-sm transition-all" type="button" data-ls-copy="<?php echo esc_url( get_permalink() ); ?>" title="<?php esc_attr_e( 'کپی لینک مقاله', 'larijani-stone' ); ?>"><i class="bi bi-link-45deg text-[18px] text-primary-container" aria-hidden="true"></i><span class="hidden sm:inline"><?php esc_html_e( 'کپی پیوند', 'larijani-stone' ); ?></span></button>
					<a class="w-10 h-10 rounded-xl bg-surface-card hover:bg-surface-container flex items-center justify-center shadow-sm transition-all" href="<?php echo esc_url( $share['whatsapp'] ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'ارسال به واتساپ', 'larijani-stone' ); ?>"><i class="bi bi-whatsapp text-[18px] text-accent-emerald" aria-hidden="true"></i></a>
					<a class="w-10 h-10 rounded-xl bg-surface-card hover:bg-surface-container flex items-center justify-center shadow-sm transition-all" href="<?php echo esc_url( $share['telegram'] ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'اشتراک در تلگرام', 'larijani-stone' ); ?>"><i class="bi bi-telegram text-[18px] text-accent-cobalt" aria-hidden="true"></i></a>
					<a class="w-10 h-10 rounded-xl bg-surface-card hover:bg-secondary-container flex items-center justify-center shadow-sm transition-all" href="<?php echo esc_url( $share['linkedin'] ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'لینکدین', 'larijani-stone' ); ?>"><i class="bi bi-linkedin text-[17px] text-primary-container" aria-hidden="true"></i></a>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php $hero_img = 'yes' === $s['show_image'] ? larijani_post_image_url( $post_id, 'ls-wide' ) : ''; ?>
			<?php if ( $hero_img ) : ?>
			<div class="relative w-full rounded-3xl overflow-hidden shadow-xl aspect-[16/9] sm:aspect-[21/9] max-h-[520px]">
				<?php echo larijani_img( $hero_img, 'w-full h-full object-cover', get_the_title( $post_id ), 'ls-wide', false ); // phpcs:ignore ?>
				<?php if ( $caption || $ibadge ) : ?>
				<div class="absolute inset-0 bg-gradient-to-t from-surface-dark/80 via-surface-dark/20 to-transparent"></div>
				<div class="absolute bottom-4 sm:bottom-6 right-4 sm:right-6 left-4 sm:left-6 flex flex-wrap items-center justify-between gap-4 text-on-primary">
					<?php if ( $caption ) : ?><div class="flex items-center gap-2 font-body-sm text-body-sm bg-surface-dark/60 backdrop-blur-md px-3.5 py-1.5 rounded-full"><i class="bi bi-camera text-primary-fixed" aria-hidden="true"></i><?php echo esc_html( $caption ); ?></div><?php endif; ?>
					<?php if ( $ibadge ) : ?><span class="font-label-badge text-label-badge px-3 py-1 bg-primary-container/90 rounded-full tracking-wide"><?php echo esc_html( $ibadge ); ?></span><?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ( $s['metrics'] ) : ?>
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
				<?php foreach ( $s['metrics'] as $m ) : ?>
				<div class="bg-surface-card rounded-2xl p-5 shadow-sm flex flex-col justify-between">
					<div class="flex items-center justify-between text-on-surface-variant gap-2"><span class="font-body-sm text-body-sm font-semibold"><?php echo esc_html( $m['label'] ?? '' ); ?></span><?php echo larijani_icon( $m['icon'] ?? '', 'text-[20px] ' . larijani_tone( $m['tone'] ?? 'primary', 'text' ) ); // phpcs:ignore ?></div>
					<div class="mt-3 flex items-baseline gap-2 flex-wrap"><span class="font-headline-lg text-headline-lg <?php echo 'emerald' === ( $m['value_tone'] ?? '' ) ? 'text-accent-emerald' : 'text-surface-dark'; ?> font-black tracking-tight"><?php echo esc_html( $m['value'] ?? '' ); ?></span><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $m['unit'] ?? '' ); ?></span></div>
					<?php if ( ! empty( $m['note'] ) ) : ?><div class="mt-2 <?php echo 'emerald' === ( $m['note_tone'] ?? '' ) ? 'text-accent-emerald' : 'text-on-surface-variant'; ?> font-body-sm text-body-sm flex items-center gap-1"><?php echo larijani_icon( $m['note_icon'] ?? 'bi bi-check2', 'text-[13px]' ); // phpcs:ignore ?><?php echo esc_html( $m['note'] ); ?></div><?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Split post content into section cards at each <h2> (design: numbered cards).
 *
 * @param string $content HTML.
 * @return array [ [id, html], ... ]
 */
function larijani_split_content_sections( $content ) {
	$parts    = preg_split( '/(?=<h2[\s>])|(?=<div class="ls-article-block)/i', $content );
	$sections = array();
	$n        = 0;
	foreach ( $parts as $part ) {
		if ( '' === trim( wp_strip_all_tags( $part, true ) ) && false === stripos( $part, '<img' ) && false === stripos( $part, '<iframe' ) ) {
			continue;
		}
		$id = '';
		if ( preg_match( '/^<h2([^>]*)>(.*?)<\/h2>/is', ltrim( $part ), $m ) ) {
			$n++;
			if ( preg_match( '/id=["\']([^"\']+)/', $m[1], $idm ) ) {
				$id = $idm[1];
			} else {
				$id = 'section-' . $n;
			}
			$badge = '<span class="w-9 h-9 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold shrink-0 text-base">' . esc_html( larijani_fa_num( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ) ) . '</span>';
			$head  = '<div class="flex items-center gap-3 not-prose">' . $badge . '<h2 id="' . esc_attr( $id ) . '" class="!m-0 font-headline-md text-headline-md text-surface-dark font-black">' . $m[2] . '</h2></div>';
			$part  = $head . substr( ltrim( $part ), strlen( $m[0] ) );
		}
		$sections[] = array( $id, $part );
	}
	return $sections;
}

/**
 * Article body: section cards, tags, author bio and prev/next links.
 *
 * @param array $s Settings.
 */
function larijani_render_post_body( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'split_sections' => 'yes',
			'show_tags'      => 'yes',
			'tags_label'     => __( 'برچسب‌های تخصصی:', 'larijani-stone' ),
			'show_author'    => 'yes',
			'author_prefix'  => __( 'درباره نویسنده:', 'larijani-stone' ),
			'author_cta'     => __( 'گفتگوی مستقیم با نویسنده', 'larijani-stone' ),
			'show_nav'       => 'yes',
		)
	);
	$content = apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	$content = str_replace( ']]>', ']]&gt;', $content );
	echo '<div class="ls-article flex flex-col gap-10">';
	if ( 'yes' === $s['split_sections'] ) {
		foreach ( larijani_split_content_sections( $content ) as $sec ) {
			if ( 0 === strpos( ltrim( $sec[1] ), '<div class="ls-article-block' ) ) {
				echo $sec[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- post content.
				continue;
			}
			echo '<section class="bg-surface-card rounded-3xl p-6 sm:p-8 lg:p-10 shadow-sm space-y-6 ls-prose"' . ( $sec[0] ? ' id="' . esc_attr( $sec[0] ) . '-card"' : '' ) . '>' . $sec[1] . '</section>'; // phpcs:ignore
		}
	} else {
		echo '<section class="bg-surface-card rounded-3xl p-6 sm:p-8 lg:p-10 shadow-sm ls-prose">' . $content . '</section>'; // phpcs:ignore
	}
	wp_link_pages( array( 'before' => '<nav class="ls-pagination flex gap-2">', 'after' => '</nav>' ) );

	$tags = get_the_tags();
	if ( 'yes' === $s['show_tags'] && $tags ) {
		echo '<div class="flex flex-wrap items-center gap-2 pt-2"><span class="font-body-sm text-body-sm text-on-surface-variant font-bold ml-2">' . esc_html( $s['tags_label'] ) . '</span>';
		foreach ( $tags as $tag ) {
			echo '<a class="px-3 py-1.5 rounded-xl bg-surface-card hover:bg-secondary-container text-on-surface-variant hover:text-on-secondary-container font-body-sm text-body-sm shadow-sm transition-colors" href="' . esc_url( get_tag_link( $tag ) ) . '">#' . esc_html( str_replace( ' ', '_', $tag->name ) ) . '</a>';
		}
		echo '</div>';
	}

	if ( 'yes' === $s['show_author'] ) {
		$aid  = (int) get_post_field( 'post_author', get_the_ID() );
		$name = get_post_meta( get_the_ID(), '_ls_author_name', true );
		$name = $name ? $name : get_the_author_meta( 'display_name', $aid );
		$bio  = get_post_meta( get_the_ID(), '_ls_author_bio', true );
		$bio  = $bio ? $bio : get_the_author_meta( 'description', $aid );
		?>
		<div class="bg-surface-card rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col sm:flex-row items-center sm:items-start gap-6">
			<div class="w-20 h-20 rounded-2xl bg-surface-canvas p-1 shrink-0 shadow-sm"><?php echo get_avatar( $aid, 160, '', $name, array( 'class' => 'w-full h-full rounded-xl object-cover' ) ); ?></div>
			<div class="space-y-3 text-center sm:text-right flex-1">
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
					<div>
						<h3 class="font-headline-sm text-headline-sm text-surface-dark font-black"><?php echo esc_html( trim( $s['author_prefix'] . ' ' . $name ) ); ?></h3>
						<a class="font-body-sm text-body-sm text-primary-container font-bold" href="<?php echo esc_url( get_author_posts_url( $aid ) ); ?>"><?php esc_html_e( 'همه نوشته‌های نویسنده', 'larijani-stone' ); ?></a>
					</div>
					<?php if ( $s['author_cta'] ) : ?>
					<a class="inline-flex items-center gap-1.5 text-primary-container hover:text-primary font-label-nav text-body-sm" href="<?php echo esc_url( larijani_tel( larijani_opt( 'phone_1' ) ) ); ?>"><i class="bi bi-telephone-forward" aria-hidden="true"></i><?php echo esc_html( $s['author_cta'] ); ?></a>
					<?php endif; ?>
				</div>
				<?php if ( $bio ) : ?><p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $bio ); ?></p><?php endif; ?>
			</div>
		</div>
		<?php
	}

	if ( 'yes' === $s['show_nav'] ) {
		$prev = get_previous_post();
		$next = get_next_post();
		if ( $prev || $next ) {
			echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
			if ( $prev ) {
				echo '<a class="p-6 rounded-2xl bg-surface-card hover:bg-surface-container-high transition-all shadow-sm flex flex-col justify-between group" href="' . esc_url( get_permalink( $prev ) ) . '"><div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm"><i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform" aria-hidden="true"></i>' . esc_html__( 'مقاله قبلی', 'larijani-stone' ) . '</div><span class="font-title-card text-title-card text-surface-dark font-bold mt-2">' . esc_html( get_the_title( $prev ) ) . '</span></a>';
			} else {
				echo '<span></span>';
			}
			if ( $next ) {
				echo '<a class="p-6 rounded-2xl bg-surface-card hover:bg-surface-container-high transition-all shadow-sm flex flex-col justify-between text-left group" href="' . esc_url( get_permalink( $next ) ) . '"><div class="flex items-center justify-end gap-2 text-on-surface-variant font-body-sm text-body-sm">' . esc_html__( 'مقاله بعدی', 'larijani-stone' ) . '<i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform" aria-hidden="true"></i></div><span class="font-title-card text-title-card text-surface-dark font-bold mt-2 text-right">' . esc_html( get_the_title( $next ) ) . '</span></a>';
			}
			echo '</div>';
		}
	}
	echo '</div>';
}

/**
 * Related posts block.
 *
 * @param array $s Settings.
 */
function larijani_render_related_posts( $s = array() ) {
	$s     = wp_parse_args( $s, array( 'title' => __( 'مقالات و فرمولاسیون‌های مرتبط کارگاهی', 'larijani-stone' ), 'count' => 3, 'link_text' => __( 'مشاهده همه', 'larijani-stone' ) ) );
	$posts = get_posts( larijani_posts_query_args( array( 'posts_per_page' => $s['count'], 'related' => true ) ) );
	if ( ! $posts ) {
		$posts = get_posts( larijani_posts_query_args( array( 'posts_per_page' => $s['count'], 'exclude_current' => true ) ) );
	}
	if ( ! $posts ) {
		return;
	}
	?>
	<div class="space-y-4 pt-4">
		<div class="flex items-center justify-between gap-3">
			<h3 class="font-headline-md text-headline-md text-surface-dark font-black"><?php echo esc_html( $s['title'] ); ?></h3>
			<?php if ( $s['link_text'] ) : ?><a class="font-body-sm text-body-sm text-primary-container font-bold hover:underline flex items-center gap-1 shrink-0" href="<?php echo esc_url( larijani_blog_url() ); ?>"><?php echo esc_html( $s['link_text'] ); ?><i class="bi bi-arrow-left" aria-hidden="true"></i></a><?php endif; ?>
		</div>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<?php foreach ( $posts as $p ) : ?>
				<?php echo larijani_post_card( larijani_post_data( $p ), 'related' ); // phpcs:ignore ?>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Comments block.
 *
 * @param array $s Settings.
 */
function larijani_render_post_comments( $s = array() ) {
	$s = wp_parse_args( $s, array( 'badge' => __( 'پاسخگویی مستقیم توسط تیم فنی', 'larijani-stone' ) ) );
	if ( ! comments_open() && ! get_comments_number() ) {
		return;
	}
	$GLOBALS['larijani_comments_badge'] = $s['badge'];
	comments_template();
}

/**
 * Default single post sidebar.
 */
function larijani_render_single_sidebar() {
	echo '<div class="sticky top-28 space-y-6">';
	larijani_render_toc();
	larijani_render_resin_calculator();
	larijani_render_sidebar_cta();
	larijani_render_popular_posts(
		array(
			'title' => __( 'پربازدیدترین فرمول‌های ماه', 'larijani-stone' ),
			'icon'  => '',
			'count' => 3,
			'style' => 'compact',
		)
	);
	larijani_render_promo_box();
	if ( is_active_sidebar( 'blog-sidebar' ) ) {
		echo '<div class="ls-widget-area">';
		dynamic_sidebar( 'blog-sidebar' );
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Post metric cards stored in `_ls_metrics` (one per line:
 * label | value | unit | icon | tone | note | note icon).
 *
 * @param int $post_id Post id.
 * @return array
 */
function larijani_post_metrics( $post_id ) {
	$out = array();
	foreach ( larijani_lines( (string) get_post_meta( $post_id, '_ls_metrics', true ) ) as $line ) {
		$p = array_map( 'trim', explode( '|', $line ) );
		if ( count( $p ) < 2 ) {
			continue;
		}
		$tone  = $p[4] ?? 'primary';
		$out[] = array(
			'label'      => $p[0],
			'value'      => $p[1],
			'unit'       => $p[2] ?? '',
			'icon'       => $p[3] ?? '',
			'tone'       => $tone,
			'value_tone' => 'emerald' === $tone && 0 === strpos( $p[1], '+' ) ? 'emerald' : '',
			'note'       => $p[5] ?? '',
			'note_icon'  => $p[6] ?? 'bi bi-check2',
			'note_tone'  => false !== strpos( $p[6] ?? '', 'graph-up' ) ? 'emerald' : '',
		);
	}
	return $out;
}
