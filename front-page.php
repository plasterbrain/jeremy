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

$post_format = get_post_format() ?: 'page';
$sticky_post = array();

get_header(); ?>
	<div class="main__page" role="presentation">
		<?php if ( ! is_front_page() ) jeremy_breadcrumbs(); ?>
	
		<?php if ( have_posts() ) { ?>
			<?php if ( is_home() && ! is_front_page() ) { ?>
				<h1 class="page__title"><?php single_post_title(); ?></h1>
			<?php } else { ?>
				<h2 class="page__title"><?php bloginfo( 'description' ); ?></h2>
			<?php } ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php if ( ! is_home() ) { ?>
	        <?php $posts = get_posts( array(
						'numberposts' 				=> 1,
	          'post_type' 					=> 'post',
						'post__in' 						=> get_option( 'sticky_posts' ),
						'ignore_sticky_posts' => 1
	        ) );
					
					if ( $posts ) {
						foreach ( $posts as $post ) {
							$permalink = get_the_permalink();
							$sticky_post[] = $post->ID; ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-post' );?>>
								<?php if ( has_post_thumbnail() ) { ?>
									<a href="<?php echo esc_url( $permalink ); ?>">
										<div class="entry__thumb" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url() ); ?>');" role="presentation"></div>
									</a>
								<?php } ?>
								<h3 class="entry__title">
									<a href="<?php echo esc_url( $permalink ); ?>">
										<?php echo get_the_title( $post ); ?>
									</a>
								</h3>
								<p class="entry__meta-time"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date( '', $post ); ?></time></p>
								<?php echo jeremy_get_the_excerpt( array(
									'length' 	 => 30,
								) ); ?>
							</article>
						<?php }
					} // endif $posts
				} // endif ! is_home()
	      wp_reset_postdata(); ?>
				
				<?php get_template_part( 'partials/content', $post_format ); ?>
				<?php if ( ! is_home() ) { ?>
	        <?php $posts = get_posts( array(
						'numberposts' 				=> 3,
	          'post_type' 					=> 'post',
						'post__not_in'				=> $sticky_post,
	        ) );
					if ( $posts ) { ?>
						<div class="home__grid" role="presentation">
							<?php foreach ( $posts as $post ) {
								$author = '<a href="' . esc_url( jeremy_get_author_link( $post->post_author ) ) . '">' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</a>'; ?>
								<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-post' );?>>
									<header class="entry__header">
										<time class="entry__meta-time" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
										<h3 class="entry__title">
											<a href="<?php echo esc_url( get_the_permalink() ); ?>">
												<?php echo get_the_title( $post ); ?>
											</a>
										</h3>
										<div class="entry__meta" role="presentation">
											<p class="entry__meta-author">
												<?php echo wp_kses_post( sprintf( __( 'Posted by %s', 'jeremy' ), $author ) ); ?>
											</p>
										</div>
									</header>
									<?php echo jeremy_get_the_excerpt(); ?>
								</article>
							<?php } ?>
						</div>
					<?php }
				wp_reset_postdata();
			} // endif ! is_home()
			endwhile; ?>
			
		<?php } ?>
	</div><!-- .main__page -->
	
	<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();
