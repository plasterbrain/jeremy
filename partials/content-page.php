<?php
/**
 * Template partial for displaying page content.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
?>
<article <?php post_class(); ?>>
	<?php if ( ! is_singular() ) {
		jeremy_post_banner();
	} ?>
	<?php if ( has_post_thumbnail() ) { ?>
		<?php the_post_thumbnail( 'post-thumbnail', array(
			'class' => 'entry__thumb entry-event__thumb'
		) ); ?>
	<?php } ?>
	<?php if ( ! is_front_page() ) jeremy_the_title(); ?>
	
	<div class="entry__content" role="presentation">
		<?php if ( is_singular() ) {
			the_content();
		} else {
			echo jeremy_get_the_excerpt();
		} ?>

		<?php wp_link_pages( array(
			'before' => '<div class="nav-pages nav__list nav__list-h">' . esc_html__( 'Pages:', 'jeremy' ),
			'after'  => '</div>',
		) ); ?>
	</div><!-- .entry__content -->
	
	<?php if ( is_singular() ) {?>
		<?php get_template_part( 'partials/content_footer' ); ?>
	<?php } // Endif is_single() ?>
</article><!-- #post-<?php the_ID(); ?> -->
