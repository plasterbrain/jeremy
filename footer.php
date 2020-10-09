<?php
/**
 * Displays the site footer.
 * 
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Twitter/Facebook theme options replaced by new Footer Menu.
 */

$preview = is_customize_preview();

$credits_toggle = get_theme_mod(
	'show_theme_credits', true ) ? '' : 'display:none;';
$poweredby_toggle = get_theme_mod(
	'show_powered_by', true ) ? '' : 'display:none;';
?>
	<footer id="colophon" class="site__footer color3-overlay">
		<div class="footer__widgets inner flex flex-wrap-m" role="complementary" aria-label="<?php esc_attr_e( 'Site footer', 'jeremy' ); ?>">
			<?php dynamic_sidebar( 'sidebar-footer' ); ?>
		</div>

		<div class="footer__bottom__container color4-bg">
			<div class="footer__bottom inner flex flex-wrap-m">
				<?php if ( has_nav_menu( 'footer' ) ) { ?>
					<nav class="footer__bottom__nav nav-footer" aria-label="<?php esc_attr_e( 'Footer menu', 'jeremy' ); ?>">
						<?php
							wp_nav_menu( array(
								'menu_class' 		 => 'nav__list nav__list-h',
								'menu_id' 			 => '',
								'container' 		 => '',
								'depth' 				 => 0,
								'theme_location' => 'footer',
								'walker' 				 => new Jeremy_Walker_Footer_Menu,
								'fallback_cb' 	 => false,
							) );
						?>
					</nav>
				<?php } ?>
				<div class="footer__bottom__copyright">
					<span class="copyright-site"><?php jeremy_copyright(); ?></span>

					<?php if ( get_theme_mod( 'show_theme_credits' ) || $preview ) {
						// Theme Credit ?>
						<span class="copyright-theme" style="<?php echo esc_attr( $credits_toggle ); ?>">
							<?php /* translators: %s is presto.blog link */
							echo wp_kses_post( sprintf(
								__( 'Theme by <a href="%s" rel="nofollow">Presto!</a>', 'jeremy' ),
								'https://presto.blog/' )
							); ?>
						</span>
					<?php } ?>
					<?php if ( get_theme_mod( 'show_powered_by' ) || $preview ) {
						// WordPress Link ?>
						<span class="copyright-wp" style="<?php echo esc_attr( $poweredby_toggle ); ?>">
							<?php /* translators: %s is wordpress.org link. */
							echo wp_kses_post( sprintf(
								__( 'Proudly powered by <a href="%s" rel="nofollow">WordPress</a>', 'jeremy' ),
								'https://wordpress.org' )
							); ?>
						</span>
					<?php } ?>
				</div><!-- .footer__bottom__copyright -->
			</div><!-- .footer__bottom -->
		</div><!-- .footer__bottom__container -->
	</footer><!-- #colophon -->
<?php wp_footer(); ?>
</body>
</html>