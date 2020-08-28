<?php
/**
 * The template for displaying comments
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<h2 class="comments-title"><?php echo __( 'Comments', 'jeremy' ); ?></h2>
	<?php if ( have_comments() ) { ?>
		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'walker' => new Jeremy_Walker_Comment(),
					'style'      => 'ol',
					'short_ping' => true,
					'type' => 'comment',
					'max_depth' => 2,
					'avatar_size' => 64,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { ?>
		<nav id="comment-nav" class="navigation comment-navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'jeremy' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'jeremy' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'jeremy' ) ); ?></div>
			</div><!-- .nav-links -->
		</nav><!-- #comment-nav -->
		<?php }

	} else { // If there are no comments to show ?>
		<p><?php esc_html_e( 'There are no comments on this article yet.', 'jeremy' ); ?></p>
	<?php }

	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) { ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are now closed.', 'jeremy' ); ?></p>
	<?php } else {
		jeremy_comment_form();
	} ?>

</div><!-- #comments -->
