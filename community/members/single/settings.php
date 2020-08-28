<?php
/**
 * BuddyPress profile user settings main page.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */
?>
<h2><?php _e( 'Settings', 'jeremy' ); ?></h2>
<nav class="settings-subnav">
	<ul class="menu">
	<?php bp_get_options_nav(); ?>
	</ul>
</nav><!-- .settings-subnav -->

<?php switch ( bp_current_action() ) :
	case 'notifications'  :
		bp_get_template_part( 'members/single/settings/notifications'  );
		break;
	case 'capabilities'   :
		bp_get_template_part( 'members/single/settings/capabilities'   );
		break;
	case 'delete-account' :
		bp_get_template_part( 'members/single/settings/delete-account' );
		break;
	case 'general'        :
		bp_get_template_part( 'members/single/settings/general'        );
		break;
	case 'profile'        :
		bp_get_template_part( 'members/single/settings/profile'        );
		break;
	default:
		bp_get_template_part( 'members/single/plugins'                 );
		break;
endswitch;