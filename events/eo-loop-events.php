<?php
/**
 * The Event Organiser events loop. Used by archive-events.php,
 * taxonomy-event-venue.php, taxonomy-event-category.php and
 * taxonomy-event-tag.php.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
?>
<main id="main" class="site-main archive-event">
<?php if ( have_posts() ) { ?>

	<?php
	while ( have_posts() ) : the_post();
		eo_get_template_part( 'events/eo-loop-single-event' );
	endwhile; ?>
	<div class="pagination">
		<?php echo jeremy_paginate_links(); ?>
	</div>

<?php } else { ?>

	<!-- If there are no events -->
	<article class="post no-results not-found">
		<header class="entry-header">
			<h1 class="entry-title"><?php _e( 'Nothing Found', 'jeremy' ); ?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<p><?php _e( 'Sorry, there are no events to show at this time.', 'jeremy' ); ?></p>
		</div><!-- .entry-content -->
	</article><!-- #post-0 -->

<?php }; ?>
</main><!-- site-main -->