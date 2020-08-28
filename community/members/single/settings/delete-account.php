<?php
/**
 * BuddyPress - Members Settings Delete Account
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_before_member_settings_template' ); ?>

<h2 class="screen-reader-text"><?php _e( 'Delete Account', 'jeremy' ); ?></h2>
<form class="profile-settings" action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account'; ?>" method="post">
	<p class="warning">
	<?php if ( bp_is_my_profile() ) : ?>
		<?php _e( 'Deleting your account will delete all of the content you have created. It will be completely irrecoverable.', 'buddypress' ); ?>
	<?php else : ?>
		<?php _e( 'Deleting this account will delete all of the content it has created. It will be completely irrecoverable.', 'buddypress' ); ?>
	<?php endif; ?>
	</p>
	<?php
	/**
	 * Fires before the display of the submit button for user delete account submitting.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_members_delete_account_before_submit' ); ?>

	<label for="delete-account-understand">
		<input type="checkbox" name="delete-account-understand" id="delete-account-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-account-button').disabled = ''; } else { document.getElementById('delete-account-button').disabled = 'disabled'; }" />
		 <?php _e( 'I understand that deleting this account will delete all associated content.', 'jeremy' ); ?>
	</label>
	<div class="profile-submit">
		<input type="submit" disabled="disabled" value="<?php esc_attr_e( 'Delete Account', 'buddypress' ); ?>" id="delete-account-button" name="delete-account-button" />
	</div>

	<?php
	/**
	 * Fires after the display of the submit button for user delete account submitting.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_members_delete_account_after_submit' ); ?>

	<?php wp_nonce_field( 'delete-account' ); ?>
</form>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_after_member_settings_template' );
