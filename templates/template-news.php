<?php
/**
 * Template Name: News Page
 * Description: Display news on the homepage.
 *
 * @subpackage Jeremy
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php while ( have_posts() ) : the_post();
			the_content();
		endwhile;
		if ( get_edit_post_link() ) edit_post_link(__( 'Edit', 'jeremy' ), '<p class="edit-link">', '</p>');
		?>
		<div class="news">
			<?php echo '<h2>' . __( "What's new", 'jeremy' ) . '</h2>';
			$news = new WP_Query( array(
				'posts_per_page' => 5,
			) );
			if ( $news->have_posts() ) {
				while ( $news->have_posts() ) : $news->the_post();
				$class = is_sticky() ? 'class="sticky"' : '';?>
					<article <?php echo $class;?>>
						<a href="<?php the_permalink(); ?>"><?php the_title( '<h3 class="entry-title">', '</h3>' ); ?></a>
						<?php printf( '<time class="fancy" datetime="%s">' . __( 'on %s', 'jeremy' ) . '</time>', get_the_date( 'c' ), get_the_date() );?>
						<div class="entry-content"><?php echo jeremy_get_the_excerpt( array( 'length'=>30 ) ); ?></div>
					</article>
				<?php endwhile;
				wp_reset_postdata();
			} else {
				_e( 'There are no news posts yet. Check back soon!', 'jeremy' );
			} ?>
		</div>
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
