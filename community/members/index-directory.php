<?php
/**
 * The template for displaying the BuddyPress member directory search results.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
get_header();
?>
<div id="primary" class="content-area">
	<?php jeremy_breadcrumbs(); ?>
	<main id="main" class="site-main">
	<header class="page-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php
	/**
	 * Fires at the top of the members directory template file.
	 * @since 1.5.0
	 */
	do_action( 'bp_before_directory_members_page' );
	?>

	<?php
	/**
	 * Fires before the display of the members list tabs.
	 *
	 * @since 1.8.0
	 */
	do_action( 'bp_before_directory_members_tabs' ); ?>

	<?php
	// Get the search term if any
	$search_msg = null;
	$search_term = null;
	if ( isset( $_REQUEST['s'] ) || isset( $_REQUEST['members_search'] ) ) {
		$search_term = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : $_REQUEST['members_search'];
		$search_term = stripcslashes( $search_term );
	}
	// Generate the list of categories, if any
	$categories = jeremy_xprofile_get_categories();
	if ( $categories ) {
		?>
		<h3 id="members-cats-title" class="screen-reader-text"><?php printf( _x( 'View by %s', 'Label for list of categories', 'jeremy' ), get_theme_mod( 'bp_profile_category' ) ); ?></h3>
		<a class="screen-reader-text" href="#members-list">Skip to members list</a>
		<div class="members-cats profile-subnav" aria-labelledby="members-cats-title">
			<?php foreach ( $categories as $category ) {
				if ( $search_term && strtolower( $search_term ) === strtolower( $category->name ) ) {
					$search_msg = $category->name;
				}
				jeremy_bp_the_search_link( $category->name );
			} ?>
			<a href="<?php echo bp_get_members_directory_permalink(); ?>" aria-label="Search all members"><?php _ex( 'View All', 'View all members', 'jeremy' ); ?></a>
		</div>
	<?php }

	/**
	 * Fires before the display of the members.
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_members' );

	// Generate the heading to show above the members loop.
	if ( $search_msg ) { ?>
		<h2><?php printf( _x( 'Showing all members in %s', 'Results of member category search', 'jeremy' ), $search_msg ); ?></h2>
	<?php }
	if ( $search_term && ! $search_msg ) {
		$search_term = esc_html( $search_term ); ?>
		<h2><?php printf( _x( 'Showing results for search term "%s"', 'Results of member search', 'jeremy' ), $search_term ); ?></h2>
	<?php }
	if ( ! $search_term ) { ?>
		<h2><?php _e( 'Showing all members', 'jeremy' ); ?></h2>
	<?php }

	/**
	 * Fires before the display of the members content.
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_members_content' ); ?>

	<div id="members-dir-list" class="members dir-list">
		<?php bp_get_template_part( 'members/members-loop' ); ?>
	</div><!-- #members-dir-list -->

	<?php
	/**
	 * Fires and displays the members content.
	 * @since 1.1.0
	 */
	do_action( 'bp_directory_members_content' ); ?>

	<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

	<?php
	/**
	 * Fires after the display of the members content.
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_members_content' ); ?>

	<?php
	/**
	 * Fires after the display of the members.
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_members' ); ?>

	<?php
	/**
	 * Fires at the bottom of the members directory template file.
	 * @since 1.5.0
	 */
	do_action( 'bp_after_directory_members_page' );
	?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_template_part( 'community/members/sidebar-directory' );
get_footer();
?>

