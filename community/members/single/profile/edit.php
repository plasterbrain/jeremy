<?php
/**
 * BuddyPress - Members Single Profile Edit
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_profile_edit_content' );
$args = array(
	'profile_group_id' => bp_get_current_profile_group_id(),
	'hide_empty_fields' => false,
);
if ( bp_has_profile( $args ) ) :
	while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

		<h2><?php _e( 'Edit Profile', 'jeremy' ); ?></h2>

		<?php if ( bp_profile_has_multiple_groups() ) : ?>
			<nav class="settings-subnav">
				<ul class="menu" aria-label="<?php esc_attr_e( 'Select a profile section to edit', 'Jeremy' ); ?>">
					<?php bp_profile_group_tabs(); ?>
				</ul>
			</nav>
		<?php endif ;?>

		<form class="profile-settings" action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post">

			<?php

				/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
				do_action( 'bp_before_profile_field_content' ); ?>

				<h3><?php printf( __( "Editing %s", 'jeremy' ), bp_get_the_profile_group_name() ); ?></h3>
				<div class="profile-edit">
				<?php while ( bp_profile_fields() ) : bp_the_profile_field();

					jeremy_bp_xprofile();

					/**
					 * Fires before the display of visibility options for the field.
					 *
					 * @since 1.7.0
					 */
					do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
					/**
					 * Fires after the visibility options for a field, which I DELETED, BITCHES.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_custom_profile_edit_fields' );


				endwhile; ?>
				</div>

			<?php

			/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
			do_action( 'bp_after_profile_field_content' ); ?>

			<div class="profile-submit">
				<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?> " />
			</div>

			<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

			<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>

		</form>

<?php
endwhile;
endif;

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_edit_content' );
