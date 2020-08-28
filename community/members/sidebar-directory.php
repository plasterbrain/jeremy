<?php
/**
 * A custom sidebar partial which shows recent posts and a member directory search form.
 * It also shows a map on individual profiles.
 * 
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */
?>
<aside id="secondary" class="sidebar">
    <section class="widget_search" role="search">
		<form class="search-form" role="search" action="<?php echo bp_get_members_directory_permalink(); ?>" method="get">
			<label>
				<span class="screen-reader-text"><?php _e( 'Search members', 'jeremy' ); ?></span>
				<input class="search-field" type="search" name="members_search" placeholder="<?php _e( 'Search local businesses', 'jeremy' ); ?>" />
			</label>
			<input class="search-submit" type="submit" value="<?php _e( 'Search', 'jeremy' ); ?>" />
		</form>
	</section>
	<?php
	if ( bp_is_user() && get_theme_mod( 'use_maps', 'true' ) ) { ?>
		<section class="widget_profile_map">
			<?php
			jeremy_the_profile_map( true );
			jeremy_the_directions();
			?>
		</section>
	<?php
	}
	the_widget( 'WP_Widget_Recent_Posts', $instance=array(
		'title' => __( 'Member News', 'jeremy' ),
	),
		$args=array(
		'before_widget' => '<section class="widget_recent_entries">',
		'after_widget' => '</section>',
		'before_title' => '<h3 class="widget-name">',
		'after_title' => '</h3>',
	));?>
</aside><!-- #secondary -->
