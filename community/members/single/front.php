<?php
/**
 * BuddyPress - Members Single Front-Facing Profile
 * 
 * A template for the public-facing side of single BuddyPress member pages. It
 * shows four profile fields which can be chosen in the Customizer and the
 * member's address if BP XProfile Location is activated. 
 *
 * @package Jeremy
 * @subpackage BuddyPress
 * @since 1.0.0
 */

do_action( 'bp_before_profile_content' );

$about = sanitize_text_field( get_theme_mod( 'bp_profile_about', false ) );

if ( $about ) { ?>
	<section aria-label="<?php esc_attr_e( 'About', 'jeremy' ); ?>">
		<?php echo wp_kses_post( wpautop( xprofile_get_field_data( $about, '', 'comma' ) ) ); ?>
	</section>
<?php } ?>

<?php do_action( 'bp_after_profile_content' ); ?>

</div><!-- . main__page-noflex__content -->

<div class="main__page-noflex__footer" role="presentation">
	<?php
	$post_types = array(
		'post',
		'event',
		'jeremy_deal',
		'jeremy_job',
	);
	foreach ( $post_types as $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}
		
		$title_id = 'profile__section-' . $post_type . '__title';
		
		$archive_link = add_query_arg( 'post_type', sanitize_title( $post_type ), get_author_posts_url( bp_displayed_user_id() ) );
		
		$loop = new WP_Query( array(
			'author' 					 => bp_displayed_user_id(),
			'post_type' 			 => $post_type,
			'suppress_filters' => false,
			'posts_per_page'	 => 3,
		) );
		
		$svg = jeremy_get_svg( array(
			'img' 	 => "post-{$post_type}",
			'inline' => true,
		) );

		$plural_label = strtolower( get_post_type_object( $post_type )->label ); ?>
		<hr />
		<section class="profile__section profile__section-<?php echo esc_attr( $post_type ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
			<header class="profile__section_header flex">
				<h2 id="<?php echo esc_attr( $title_id ); ?>" class="profile__subtitle"><?php echo $svg; ?><?php echo esc_html( ucwords( $plural_label ) ); ?></h2>
				<?php /* translators: %1$s is author name, %2$s is plural post type */
				jeremy_feed_button(
					$post_type === 'event',
					$archive_link,
					sprintf( __( '%1$s %2$s feed', 'jeremy' ), bp_get_displayed_user_fullname(), $plural_label ) ); ?>
			</header>
			
			<?php if ( $loop->have_posts() ) { ?>
				<?php while ( $loop->have_posts() ) { $loop->the_post();
					get_template_part( 'partials/bp_tab', $post_type );
				} ?>
				<?php if ( $loop->found_posts > $loop->post_count ) { ?>
					<a href="<?php echo esc_url( $archive_link ); ?>"><?php esc_html_e( 'View all', 'jeremy' ); ?></a>
				<?php } ?>
			<?php } else { ?>
				<p>
					<?php /* translators: %1$s - user display name. %2$s - post type. */
					printf( esc_html__( '%1$s has no %2$s right now.', 'jeremys friend' ), bp_get_displayed_user_fullname(), $plural_label ); ?>
				</p>
			<?php } ?>
		</section><!-- .archive -->
		<?php wp_reset_postdata(); ?>
	<?php } // end foreach post type loop ?>
</div>
