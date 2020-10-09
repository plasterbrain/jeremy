<?php
/**
 * Displays the event meta in the sidebar.
 *
 * @package Jeremy
 * @subpackage Events
 * @since 1.0.0
 */

$id = get_the_id();

$format_day = esc_attr_x( 'F jS, Y', 'Event date format (day)', 'jeremy' );
$format_time = esc_attr_x( 'g:ia', 'Event date format (time)', 'jeremy' );

$rsvp_cost = get_post_meta( get_the_ID(), '_jeremysfriend_cost', true );
$rsvp_link = get_post_meta( get_the_ID(), '_jeremysfriend_link', true );
$rsvp_tel = get_post_meta( get_the_ID(), '_jeremysfriend_phone', true );

$cats = get_the_terms( get_the_ID(), 'event-category' );
?>

<aside id="secondary" class="main__page-noflex__sidebar" aria-labelledby="sidebar__title">
	<h2 id="sidebar__title" class="screen-reader-text"><?php esc_html_e( 'Event Details' ); ?></h2>
	<section aria-labelledby="date__title">
	<?php 
	$past = false; // Whether any of the occurences are upcoming.
	if ( eo_recurs() ) { // Recurring events ?>
		<h3 id="date__title" class="widget-name"><?php esc_html_e( 'Event Dates', 'jeremy' );?></h3>
		
		<?php $upcoming = new WP_Query(array(
			'post_type'         => 'event',
			'posts_per_page'    => -1,
			'event_series'      => get_the_ID(),
			'group_events_by'   => 'occurrence',
		) );
		
		if ( $upcoming->have_posts() ) {
			$past = true; // True until we find an upcoming occurence. ?>
			
			<ul class="entry__meta-date">
				<?php while ( $upcoming->have_posts() ) : $upcoming->the_post();
					global $post;
					// Check if this occurence has already passed.
					$past_class = ' deleted';
					if ( ! jeremy_eo_is_past( $id, $post->occurrence_id ) ) {
						$past_class = '';
						$past = false;
					} ?>
					<li class="entry__meta-date__list__item<?php echo $past_class; ?>">
						<time datetime="<?php echo esc_attr( eo_get_the_start( 'c' ) ); ?>">
							<?php echo esc_html( eo_format_event_occurrence( false, false, $format_day, $format_time, ' &ndash; ', false ) ); ?>
						</time>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php wp_reset_postdata(); ?>
		<?php } else { ?>
			<p><?php esc_html_e( 'There are no dates to show. :(', 'jeremy' ); ?></p>
		<?php } // endif has recurring dates ?>
	<?php } else { // Singular events
    $past = true;
		$past_class = ' deleted';
		if ( ! jeremy_eo_is_past( $id ) ) {
			$past_class = '';
			$past = false;
		} 
		
    // If an event is all within a single day, only show one date section.
		$show_end = false;
		$start_day = eo_get_the_start( $format_day );
		$end_day = eo_get_the_end( $format_day );
		if ( ! eo_is_all_day() ) {
			$show_end = $start_day !== $end_day && ! jeremy_eo_cinderella();
		}
		if ( $show_end ) {
			$start_time = eo_get_the_start( $format_time );
		} else {
			$start_time = eo_format_event_occurrence( false, false, '', $format_time, ' &ndash; ', false );
		}
		?>
		<h3 class="entry__meta-date__title screen-reader-text" id="date__title">
			<?php esc_html_e( 'Event Date', 'jeremy' ); ?>
		</h3>
		<div class="entry__meta-date" role="presentation">
			<?php if ( $show_end ) { ?>
				<h4><?php echo esc_html_x( 'Starts', 'As in, "the party starts now"', 'jeremy' ); ?></h4>
			<?php } ?>
			<p class="entry__meta-date-start">
				<time class="entry__meta-date__day<?php echo $past_class; ?>" datetime="<?php echo esc_attr( eo_get_the_start( 'c' ) ); ?>">
					<?php echo esc_html( eo_get_the_start( $format_day ) ); ?>
				</time>
				<?php if ( ! eo_is_all_day() ) { ?>
					<span class="event__meta-date__time<?php echo $past_class; ?>">
						<?php echo esc_html( $start_time ); ?>
					</span>
				<?php } ?>
			</p>
			<?php if ( $show_end ) { ?>
				<h4><?php echo esc_html_x( 'Ends', 'As in, "the event ends tomorrow"', 'jeremy' ); ?></h4>
				<p class="entry__meta-date-end">
					<time class="entry__meta-date__day<?php echo $past_class; ?>" datetime="<?php echo esc_attr( eo_get_the_end( 'c' ) ); ?>">
						<?php echo esc_html( eo_get_the_end( $format_day ) ); ?>
					</time>
					<?php if ( ! eo_is_all_day() ) { ?>
						<span class="event__meta-date__time<?php echo $past_class; ?>">
							<?php echo esc_html( eo_get_the_end( $format_time ) ); ?>
						</span>
					<?php } ?>
				</p>
			<?php } ?>
		</div><!-- .entry__meta-date -->
	<?php } // End of event date ?>
	<?php if ( $past ) { ?>
		<p><?php esc_html_e( 'This event has passed.', 'jeremy' ); ?></p>
	<?php } else { ?>			
		<?php jeremy_addtocal_button( jeremy_eo_get_addtocal_links() ); ?>
	<?php } ?>
	</section>
	
	<?php if ( has_post_thumbnail() ) { ?>
		<section>
			<?php the_post_thumbnail(); ?>
		</section>
	<?php } ?>
	
	<?php if ( $rsvp_cost || $rsvp_link || $rsvp_tel ) { ?>
		<section aria-labelledby="rsvp__title">
			<h3 id="rsvp__title" class="widget-name">
				<?php esc_html_e( 'RSVP', 'jeremy' ); ?>
			</h3>
			<?php if ( $rsvp_cost ) { ?>
				<section class="entry__meta-rsvp">
					<h4 class="entry__meta-rsvp__label">
						<?php esc_html_e( 'Cost:', 'jeremy' ); ?>
					</h4>
					<span class="entry__meta-rsvp__content">
						<?php echo esc_html( $rsvp_cost ); ?>
					</span>
				</section>
			<?php } ?>
			<?php if ( $rsvp_link ) {
				$link_name = parse_url( $rsvp_link );
				if ( array_key_exists( 'host', $link_name ) ) {
					$link_name = $link_name['host'];
				} elseif( array_key_exists( 'scheme', $link_name ) && $link_name['scheme'] === 'mailto' ) {
					/* translators: %s is the email address. Duh. */
					$link_name = array_key_exists( 'path', $link_name ) ? sprintf( __( 'Email %s' ), $link_name['path'] ) : __( 'Send an Email', 'jeremy' );
				} else {
					$link_name = $rsvp_link;
				}
				?>
				<section class="entry__meta-rsvp">
					<h4 class="entry__meta-rsvp__label">
						<?php esc_html_e( 'Online:', 'jeremy' ); ?>
					</h4>
					<a href="<?php echo esc_url( $rsvp_link ); ?>" class="entry__meta-rsvp__content">
						<?php echo esc_html( $link_name ); ?>
					</a>
				</section>
			<?php } ?>
			<?php if ( $rsvp_tel ) { ?>
				<section class="entry__meta-rsvp">
					<h4 class="entry__meta-rsvp__label">
						<?php esc_html_e( 'Call:', 'jeremy' ); ?>
					</h4>
					<a href="tel:<?php echo esc_attr( $rsvp_tel ); ?>" class="entry__meta-rsvp__content">
						<?php echo esc_html( $rsvp_tel ); ?>
					</a>
				</section>
			<?php } ?>
		</section>
	<?php } ?>

	<?php if ( eo_get_venue() ) {
		$venue_name = eo_get_venue_name();
		?>
		<section aria-labelledby="venue__title">
			<h3 id="venue__title" class="widget-name">
				<?php esc_html_e( 'Location', 'jeremy' ); ?>
			</h3>
			<h4 class="entry__meta-venue"><a href="<?php echo esc_url( jeremy_get_the_venue_link( $venue_name ) ); ?>"><?php echo esc_html( $venue_name ); ?></a></h4>
			<address><?php echo esc_html( jeremy_eo_get_address() ); ?></address>
			<?php
			if ( eo_venue_has_latlng( eo_get_venue() ) &&
					 get_theme_mod( 'use_maps' ) ) { ?>
				<?php echo eo_get_venue_map(); ?>
				<?php jeremy_directions_button( $venue_name ); ?>
			<?php } ?>
		</section>
	<?php } // End of event venue ?>
	
	<?php if ( $cats && ! is_wp_error( $cats ) ) { ?>
		<section aria-labelledby="cat__title">
			<h3 id="cat__title" class="widget-name">
				<?php esc_html_e( 'Category', 'jeremy' ); ?>
			</h3>
			<ul class="nav__list nav__list-h">
				<?php foreach ( $cats as $cat ) { ?>
					<li>
						<a class="entry__meta-terms__item" style="background: <?php echo esc_attr( eo_get_category_color( $cat->term_id ) ); ?>" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
							<?php echo esc_html( $cat->name ); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
		</section>
	<?php } // End of categories ?>

	<?php if ( has_action( 'eventorganiser_additional_event_meta' ) ) { ?>
	<section>
		<h3 class="widget-name"><?php esc_html_e( 'More Info', 'jeremy' ); ?></h3>
		<?php do_action( 'eventorganiser_additional_event_meta' ); ?>
	</section>
	<?php } // End of additional meta ?>
</aside>