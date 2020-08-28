<?php
/**
 * The template is used for displaying the Event List widget if the placeholder option isn't used.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.0
 * 
 * @link http://docs.wp-event-organiser.com/widgets/events-list/
 */

global $eo_event_loop,$eo_event_loop_args;

if ( $eo_event_loop->have_posts() ) : ?>
	<ul class="event-list unstyled-list">
	<?php $event_number = 1;
	while ( $eo_event_loop->have_posts() ) :  $eo_event_loop->the_post();
		if ( $event_number === 1 ) {
			$event_number ++;
			$class = 'featured-entry flex';
		} else {
			$class = 'flex';
		} ?>
		<li>
			<article class="<?php echo $class; ?>">
				<div class="entry-time"><time datetime="<?php echo eo_get_the_start( 'c' ); ?>">
					<p class="month"><?php echo eo_get_the_start( 'M' ); ?></p><p class="day"><?php echo eo_get_the_start( 'j' ); ?></p>
				</time></div>
				<section>
					<h4 class="entry-title"><a href="<?php echo eo_get_permalink(); ?>"><?php the_title(); ?></a></h4>
					<?php echo jeremy_get_the_excerpt( array( 'length' => 12 ) ); ?>
				</section>
			</article>
		</li>
	<?php endwhile; ?>
	</ul>
	<p class="readmore"><a href="<?php echo eo_get_event_archive_link(); ?>"><?php _e( 'Full calendar', 'jeremy' ); ?></a></p>

<?php elseif ( ! empty( $eo_event_loop_args['no_events'] ) ) : ?>
	<p><?php echo $eo_event_loop_args['no_events']; ?></p>
<?php endif; ?>