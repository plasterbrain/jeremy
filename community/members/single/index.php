<?php
/**
 * BuddyPress single user page
 * 
 * A template which sets up a single BuddyPress user page and calls the various
 * partials and actions required to render its content.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */
get_header(); ?>
<div id="primary" class="content-area">
    <?php jeremy_breadcrumbs(); ?>
    <main id="main" class="site-main">
        <article class="profile">
            <header class="profile-header">
                <?php
                // Use the cover image header template if cover images are enabled
                if ( bp_displayed_user_use_cover_image_header() ) {
                    bp_get_template_part( 'members/single/cover-image-header' );
                } else {
                    bp_get_template_part( 'members/single/member-header' );
                }
                
                bp_nav_menu( array(
                    'container' => 'nav',
                    'container_class' => 'profile-nav',
                    'depth' => 1,
                    'items_wrap' => '<ul class="%2$s">%3$s</ul>',
                    'walker' => new Jeremy_Walker_BP_Nav_Menu,
                ) );
                ?>
            </header><!-- .entry-header -->
            <?php $class = bp_is_user_front() ? 'profile-content profile-fields' : 'profile-content'; ?>
            <div class="<?php echo $class; ?>">
                <?php
                /** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
                do_action( 'template_notices' );
    
                /**
                 * Fires before the display of member home content.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_before_member_home_content' );
                
                /**
                 * Fires before the display of member body content.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_before_member_body' );

                if ( bp_is_user_front() ) :
                    bp_displayed_user_front_template_part();

                elseif ( bp_is_user_activity() ) :
                    bp_get_template_part( 'members/single/activity' );

                elseif ( bp_is_user_blogs() ) :
                    bp_get_template_part( 'members/single/blogs'    );

                elseif ( bp_is_user_friends() ) :
                    bp_get_template_part( 'members/single/friends'  );

                elseif ( bp_is_user_groups() ) :
                    bp_get_template_part( 'members/single/groups'   );

                elseif ( bp_is_user_messages() ) :
                    bp_get_template_part( 'members/single/messages' );

                elseif ( bp_is_user_profile() ) :
                    bp_get_template_part( 'members/single/profile'  );

                elseif ( bp_is_user_notifications() ) :
                    bp_get_template_part( 'members/single/notifications' );

                elseif ( bp_is_user_settings() ) :
                    bp_get_template_part( 'members/single/settings' );

                // If nothing sticks, load a generic template
                else :
                    bp_get_template_part( 'members/single/plugins'  );

                endif;

                /**
                 * Fires after the display of member body content.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_after_member_body' );

                /**
                 * Fires after the display of member home content.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_after_member_home_content' ); ?>

            </div><!-- .entry-content -->
        </article><!-- .profile -->
    </main><!-- #main -->
</div><!-- #primary -->
<?php
get_template_part( 'community/members/sidebar-directory' );
get_footer();
