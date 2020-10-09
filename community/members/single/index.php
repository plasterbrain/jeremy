<?php
/**
 * BuddyPress - Members Single Profile Index
 * 
 * A template which sets up a single BuddyPress user page and calls the various
 * partials and actions required to render its content.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

get_header(); ?>
	<?php do_action( 'template_notices' ); ?>

	<?php if ( ! jeremy_bp_is_public_profile() ) { ?>
		<?php
		if ( jeremy_bp_is_editor() && bp_is_user_profile() )  {
			// Edit Profile
			bp_get_template_part( 'members/single/profile' );
		} elseif ( bp_is_user_settings() ) {
			// Account settings
			bp_get_template_part( 'members/single/settings' );
		} elseif ( bp_is_user_messages() ) {
			// PM Inbox
			bp_get_template_part( 'members/single/messages' );
		} elseif ( bp_is_user_notifications() ) {
			// Notifications
			bp_get_template_part( 'members/single/notifications' );
		}
		return;
	} ?>
	
	<article class="main__page-noflex profile">
		<div class="main__page-noflex__content" role="presentation">
			<?php jeremy_breadcrumbs(); ?>

			<?php do_action( 'bp_before_member_header' ); ?>
			
			<div class="profile__avatar-mobile" role="presentation">
				<?php echo bp_core_fetch_avatar( array(
					'type' => 'full',
				) ); ?>
			</div>
			
			<header class="profile__header">
				<?php do_action( 'bp_before_member_header_meta' ); ?>
				
				<div class="profile__header__text" role="presentation">
					<h1 class="profile__title profile__header__title"><?php echo esc_html( bp_get_displayed_user_fullname() ); ?></h1>
					<?php jeremy_author_tools( false, true ); ?>
				</div>
				
				<?php do_action( 'bp_profile_header_meta' ); ?>

				<?php do_action( 'bp_member_header_actions' ); ?>
			</header><!-- .profile__header -->
			
			<?php do_action( 'bp_before_member_home_content' ); ?>
				
			<?php do_action( 'bp_before_member_body' ); ?>

			<?php if ( bp_is_user_front() ||
							 ( ! jeremy_bp_is_editor() && bp_is_user_profile() ) ||
						   bp_current_action() === 'public' ) {
				// Public Profile ?>
				<?php bp_displayed_user_front_template_part(); ?>
			<?php } elseif ( bp_is_user_activity() ) {
				// Activity Feed
				bp_get_template_part( 'members/single/activity' );
			} elseif ( bp_is_user_blogs() ) {
				// User Blog
				bp_get_template_part( 'members/single/blogs' );
			} elseif ( bp_is_user_friends() ) {
				// Friends List
				bp_get_template_part( 'members/single/friends' );
			} elseif ( bp_is_user_groups() ) {
				// Groups
				bp_get_template_part( 'members/single/groups' );
			} else {
				// Plug-in Pages
				bp_get_template_part( 'members/single/plugins' );
			} ?>

			<?php do_action( 'bp_after_member_body' ); ?>
			
			<?php do_action( 'bp_after_member_home_content' ); ?>
			
			
 		<?php get_template_part( 'partials/sidebar', 'profile' ); ?>
	</article><!-- .main__page -->
</main><!-- #main -->

<?php get_footer();