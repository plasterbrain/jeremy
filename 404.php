<?php
/**
 * 404 (Not Found) Page
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Removed breadcrumbs, because why even.
 */

// Change the title depending on requested URL.
global $wp;
$cpts = array(
  /* @TODO Find a better way of handling this that coordinates with plug-in */
	_x( '/deals/', 'Deal post type slug', 'jeremy' ) 			 =>
		_x( 'deal', 'Singular of special offer post type, used in 404 page description', 'jeremy' ),
	_x( '/jobs/', 'Job listing post type slug', 'jeremy' ) =>
		_x( 'job', 'Singular of job listing post type, used in 404 page description', 'jeremy' )
);
if ( function_exists( 'eventorganiser_get_option' ) ) {
	$cpts[eventorganiser_get_option( 'url_event' )] =
		_x( 'event', 'Singular of event post type, used in 404 page description', 'jeremy' );
}
// BuddyPress Profile
if ( function_exists( 'jeremy_bp_get_component_link' ) ) {
  $cpts[jeremy_bp_get_component_link()] =
    _x( 'member', 'Single site user, used in 404 page description', 'jeremy' );
}

$requested_type = _x( 'page', 'Singular of WordPress "page" post type, used in the 404 page description', 'jeremy' );

foreach ( $cpts as $slug => $type ) {
  if ( strpos( $wp->request, sanitize_title( $slug ) ) !== false ) {
    $requested_type = $type;
    break;
  }
}

get_header(); ?>

	<div class="page__content">
	<h1 class="page__title page-404__title">
		<?php esc_html_e( 'Sorry!', 'jeremy' ); ?>
	</h1>
	<h2 class="page__subtitle page-404__subtitle">
		<?php /* translators: %s is a singular version of a post type, e.g. "page */
    echo esc_html( sprintf( __( 'The %s you are looking for may have been moved or deleted.', 'jeremy' ), $requested_type ) ); ?>
	</h2>
	<p><?php esc_html_e( 'A search might point you in the right direction.', 'jeremy' ); ?></p>
	
	<?php get_search_form(); ?>
	</div><!-- .page-content -->
</main><!-- #main -->
<?php get_footer();
