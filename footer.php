<?php
/**
 * Displays the site footer.
 * 
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
?>
	</div><!-- #content -->
	<footer id="colophon" class="site-footer">
		<?php if ( is_active_sidebar( 'sidebar-footer' ) ) { ?>
		<aside class="footer-widgets flex" role="complementary">
    		<?php dynamic_sidebar( 'sidebar-footer' ); ?>
		</aside>
		<?php } ?>
		<div class="footer-copyright flex">
			<?php jeremy_footer_credits(); ?>
		</div><!-- .footer-copyright -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>
<script>
	jQuery( document ).ready( function($) {
		<?php
		if ( has_nav_menu( 'mainmenu' ) ) {
			?>
			var nav = responsiveNav("#site-navigation");
			<?php
		} ?>
	});
</script>
</body>
</html>