<?php
/**
 * Single Post Footer
 * 
 * A block of functions and formatting that goes at the bottom of various types
 * of posts in singular displays. By modifiying this template, you can edit how
 * the footer appears across every kind of singular post template. WOW!
 *
 * Most of this template is for the "related posts" section, so it looks more
 * complicated than it actually is.
 *
 * @TODO Let users choose which post types get a "more by author" block
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 2.0.0
 */

$post_type = get_post_type();
$post_class = esc_attr( '-' . $post_type );
$post_plural = strtolower( get_post_type_object( $post_type )->label );
?>
		<hr class="entry__divider">
		<footer class="entry__footer entry__footer<?php echo $post_class; ?>">
			<?php jeremy_author_tools(); ?>
			<?php jeremy_entry_meta(); ?>
			<?php jeremy_sharemy(); ?>
			
			<?php
			if ( get_theme_mod( 'use_post_nav', true ) && $post_type !== 'page' ) { ?>
				<?php if ( $post_type === 'post' ) { 
					$postnav_title = _x( 'Read More',
						'Title for next/previous post navigation', 'jeremy' );
					$postnav_desc = get_theme_mod( 'postnav-desc',
						__( 'What else is happening?', 'jeremy' ) );
						
					$has_next = is_a( get_next_post(), 'WP_Post' );
					$has_prev = is_a( get_previous_post(), 'WP_Post' );
					$should_bother = $has_next || $has_prev;
					$ul_class = 'class="postnav__list nav__list nav__list-h flex"';
				} else {
					$postnav_title = sprintf(
						/* translators: "More [posts] by [Author]" */
						__( 'More %1$s by %2$s', 'jeremy'), $post_plural, get_the_author());
					$postnav_desc = '';
					
					$related = new WP_Query( array(
						'posts_per_page'   => 3,
						'offset'           => 0,
						'post_type'        => $post_type,
						'post_mime_type'   => '',
						'post_parent'      => '',
						'post__not_in'     => array( get_the_ID() ),
						'author'	         => get_the_author_meta( 'ID' ),
						'post_status'      => 'publish',
						'suppress_filters' => false,
						'showpastevents'	 => 0, // For Event Organiser
					) );
					$should_bother = $related->have_posts();
					$ul_class = '';
				}
				if ( $should_bother ) { ?>
					<div class="entry__footer-postnav entry__footer-postnav<?php echo $post_class ;?>">
						<h3 class="postnav__title"><?php esc_html_e( $postnav_title);?></h3>
						<p class="postnav__desc"><?php esc_html_e( $postnav_desc ); ?></p>
						
						<ul <?php echo $ul_class; ?>>
							<?php if ( $post_type === 'post' ) {
                // Regular posts, showing adjacent entries. ?>
								<?php if ( $has_prev ) { ?>
									<li>
                    <?php previous_post_link( '%link','&#10094; %title' );?>
                  </li>
                <?php } ?>
                <?php if ( $has_next ) { ?>
                  <li>
                    <?php next_post_link( '%link', '%title &#10095;' ); ?>
                  </li>
                <?php } ?>
							<?php } else { ?>
								<?php while ( $related->have_posts() ) {
									$related->the_post();
                  // Custom post types, showing more by same author. ?>
									<li>
										<a href="<?php the_permalink(); ?>">
											<?php the_title(); ?>
										</a>
									</li>
								<?php } wp_reset_postdata(); ?>
							<?php } // endif $post_type check ?>
						</ul>
				  </div><!-- .entry__footer-postnav -->
			  <?php } // endif $should_bother ?>
      <?php } // endif get_theme_mod() ?>
	  </footer><!-- .entry__footer -->