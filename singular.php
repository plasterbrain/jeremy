<?php
/**
 * Singular View
 * 
 * The template for displaying all single posts and pages.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */

get_header(); ?>
	<div class="main__page" role="presentation">
		<?php if ( ! is_front_page() ) jeremy_breadcrumbs(); ?>
	
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'partials/content', get_post_type() ); ?>

			<?php if ( comments_open() || get_comments_number() ) { ?>
				<?php comments_template(); ?>
			<?php } ?>
		<?php endwhile; ?>
	</div><!-- .main__page -->
	
	<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();
