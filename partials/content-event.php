<?php
/**
 * Post Type - Event | Archive Views
 * 
 * Used to show each event in an archive view. Note that this template is *not*
 * used for single event pages, which are handled by single-event.php.
 *
 * For whatever reason EO can't get the occurrence IDs properly in certain
 * contexts (like global site search) so they have to be explicitly provided.
 *
 * @package Jeremy
 * @subpackage Events
 * @since 2.0.0
 *
 * Changelog:
 * 2.0.0 - Add to Calendar button!
 */
 
if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
	get_template_part( 'partials/content' );
	return;
}

$id = get_the_ID();
$occ_id = jeremy_eo_get_next_occurrence_id( $id );

$categories = get_the_terms( $id, 'event-category' );

$format_day = esc_attr_x(
	'l, F jS, Y',
	'PHP date format for events in an archive listing (day only)',
	'jeremy' );
$format_time = esc_attr_x(
	'g:ia',
	'PHP date format for events in an archive listing (time only)',
	'jeremy' );
?>

<?php if ( is_search() ) { jeremy_post_banner(); } ?>

<article id="event-<?php the_ID(); ?>" <?php post_class( 'archive-events__item entry-event flex'); ?>>
	<?php if ( has_post_thumbnail() ) { ?>
		<?php the_post_thumbnail( 'post-thumbnail', array(
			'class' => 'entry__thumb entry-event__thumb'
		) ); ?>
	<?php } else { ?>
		<div class="entry__thumb" role="presentation">
			<?php echo eo_get_the_start( 'M \<\b\r \/\> j', $id, $occ_id ); ?>
		</div>
	<?php }?>
	<div class="article__container" role="presentation">
		<header class="entry__header">
			<h2 class="entry__title">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php echo wp_kses_post( get_the_title() ); ?>
				</a>
			</h2>
			<?php jeremy_author_tools(); ?>
			<p class="entry__meta-date">
				<?php 
				if ( jeremy_eo_cinderella( $id, $occ_id ) !== null ) {
					/* translators: "[date] from [time range]," e.g. "from 10pm-11pm" */
					printf( esc_html__( '%1$s from %2$s', 'jeremy' ), eo_get_the_start( $format_day, $id, $occ_id ), eo_format_event_occurrence( $id, $occ_id, '', $format_time, ' &ndash; ', false ) );
				} else { 
					echo eo_format_event_occurrence( $id, $occ_id, $format_day, $format_time, ' &ndash; ', false );
				} ?></p>
			<p class="entry__meta-venue"><?php echo eo_get_venue_name(); ?></p>
		</header><!-- .entry__header -->
		<?php echo jeremy_get_the_excerpt( array(
			'link' => false,
		) ); ?>
		<footer class="entry__footer flex flex-wrap-m">
			<?php if ( $categories && ! is_wp_error( $categories ) ) { ?>
				<ul class="nav__list nav__list-h">
				<?php foreach ( $categories as $cat ) { ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="entry__meta-terms__item" style="background: <?php echo esc_attr( eo_get_category_color( $cat->term_id ) ); ?>">
							<?php echo esc_html( $cat->name ); ?>
						</a>
					</li>
				<?php } ?>
				</ul>
			<?php } else { ?>
				<div role="presentation"></div>
			<?php } ?>
			<?php jeremy_addtocal_button(
				jeremy_eo_get_addtocal_links( $id, $occ_id ) ); ?>
		</footer>
	</div><!-- .article__container -->
</article>