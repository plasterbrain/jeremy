<?php
/**
 * The template for displaying single deal pages.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
get_header(); ?>

	<div id="primary" class="content-area archive-deal">
		<?php jeremy_breadcrumbs(); ?>
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', 'jeremy_deal' );
		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php
get_sidebar( 'posts' );
get_footer();
