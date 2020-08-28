<?php
/**
 * The sidebar containing the widget area that displays alongside posts and archives.
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Jeremy
 */
?>
<aside id="secondary" class="sidebar">
	<?php if ( ! dynamic_sidebar( 'sidebar-posts' ) ) { ?>
		<section class="widget_archive">
			<h3 class="widget-name"><?php _e( 'Archives', 'jeremy' ); ?></h3>
			<ul>
				<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
			</ul>
		</section>
	<?php } ?>
</aside><!-- #secondary -->
