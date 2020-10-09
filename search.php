<?php
/**
 * Search Results
 * 
 * The template for displaying search results pages.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
 
$has_search = true;
if ( get_search_query() === '' ) {
	$has_search = false;
	$search_title = __( 'Search', 'jeremy' );
} else {
	/* translators: %s is the search query. */
	$search_title = sprintf( __( 'Search Results for "%s"', 'jeremy' ), '<span>' . get_search_query() . '</span>' );
}

get_header();
?>
	<div class="main__page" role="presentation">
		<?php jeremy_breadcrumbs(); ?>
		
		<h1 class="page__title page-search__title">
			<?php echo wp_kses_post( $search_title ); ?>
		</h1>
		
		<?php get_search_form(); ?>
		
		<?php if ( $has_search ) { ?>
			<?php if ( function_exists( 'buddypress' ) ) { ?>
				<section aria-labelledby="search__title-members" class="search__members">
					<h2 id="search__title-members">
						<?php esc_html_e( 'Members', 'jeremy' ); ?>
					</h2>
					<?php get_template_part( 'community/members/members-loop' ); ?>
				</section>
				<hr>
			<?php } ?>
			
			<section aria-labelledby="search__title-posts" class="search__posts">
				<h2 id="search__title-posts">
					<?php esc_html_e( 'Posts', 'jeremy' ); ?>
				</h2>
				<?php if ( have_posts() ) { ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php
						$post_type = get_post_type();
						if ( $post_type === 'event' && ! defined( 'EVENT_ORGANISER_VER' ) ) {
							$post_type = 'post';
						}
						get_template_part( 'partials/content', $post_type ); ?>
					<?php endwhile; ?>

					<?php jeremy_posts_navigation(); ?>
				<?php } else { ?>
					<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'jeremy' ); ?></p>
					
					<?php get_search_form(); ?>
				<?php } ?>
			</section>
		<?php } ?>
	</div><!-- .main__page -->
	
	<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();

