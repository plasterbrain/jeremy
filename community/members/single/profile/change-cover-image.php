<?php
/**
 * BuddyPress - Members Profile Change Cover Image
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<h2><?php _e( 'Edit Cover Image', 'jeremy' ); ?></h2>

<?php

/**
 * Fires before the display of profile cover image upload content.
 *
 * @since 2.4.0
 */
do_action( 'bp_before_profile_edit_cover_image' );
$recommended_size = '';
if ( bp_attachments_get_cover_image_settings() ) {
    $width = bp_attachments_get_cover_image_settings()['width'];
    $height = bp_attachments_get_cover_image_settings()['height'];
    $recommended_size = sprintf( ' ' . __( 'Recommended size is %s x %s.', 'jeremy' ), $width, $height );
}?>

<p><?php _e( 'Upload a custom cover image for your profile.', 'jeremy' ); echo $recommended_size; ?></p>

<?php bp_attachments_get_template_part( 'cover-images/index' ); ?>

<?php

/**
 * Fires after the display of profile cover image upload content.
 *
 * @since 2.4.0
 */
do_action( 'bp_after_profile_edit_cover_image' );
