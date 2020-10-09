<?php
/**
 * Shows the content for a single Jeremy Deal post type on a singular view. The
 * article element is a semantic container, while the div immediately inside it
 * is used to style the post content (excluding the footer) in a coupon box.
 *
 * This template is for single post pages. The deal archive page only lists
 * deal post titles, and search results/profile pages pull from the bp_tab file.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */

if ( ! is_single() ) {
	get_template_part( 'partials/bp_tab', 'jeremy_deal' );
	return;
}

$deal_code = get_post_meta( get_the_ID(), '_jeremysfriend_code', true );
$deal_link = get_post_meta( get_the_ID(), '_jeremysfriend_link', true );
$deal_fineprint = get_post_meta( get_the_ID(), '_jeremysfriend_fineprint', true );
$deal_expires = get_post_meta( get_the_ID(), '_jeremysfriend_expiry', true );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-deal' );?>>
	<?php jeremy_entry_byline(); ?>
	
	<div class="entry-deal__coupon">
		<?php jeremy_the_title( array(
			'class' 			=> 'entry__title entry-deal__title',
			'tag_archive' => 'h3',
		) ); ?>
		
		<div class="entry-deal__inner flex">
			<?php if ( has_post_thumbnail() ) { ?>
				<div class="entry__img entry-deal__img" aria-hidden="true" style="background-image: url('<?php echo esc_url( the_post_thumbnail_url() ); ?>'); flex: 0 0 30%;">
				</div>
			<?php } ?>
			<div class="entry__content entry-deal__content">
				<?php if ( is_single() ) { ?>
					<?php the_content(); ?>
					<?php wp_link_pages(); // Eh?! Pages?! ?>
				<?php } else { ?>
					<?php echo jeremy_get_the_excerpt(); ?>
				<?php }?>
				<?php if ( $deal_code ) { ?>
					<p class="entry__meta-code">
						<?php echo esc_html_x( 'CODE:', 'Goes before the coupon code.', 'jeremy' ); ?>
						<?php echo esc_html( $deal_code ); ?></p>
				<?php } ?>
				<?php if ( ( $deal_fineprint && is_single() ) || $deal_expires ) { ?>
					<div class="entry__meta-fineprint" aria-label="<?php esc_attr_e( 'Terms and conditions' ); ?>">
						<?php if ( $deal_fineprint && is_single() ) { ?>
							<?php echo wp_kses_post( $deal_fineprint ); ?>
						<?php } ?>
						<?php if ( $deal_expires ) { ?>
							<?php echo esc_html( sprintf(
								/* translators: %s is the expiration date */
								__( 'Expires %s', 'jeremy' ), date( get_option( 'date_format' ), strtotime( $deal_expires ) ) ) ); ?>
						<?php } ?>
					</div>
				<?php } ?>
				<?php if ( $deal_link ) { ?>
					<div class="entry__meta-button">
						<a href="<?php echo esc_url( $deal_link ); ?>" class="button button-deal"><?php esc_html_e( 'Get deal', 'jeremy' ); ?></a>
					</div>
				<?php } ?>
			</div><!-- .entry-deal__content -->
		</div><!-- .entry-deal__inner -->
	</div><!-- .entry-deal__coupon -->
	
	<?php get_template_part( 'partials/content_footer' ); ?>
</article>
