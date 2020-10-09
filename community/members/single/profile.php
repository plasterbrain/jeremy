<?php
/**
 * BuddyPress - Edit Profile
 * 
 * A template which calls template partials and does actions required to
 * generate the profile edit pages. BuddyPress shows a "front" page first and a
 * shared "view/edit profile" tab out of the box -- this has been modified so
 * that profile data shows as the front content, while the "profile" section is
 * used solely for editing.
 *
 * @TODO Find a way to fix Cover Image Upload so that it gives some indication
 * of a successful upload the way the avatar form does...
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Edit Cover Image template is inlined
 */

$action = bp_current_action();
?>
	<div class="main__page" role="presentation">
		<?php jeremy_breadcrumbs(); ?>
		<article class="profile">
			<header class="page__header">
				<h1 class="page__title">
					<?php esc_html_e( 'Edit Profile', 'jeremy' ); ?>
				</h1>
				<nav class="nav-profile">
					<ul class="nav__list nav__list-h">
						<?php bp_get_options_nav(); ?>
					</ul>
				</nav>
				<?php if ( $action === 'edit' ) { ?>
					<nav class="nav-profile-sub">
						<ul class="nav__list nav__list-h" aria-label="<?php esc_attr_e( 'Select a profile section to edit', 'Jeremy' ); ?>">
							<?php bp_profile_group_tabs(); ?>
						</ul>
					</nav>
				<?php } ?>
			</header><!-- .page-header -->

			<?php switch ( $action ) {
				// Edit Profile
				case 'edit':
				case 'public':
					// Don't show this form if a non-logged in user somehow gets here :T
					if ( jeremy_bp_is_editor() ) {
						bp_get_template_part( 'members/single/profile/edit' );
					}
					break;

				// Change Avatar
				case 'change-avatar':
					bp_get_template_part( 'members/single/profile/change-avatar' );
					break;

				// Change Cover Image
				case 'change-cover-image': ?>
					<?php do_action( 'bp_before_profile_edit_cover_image' ); ?>
					
					<h3><?php esc_html_e( 'Cover Image', 'jeremy' ); ?></h3>
					<p><?php esc_html_e( 'Upload a cover image for your profile.', 'jeremy' ); ?></p>
					
					<img alt="" src="<?php echo esc_url( jeremy_bp_get_cover_image() ); ?>">

					<?php bp_attachments_get_template_part( 'cover-images/index' ); ?>

					<?php do_action( 'bp_after_profile_edit_cover_image' ); ?>
				<?php
				// Anything else
				default:
					bp_get_template_part( 'members/single/plugins' );
					break;
			} ?>
		</article>
	</div><!-- .main__page -->

	<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();