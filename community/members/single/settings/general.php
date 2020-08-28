<?php
/**
 * BuddyPress user profile general account settings module.
 *
 * @package Jeremy
 * @subpackage Templates
 */
/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_before_member_settings_template' ); ?>

<h2 class="screen-reader-text"><?php _e( 'Account Settings', 'jeremy' ); ?></h2>
<form class="profile-settings" action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post">

	<h3><?php _e( 'Change your email', 'jeremy' ); ?></h3>
	<p><em><?php _e( 'Change the email you use to access your account.', 'jeremy' ); ?></em></p>

	<label for="email"><?php _e( 'Account Email', 'jeremy' ); ?></label>
	<input type="email" name="email" id="email" size="50" value="<?php echo bp_get_displayed_user_email(); ?>" <?php bp_form_field_attributes( 'email' ); ?>/>

	<h3><?php _e( 'Change your password', 'jeremy' ); ?></h3>

	<div class="new-password flex">
		<div>
			<label for="pass1"><?php _e( 'New password', 'jeremy' ); ?></label>
			<input type="password" name="pass1" id="pass1" value="" <?php bp_form_field_attributes( 'password' ); ?>/>
			<div id="password-strength"></div>
		</div>
		<div>
			<label for="pass2"><?php _e( 'Confirm New Password', 'jeremy' ); ?></label>
			<input type="password" name="pass2" id="pass2" value="" <?php bp_form_field_attributes( 'password' ); ?>/>
		</div>
	</div>
	<?php if ( ! is_super_admin() ) : ?>
		<?php printf( '<p><em>' . __( 'Enter your current password to save account changes. <a href="%s">Forgot your password?</a>', 'jeremy' ) . '</em></p>', wp_lostpassword_url( get_permalink() ) ); ?></p>
		
		<label for="pwd"><?php _e( 'Current Password', 'jeremy' ); ?><span class="required">*</span></label>
		<input type="password" name="pwd" id="pwd" value="" <?php bp_form_field_attributes( 'password' ); ?>/>
		
	<?php endif; ?>

	<?php
	/**
	 * Fires before the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_core_general_settings_before_submit' ); ?>

	<div class="profile-submit">
		<input type="submit" name="submit" id="submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?>" />
	</div>
	<?php
	/**
	 * Fires after the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_core_general_settings_after_submit' );
	wp_nonce_field( 'bp_settings_general' );
	?>
</form>

<?php
/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_after_member_settings_template' );
