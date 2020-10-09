<?php
/**
 * The template for displaying lists of events. This template is intended to be
 * used with the Event Organiser plug-in.
 *
 * @TODO There's no current way outside of Javascript IIRC to limit the next
 * previous month buttons to a certain month range for year archives
 * @TODO test if plug-in not installed but CPT named 'event' exists
 *
 * @package Jeremy
 * @subpackage Events
 * @since 1.0.0
 */

if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
	// *Gordon Ramsay voice* Get out of here.
	get_template_part( 'archive' );
}

$use_calendar = true;
$calendar_args = array(
	'headerleft' 	 => '',
	'headerright'  => '',
	'headercenter' => 'prev title next',
	'tooltip'			 => false,
);

$use_maps = false;

$archive_none = __( 'There are no events to show.', 'jeremy' );

switch ( true ) {
	case is_tax( 'event-category' ):
		$cat = get_queried_object();
		
		/* translators: %s is the name of the event category */
		$archive_title = sprintf( __( '%s Events', 'jeremy' ), $cat->name );
		
		//$feed = eo_get_event_category_feed( $cat->slug );
		
		$calendar_args['event-category'] = esc_js( $cat->slug );
		
		$archive_none = __( 'There are no upcoming events in this category.', 'jeremy' );
		break;
	case is_tax( 'event-venue' ):
		$venue = get_queried_object();
		
		/* translators: %s is the name of an event venue */
		$archive_title = sprintf( __( 'Events @ %s', 'jeremy' ),  $venue->name );
		
		//$feed = eo_get_event_venue_feed( $venue->term_id );
		
		$calendar_args['event-venue'] = esc_js( $venue->slug );
		
		$use_maps = eo_venue_has_latlng( $venue->term_id );
		
		$archive_none = __( 'There are no upcoming events at this location.', 'jeremy' );
		break;
	case eo_is_event_archive( 'day' ):
		$date = eo_get_event_archive_date( DATETIMEOBJ );
		
		/* translators: %s is the given day for a day-based archive */
		$archive_title = sprintf( __( 'Events on %s','jeremy' ), $date->format( esc_attr_x( 'F jS, Y', 'PHP date format used in the page title for single day-based event archives.', 'jeremy' ) ) );

		/*$feed = jeremy_eo_get_feed_link( null, array(
			'year' 	=> $date->format( 'Y' ),
			'month' => $date->format( 'n' ),
			'day' 	=> $date->format( 'j' ),
		) );*/
		
		$use_calendar = false;
		/* If you have a lot of events every day, you may want to do this:
		$calendar_args['month'] = eo_get_event_archive_date( 'n' );
		$calendar_args['year'] = eo_get_event_archive_date( 'Y' );
		$calendar_args['date'] = eo_get_event_archive_date( 'd' );
		$calendar_args['defaultview'] = 'basicDay';
		*/
		break;
	case eo_is_event_archive( 'month' ):
		$date = eo_get_event_archive_date( DATETIMEOBJ );
		
		/* translators: %s is the month (e.g. January 2020) for a month archive. */
		$archive_title = sprintf( __( 'Events in %s', 'jeremy' ), $date->format( esc_attr_x( 'F Y', 'PHP date format used in the page title for month-based event archives.', 'jeremy' ) ) );
		
		$calendar_args['month'] = eo_get_event_archive_date( 'n' );
		$calendar_args['year'] = eo_get_event_archive_date( 'Y' );
		$calendar_args['headercenter'] = 'title';
		break;
	case eo_is_event_archive( 'year' ):
		$date = eo_get_event_archive_date( DATETIMEOBJ );
		
		/* translators: %s is the year for a year-based archive. */
		$archive_title = sprintf( __( 'Events in %s', 'jeremy' ), eo_get_event_archive_date( 'Y' ) );
		
		$calendar_args['year'] = eo_get_event_archive_date( 'Y' );
		break;
	case is_author():
		/* translators: %s is the name of the post author */
		$archive_title = sprintf( __( "%s's Events", 'jeremy' ), get_the_author_meta( 'display_name', get_query_var( 'author' ) ) );
		
		$use_calendar = false;
		break;
	default:
		$archive_title = __( 'Events', 'jeremy' );		
		$archive_none = __( 'There are no upcoming events.', 'jeremy' );
}

$use_calendar = $use_calendar && get_theme_mod( 'use_calendar', true );

get_header(); ?>

	<article class="main__page">
		<?php jeremy_breadcrumbs(); ?>
		
		<h1 class="page__title"><?php echo esc_html( $archive_title ); ?></h1>
		
		<?php if ( $use_maps ) {
			if ( get_theme_mod( 'use_maps' ) ) {
				echo eo_get_venue_map( $venue->term_id, array(
					'width' => '100%'
				) );
			} else { ?>
				<p>
					<?php echo esc_html( jeremy_eo_get_address( $venue->term_id ) ); ?>
				</p>
			<?php }
		} ?>
		
		<?php if ( $use_calendar && ! wp_is_mobile() ) {
			echo eo_get_event_fullcalendar( $calendar_args );
		} ?>
		
		<div class="archive__meta-button" role="presentation">
			<?php jeremy_feed_button( true ); // ICS ?>
			<?php jeremy_feed_button( false ); // RSS ?>
		</div>

		<?php if ( have_posts() ) { ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php eo_get_template_part( 'partials/content', 'event' ); ?>
			<?php endwhile; ?>
			
			<?php echo jeremy_paginate_links(); ?>
		<?php } else { ?>
			<p><?php echo esc_html( $archive_none ); ?></p>
		<?php } ?>
	</article><!-- .main__page -->

	<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();
