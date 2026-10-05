<?php
/**
 * Comments template ("پرسش و پاسخ کارگاهی").
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
$ls_badge = isset( $GLOBALS['ls_comments_badge'] ) ? $GLOBALS['ls_comments_badge'] : __( 'پاسخگویی مستقیم توسط تیم فنی', 'larijani' );
$ls_input = 'w-full px-4 py-3 rounded-xl bg-surface-card text-on-surface placeholder:text-outline-variant font-body-md text-body-md border-0 focus:outline-none focus:ring-2 focus:ring-primary-container transition-all';
$ls_req   = get_option( 'require_name_email' ) ? ' required' : '';
?>
<section id="comments" class="ls-root ls-comments bg-surface-card rounded-3xl p-6 sm:p-8 lg:p-10 shadow-sm space-y-8">
	<div class="flex items-center justify-between gap-3 flex-wrap">
		<div class="flex items-center gap-3">
			<i class="bi bi-chat-square-text text-[26px] text-primary-container" aria-hidden="true"></i>
			<h3 class="font-headline-md text-headline-md text-surface-dark font-black">
				<?php
				/* translators: %s comments count */
				echo esc_html( sprintf( __( 'دیدگاه‌ها و پرسش‌های فنی کارگاه‌ها (%s نظر)', 'larijani' ), ls_fa_num( get_comments_number() ) ) );
				?>
			</h3>
		</div>
		<?php if ( $ls_badge ) : ?><span class="font-body-sm text-body-sm text-on-surface-variant bg-surface-canvas px-3 py-1 rounded-full"><?php echo esc_html( $ls_badge ); ?></span><?php endif; ?>
	</div>

	<div class="bg-surface-canvas rounded-2xl p-6">
	<?php
	comment_form(
		array(
			'title_reply'          => __( 'طرح پرسش یا ارسال تجربه کارگاهی', 'larijani' ),
			/* translators: %s author name */
			'title_reply_to'       => __( 'پاسخ به %s', 'larijani' ),
			'cancel_reply_link'    => __( 'انصراف', 'larijani' ),
			'title_reply_before'   => '<h4 id="reply-title" class="comment-reply-title font-headline-sm text-title-card text-surface-dark font-bold mb-4">',
			'title_reply_after'    => '</h4>',
			'comment_notes_before' => '',
			'class_submit'         => 'bg-primary-container hover:bg-primary text-on-primary font-label-nav text-label-nav px-6 py-2.5 rounded-full shadow-md transition-all',
			'label_submit'         => __( 'ثبت و ارسال دیدگاه فنی', 'larijani' ),
			'fields'               => array(
				'author' => '<p class="comment-form-author"><label class="sr-only" for="author">' . esc_html__( 'نام', 'larijani' ) . '</label><input id="author" name="author" type="text" class="' . esc_attr( $ls_input ) . '" placeholder="' . esc_attr__( 'نام و نام خانوادگی یا نام کارگاه', 'larijani' ) . '" autocomplete="name"' . $ls_req . '></p>',
				'email'  => '<p class="comment-form-email"><label class="sr-only" for="email">' . esc_html__( 'ایمیل', 'larijani' ) . '</label><input id="email" name="email" type="email" dir="ltr" class="' . esc_attr( $ls_input ) . ' text-right" placeholder="' . esc_attr__( 'ایمیل (نمایش داده نمی‌شود)', 'larijani' ) . '" autocomplete="email"' . $ls_req . '></p>',
			),
			'comment_field'        => '<p class="comment-form-comment"><label class="sr-only" for="comment">' . esc_html__( 'متن پرسش یا تجربه', 'larijani' ) . '</label><textarea id="comment" name="comment" rows="3" class="' . esc_attr( $ls_input ) . '" required placeholder="' . esc_attr__( 'متن پرسش فنی، میزان عیار بتن، یا شرح مشکل حباب و شکستگی...', 'larijani' ) . '"></textarea></p>',
		)
	);
	?>
	</div>

	<?php if ( have_comments() ) : ?>
	<ol class="comment-list">
		<?php
		wp_list_comments(
			array(
				'style'    => 'ol',
				'callback' => 'ls_comment_item',
				'max_depth' => 3,
			)
		);
		?>
	</ol>
		<?php the_comments_pagination( array( 'class' => 'ls-pagination' ) ); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
	<p class="text-on-surface-variant text-sm"><?php esc_html_e( 'امکان ارسال دیدگاه بسته شده است.', 'larijani' ); ?></p>
	<?php endif; ?>
</section>
