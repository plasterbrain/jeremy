<?php
/**
 * BuddyPress Profile - Deal Loop
 *
 * Used to show each deal post item on a user's profile page.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 2.0.0
 */

$deal_code = get_post_meta( get_the_ID(), '_jeremysfriend_code', true );
$deal_link = get_post_meta( get_the_ID(), '_jeremysfriend_link', true );
$deal_expires = get_post_meta( get_the_ID(), '_jeremysfriend_expiry', true );

?>

<?php if ( is_search() ) { jeremy_post_banner(); } ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'tab-deals__item entry-deal' );?>>
	<div class="entry-deal__header" role="presentation">		
		<div class="entry-deal__header__inner flex flex-wrap-m" role="presentation">
			<h3 class="entry__title entry-deal__title">
				<a href="<?php echo esc_url( get_the_permalink() ); ?>">
					<?php echo wp_kses_post( get_the_title() ); ?>
				</a>
			</h3>
			<?php if ( $deal_code ) { ?>
				<p class="entry__meta-code">
					<?php echo esc_html_x( 'CODE:', 'Goes before the coupon code.', 'jeremy' ); ?>
					<?php echo esc_html( $deal_code ); ?></p>
			<?php } ?>
		</div>
	</div>
		
	<?php echo jeremy_get_the_excerpt( array(
		'readmore' => esc_html__( 'See this deal', 'jeremy' )
	) ); ?>
</article>
