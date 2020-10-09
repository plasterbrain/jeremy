<?php
/**
 * BuddyPress - Members Single Settings
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

?>
<div class="main__page" role="presentation">
	<?php jeremy_breadcrumbs(); ?>
	<article class="profile">
		<header class="profile__header page__header">
			<h1 class="profile__title page__title">
				<?php esc_html_e( 'Settings', 'jeremy' ); ?>
			</h1>
			<nav class="nav-profile">
				<ul class="nav__list nav__list-h">
					<?php bp_get_options_nav(); ?>
				</ul>
			</nav>
		</header>

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
			case 'data'        :
				bp_get_template_part( 'members/single/settings/data'        	 );
				break;
			default:
				bp_get_template_part( 'members/single/plugins'                 );
				break;
		endswitch;
		?>
	</article>
</div><!-- .main__page -->

<?php get_sidebar(); ?>
</main><!-- #main -->

<?php get_footer();