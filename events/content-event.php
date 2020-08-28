<?php
/**
 * Displays the content for a single Event Organiser event page.
 *
 * @package Jeremy
 * @subpackage Jeremy/Events
 * @since 1.0.0
 */
?>
<article>
	<header class="entry-header">
        <?php
        if ( has_post_thumbnail() ) {
            the_post_thumbnail( 'post-thumbnail', ['class' => 'featured-img'] );
        }
        the_title( '<h1 class="entry-title">', '</h1>' );
        echo '<p><em>' . eo_format_event_occurrence() . '</em></p>';
        ?>
    </header><!-- .entry-header -->
	<div class="entry-content">
		<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'jeremy' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) );

			wp_link_pages();
		?>
	</div><!-- .entry-content -->

	<footer>
		<div class="event-meta flex">
            <?php
            if ( get_the_terms( get_the_ID(), 'event-category' ) && ! is_wp_error( get_the_terms( get_the_ID(), 'event-category' ) ) ) {
                ?><span><?php
                esc_html_e( 'Posted in', 'jeremy' ); ?>: <?php echo get_the_term_list( get_the_ID(),'event-category', '', ', ', '' );
                ?></span><?php
            }
            echo '<span>';
            if ( get_post_meta( get_the_ID(), 'link', true ) ) {
                $label = sprintf( __( 'RSVP for %s', 'jeremy' ), get_the_title() );
                printf( '<a aria-label="%s" href="%s">' . __( 'RSVP', 'jeremy' ) . '</a> &bullet; ',  $label, get_post_meta( get_the_ID(), 'link', true ) );
            }
            printf( '<a href="%s">' . __( 'Add to Google Calendar', 'jeremy' ) . '</a>', esc_url( eo_get_add_to_google_link() ) );
            echo '</span>';
            ?>
		</div>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
