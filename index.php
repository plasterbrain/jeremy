<?php
/**
 * General Template
 *
 * This is the most generic template file in a WordPress theme and one of the
 * two required files for a theme (the other being style.css). It is used to
 * display a page when nothing more specific matches a query.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Semantic layout has been simplified.
 */

get_header(); ?>
	<div class="main__page" role="presentation">
		<?php if ( ! is_front_page() ) jeremy_breadcrumbs(); ?>
	
		<?php if ( have_posts() ) { ?>
			<?php if ( is_home() && ! is_front_page() ) { ?>
				<h1 class="page__title"><?php single_post_title(); ?></h1>
			<?php } ?>
			
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part(
					'partials/content', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php echo jeremy_paginate_links(); ?>
			
		<?php } else { ?>
			<h1 class="page__title">
				<?php esc_html_e( 'Nothing Found', 'jeremy' ); ?>
			</h1>
			
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) { ?>
				<p><?php
					/* translators: 1: link to WP admin new post page. */
					echo wp_kses_post( sprintf(
						__( 'Ready to publish your first post? <a href="%1$s" aria-label="Create a new post">Get started here</a>.', 'jeremy' ),
						esc_url( admin_url( 'post-new.php' ) )
					) ); ?>
				</p>
			<?php } else { ?>
				<p><?php esc_html_e( "It seems we can't find what you're looking for. Perhaps a search will help.", 'jeremy' ); ?></p>
				
				<?php get_search_form(); ?>
			<?php } ?>
		<?php } ?>
	</div><!-- .main__page -->
	
	<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();
