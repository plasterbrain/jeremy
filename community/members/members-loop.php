<?php
/**
 * BuddyPress member loop
 * 
 * Outputs a list of member cards showing display name, avatar, and the two profile
 * fields set in the Customizer.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

/**
 * Fires before the display of the members loop.
 * @since BuddyPress 1.2.0
 */
do_action( 'bp_before_members_loop' );

/**
 * @var string 	URL query string page number, default "1" if it isn't set.
 * */
$members_page = ( bp_ajax_querystring( 'members' ) && strpos( bp_ajax_querystring( 'members' ), 'page=' ) !== false ) ? substr( bp_ajax_querystring( 'members' ), -1 ) : '1';

/**
 * @var array		A list of user IDs to exclude from the main members directory.
 * 					Excludes admins and suspended users by default.
*/
$members_exclude = get_users( array(
	'role__in' => array( 'administrator', 'suspended' ),
	'fields' => 'ID',
) );

/**
 * @link https://codex.buddypress.org/developer/loops-reference/the-members-loop/
 * 
 * @var array		The parameters used to initiate the BuddyPress members loop.
 * 					Defaults to an alphabetical list of 20 members per page. See
 * 					BuddyPress documentation for a list of options.
*/
$members_params = array(
	'type' => 'alphabetical',
	'exclude' => $members_exclude,
	'page' => $members_page,
	'per_page' => 20
);

if ( bp_has_members( $members_params ) ) {
	/**
	 * @global object Instance of BP_Core_Members_Template class
	 */
	global $members_template;

	if ( ( int ) $members_template->total_member_count && ( int ) $members_template->pag_num ) {
		$members_pageQuery = array(
			'upage' => '%#%',
		);
		if ( defined( 'DOING_AJAX' ) && true === (bool) DOING_AJAX ) {
			$members_pageBase = remove_query_arg( 's', wp_get_referer() );
		} else {
			$members_pageBase = '';
		}
	}
	$members_total = ceil( ( int ) $members_template->total_member_count / ( int ) $members_template->pag_num );
	$members_current = (int) $members_template->pag_page;

	$members_pageArgs = array(
		'base' => add_query_arg( $members_pageQuery, $members_pageBase ),
		'format' =>'',
		'total'=> $members_total,
		'current'=> $members_current,
	);

	/**
	 * Fires before the display of the members list.
	 * @since BuddyPress 1.1.0
	 */
	do_action( 'bp_before_directory_members_list' );
	?>

	<ul id="members-list" class="members-list" aria-live="assertive" aria-relevant="all">
		<?php 
		while ( $members_template->members() ) : bp_the_member();
		$member_img = bp_core_fetch_avatar( array(
			'item_id' => $members_template->member->id,
			'type' => 'full',
			'html' => false,
			'width' => 90,
		) );
		?>
			<li class="flex">
				<div class="member-img">
					<a tabindex="-1" href="<?php bp_member_permalink(); ?>"><img width="90" alt="" src="<?php echo esc_url( $member_img ); ?>"></a>
				</div>
				<div class="member-content">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

					<?php
					$field1 = sanitize_text_field( get_theme_mod( 'bp_profile_1' ) );
					$field2 = sanitize_text_field( get_theme_mod( 'bp_profile_2' ) );
					jeremy_bp_display_field( $field1, false, $members_template->member->id );
					jeremy_bp_display_field( $field2, false, $members_template->member->id );

					/**
					 * Fires inside the display of a directory member item.
					 * @since BuddyPress 1.1.0
					 */
					do_action( 'bp_directory_members_item' );

					/**
					 * Fires inside the members action HTML markup to display actions.
					 * @since BuddyPress 1.1.0
					 */
					do_action( 'bp_directory_members_actions' );
					?>
				</div>
			</li>
		<?php endwhile; ?>
	</ul>

	<?php
	/**
	 * Fires after the display of the members list.
	 * @since BuddyPress 1.1.0
	 */
	do_action( 'bp_after_directory_members_list' );
	
	bp_member_hidden_fields();
	?>

	<div id="members-pages-bot" class="pagination">
		<p><?php bp_members_pagination_count(); ?></p>
		<?php echo jeremy_paginate_links( $members_pageArgs ); ?>
	</div>

<?php
} else {
	?>
	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'jeremy' ); ?></p>
	</div>
	<?php
}

/**
 * Fires after the display of the members loop.
 * @since BuddyPress 1.2.0
 */
do_action( 'bp_after_members_loop' );
