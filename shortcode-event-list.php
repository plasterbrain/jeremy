<?php
/**
 * Event List Widget: Standard List
 *
 * The template is used for displaying the [eo_event] shortcode *unless* it is wrapped around a placeholder: e.g. [eo_event] {placeholder} [/eo_event].
 *
 * You can use this to edit how the output of the eo_event shortcode. See http://docs.wp-event-organiser.com/shortcodes/events-list
 * For the event list widget see widget-event-list.php
 *
 * For a list of available functions (outputting dates, venue details etc) see http://codex.wp-event-organiser.com/
 *
 * @package Event Organiser (plug-in)
 * @since 1.7
 */

global $eo_event_loop,$eo_event_loop_args;

if ( $eo_event_loop->have_posts() ) { ?>

    <ul class="event-list"> 
        <?php while ( $eo_event_loop->have_posts() ) :  $eo_event_loop->the_post(); ?>
            <li>
                <?php echo '<span class="event-list-time">' . eo_get_the_start( 'M j' ) . '</span>'; ?>
                <a href="<?php echo eo_get_permalink(); ?>"><?php the_title(); ?></a>
                <p><?php echo jeremy_get_the_excerpt( array( 'length' => 20 ) ); ?></p>
            </li>
        <?php endwhile; ?>
    </ul>
    </ul>
<?php
} elseif ( ! empty( $eo_event_loop_args['no_events'] ) ) {
    echo $eo_event_loop_args['no_events'];
} else {
    _e( 'There are no events to display.', 'jeremy' );
}

