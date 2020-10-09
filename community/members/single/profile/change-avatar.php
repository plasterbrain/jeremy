<?php
/**
 * BuddyPress - (Profile) Upload Avatar
 *
 * @TODO Remove this tab if avatar uploads disabled
 * @TODO Test "Gravatars disabled" text
 * @TODO use plugin textdomain without pissing off the theme review gods
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Gravatar upload instructions.
 */

$grav_text = '';
if ( ! apply_filters( 'bp_core_fetch_avatar_no_grav', false, array() ) ) {
	$grav_text = _x( " By default, your profile will show the <a href='http://en.gravatar.com/'>Gravatar</a> associated with your email, if you have one.", "There's a space at the start because this gets conditionally added to a paragraph description.", 'jeremy' );
}
?>

<?php do_action( 'bp_before_profile_avatar_upload_content' ); ?>

	<header class="profile-avatar__header flex flex-wrap-m">
		<div role="presentation" class="profile-avatar__header__text">
			<h3><?php esc_html_e( 'Change Profile Photo', 'jeremy' ); ?></h3>
			<p><?php esc_html_e( 'Upload a picture to show on your profile and represent you around the site.', 'jeremy' ); echo wp_kses_post( $grav_text ); ?></p>
		</div>
		<?php echo bp_core_fetch_avatar( array(
			'type' => 'full',
		) ); ?>
	</header>

<?php if ( ! (int) bp_get_option( 'bp-disable-avatar-uploads' ) ) { ?>

	<?php // This will get replaced with Javascript if it's enabled. ?>
	<form method="post" id="avatar-upload-form" class="standard-form" enctype="multipart/form-data">

		<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) { ?>

			<?php wp_nonce_field( 'bp_avatar_upload' ); ?>
			<p><?php esc_html_e( 'Select a JPG, GIF or PNG format photo from your computer and then click "Upload Image."', 'jeremy' ); ?></p>

			<p id="avatar-upload">
				<label for="file" class="bp-screen-reader-text"><?php esc_attr_e( 'Select an image', 'jeremy' ); ?></label>
				<input type="file" name="file" id="file" />
				<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'jeremy' ); ?>" />
				<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
			</p>

			<?php if ( bp_get_user_has_avatar() ) { ?>
				<p><?php esc_html_e( "Delete your current profile picture. This cannot be undone!", 'jeremy' ); ?></p>
				<p><a class="button edit" href="<?php bp_avatar_delete_link(); ?>"><?php esc_html_e( 'Delete Profile Photo', 'jeremy' ); ?></a></p>
			<?php } ?>

		<?php } // end upload image step ?>

		<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) { ?>
			<h5><?php esc_html_e( 'Crop Your Profile Photo', 'jeremy' ); ?></h5>
			<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_attr_e( 'Profile photo to crop', 'jeremy' ); ?>" />

			<div id="avatar-crop-pane">
				<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_attr_e( 'Profile photo preview', 'jeremy' ); ?>" />
			</div>

			<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'jeremy' ); ?>" />

			<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />

			<?php wp_nonce_field( 'bp_avatar_cropstore' ); ?>

		<?php } // end crop image step ?>

	</form>

	<?php bp_avatar_get_templates(); ?>

<?php } else { ?>
	<?php if ( $grav_text ) {
		// Some Gravatar instructions for Boomers.
		$bp = buddypress();
		$user_email = $bp->displayed_user->userdata->user_email; ?>
		
		<p><?php esc_html_e( "This site doesn't support uploading profile pictures, but you can still update your picture using Gravatar.", 'jeremy' ); ?></p>
		<ol>
			<li><?php echo wp_kses_post( __( '<a href="https://en.gravatar.com/connect/">Sign in to Gravatar</a> using a new or existing WordPress.com account. (Learn more about logging in with WordPress Connect <a href="https://wordpress.com/support/wpcc-faq/" aria-label="More about WordPress.com Connect">here</a>.)', 'jeremy' ) ); ?></li>
			<li><?php echo wp_kses_post( __( 'Login and navigate to the <a href="https://en.gravatar.com/emails/">Manage Gravatars</a> page.', 'jeremy' ) ); ?></li>
			<li><?php echo esc_html( sprintf( __( 'Under "1. Pick an email to modify," add the email address associated with your account on the %1$s website (%2$s) if it\'s not there already.', 'jeremy' ), get_bloginfo( 'name' ), $user_email ) ); ?></li>
			<li><?php echo wp_kses_post( __( '<a href="https://en.gravatar.com/gravatars/new">Upload your profile picture</a> to Gravatar and proceed through the steps. When prompted, make sure to check the box by your email to use your new picture with this account.', 'jeremy' ) ); ?></li>
		</ol>
	<?php } else {  ?>
		<p><?php esc_html_e( "This site doesn't support custom profile pictures. :(", 'jeremy' ); ?></p>
		<?php // So why are we here, you may well ask. ?>
	<?php } ?>

<?php } //endif avatars enabled ?>

<?php do_action( 'bp_after_profile_avatar_upload_content' ); ?>
