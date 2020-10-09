<?php
/**
 * BuddyPress Profile - Job Loop
 *
 * Used to show each job listing item on a user's profile page.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 2.0.0
 */

$link = get_post_meta( get_the_ID(), '_jeremysfriend_link', true );
$link = has_post_format( 'link' ) && $link ? $link : get_the_permalink();
?>

<article class="entry-job">
	<header class="entry__header entry-job__header">
		<h3 class="entry__title entry-job__title">
			<?php the_title( '<a href="' . esc_url( $link ) . '">', '</a>' ); ?>
		</h3>
		<?php jeremy_author_tools(); ?>
		<p class="entry__meta-postdate"><?php printf( esc_html__( 'Posted on %s', 'jeremy' ), get_the_date() ); ?></p>
	</header>

	<div class="entry__content entry-job__content" role="presentation">
		<?php echo jeremy_get_the_excerpt( array(
			'length' 	 => 25,
			'readmore' => __( 'Apply now', 'jeremy' ),
			'link' 		 => $link,
		) ); ?>
	</div><!-- .entry-job__content -->
</article>
