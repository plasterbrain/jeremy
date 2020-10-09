<?php
/**
 * BuddyPress - Member Directory
 * 
 * The template for displaying the BuddyPress member directory. The layout for
 * each individual member result is handled by members-loop.php. It may behoove
 * you to edit a copy of this template in a child theme if you want to adjust
 * how the member query and URL parameters are set up.
 * 
 * @link codex.buddypress.org/developer/loops-reference/the-members-loop
 *
 * @TODO Customizer setting for number of members shown per page.
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - "category" query parameter {@see jeremy_bp_cat_filter}
 */

// Use "category" parameter to filter the list if it exists.
$cat_string = jeremy_bp_cat_query();
if ( isset( $_REQUEST[$cat_string] ) ) {
	$cat = jeremy_bp_cat_filter( urldecode( $_REQUEST[$cat_string] ) );
	if ( $cat !== false ) {
		$cat_term = ucfirst( $_REQUEST[$cat_string] );
	}
}

// Setting up a "search results" message
if ( isset( $_REQUEST['s'] ) ) {
	$search_term = $_REQUEST['s'];
} elseif( isset( $_REQUEST['members_search'] ) ) {
	$search_term = $_REQUEST['members_search'];
}

if ( isset( $search_term ) ) {
	if ( isset( $cat_term ) ) {
		/* translators: %1$s is the category name, %2$s is the search term. */
		$search_msg = sprintf( __( 'Showing all members in "%1$s" matching "%2$s"', 'jeremy' ), $cat_term, $search_term );
	} else {
		/* translators: %s is the search term. */
		$search_msg = sprintf( __( 'Showing all businesses matching "%s"', 'jeremy' ), $search_term );
	}
} elseif ( isset( $cat_term ) ) {
	$search_msg = sprintf(__( 'Showing all members in "%s"', 'jeremy' ), $cat_term );
}

get_header(); ?>
	<?php jeremy_breadcrumbs(); ?>
	
	<?php do_action( 'bp_before_directory_members_page' ); ?>
	
	<div aria-label="<?php esc_attr_e( 'Members Map', 'jeremy' ); ?>" class="members__map" id="members__map">
		<?php esc_html_e( "We're having trouble loading the member map. Your browser may have Javascript disabled.", 'jeremy' ); ?>
	</div>
	<?php the_title( '<h1 id="page__title" class="page__title">', '</h1>' ); ?>

	<?php do_action( 'bp_before_directory_members_tabs' ); ?>
	
	<?php /*== Member Categories ==*/
	$categories = jeremy_xprofile_get_categories();
	if ( $categories ) { ?>
		<section class="members__cats">
			<header class="members__cats__header">
				<h2 id="members__cats__title" class="screen-reader-text">
					<?php /* translators: %s is the name of the category field */
					echo esc_html( sprintf(
						__( 'View by %s', 'Label for list of categories', 'jeremy' ), get_theme_mod( 'bp_profile_category' )
					) ); ?>
				</h2>
			</header>
			<ul class="nav__list nav__list-h">
				<?php foreach ( $categories as $category ) { ?>
					<li>
						<?php echo jeremy_bp_get_search_link( $category->name ); ?>
					</li>
				<?php } ?>
				<li>
					<a href="<?php echo esc_url( bp_get_members_directory_permalink() ); ?>" aria-label="<?php esc_attr_e( 'View all members', 'jeremy' ); ?>">
						<?php _ex( 'View all', 'View all members', 'jeremy' ); ?>
					</a>
				</li>
			</ul>
		</section><!-- .members__cats -->
	<?php } ?>
	
	<form action="" role="search" method="get" class="form-search flex" aria-label="<?php esc_attr_e( 'Member Search', 'jeremy' ); ?>">
		<label class="screen-reader-text" for="<?php bp_search_input_name(); ?>">
			<?php esc_html_e( 'Search term', 'jeremy' ); ?>
		</label>
		<input
			type="text"
			class="search__input"
			name="<?php echo esc_attr( bp_core_get_component_search_query_arg() ); ?>"
			id="<?php bp_search_input_name(); ?>"
			placeholder="<?php echo esc_attr__( 'Search&hellip;', 'jeremy' ); ?>"
			value="<?php bp_search_placeholder(); ?>" />
			
		<button
		type="submit"
			class="button button-search"
			id="<?php echo esc_attr( bp_get_search_input_name() ); ?>_submit" />
	  	<?php echo jeremy_get_svg( array(
				'img' 	=> 'form-search',
				'alt' 	=> esc_attr__( 'Submit', 'jeremy' ),
				'class' => 'button-search__icon',
				'inline'=> true,
			) ); ?>
	  </button>
	</form>
	
	<?php do_action( 'bp_before_directory_members' ); ?>
		
	<h2 class="page__subtitle">
		<?php if ( isset( $search_msg ) ) { ?>
			<?php esc_html_e( $search_msg ); ?>
			<a href="<?php echo esc_url( home_url( $wp->request ) ); ?>"><?php esc_html_e( "(Clear filters)", 'jeremy' ); ?></a>
		<?php } else { ?>
			<?php esc_html_e( 'Showing all members', 'jeremy' ); ?>
		<?php } ?>
	</h2>
		
	<?php do_action( 'bp_before_directory_members_content' ); ?>
	
	<div id="members" class="members dir-list" role="presentation">
		<?php do_action( 'bp_before_members_loop' ); ?>
		
		<?php do_action( 'bp_directory_members_content' ); ?>
		<?php bp_get_template_part( 'members/members-loop' ); ?>
		
		<?php do_action( 'bp_after_members_loop' ); ?>
		<?php do_action( 'bp_after_directory_members_content' ); ?>
	</div><!-- #members -->

	<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

	<?php do_action( 'bp_after_directory_members' ); ?>

	<?php do_action( 'bp_after_directory_members_page' ); ?>
</main><!-- #main -->

<?php get_footer();
