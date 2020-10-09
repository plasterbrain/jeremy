<?php
/**
 * The widget area that appears alongside main page content.
 * 
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0
 * 	- Removed `is_active_sidebar` check.
 * 	- BuddyPress and event sidebars have been consolidated to this template.
 */
 
if ( is_page_template( 'templates/template-full.php' ) ) {
	return;
}
?>
<aside id="secondary" class="sidebar" aria-labelledby="sidebar__title">
  <h2 id="sidebar__title" class="screen-reader-text">
    <?php esc_html_e( 'Site Widgets' ); ?>
  </h2>
	<?php dynamic_sidebar( 'sidebar' ); ?>
</aside><!-- #secondary -->
