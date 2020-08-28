<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Jeremy
 */

get_header(); ?>

<div id="primary" class="content-area">
    <?php jeremy_breadcrumbs(); ?>
	<main id="main" class="site-main error-404">
		<header class="page-header">
			<h1 class="page-title"><?php echo __( '404', 'jeremy' ); ?></h1>
			<h2><?php _e( 'Page not found. Try a search?', 'jeremy' ); ?></h2>
		</header><!-- .page-header -->

		<div class="page-content">
			<?php
				get_search_form();
			?>
		</div><!-- .page-content -->
	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
