<?php
/**
 * BuddyPress user header (no cover image)
 * 
 * The user profile header template used when cover images are disabled.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

/**
 * Fires before the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_member_header' );
?>
<section class="profile-actions flex">
    <?php
    /**
     * Outputs member action links with the generic-button class.
     * @since 1.2.6
     */
    do_action( 'bp_member_header_actions' );
    ?>
</section>
<section class="profile-cover">
	<div class="profile-social">
		<?php jeremy_bp_social_links(); ?>
	</div>
</section>
<section class="profile-nocover-info flex">
	<div class="profile-avatar">
        <?php bp_displayed_user_avatar( array( 'type'=>'full', 'extra_attr'=>'aria-role="presentation"' ) ); ?>
        <?php if ( jeremy_bp_is_editor() ) { ?>
            <a class="profile-avatar-edit profile-img-edit" href="<?php echo trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() . '/change-avatar' ); ?>"><?php echo jeremy_get_svg( array( 'img' => 'edit', 'alt' => __( 'Edit your profile picture', 'jeremy' ) ) ); ?></a>
        <?php } ?>
    </div>
	<div class="profile-nocover-meta">
		<h1 class="profile-title"><?php echo bp_get_displayed_user_fullname(); ?></h1>
		<?php
		/**
		 * Fires before the display of the member's header meta.
		 * @since 1.2.0
		 */
		do_action( 'bp_before_member_header_meta' );
		/**
		 * Fires after the group header actions section.
		 *
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_profile_header_meta' );
		?>
	</div><!-- .profile-nocover-meta -->
</section><!-- .profile-nocover-info -->
<?php
/**
 * Fires after the display of a member's header.
 * @since 1.2.0
 */
do_action( 'bp_after_member_header' );
?>
