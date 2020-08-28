<?php
/**
 * The template for displaying lists of events
 *
 * @package Jeremy
 * @subpackage Event Organiser (plug-in)
 */

get_header(); ?>

<div id="primary" role="main" class="content-area">
	<?php jeremy_breadcrumbs(); ?>
	<header class="page-header">
		<h1 class="page-title">
		<?php
		if ( eo_is_event_archive( 'day' ) ) {
			//Viewing date archive
			echo __( 'Events on ','jeremy' ) . ' ' . eo_get_event_archive_date( 'jS F Y' );
		} elseif ( eo_is_event_archive( 'month' ) ) {
			//Viewing month archive
			echo __( 'Events in ','jeremy' ) . ' ' . eo_get_event_archive_date( 'F Y' );
		} elseif ( eo_is_event_archive( 'year' ) ) {
			//Viewing year archive
			echo __( 'Events in ', 'jeremy' ) . ' ' . eo_get_event_archive_date( 'Y' );
		} else {
			_e( 'Events', 'jeremy' );
		}
		?>
		</h1>
		<p><?php printf( '<a href="%s">' . __( 'Download iCalendar', 'jeremy' ) . '</a></span>', esc_url( eo_get_events_feed() ) ); ?></p>
	</header>
	<?php eo_get_template_part( 'events/eo-loop-events' ); //Lists the events ?>
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer();
