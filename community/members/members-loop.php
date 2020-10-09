<?php
/**
 * BuddyPress - Member Loop
 * 
 * Outputs a list of member cards showing display name, avatar, and the two
 * profile fields set in the Customizer.
 * 
 * @see BP_Core_Members_Template
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 *
 * Changelog
 * 2.0.0 - Added some checks for when this template gets included in search.php
 */

$search = is_search();
$search_number = 4;

$ajax_page = ( bp_ajax_querystring( 'members' ) && strpos( bp_ajax_querystring( 'members' ), 'page=' ) !== false ) ? substr( bp_ajax_querystring( 'members' ), -1 ) : '1';

$members_params = array(
	'type'       => 'alphabetical',
	'exclude'    => get_users( array(
		'role__in'   => array( 'administrator', 'suspended' ),
		'fields'     => 'ID',
	) ),
	'page'       => $ajax_page,
	'per_page'   => $search ? $search_number : 21,
);

$cat_string = jeremy_bp_cat_query();

if ( isset( $_REQUEST[$cat_string] ) ) {
	$cat = jeremy_bp_cat_filter( urldecode( $_REQUEST[$cat_string] ) );
	if ( $cat !== false ) {
		$members_params['include'] = $cat;
	}
}

if ( bp_has_members( $members_params ) ) {
	global $members_template;
	
	$members_pageQuery = array();
	if ( $members_template->total_member_count && $members_template->pag_num ) {
		$members_pageQuery = array(
			'upage' => '%#%',
		);
	  
		$members_pageBase = defined( 'DOING_AJAX' ) && DOING_AJAX ? remove_query_arg( array( 's', $cat_string, 'members_search' ), wp_get_referer() ) : '';
	}
	$total_pages = ceil( $members_template->total_member_count / $members_template->pag_num );

	$current_page = $members_template->pag_page;
	
	$members_pageArgs = array(
		'base'    => add_query_arg( $members_pageQuery, $members_pageBase ),
		'format'  => '',
		'total'   => $total_pages,
		'current' => $current_page,
	);
	?>
	<ul class="members__list" aria-live="assertive" aria-relevant="all">
		<?php while ( $members_template->members() ) : bp_the_member(); ?>
			<?php $member_img = bp_core_fetch_avatar( array(
				'item_id' => $members_template->member->id,
				'type' => 'full',
				'html' => false,
				'width' => 90,
			) );
			?>
	    
			<li class="flex coolbox">
				<div class="members__item__img" role="presentation">
					<a role="presentation" href="<?php bp_member_permalink(); ?>"><img width="90" alt="" src="<?php echo esc_url( $member_img ); ?>"></a>
				</div>
	      
				<div class="members__item__content" role="presentation">
					<h3 class="members__item__title">
						<a href="<?php bp_member_permalink(); ?>">
		          <?php bp_member_name(); ?>
		        </a>
					</h3>

					<?php jeremy_bp_field( array(
						'field' => sanitize_text_field( get_theme_mod( 'bp_profile_contact1' ) ),
					), $members_template->member->id ); ?>

					<?php jeremy_bp_field( array(
						'field' => sanitize_text_field( get_theme_mod( 'bp_profile_contact2' ) ),
					), $members_template->member->id ); ?>

					<?php do_action( 'bp_directory_members_item' ); ?>
					
					<?php do_action( 'bp_directory_members_actions' ); ?>
				</div>
			</li>
		<?php endwhile; ?>
	</ul>

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="members-pages-bot" class="members__nav-pagination" role="presentation">
		<?php if ( $search ) {
			$members_page = jeremy_bp_get_component_link();
			$num = bp_core_number_format( $members_template->total_member_count ); ?>
			
			<p><?php echo esc_html( sprintf(
				_n( 'Found %s matching member.',
				'Found %s matching members.', $num, 'jeremy' ), $num ) ); ?>
				
				<?php if ( $num > $search_number && $members_page ) { ?>
					<a href="<?php echo esc_url( add_query_arg( 's', get_search_query(), $members_page . '#page__title' ) ); ?>"><?php esc_html_e( 'See all results'); ?></a>
				<?php } ?>
			</p>
			
		<?php } else { ?>
			<p><?php bp_members_pagination_count(); ?></p>
			
			<?php echo jeremy_paginate_links( $members_pageArgs ); ?>
		<?php } ?>
	</div>
<?php } else { ?>
	<p class="members__none">
		<?php esc_html_e( "No members were found.", 'jeremy' ); ?>
	</p>
<?php }