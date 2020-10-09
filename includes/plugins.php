<?php
/**
 * Jeremy Plugin Compatibility
 *
 * Setup for compatibility with, and extra features for, BuddyPress, Event
 * Organiser, BP xProfile Location, Jetpack, and Soil.
 *
 * @package Jeremy
 * @subpackage Jeremy/Includes
 * @since 1.0.0
 */

/**
 * BuddyPress Compatibility
 * @since 2.0.0
 */
require $jeremy_directory . '/includes/plugins-buddypress.php';

/**
 * Jetpack Compatibility
 * @since 2.0.0
 */
require $jeremy_directory . '/includes/plugins-jetpack.php';

/**
 * Event Organiser Compatibility
 * @since 2.0.0
 */
require $jeremy_directory . '/includes/plugins-eventorganiser.php';

/**
 * [jeremy_shortcode_override description]
 * @return [type] [description]
 * @TODO customizer option to disable this.
 * @TODO remove this from .org theme submission :T
 */
function jeremy_shortcode_override() {
	if ( shortcode_exists( 'shariff' ) ) {
		remove_shortcode( 'shariff' );
		add_shortcode( 'shariff', '__return_false' );
	}
}
add_action( 'wp_head','jeremy_shortcode_override' );

if ( ! function_exists( 'jeremy_directions_button' ) ) :
/**
 * Prints a link to directions for an event venue or user address.
 *
 * @TODO Possibly add a fallback for if an address is specified but not a
 * geocode -- that way the theme doesn't rely on the xProfile Location plug-in
 * and a silently failing Google Places script doesn't screw us over.
 *
 * @link https://developer.here.com/documentation/deeplink-web/dev_guide/topics/share-location.html
 * @link https://developers.google.com/waze/deeplinks
 * @link https://developer.apple.com/library/archive/featuredarticles/iPhoneURLScheme_Reference/MapLinks/MapLinks.html
 *
 * @since 1.0.0
 *
 * @param string $venue  The venue name. If not given, it'll use the displayed
 * 											 BuddyPress username.
 */
function jeremy_directions_button( $venue = null ) {
	$address = '';
	if ( class_exists( 'BuddyPress' ) && class_exists( 'PP_Field_Location' ) ) {
		$latlng = get_user_meta( bp_displayed_user_id(), jeremy_xprofile_get_geocode_key(), true );
		$venue = $venue ?: bp_get_displayed_user_fullname();
		$address = ',+' . urlencode( jeremy_bp_get_address() );
	}
	if ( ! $venue ) {
		return;
	}
	$links = array(
		'google_maps' => "https://www.google.com/maps/dir/?api=1&destination={$venue}{$address}"
	);
	
	if ( $latlng ) {
		$latlng = urlencode( $latlng );
		$links['waze'] = "https://www.waze.com/ul?ll={$latlng}&navigate=yes";
		$links['apple'] = "https://maps.apple.com/?daddr={$latlng}&q={$venue}";
		$links['here'] = "https://share.here.com/l/{$latlng},{$venue}";
	}
	
	$labels = array(
		'google_maps' => __( 'Google Maps', 'jeremy' ),
		'waze' 	 			=> __( 'Waze', 'jeremy' ),
		'here' 	 			=> __( 'HERE WeGo', 'jeremy' ),
		'apple'  			=> __( 'Apple Maps', 'jeremy' ),
	);
	
	?>
	<div class="dropdown__container">
		<button class="button-ignore button-secondary button-dropdown button-directions">
			<?php esc_html_e( 'Directions', 'jeremy' ); ?>
			<?php echo jeremy_get_svg( array(
				'img' 	 => 'nav-toggle',
				'inline' => true,
			) ); ?>
		</button>
		<ul class="nav-dropdown nav__list hidden">
			<?php foreach ( $links as $service => $link ) {
				if ( ! empty( $link ) ) { ?>
					<li class="nav-dropdown__item nav-dropdown__item-<?php echo esc_attr( $service ); ?>">
						<a class="nav-dropdown__link nav-dropdown__link-<?php echo esc_attr( $service ); ?>" href="<?php echo $link; ?>">
							<?php echo esc_html( $labels[$service] ); ?>
						</a>
					</li>
				<?php }	
			} ?>
		</ul>
	</div>
	<?php
}
endif;

/**
 * Adds hooks to alter the output of the Custom Post Type Widgets plug-in.
 * 
 * @link https://wordpress.org/plugins/custom-post-type-widgets/
 *
 * @since 2.0.0
 */
function jeremy_cptw_setup_hooks() {
	add_action( 'custom_post_type_widgets/recent_posts/widget/append', 'jeremy_cptw_add_author', 10, 4 );
	add_action( 'custom_post_type_widgets/recent_posts/widget/after', 'jeremy_cptw_add_archive', 10, 3 );
}
add_action( 'after_setup_theme', 'jeremy_cptw_setup_hooks' );

/**
 * Adds the author name to Custom Post Type Widgets "Recent Posts" widgets.
 *
 * @since 2.0.0
 * 
 * @param int $id        		 Widget ID, not used.
 * @param string $post_type	 Current post type.
 * @param array $instance		 Widget arguments.
 * @param WP_Post $post      Current post object.
 */
function jeremy_cptw_add_author( $id, $post_type, $instance, $post ) {
	/* translators: %s is the author's name. This goes after the name of a job listing or special offer in a recent posts widget. */
	$author = sprintf( __( '@ %s' ), get_the_author_meta( 'display_name', $post->post_author ) );
	echo '<span class="entry__meta-author">' . esc_html( $author ) . '</span>';
	
	if ( ! empty( $instance['show_date'] ) && $instance['show_date'] ) {
		echo '<time datetime="' . esc_attr( get_the_date( 'c', $post->ID ) ) . '" class="entry__meta-date">' . esc_html( get_the_date( '', $post->ID ) ) . '</time>';
	}
}

function jeremy_cptw_add_archive( $id, $post_type, $instance ) {
	$aria = sprintf( __( 'View all %s', 'jeremy' ), get_post_type_object( $post_type )->label );
	echo '<a aria-label="' . esc_attr( $aria ) . '" class="entry__excerpt-more" href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '">' . esc_html__( 'View all', 'jeremy' ) . '</a>';
}