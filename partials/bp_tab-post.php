<?php
/**
 * BuddyPress Profile - Post Loop
 *
 * Used to show each regular post item on a user's profile page.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 2.0.0
 */

?>
<article class="tab-posts__item entry-post">
	<h3 class="entry__title entry-post__title">
		<?php the_title( '<a href="' . esc_url( get_the_permalink() ) . '">', '</a>' ); ?>
	</h3>
	<p class="entry__meta-postdate"><time datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time></p>
	<?php echo jeremy_get_the_excerpt( array(
		'length' => 45,
	) ); ?>
</article>