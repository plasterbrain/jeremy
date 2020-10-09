<?php
/**
 * Post Type - Event | Singular View
 * 
 * Displays a single event post page. It's a whole template because the sidebar
 * is used to show post meta instead of widgets. This template (and theme) is
 * intended for use with Event Organiser by Stephen Harris.
 *
 * By adding a custom meta with the key name "link" to an event post, you can
 * generate an RSVP button on singular views.
 *
 * @package Jeremy
 * @subpackage Events
 * @since 2.0.0
 */

get_header(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'main__page-noflex entry-event' );?>>
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="main__page-noflex__content" role="presentation">
				<?php jeremy_breadcrumbs(); ?>
				<header class="entry__header">
					<?php the_title( '<h1 class="entry__title">', '</h1>'); ?>
					
					<?php jeremy_entry_byline( true ); ?>
				</header><!-- .entry__header -->
				
				<?php the_content(); ?>
				<?php wp_link_pages(); // Why does your event have pages tho ?>
			</div>
		<?php endwhile; ?>
	
		<div role="presentation" class="main__page-noflex__footer">
			<?php get_template_part( 'partials/content_footer' ); ?>
		</div>
		
		<?php if ( defined( 'EVENT_ORGANISER_VER' ) ) {
			get_template_part( 'partials/sidebar', 'event' ); ?>
		<?php } else { ?>
			<div role="presentation"></div>
		<?php } ?>
	
	</article><!-- .main__page -->
</main><!-- #main -->

<?php get_footer();
