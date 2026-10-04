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
?>
<section id="comments" class="ls-root ls-comments bg-surface-card rounded-3xl p-6 sm:p-8 lg:p-10 shadow-sm space-y-8">
	<div class="flex items-center justify-between gap-3 flex-wrap">
		<div class="flex items-center gap-3">
			<i class="bi bi-chat-square-text text-[26px] text-primary-container" aria-hidden="true"></i>
			<h3 class="font-headline-md text-headline-md text-surface-dark font-black">
				<?php
				/* translators: %s comments count */
				echo esc_html( sprintf( __( 'دیدگاه‌ها و پرسش‌های فنی (%s نظر)', 'larijani' ), ls_fa_num( get_comments_number() ) ) );
				?>
			</h3>
		</div>
		<?php if ( $ls_badge ) : ?><span class="font-body-sm text-body-sm text-on-surface-variant bg-surface-canvas px-3 py-1 rounded-full"><?php echo esc_html( $ls_badge ); ?></span><?php endif; ?>
	</div>

	<?php
	comment_form(
		array(
			'title_reply'          => __( 'طرح پرسش یا ارسال تجربه کارگاهی', 'larijani' ),
			'title_reply_before'   => '<h4 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h4>',
			'label_submit'         => __( 'ثبت و ارسال دیدگاه فنی', 'larijani' ),
			'comment_notes_before' => '',
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'متن پرسش یا تجربه', 'larijani' ) . '</label><textarea id="comment" name="comment" rows="4" required placeholder="' . esc_attr__( 'متن پرسش فنی، میزان عیار بتن، یا شرح مشکل حباب و شکستگی...', 'larijani' ) . '"></textarea></p>',
		)
	);
	?>

	<?php if ( have_comments() ) : ?>
	<ol class="comment-list">
		<?php
		wp_list_comments(
			array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 40,
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
