<?php
/**
 * Default template partial for displaying post content.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */

$sticky = is_sticky();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry__header">
		<?php if ( $sticky || ( ! is_single() && ! is_author() && ! is_home() ) ) jeremy_post_banner( $sticky ? 'sticky' : null ); ?>
		
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="entry__thumb" style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url() ); ?>');" role="presentation"></div>
		<?php } ?>
		
		<?php jeremy_the_title( array(
			'tag_archive' => 'h2',
		) ); ?>
		<?php jeremy_entry_byline( true ); ?>
	</header><!-- .entry__header -->
	<div class="entry__content">
		<?php
		if ( is_singular() ) {
			the_content();
			wp_link_pages();
		} else {
			if ( ! has_excerpt() ) {
				echo jeremy_get_the_excerpt( array( 'length' => 50 ) );
			} else {
				the_excerpt();
				printf( ' <p class="read-more"><a href="%s" rel="bookmark">' . __( 'Read more', 'jeremy' ) . '</a></p>', eo_get_permalink() );
			}
		}
		?>
	</div><!-- .entry__content -->
	<?php if ( is_singular() ) {?>
		<?php get_template_part( 'partials/content_footer' ); ?>
	<?php } // Endif is_single() ?>
</article><!-- #post-<?php the_ID(); ?> -->