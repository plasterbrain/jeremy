<?php
/**
 * Shows the content for a single Jeremy Job post type in the loop.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
?>
<article class="single-job">
    <section class="job-meta">
        <header class="job-header">
            <h2 class="entry-title">
                <a href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <div class="entry-meta">
                <?php jeremy_entry_meta(); ?>
            </div>
        </header><!-- .entry-header -->
        <?php if ( is_single() ) {
        // Generate a list of jobs by the same author in the entry footer.
        ?>
            <footer class="job-footer">
                <?php
                $args = array(
                    'posts_per_page'   => 3,
                    'offset'           => 0,
                    'post_type'        => 'jeremy_job',
                    'post_mime_type'   => '',
                    'post_parent'      => '',
                    'post__not_in' => array( get_the_ID() ),
                    'author'	   => get_the_author_meta( 'ID' ),
                    'post_status'      => 'publish',
                    'suppress_filters' => true 
                );
                $related = new WP_Query( $args );
                if ( $related->have_posts() ) {
                    printf( '<h3>' . __( 'More jobs' ) . '</h3>' );
                    echo '<ul>';
                    while ( $related->have_posts() ) {
                        $related->the_post();
                        printf( '<a href="%s"><li>%s</li></a>', get_the_permalink(), get_the_title() );
                    }
                    echo '</ul>';
                }
                wp_reset_postdata();
                ?>
            </footer><!-- .job-footer -->
        <?php } ?>
    </section>
    <section class="job-content">
        <?php if ( is_single() ) {
            the_content();
            $apply = esc_url( get_post_meta( get_the_ID(), '_jeremyjob_link', true ) );
            if ( ! empty( $apply ) )
                echo '
                <div class="job-button">
                    <a class="button" href="' . $apply . '">' . __( 'Apply', 'jeremy-cpt' ) . '</a>
                </div>';
        ?>
        <?php
        } else {
            // If not single, show the excerpt and a "read more" link.
            $args = array(
                'length' => 50,
                'readmore' => true,
            );
            echo jeremy_get_the_excerpt( $args );
        } ?>
    </section><!-- .job-content -->
</article>
