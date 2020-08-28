<?php
/**
 * The template for displaying single events.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
$using_eo = function_exists ( 'eo_insert_event' ) ?: False;
get_header(); ?>
	<div id="primary" class="content-area">
		<?php jeremy_breadcrumbs(); ?>
		<main id="main" class="site-main">
			<?php
			while ( have_posts() ) : the_post();
				if ( $using_eo ) {
					get_template_part( 'events/content-event' );
				} else {
					get_template_part( 'template-parts/content', 'event' );
				}
			endwhile; // End of the loop.
			?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php
if ( $using_eo ) {
	get_template_part( 'events/sidebar-event' );
}
else {
	get_sidebar();
}
get_footer();
