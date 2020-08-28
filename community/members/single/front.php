<?php
/**
 * BuddyPress public-facing profile
 * 
 * A template for the public-facing side of single BuddyPress member pages. It
 * shows four profile fields which can be chosen in the Customizer and the member's
 * address if BP XProfile Location is activated. 
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

/**
 * Fires before the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_profile_content' );

$about = wp_kses_data( get_theme_mod( 'bp_profile_about' ) );
$address = sanitize_text_field( get_theme_mod( 'bp_profile_address' ) );
$field1 = sanitize_text_field( get_theme_mod( 'bp_profile_1' ) );
$field2 = sanitize_text_field( get_theme_mod( 'bp_profile_2' ) );
$field3 = sanitize_text_field( get_theme_mod( 'bp_profile_3' ) );

if ( $about ) {
	?>
	<section>
		<h3><?php echo $about; ?></h3>
		<?php echo wpautop( xprofile_get_field_data( $about, '', 'comma' ) ); ?>
	</section>
	<?php
} ?>

<section>
	<?php
	jeremy_bp_display_field( $address );
	jeremy_bp_display_field( $field1 );
	jeremy_bp_display_field( $field2 );
	jeremy_bp_display_field( $field3 );
	?>
</section>

<?php
/**
 * Fires after the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_content' );
