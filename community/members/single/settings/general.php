<?php
/**
 * BuddyPress - Members Single Settings (general)
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

do_action( 'bp_before_member_settings_template' ); ?>

<h2 class="profile__subtitle">
	<?php esc_html_e( 'Account Info', 'jeremy' ); ?>
</h2>

<form class="form-settings" action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post">
	
	<h3 class="form__subtitle">
		<?php esc_html_e( 'Change email', 'jeremy' ); ?>
	</h3>
	<label for="email"><?php esc_html_e( 'Email address', 'jeremy' ); ?></label>
	<p class="form__desc">
		<?php esc_html_e( 'Change the email you use to log in.', 'jeremy' ); ?>
	</p>
	<input type="email" name="email" id="email" size="50" value="<?php echo bp_get_displayed_user_email(); ?>" <?php bp_form_field_attributes( 'email' ); ?>/>
	
	<h3 class="form__subtitle">
		<?php esc_html_e( 'Change password', 'jeremy' ); ?>
	</h3>
	<label for="pass1">
		<?php esc_html_e( 'New password', 'jeremy' ); ?>
	</label>
	<input type="password" class="password-entry" name="pass1" id="pass1" value="" <?php bp_form_field_attributes( 'password' ); ?>/>
			
	<label for="pass2">
		<?php esc_html_e( 'Confirm new password', 'jeremy' ); ?>
	</label>
	<input type="password" class="password-entry-confirm" name="pass2" id="pass2" value="" <?php bp_form_field_attributes( 'password' ); ?>/>
	
	<span class="screen-reader-text">
		<?php esc_html_e( 'Password Strength:', 'jeremy' ); ?>
	</span>
	<span id="pass-strength-result"></span>
	
	<?php if ( true ) : ?>
		<p class="form__desc">
			<?php /* translators: %s is the lost password url, no big deal. */
			printf( wp_kses_post( __( 'Enter your current password to save changes. <a href="%s">Forgot your password?</a>', 'jeremy' ) ), wp_lostpassword_url( get_permalink() ) ); ?>
		</p>
		
		<label for="pwd"><?php _e( 'Current password', 'jeremy' ); ?><span class="required">*</span></label>
		<input type="password" name="pwd" id="pwd" value="" <?php bp_form_field_attributes( 'password' ); ?>/>
	<?php endif; ?>

	<?php do_action( 'bp_core_general_settings_before_submit' ); ?>

	<div class="form__submit" role="presentation">
		<input type="submit" name="submit" id="submit" value="<?php esc_attr_e( 'Submit', 'jeremy' ); ?>" />
	</div>
	
	<?php do_action( 'bp_core_general_settings_after_submit' ); ?>
	<?php wp_nonce_field( 'bp_settings_general' ); ?>
</form>

<?php do_action( 'bp_after_member_settings_template' );
