<?php
/**
 * The template for displaying an event-category page
 *
 * @package Jeremy
 * @subpackage Event Organiser (plug-in)
 */

get_header(); ?>

<div id="primary" role="main" class="content-area">
	<?php jeremy_breadcrumbs(); ?>
	<header class="page-header">
		<h1 class="page-title">
			<?php printf( __( '%s Events', 'jeremy' ), single_cat_title( '', false ) ); ?>
		</h1>
		<?php
		$category_description = category_description();
		if ( ! empty( $category_description ) ) {
			echo apply_filters( 'category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>' );
		}
		?>
	</header>

	<?php eo_get_template_part( 'events/eo-loop-events' ); //Lists the events ?>
	
</div><!-- #primary -->

<!-- Call template sidebar and footer -->
<?php get_sidebar(); ?>
<?php get_footer();
