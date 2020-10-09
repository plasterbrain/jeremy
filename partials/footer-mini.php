<?php
/**
 * A mini version of the site footer.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 2.0.0
 */

?>
	<footer class="site__footer-mini inner">
		<ul class="nav__list nav__list-h">
			<li class="nav__item-back">
				<a href="<?php echo esc_url( get_site_url() ); ?>"><?php esc_html_e( 'Back to Site' ); ?></a>
			</li>
			<li class="nav__item-privacy">
				<?php the_privacy_policy_link(); ?>
			</li>
		</ul>
	</footer>
<?php wp_footer(); ?>
</body>
</html>