<?php
/**
 * Event Organiser Compatibility Layer
 *
 * The Jeremy theme is designed to work with the free version of Event
 * Organiser, a calendar/events plug-in by Stephen Harris.
 *
 * @link https://wp-event-organiser.com/
 *
 * @package Jeremy
 * @subpackage Events
 * @since 2.0.0
 *
 * Changelog:
 * 2.0.0
 * - Renamed to "Organiser," with an "s," to match British spelling :P
 * - Removed redundant template tag functions.
 */

/* Remove the inline-styled text color on the EO fullcalendar */
add_filter( 'eventorganiser_fullcalendar_event', function( $event ) {
	unset( $event['textColor'] );
	return $event;
} );

/* ===== Template Tags ===== */

/**
 * A wrapper for {@see jeremy_get_addtocal_links} using Event Organiser data.
 * 
 * @since 2.0.0
 * 
 * @param WP_Post|int $post	 	The current post or post ID.
 * @param int					$occ_id	The event occurrence ID, default false.
 * @param array  			$args  Arguments for {@see jeremy_get_addtocal_links}.
 */
function jeremy_eo_get_addtocal_links( $post = false, $occ_id = false ) {
	$post = get_post( $post );
	if ( ! $post ) { return false; }
	
	$google = eo_get_add_to_google_link( $post->ID, $occ_id );
	$rrule = eventorganiser_generate_ics_rrule( $post->ID );
	if ( $rrule ) {
		// We can use the ICS RRULE format for Google links!!
		$google .= '&recur=RRULE:' . $rrule;
	}
	
	/*
	Yahoo seems to not support the address parameters anymore.
	@TODO full state names don't work for this just sayin'
	
	$address = eo_get_venue_address();
	$address2 = '';
	if ( ! empty( $address['city'] ) &&
			 ! empty( $address['state'] ) &&
			 ! empty( $address['postcode'] ) ) {
		// e.g., "Tampa, FL, 33602"
		$address2 = implode( ', ', array(
			$address['city'],
			$address['state'],
			$address['postcode'] ) );
	}
	*/
	
	return jeremy_get_addtocal_links( $post, array(
		'start'  		 => eo_get_the_start( DATETIMEOBJ, $post->ID, $occ_id ),
		'end'    		 => eo_get_the_end( DATETIMEOBJ, $post->ID, $occ_id ),
		'allday' 		 => eo_is_all_day( $post->ID ),
		'rrule'  		 => $rrule,
		'venue'  		 => eo_get_venue_name( $post->ID ),
		//'address1' => $address['address'],
		//'address2' => $address2,
		'uid'    		 => eo_get_event_uid( $post->ID ), // Unique ID for calendars
	), array(
		'google_cal' => $google,
		'ics'	 			 => jeremy_eo_get_feed_link(),
	) );
}

/**
 * Returns the "eo-events" feed link for the given single post or date. It will
 * be downloaded by the browser as a formatted .ics file.
 * 
 * @TODO test for failure on non-events or with plugin disabled.
 *
 * @since 2.0.0
 *
 * @param string|WP_Post $post	The post ID or object, default the current post.
 * 															This is ignored if $date is set.
 * @param array 				 $date	{
 * 		The date to use for a date-based feed link.
 * 		@type string $year				The year to use. Required.
 * 		@type string $month				The month to use. Required for month- and day-
 * 															based feeds.
 * 		@type string $day					The day to use. Required for day-based feeds.
 * }
 * @return string								The EO feed link for the single post.
 */
function jeremy_eo_get_feed_link( $post = null, $date = array() ) {
	global $wp_rewrite;
	$feed_name = 'eo-events';
	
	if ( $date && is_array( $date ) ) {
		$date =wp_parse_args( $date, array(
			'year' 	=> null,
			'month' => null,
			'day' 	=> null,
		) );
		
		if ( ! $date['year'] ) {
			return false;
		}
		
		if ( $date['day'] ) {
			if ( $date['month'] ) {
				$link = eo_get_event_archive_link( $date['year'], $date['month'], $date['day'] );
			} else {
				return false;
			}
		} elseif ( $date['month'] ) {
			$link = eo_get_event_archive_link( $date['year'], $date['month'] );
		} else {
			$link = eo_get_event_archive_link( $date['year'] );
		}
	} else {
		$post = get_post( $post );
		if ( ! $post || $post->post_type !== 'event' ) {
			return false;
		}
		
		$link = get_the_permalink( $post );
	}
	
	
	$feed_struct = $wp_rewrite->get_feed_permastruct();
	if ( '' !== $feed_struct ) {
		$feed_struct = str_replace( '%feed%', $feed_name, $feed_struct );
		$feed_struct = preg_replace( '#/+#', '/', "$feed_struct" );
		$link    	 	 = user_trailingslashit( $link ) . $feed_struct;
	} else {
		$link = $link . "?feed={$feed_name}";
	}
	
	/** {@see feed_link} */
	return apply_filters( 'feed_link', $link, $feed_name );
}

/**
 * Since EO returns a venue address as an array, this function is a simple
 * wrapper to implode those array elements into one uniform structure.
 * 
 * @TODO Country code removal as an option
 * @TODO A filter on this would be good too I suppose
 *
 * @since 2.0.0
 * 
 * @param  int|string $venue  The venue ID or slug, to be supplied to
 * 														{@see eo_get_venue_address}.
 * @return string				 			The venue address as a formatted string. It's not
 * 														escaped, so remember to escape the output.
 */
function jeremy_eo_get_address( $venue = null ) {
	$address = eo_get_venue_address( $venue );
	/* translators: %1$s is the address line ("123 Sesame St."), %2$s is the city name, %3$s is the state, %4$s is the zip code. */
	return sprintf( __( '%1$s, %2$s, %3$s  %4$s', 'jeremy' ), $address['address'], $address['city'], $address['state'], $address['postcode'] );
}

/**
 * Check if the given search query (an event venue name) matches any existing
 * user display names. That way we can link an event venue to a user profile
 * instead of the generic venue archive page.
 *
 * @since 1.0.0
 *
 * @see WP_User_Query
 * @see bp_core_get_user_domain
 *
 * @param string $venue   The venue name to be used as a search query
 * @param bool $force_bp  Whether to force the function to return BuddyPress
 * 						  link instead of falling back to the EO venue link.
 * 						  Default false.
 * @return string|bool    The URL of the first matching user's Buddypress
 * 						  profile on success. False if BuddyPress isn't active
 * 						  and $force_bp is true.
 */
function jeremy_get_the_venue_link( $venue, $force_bp = false ) {
	if ( $force_bp && ! function_exists( 'buddypress' ) )
		return false;
	$user_query = new WP_User_Query( array(
		'search'         => $venue,
		'search_columns' => array( 'display_name' ),
		'fields' => 'ID',
	) );
	$users = $user_query->get_results();
	if ( ! empty( $users ) ) {
		$first_user = $users[0];
		$link = bp_core_get_user_domain( $first_user );
	} else {
		$venue_slug = eo_get_venue_slug( $venue );
		$link = eo_get_venue_link( $venue_slug );
	}
	return esc_url( $link );
}

/**
 * Returns whether the given event occurrence is past.
 * 
 * @since 2.0.0
 * 
 * @param  int  $id     Event post ID.
 * @param  int  $occ_id Event occurrence ID, default the last occurrence.
 * @return boolean      Whether the occurrence is considered past, or null on
 * 											failure.
 */
function jeremy_eo_is_past( $id, $occ_id = null ) {
	if ( ! $occ_id ) {
		// Get the last occurrence
		$occurrences = eo_get_the_occurrences_of( $id );
		if ( ! is_array( $occurrences ) ) {
			return null;
		}
		$occ_id = array_key_last( $occurrences );
	}
	
	$now = current_datetime();
	
	if ( eventorganiser_get_option( 'runningisnotpast' ) ) {
		$end = eo_get_the_end( DATETIMEOBJ, $id, null, $occ_id );
		return $end < $now;
	} else {
		$start = eo_get_the_start( DATETIMEOBJ, $id, null, $occ_id );
		return $start <= $now;
	}
}

/**
 * Returns true if an event starts on one day and ends at 12:00am the next day.
 * This is used by the event archive listing template to alter the date listing
 * (e.g. "Oct 3 7pm - Oct 4 12am" becomes "Oct 3 7pm - 12am").
 *
 * @since 2.0.0
 *
 * @param int $id				The event ID, default current event in an EO loop.
 * @param int $occ_id		The event occurrence ID, default current occurrence.
 * @return bool|null		Whether an event lasting less than 24 hours ends between
 * 											12-5am. If the event is over 24 hours, it returns null
 * 											instead of false, so you can use !== null with this
 * 											function to match any occurrences that are only 1 day.
 */
function jeremy_eo_cinderella( $id = false, $occ_id = false ) {
	$end = eo_get_the_end( DATETIMEOBJ, $id, $occ_id );
	$start = eo_get_the_start( DATETIMEOBJ, $id, $occ_id );
	if ( ! is_a( $end, 'DateTime' ) ||
			 ! is_a( $start, 'DateTime' ) ) {
		return false;
	}
	
	if ( absint( eo_date_interval( $start, $end, 'h' ) ) >= 24 ) {
		return null;
	}
	
	$hour = intval( $end->format( 'G' ) );
	// Any time between 12:00am and 5:00am
	return $hour >= 0 && $hour < 5;
}

/**
 * Returns the ID of the next non-past occurrence of an event. This is to
 * prevent events with one or more finished occurrence from being listed as
 * upcoming but showing a date in the past.
 * 
 * @TODO Get first occurrence matching eo_get_event_archive_date... someday!
 *
 * @since 2.0.0
 * 
 * @param  int $event_id ID of the event to get occurrences for.
 * @return int					 ID of the correct occurrence.
 */
function jeremy_eo_get_next_occurrence_id( $event_id ) {
	$occ_ids = eo_get_the_occurrences_of( $event_id );
	
	if ( is_array( $occ_ids ) ) {
		if ( eo_get_event_archive_date() || count( $occ_ids ) == 1 ) {
			// Return either the only occurrence or the first if we're in a historical archive
			return array_key_first( $occ_ids );
		}
		
		$now = new DateTime( current_time( 'r' ) );
		$running_current = eventorganiser_get_option( 'runningisnotpast' );
		foreach ( $occ_ids as $occ_id=>$occ ) {
			if ( ! jeremy_eo_is_past( $event_id, $occ_id ) ) {
				return $occ_id;
			}
		}
		// Well we tried
		return array_key_first( $occ_ids );
	} else {
		return false;
	}
}