<?php
/**
 * Sidebar - BuddyPress Profiles
 * 
 * Displays the cover image, avatar, and various xProfile fields in the sidebar
 * on public BuddyPress profile pages. The theme shows, in order:
 * - User cover image and avatar
 * - Two contact fields
 * - Social media links
 * - An xProfile Location address field
 * - A generic textarea field (e.g. for showing business hours)
 * - A category field
 * - Output from the feed link of an RSS field
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0
 * 	- Previously this sidebar only showed the user map and then some widgets.
 */

$contact1 = sanitize_text_field( get_theme_mod( 'bp_profile_contact1' ) );
$contact2 = sanitize_text_field( get_theme_mod( 'bp_profile_contact2' ) );

$address = sanitize_text_field( get_theme_mod( 'bp_profile_address' ), false );

$category = get_theme_mod( 'bp_profile_category', false );

$misc = get_theme_mod( 'bp_profile_misc', false );

$rss_url = jeremy_xprofile_get_rss();
$rss = $rss_url ? fetch_feed( $rss_url ) : null;
?>

<aside id="secondary" class="main__page-noflex__sidebar" aria-labelledby="sidebar__title">
	<h2 id="sidebar__title" class="screen-reader-text"><?php esc_html_e( 'User Info' ); ?></h2>
	
	<?php if ( bp_displayed_user_use_cover_image_header() ) { ?>
		<img alt="" src="<?php echo esc_url( jeremy_bp_get_cover_image() ); ?>">
	<?php } ?>
	<div class="profile__avatar" role="presentation">
		<?php echo bp_core_fetch_avatar( array(
			'type' => 'full',
		) ); ?>
	</div>

  <section class="entry__meta-contact" aria-labelledby="contact__title">
    <h3 id="contact__title" class="widget-name">
			<?php esc_html_e( 'Contact', 'jeremy' ); ?>
		</h3>
		<?php jeremy_bp_field( array( 'field' => $contact1 ) ); ?>
		<?php jeremy_bp_field( array( 'field' => $contact2 ) ); ?>
		<?php jeremy_bp_social_links(); ?>
	</section>
	<?php if ( $address && jeremy_bp_get_address() ) { ?>
		<section aria-labelledby="address__title">
			<h3 id="address__title" class="widget-name">
				<?php esc_html_e( 'Address', 'jeremy' ); ?>
			</h3>			
			<?php if ( get_theme_mod( 'use_maps', true ) ) { ?>
				<div id="members__map" class="entry__meta-map"></div>
			<?php } ?>
			
			<div class="entry__meta-address flex" role="presentation">
				<?php jeremy_bp_field( array(
					'field' => $address,
				) ); ?>
				<?php jeremy_directions_button(); ?>
			</div>
		</section>
	<?php } ?>
	
	<?php if ( $misc ) { ?>
		<section class="entry__meta-misc" aria-labelledby="misc__title">
			<?php jeremy_bp_field( array(
				'field' 	=> $misc,
				'heading' => true,
				'hclass'	=> 'widget-name',
				'hid'			=> 'misc__title',
			) ); ?>
		</section>
	<?php } ?>
	
	<?php if ( $category ) { ?>
		<section aria-labelledby="cat__title">
			<h3 id="cat__title" class="widget-name"><?php esc_html_e( 'Category', 'jeremy' ); ?></h3>
			<?php jeremy_bp_field( array(
				'field' 	=> $category,
			) ); ?>
		</section>
	<?php } ?>
	
	<?php if ( $rss && ! is_wp_error( $rss ) ) {
		$rss_site_url = wp_parse_url( esc_url( $rss_url ) );
		$rss_site_name = $rss_site_url['host'];
		$rss_site_url = esc_url( $rss_site_url['scheme'] . '://' . $rss_site_url['host'] ); ?>
		<section class="widget_rss" aria-labelledby="rss__title">
			<?php the_widget( 'Jeremy_Widget_RSS', array(
				'title' 			 => sprintf(
											 /* translators: %s is the website name */
											 __( 'News from %s', 'jeremy' ),
											 "<a href='{$rss_url}'>" . str_replace( 'www.', '', $rss_site_name ) . "</a>" ),
				'url' 	 			 => $rss_url,
				'show_date'    => 1,
				'show_summary' => 1,
				'items'        => 3,
				'bp'					 => true,
			), array(
				'before_title' => '<h3 class="widget-name">',
				'after_title'	 => '</h3>',
			) ); ?>
		</section>
	<?php } ?>
</aside>