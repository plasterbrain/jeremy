<?php
/**
 * The template for displaying the venue page
 *
 * @package Jeremy
 * @subpackage Event Organiser (plug-in)
 */

//Call the template header
get_header(); ?>

<div id="primary" role="main" class="content-area">
	<?php jeremy_breadcrumbs(); ?>
	<!-- Page header, display venue title-->
	<header class="page-header">
		
		<?php $venue_id = get_queried_object_id(); ?>
		
		<h1 class="page-title">
			<?php printf( __( 'Events at %s', 'eventorganiser' ), eo_get_venue_name( $venue_id ) ); ?>
		</h1>
	
		<?php
		if ( $venue_description = eo_get_venue_description( $venue_id ) ) {
			echo '<div class="venue-archive-meta">' . $venue_description . '</div>';
		}
		?>

		<!-- Display the venue map. If you specify a class, ensure that class has height/width dimensions-->
		<?php
		if ( eo_venue_has_latlng( $venue_id ) ) {
			echo eo_get_venue_map( $venue_id, array( 'width' => '100%' ) );
		}
		?>
	
	</header>
	<?php eo_get_template_part( 'events/eo-loop-events' ); //Lists the events ?>

</div><!-- #primary -->

<!-- Call template sidebar and footer -->
<?php get_sidebar(); ?>
<?php get_footer();
