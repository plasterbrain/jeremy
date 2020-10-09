<?php
/**
 * Shows the content for a single Jeremy Job post type in the loop.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0
 * 	- Added some necessary escape wrappers
 * 	- Footer is now at the bottom
 * 	- Post title no longer linked on single display
 */

// Post meta URL value used to show an "apply for this job" link.
$apply_link = get_post_meta( get_the_ID(), '_jeremysfriend_link', true );
/* translators: %s is the title of the job listing post. */
$apply_alt = sprintf( __( 'Apply for %s online', 'jeremy' ), get_the_title() );
$apply_link_data = parse_url( $apply_link, PHP_URL_SCHEME );
if ( $apply_link_data === false ) {
	$apply_link = false;
} elseif ( $apply_link_data === 'mailto' ) {
	/* translators: %1$s is the user who posted the job listing, %2$s is the title of the job listing post. */
	$apply_alt = sprintf( __( 'Email %1$s about the %2$s job listing', 'jeremy' ), get_the_author(), get_the_title() );
}
?>

<article class="single-job">
	<?php jeremy_post_banner(); ?>
	<header class="entry__header entry-job__header">
		<?php jeremy_the_title(); ?>
		<?php if ( is_single() ) { ?>
			<?php jeremy_entry_byline( true ); ?>
		<?php } ?>
	</header><!-- .entry__header -->

	<div class="entry__content entry-job__content">
		<?php if ( is_single() ) { ?>
			<?php the_content(); ?>
			<?php wp_link_pages(); // Eh?! Pages?! ?>
			<?php if ( $apply_link ) { ?>
				<div class="entry__meta-button">
					<a aria-label="<?php echo esc_attr( $apply_alt ); ?>" class="button" href="<?php echo esc_url( $apply_link ); ?>"><?php esc_html_e( 'Apply', 'jeremy' ); ?></a>
				</div>
			<?php } ?>
		<?php } else { // Archive listing ?>
			<?php echo jeremy_get_the_excerpt(); ?>
		<?php } ?>
	</div><!-- .entry-job__content -->
	
	<?php if ( is_single() ) {?>
		<?php get_template_part( 'partials/content_footer' ); ?>
	<?php } // Endif is_single() ?>
</article>
