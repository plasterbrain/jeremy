<aside id="secondary" class="sidebar">
    <?php
    if ( ! eo_recurs() ) {
        $end_time = eventorganiser_get_option('runningisnotpast') ? new DateTime( eo_get_the_end( 'r') ) : new DateTime( eo_get_the_start( 'r' ) );
        $now = new DateTime( current_time( 'r' ) );
        echo '';
        if ( $end_time < $now ) {
            echo '<h2 class="event-date">' . __( 'This event has passed.', 'jeremy' ) . '</h2>';
        } else {
            printf( '<time class="event-date" datetime="%s">', eo_get_the_start( 'c' ) );
            echo '<h2>' . eo_get_the_start( 'F jS, Y' ) . '</h2>';
            if ( ! eo_is_all_day() )
                echo '<h3><em>' . sprintf( _x( 'Starting at %s', 'Event starting time', 'jeremy' ), eo_get_the_start( 'g:ia' ) ) . '</em></h3>';
            echo '</time>';
        }
        echo '</h2>';
    } else {
        //Event recurs - display dates.
        $upcoming = new WP_Query(array(
            'post_type'         => 'event',
            'event_start_after' => 'today',
            'posts_per_page'    => -1,
            'event_series'      => get_the_ID(),
            'group_events_by'   => 'occurrence',
        ));

        if ( $upcoming->have_posts() ) :

            echo '<h2 class="event-date">' . __( 'Upcoming Dates', 'eventorganiser' ) . ':</h2>'; ?>
            <ul class="upcoming-dates">
                <?php
                if ( ! eo_get_next_occurrence( eo_get_event_datetime_format() ) ) {
                    printf( '<li>' . __( 'There are no upcoming dates for this event.', 'jeremy' ) . '</li>' );
                } else {
                    while ( $upcoming->have_posts() ) {
                        $upcoming->the_post();
                        $time = eo_get_the_start( 'g:ia' );
                        $when = $time == '12:00am' ? eo_get_the_start( 'F jS' ) : sprintf( _x( '%s at %s', 'day at time, for event listings' ), eo_get_the_start( 'F jS' ), $time );
                        printf( '<li><time datetime="%s">%s</time></li>', eo_get_the_start( 'c' ), $when );
                    };
                }
                ?>
            </ul>

            <?php
            wp_reset_postdata();
            ?>
        <?php endif; ?>
    <?php }
	if ( eo_get_venue() && eo_venue_has_latlng( eo_get_venue() ) ) : ?>
        <?php
        $venue_name = eo_get_venue_name();
        printf( '<h3 class="event-location">' . __( '@', 'jeremy' ) . ' <a href="%s">%s</a></h3>', jeremy_get_the_venue_link( $venue_name ), $venue_name );
        jeremy_the_venue_map();
        jeremy_the_directions( $venue_name );
        ?>
    <?php endif;
    if ( get_the_terms( get_the_ID(), 'event-category' ) && ! is_wp_error( get_the_terms( get_the_ID(), 'event-category' ) ) ) {
        $category = get_the_terms( get_the_ID(), 'event-category' )[0]->name;
    } else {
        $category = get_bloginfo( 'name' );
    }
    ?>
    <?php
    do_action( 'eventorganiser_additional_event_meta' );
    ?>
</aside><!-- #secondary -->