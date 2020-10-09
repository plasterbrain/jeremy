<?php
/**
 * The template for displaying archive pages
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */

$author = get_query_var( 'author' );
$contact1 = sanitize_text_field( get_theme_mod( 'bp_profile_contact1' ) );
$contact2 = sanitize_text_field( get_theme_mod( 'bp_profile_contact2' ) );
$address = sanitize_text_field( get_theme_mod( 'bp_profile_address' ), false );

get_header(); ?>

	<article class="main__page">
		<?php jeremy_breadcrumbs(); ?>
		
		<header class="page__header">
			<?php the_archive_title( '<h1 class="page__title">', '</h1>' ); ?>
			<?php if ( $author ) {
				if ( function_exists( 'buddypress' ) && $address ) { ?>
					<div class="entry__meta flex coolbox" role="presentation">
						<a href="<?php echo esc_url( jeremy_get_author_link( $author ) ); ?>">
							<?php echo get_avatar( $author, 96, 'mystery', '', array(
								'class' => 'entry__meta-avatar'
							) ); ?>
						</a>
						<div id="entry-byline" class="entry__meta__inner" role="presentation">
							<?php
								jeremy_bp_field( array(
									'field' => $address,
									'class' => 'entry__meta-address',
								), $author );
								jeremy_bp_field( array(
									'field' => $contact1,
									'class' => 'entry__meta-contact1',
								), $author );
								jeremy_bp_field( array(
									'field' => $contact2,
									'class' => 'entry__meta-contact2',
								), $author );
							?>
						</div>
					</div>
				<?php }
			} ?>
			<p class="archive__description archive__description-jobs">
				<?php the_archive_description(); ?>
			</p>
		</header><!-- .page-header -->

		<?php
		if ( is_post_type_archive( array( 'jeremy_job', 'jeremy_deal' ) ) ) {
			if ( $author ) {
				$post_type = get_query_var( 'post_type' );
				$posts = get_posts( array(
          'post_type' => $post_type,
          'author' 		=> $author,
					'paged' 		=> get_query_var( 'paged' ),
        ) );
				if ( $posts ) {
					foreach ( $posts as $post ) {
						get_template_part( 'partials/bp_tab', $post_type );
					}
					echo jeremy_paginate_links();
				} else {
					/* translators: %s is the plural of the post type, e.g. "events" */
					echo '<p>' . esc_html( sprintf( __( "This user hasn't posted any %s yet.", 'jeremy' ), strtolower( get_post_type_object( $post_type )->label ) ) ) . '</p>';
				}
			} else {
				get_template_part( 'partials/archive', 'authorgrid' );
			}
		} elseif ( have_posts() ) { ?>
			<div class="archive" role="presentation">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'partials/content', get_post_type() ); ?>
				<?php endwhile; ?>
			
				<?php echo jeremy_paginate_links(); ?>
			</div>
		<?php } else { ?>
			<p class="archive__none">
				<?php esc_html_e( 'Sorry, there are no posts to show.', 'jeremy' ); ?>
			</p>
		<?php } ?>
	</article><!-- .main__page -->

	<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();