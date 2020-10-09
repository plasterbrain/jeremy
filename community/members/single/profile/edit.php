<?php
/**
 * BuddyPress - Members Single Profile Edit (XProfile Fields)
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

$bp = buddypress();
$bp_profile_args = array(
	'profile_group_id' 	=> bp_get_current_profile_group_id(),
	'hide_empty_fields' => false,
);
?>

<?php do_action( 'bp_before_profile_edit_content' ); ?>

<?php if ( bp_has_profile( $bp_profile_args ) ) { ?>
	<?php while ( bp_profile_groups() ) { bp_the_profile_group(); ?>
		<p class="form__feedback <?php echo esc_attr( $bp->template_message_type ); ?>">
			<?php echo esc_html( $bp->template_message ); ?>
		</p>
		
		<h2 class="profile__subtitle">
			<?php echo esc_html( bp_the_profile_group_name() ); ?>
		</h2>
		
		<form class="profile__edit" action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post">
			<?php do_action( 'bp_before_profile_field_content' ); ?>
			
			<?php while ( bp_profile_fields() ) { bp_the_profile_field(); ?>
				<?php jeremy_bp_edit_field(); ?>
				
				<?php do_action( 'bp_custom_profile_edit_fields_pre_visibility' );?>
				
				<?php do_action( 'bp_custom_profile_edit_fields' ); ?>
			<?php } ?>
			
			<?php do_action( 'bp_after_profile_field_content' ); ?>

			<div class="form__submit" role="presentation">
				<input type="submit" name="button-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?> " />
			</div>

			<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

			<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>
		</form>
	<?php } // endwhile bp_profile_groups() ?>
<?php } // endif bp_has_profile() ?>

<?php do_action( 'bp_after_profile_edit_content' ); ?>