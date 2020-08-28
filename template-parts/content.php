<?php
/**
 * Default template partial for displaying post content.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
?>
<article>
	<header class="entry-header">
		<?php
		if ( is_singular() ) {
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'post-thumbnail', array( 'class' => 'featured-img' ) );
			} else {
				printf( '<img src="%s" alt="">', get_theme_mod( 'default-featured', esc_url( get_template_directory_uri() . '/assets/jpg/default-featured.jpg' ) ) );
			}
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			echo '<a href="' . get_permalink() . '" rel="bookmark"><h2 class="entry-title">' . get_the_title() . '</h2></a>';
		}
		if ( 'post' === get_post_type() ) { ?>
		<div class="entry-meta">
			<?php jeremy_entry_meta(); ?>
		</div>
		<?php } ?>
	</header><!-- .entry-header -->
	<div class="entry-content">
		<?php
		if ( is_singular() ) {
			the_content();
			wp_link_pages();
		} else {
			if ( ! has_excerpt() ) {
				echo jeremy_get_the_excerpt( array( 'length' => 50, 'readmore' => true ) );
			} else {
				the_excerpt();
				printf( ' <p class="read-more"><a href="%s" rel="bookmark">' . __( 'Read more', 'jeremy' ) . '</a></p>', eo_get_permalink() );
			}
		}
		?>
	</div><!-- .entry-content -->
	<?php if ( is_singular() ) { ?>
		<hr />
		<footer class="entry-footer">
			<?php jeremy_entry_footer(); ?>
			<?php if ( get_theme_mod( 'use_post_nav', true ) ) { ?>
				<h3><?php _ex( 'Read More', 'Heading for next/previous post navigation', 'jeremy' ); ?></h3>
				<div class="nextprev-navigation flex">
					<?php
					previous_post_link('%link', '&lt; %title');
					next_post_link('%link', '%title &gt;');
					?>
				</div>
			<?php } ?>
		</footer>
	<?php } ?>
</article>