<?php
/**
 * BuddyPress edit profile tab
 * 
 * A template which calls template partials and does actions required to generate
 * the profile edit pages. BuddyPress shows a "front" page first and a shared
 * "view/edit profile" tab out of the box -- this has been modified so that profile
 * data shows as the front content, while the former "profile" tab is used exclusively
 * to for editing. tldr; the "view/public" tab is gone sry
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */
?>
<nav class="profile-subnav">
	<ul class="menu">
		<?php bp_get_options_nav(); ?>
	</ul>
</nav>

<?php
/**
 * Fires before the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_profile_content' ); ?>

<?php switch ( bp_current_action() ) :
	// Edit Profile
	case 'edit' :
		bp_get_template_part( 'members/single/profile/edit' );
		break;

	// Change Avatar
	case 'change-avatar' :
		bp_get_template_part( 'members/single/profile/change-avatar' );
		break;

	// Change Cover Image
	case 'change-cover-image' :
		bp_get_template_part( 'members/single/profile/change-cover-image' );
		break;

	// Anything else
	default :
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;

/**
 * Fires after the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_content' );
