<?php
/**
 * The template is used for displaying the Event List widget.
 * 
 * @link http://docs.wp-event-organiser.com/widgets/events-list/
 *
 * @package Jeremy
 * @subpackage Events
 * @since 1.0.0
 */

global $eo_event_loop, $eo_event_loop_args;

if ( $eo_event_loop->have_posts() ) : ?>
	<ul>
	<?php $event_number = 1;
	while ( $eo_event_loop->have_posts() ) :  $eo_event_loop->the_post();
		if ( $event_number === 1 ) {
			$event_number ++;
			$class = 'entry-featured flex';
		} else {
			$class = 'flex';
		} ?>
		<li>
			<article class="<?php echo $class; ?>">
				<time class="entry__meta-time" datetime="<?php echo eo_get_the_start( 'c' ); ?>">
				<p class="month"><?php echo eo_get_the_start( 'M' ); ?></p><p class="day"><?php echo eo_get_the_start( 'j' ); ?></p>
				</time>
				<div role="presentation">
					<h4 class="entry-title"><a href="<?php echo eo_get_permalink(); ?>"><?php the_title(); ?></a></h4>
					<?php echo jeremy_get_the_excerpt(); ?>
				</div>
			</article>
		</li>
	<?php endwhile; ?>
	</ul>
	<p class="entry__excerpt-more"><a href="<?php echo eo_get_event_archive_link(); ?>"><?php _e( 'Full calendar', 'jeremy' ); ?></a></p>

<?php elseif ( ! empty( $eo_event_loop_args['no_events'] ) ) : ?>
	<p><?php echo $eo_event_loop_args['no_events']; ?></p>
<?php endif; ?>