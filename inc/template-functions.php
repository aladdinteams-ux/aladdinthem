<?php
/**
 * Template helpers: menus, breadcrumbs, pagination, misc.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get flat menu items (with one level of children) for a location or menu id.
 * Falls back to a sensible default list so the header looks right before
 * menus are configured.
 *
 * @param string|int $location Theme location or menu id.
 * @param array      $fallback Fallback items [ [title, url], ... ].
 * @return array[] { title, url, active, children[] }
 */
function ls_menu_items( $location, $fallback = array() ) {
	$menu_id = 0;
	if ( is_numeric( $location ) && (int) $location > 0 ) {
		$menu_id = (int) $location;
	} else {
		$locations = get_nav_menu_locations();
		if ( ! empty( $locations[ $location ] ) ) {
			$menu_id = (int) $locations[ $location ];
		}
	}

	$items = array();
	if ( $menu_id ) {
		$raw = wp_get_nav_menu_items( $menu_id, array( 'update_post_term_cache' => false ) );
		if ( $raw ) {
			_wp_menu_item_classes_by_context( $raw );
			$by_id = array();
			foreach ( $raw as $item ) {
				$classes = (array) $item->classes;
				$entry   = array(
					'id'       => (int) $item->ID,
					'title'    => $item->title,
					'url'      => $item->url,
					'target'   => $item->target,
					'active'   => (bool) array_intersect( $classes, array( 'current-menu-item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_item', 'current_page_parent' ) ),
					'icon'     => '',
					'children' => array(),
				);
				// Allow "bi bi-xxx" classes on menu items to become icons.
				foreach ( $classes as $c ) {
					if ( 0 === strpos( $c, 'bi-' ) ) {
						$entry['icon'] = 'bi ' . $c;
					}
				}
				if ( $item->menu_item_parent && isset( $by_id[ $item->menu_item_parent ] ) ) {
					$items[ $by_id[ $item->menu_item_parent ] ]['children'][] = $entry;
				} elseif ( ! $item->menu_item_parent ) {
					$items[]               = $entry;
					$by_id[ $item->ID ] = count( $items ) - 1;
				}
			}
			return array_values( $items );
		}
	}

	foreach ( $fallback as $f ) {
		$url     = isset( $f[1] ) ? $f[1] : '#';
		$items[] = array(
			'id'       => 0,
			'title'    => $f[0],
			'url'      => $url,
			'target'   => '',
			'active'   => ( trailingslashit( $url ) === trailingslashit( home_url( add_query_arg( array() ) ) ) ),
			'icon'     => isset( $f[2] ) ? $f[2] : '',
			'children' => array(),
		);
	}
	return $items;
}

/**
 * URL of a page by slug (used for default links).
 *
 * @param string $slug Slug.
 * @param string $fallback Fallback.
 * @return string
 */
function ls_page_url( $slug, $fallback = '#' ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : $fallback;
}

/**
 * Default header navigation (before a menu is assigned).
 *
 * @return array
 */
function ls_default_nav() {
	return array(
		array( __( 'صفحه اصلی', 'larijani' ), home_url( '/' ) ),
		array( __( 'فروشگاه و کاتالوگ', 'larijani' ), ls_has_woo() ? get_permalink( wc_get_page_id( 'shop' ) ) : ls_page_url( 'shop' ) ),
		array( __( 'خدمات و خطوط تولید', 'larijani' ), ls_page_url( 'services' ) ),
		array( __( 'نمونه کارها', 'larijani' ), ls_page_url( 'portfolio', get_post_type_archive_link( 'ls_project' ) ) ),
		array( __( 'وبلاگ تخصصی', 'larijani' ), ls_page_url( 'blog', get_post_type_archive_link( 'post' ) ) ),
		array( __( 'تماس با ما', 'larijani' ), ls_page_url( 'contact' ) ),
	);
}

/**
 * Breadcrumb trail.
 *
 * @return array[] [ [label, url|null], ... ]
 */
function ls_breadcrumb_trail() {
	$trail = array( array( __( 'صفحه اصلی', 'larijani' ), home_url( '/' ) ) );

	if ( is_front_page() ) {
		return $trail;
	}

	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) {
		$shop_id = wc_get_page_id( 'shop' );
		if ( ! is_shop() ) {
			$trail[] = array( get_the_title( $shop_id ), get_permalink( $shop_id ) );
		}
		if ( is_product_category() || is_product_tag() ) {
			$term = get_queried_object();
			foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) ) as $anc ) {
				$a       = get_term( $anc, $term->taxonomy );
				$trail[] = array( $a->name, get_term_link( $a ) );
			}
			$trail[] = array( $term->name, null );
		} elseif ( is_product() ) {
			$terms = wc_get_product_terms( get_the_ID(), 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) );
			if ( $terms ) {
				$trail[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
			}
			$trail[] = array( get_the_title(), null );
		} elseif ( is_shop() ) {
			$trail[] = array( get_the_title( $shop_id ), null );
		} else {
			$trail[] = array( get_the_title(), null );
		}
		return $trail;
	}

	if ( is_home() ) {
		$trail[] = array( get_the_title( (int) get_option( 'page_for_posts' ) ) ?: __( 'وبلاگ', 'larijani' ), null );
	} elseif ( is_singular( 'post' ) ) {
		$blog = (int) get_option( 'page_for_posts' );
		$trail[] = array( $blog ? get_the_title( $blog ) : __( 'وبلاگ', 'larijani' ), $blog ? get_permalink( $blog ) : home_url( '/' ) );
		$cats = get_the_category();
		if ( $cats ) {
			$trail[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_singular( 'ls_project' ) ) {
		$trail[] = array( __( 'نمونه کارها', 'larijani' ), ls_page_url( 'portfolio', get_post_type_archive_link( 'ls_project' ) ) );
		$trail[] = array( get_the_title(), null );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $anc ) {
			$trail[] = array( get_the_title( $anc ), get_permalink( $anc ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_singular() ) {
		$trail[] = array( get_the_title(), null );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$trail[] = array( single_term_title( '', false ), null );
	} elseif ( is_post_type_archive() ) {
		$trail[] = array( post_type_archive_title( '', false ), null );
	} elseif ( is_search() ) {
		/* translators: %s search query */
		$trail[] = array( sprintf( __( 'جستجو: %s', 'larijani' ), get_search_query() ), null );
	} elseif ( is_author() ) {
		$trail[] = array( get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) ), null );
	} elseif ( is_archive() ) {
		$trail[] = array( wp_strip_all_tags( get_the_archive_title() ), null );
	} elseif ( is_404() ) {
		$trail[] = array( __( 'صفحه پیدا نشد', 'larijani' ), null );
	}
	return apply_filters( 'ls_breadcrumb_trail', $trail );
}

/**
 * Breadcrumb HTML (design style: home icon + chevrons).
 *
 * @param array $args { separator: chevron|slash, current_class }.
 * @return string
 */
function ls_breadcrumb_html( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'separator'     => 'chevron',
			'current_class' => 'text-on-surface font-semibold truncate max-w-xs sm:max-w-md',
			'items'         => null,
		)
	);

	// SEO plugins first (they output schema too).
	if ( null === $args['items'] && function_exists( 'yoast_breadcrumb' ) && ! ls_is_elementor_editor() ) {
		$yoast = yoast_breadcrumb( '<span class="ls-yoast-bc">', '</span>', false );
		if ( $yoast ) {
			return $yoast;
		}
	}

	$trail = null !== $args['items'] ? $args['items'] : ls_breadcrumb_trail();
	$sep   = 'slash' === $args['separator'] ? '<span class="text-outline-variant">/</span>' : '<i class="bi bi-chevron-left text-xs opacity-40" aria-hidden="true"></i>';
	$out   = array();
	$last  = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		$label = esc_html( wp_strip_all_tags( $crumb[0] ) );
		if ( 0 === $i ) {
			$label = '<i class="bi bi-house-door text-sm" aria-hidden="true"></i><span>' . $label . '</span>';
		}
		if ( $i === $last || empty( $crumb[1] ) ) {
			$out[] = '<span class="' . esc_attr( $i === $last ? $args['current_class'] : '' ) . ( 0 === $i ? ' flex items-center gap-1' : '' ) . '" ' . ( $i === $last ? 'aria-current="page"' : '' ) . '>' . $label . '</span>';
		} else {
			$out[] = '<a class="hover:text-primary transition-colors' . ( 0 === $i ? ' flex items-center gap-1' : '' ) . '" href="' . esc_url( $crumb[1] ) . '">' . $label . '</a>';
		}
	}
	return implode( $sep, $out );
}

/**
 * Numbered pagination (design style).
 *
 * @param WP_Query|null $query Query.
 * @return string
 */
function ls_pagination( $query = null ) {
	global $wp_query;
	$query = $query ? $query : $wp_query;
	if ( $query->max_num_pages < 2 ) {
		return '';
	}
	$current = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$links   = paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => $current,
			'mid_size'  => 1,
			'prev_text' => '<i class="bi bi-chevron-right" aria-hidden="true"></i><span class="screen-reader-text">' . __( 'قبلی', 'larijani' ) . '</span>',
			'next_text' => '<i class="bi bi-chevron-left" aria-hidden="true"></i><span class="screen-reader-text">' . __( 'بعدی', 'larijani' ) . '</span>',
			'type'      => 'array',
		)
	);
	if ( ! $links ) {
		return '';
	}
	$links = array_map( 'ls_fa_num_html_safe', $links );
	return '<nav class="ls-pagination flex items-center justify-center flex-wrap gap-2" aria-label="' . esc_attr__( 'صفحه‌بندی', 'larijani' ) . '">' . implode( '', $links ) . '</nav>';
}

/**
 * Convert digits to Persian only in text nodes of a small HTML snippet.
 *
 * @param string $html HTML.
 * @return string
 */
function ls_fa_num_html_safe( $html ) {
	return preg_replace_callback(
		'/>([^<]+)</u',
		static function ( $m ) {
			return '>' . ls_fa_num( $m[1] ) . '<';
		},
		$html
	);
}

/**
 * Social share URLs for the current post.
 *
 * @return array
 */
function ls_share_links() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	return array(
		'whatsapp' => 'https://wa.me/?text=' . $title . '%20' . $url,
		'telegram' => 'https://t.me/share/url?url=' . $url . '&text=' . $title,
		'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		'x'        => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
	);
}

/**
 * Social profile links from the Customizer.
 *
 * @return array [ [icon, url, label], ... ]
 */
function ls_social_profiles() {
	$out = array();
	$map = array(
		'whatsapp'  => array( 'bi bi-whatsapp', __( 'واتساپ', 'larijani' ) ),
		'telegram'  => array( 'bi bi-telegram', __( 'تلگرام', 'larijani' ) ),
		'instagram' => array( 'bi bi-instagram', __( 'اینستاگرام', 'larijani' ) ),
		'eitaa'     => array( 'bi bi-chat-dots-fill', __( 'ایتا', 'larijani' ) ),
		'aparat'    => array( 'bi bi-play-btn-fill', __( 'آپارات', 'larijani' ) ),
		'linkedin'  => array( 'bi bi-linkedin', __( 'لینکدین', 'larijani' ) ),
	);
	foreach ( $map as $key => $meta ) {
		$val = 'whatsapp' === $key ? ( ls_opt( 'whatsapp' ) ? ls_whatsapp_url() : '' ) : ls_opt( $key );
		if ( $val ) {
			$out[] = array( $meta[0], $val, $meta[1] );
		}
	}
	return $out;
}

/**
 * Logo markup (custom logo or bundled mark).
 *
 * @param string     $img_class Image classes.
 * @param array|null $media     Optional override media.
 * @return string
 */
function ls_logo_img( $img_class = 'w-full h-full object-contain', $media = null ) {
	if ( is_array( $media ) && ( ! empty( $media['url'] ) || ! empty( $media['id'] ) ) ) {
		return ls_img( $media, $img_class, ls_opt( 'brand_name' ), 'medium', false );
	}
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		return wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => $img_class, 'alt' => ls_opt( 'brand_name' ), 'loading' => 'eager' ) );
	}
	return '<img src="' . esc_url( LS_URI . '/assets/images/logo.svg' ) . '" class="' . esc_attr( $img_class ) . '" alt="' . esc_attr( ls_opt( 'brand_name' ) ) . '">';
}

/**
 * Posts query args from common widget settings.
 *
 * @param array $s Settings: source, posts_per_page, category, orderby, exclude_current, offset.
 * @return array
 */
function ls_posts_query_args( $s ) {
	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => isset( $s['posts_per_page'] ) ? (int) $s['posts_per_page'] : 6,
		'ignore_sticky_posts' => true,
		'post_status'         => 'publish',
	);
	if ( ! empty( $s['category'] ) ) {
		$args['cat'] = (int) $s['category'];
	}
	if ( ! empty( $s['offset'] ) ) {
		$args['offset'] = (int) $s['offset'];
	}
	$orderby = isset( $s['orderby'] ) ? $s['orderby'] : 'date';
	if ( 'views' === $orderby ) {
		$args['meta_key'] = 'ls_views'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$args['orderby']  = 'meta_value_num';
	} elseif ( 'comments' === $orderby ) {
		$args['orderby'] = 'comment_count';
	} elseif ( 'rand' === $orderby ) {
		$args['orderby'] = 'rand';
	} else {
		$args['orderby'] = 'date';
	}
	if ( ! empty( $s['exclude_current'] ) && is_singular() ) {
		$args['post__not_in'] = array( get_the_ID() );
	}
	if ( ! empty( $s['related'] ) && is_singular( 'post' ) ) {
		$cats = wp_get_post_categories( get_the_ID() );
		if ( $cats ) {
			$args['category__in'] = $cats;
		}
		$args['post__not_in'] = array( get_the_ID() );
	}
	return $args;
}

/**
 * Category options for SELECT controls.
 *
 * @param string $taxonomy Taxonomy.
 * @return array
 */
function ls_term_options( $taxonomy = 'category' ) {
	$out   = array( '' => __( 'همه', 'larijani' ) );
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			$out[ $t->term_id ] = $t->name;
		}
	}
	return $out;
}

/**
 * Primary category of a post.
 *
 * @param int|null $post_id Post.
 * @return WP_Term|null
 */
function ls_primary_category( $post_id = null ) {
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0] : null;
}

/**
 * Persian (Jalali) aware date for posts. Uses WordPress date_i18n which is
 * converted by Persian date plugins (e.g. wp-parsidate) when installed.
 *
 * @param int|null $post_id Post.
 * @return string
 */
function ls_post_date( $post_id = null ) {
	if ( ls_opt( 'jalali_dates' ) ) {
		return ls_jalali_date( (int) get_post_time( 'U', true, $post_id ) );
	}
	return ls_fa_num( get_the_date( '', $post_id ) );
}

/**
 * Persian relative time ("۲ روز پیش").
 *
 * @param int $timestamp Unix timestamp.
 * @return string
 */
function ls_time_ago( $timestamp ) {
	$diff  = max( 0, time() - (int) $timestamp );
	$units = array(
		YEAR_IN_SECONDS   => __( 'سال', 'larijani' ),
		MONTH_IN_SECONDS  => __( 'ماه', 'larijani' ),
		WEEK_IN_SECONDS   => __( 'هفته', 'larijani' ),
		DAY_IN_SECONDS    => __( 'روز', 'larijani' ),
		HOUR_IN_SECONDS   => __( 'ساعت', 'larijani' ),
		MINUTE_IN_SECONDS => __( 'دقیقه', 'larijani' ),
	);
	foreach ( $units as $sec => $label ) {
		if ( $diff >= $sec ) {
			/* translators: 1: number 2: unit */
			return ls_fa_num( sprintf( __( '%1$d %2$s پیش', 'larijani' ), floor( $diff / $sec ), $label ) );
		}
	}
	return __( 'لحظاتی پیش', 'larijani' );
}

/**
 * Comment markup (wp_list_comments callback) following the design's
 * "answered workshop question" cards. Replies by the post author / staff get
 * the "approved by the technical unit" badge.
 *
 * @param WP_Comment $comment Comment.
 * @param array      $args    Args.
 * @param int        $depth   Depth.
 */
function ls_comment_item( $comment, $args, $depth ) {
	$is_reply = $depth > 1;
	$staff    = $comment->user_id && ( user_can( $comment->user_id, 'moderate_comments' ) || (int) get_post_field( 'post_author', $comment->comment_post_ID ) === (int) $comment->user_id );
	$staff    = $staff || get_comment_meta( $comment->comment_ID, '_ls_staff', true );
	$badge    = get_comment_meta( $comment->comment_ID, '_ls_badge', true );
	$name     = get_comment_author( $comment );
	$time     = ls_time_ago( (int) get_comment_date( 'U', $comment ) );
	$reply    = get_comment_reply_link(
		array_merge(
			$args,
			array(
				'depth'      => $depth,
				'max_depth'  => $args['max_depth'],
				'reply_text' => __( 'پاسخ', 'larijani' ),
				'before'     => '<span class="font-body-sm text-body-sm font-bold text-primary-container">',
				'after'      => '</span>',
			)
		),
		$comment
	);
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( '', $comment ); ?>>
	<?php if ( $is_reply ) : ?>
		<div class="mr-4 lg:mr-8 p-5 rounded-xl bg-surface-card space-y-2 border-r-4 border-primary-container">
			<div class="flex items-center justify-between gap-2 flex-wrap">
				<div class="flex items-center gap-2 flex-wrap">
					<span class="font-headline-sm text-body-md text-surface-dark font-black"><?php echo esc_html( sprintf( /* translators: %s name */ __( 'پاسخ %s', 'larijani' ), $name ) ); ?></span>
					<?php if ( $staff ) : ?>
					<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-badge text-label-badge"><i class="bi bi-patch-check-fill text-[12px]" aria-hidden="true"></i><?php esc_html_e( 'تایید شده توسط واحد فنی', 'larijani' ); ?></span>
					<?php endif; ?>
				</div>
				<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $time ); ?></span>
			</div>
			<div class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php comment_text( $comment ); ?></div>
			<?php echo $reply ? wp_kses_post( $reply ) : ''; ?>
		</div>
	<?php else : ?>
		<div class="p-6 rounded-2xl bg-surface-canvas space-y-4">
			<div class="flex items-center justify-between gap-3 flex-wrap">
				<div class="flex items-center gap-3">
					<div class="w-10 h-10 rounded-full bg-surface-card flex items-center justify-center font-bold text-primary-container text-sm"><?php echo esc_html( ls_initials( $name ) ); ?></div>
					<div>
						<span class="font-headline-sm text-body-lg text-surface-dark font-black"><?php echo esc_html( $name ); ?></span>
						<span class="block font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $time ); ?></span>
					</div>
				</div>
				<?php if ( $badge ) : ?>
				<span class="inline-flex items-center gap-1 font-label-badge text-label-badge px-2.5 py-1 rounded-full bg-surface-card text-on-surface-variant"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>
			</div>
			<?php if ( '0' === $comment->comment_approved ) : ?>
			<p class="font-body-sm text-body-sm text-accent-amber"><?php esc_html_e( 'دیدگاه شما پس از بررسی منتشر می‌شود.', 'larijani' ); ?></p>
			<?php endif; ?>
			<div class="font-body-md text-body-md text-on-surface leading-relaxed"><?php comment_text( $comment ); ?></div>
			<?php echo $reply ? wp_kses_post( $reply ) : ''; ?>
		</div>
	<?php endif; ?>
	<?php
}

/**
 * Comment form: name/e-mail first, then the message (design order) and a
 * Persian cookie-consent label.
 *
 * @param array $fields Fields.
 * @return array
 */
function ls_comment_form_fields( $fields ) {
	if ( isset( $fields['url'] ) ) {
		unset( $fields['url'] );
	}
	if ( isset( $fields['cookies'] ) ) {
		$checked           = empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] ) ? '' : ' checked'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $checked . '> <label for="wp-comment-cookies-consent">' . esc_html__( 'نام و ایمیل من برای دیدگاه‌های بعدی در این مرورگر ذخیره شود.', 'larijani' ) . '</label></p>';
	}
	if ( isset( $fields['comment'] ) ) {
		$comment = $fields['comment'];
		unset( $fields['comment'] );
		$cookies = $fields['cookies'] ?? null;
		unset( $fields['cookies'] );
		$fields['comment'] = $comment;
		if ( $cookies ) {
			$fields['cookies'] = $cookies;
		}
	}
	return $fields;
}
add_filter( 'comment_form_fields', 'ls_comment_form_fields' );
