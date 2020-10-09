<?php
/**
 * Post Type - Event | BuddyPress Profile View
 *
 * Used to show each event item on a user's profile page. It's more minimalist
 * than the archive view version.
 *
 * @package Jeremy
 * @subpackage Events
 * @since 2.0.0
 */
 
if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
	get_template_part( 'partials/content' );
	return;
}

/* For whatever reason EO can't get the occurrence IDs properly in certain contexts. #ShouldNotBeMyProblem */
$id = get_the_ID();
$occ_id = jeremy_eo_get_next_occurrence_id( $id );

$categories = get_the_terms( $id, 'event-category' );
$venue =eo_get_venue_name();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'tab-events__item entry-event' );?>>
  <?php if ( is_search() ) { jeremy_post_banner(); } ?>
	<div class="article__container" role="presentation">
		<header class="entry__header">
			<time class="entry__meta-date" datetime="<?php echo esc_attr( eo_get_the_start( 'c', $id, $occ_id ) ); ?>"><?php echo esc_html( eo_get_the_start( 'M j', $id, $occ_id ) ); ?></time>
			<h3 class="entry__title">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php echo wp_kses_post( get_the_title() ); ?>
				</a>
			</h2>
			<?php if ( $venue ) { ?>
				<span class="entry__meta-venue">
					<?php /* translators: %s is the name of the event venue */
					echo esc_html( sprintf( '@ %s', 'jeremy' ), $venue ); ?>
				</span>
			<?php } ?>
			<?php jeremy_author_tools(); ?>
		</header><!-- .entry__header -->
		<?php echo jeremy_get_the_excerpt(); ?>
	</div><!-- .article__container -->
</article>