<?php
/**
 * The template for displaying the Jeremy Job post type archive. It shows
 * linked names of every author who has at least one post of this type, with
 * a list of their linked posts underneath.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
get_header(); ?>
<div id="primary" class="content-area">
    <?php jeremy_breadcrumbs(); ?>
    <header>
        <?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
        <p><?php _e( 'Here are current job openings from our members.', 'jeremy' ); ?></p>
    </header><!-- .page-header -->
    <main id="main" class="site-main job-archive">

        <?php if ( have_posts() ) {
            $authors = get_users( array(
                'orderby' => 'post_count',
                'order' => 'DESC',
                'has_published_posts' => array( 'jeremy_job' ),
            ) );
            if ( $authors ) {
                foreach ( $authors as $author ) {
                    echo '<section>';
                    echo '<h3>' . $author->display_name . '</h3>';
                    $deals = get_posts( array(
                        'post_type' => 'jeremy_job',
                        'author' => $author->ID,
                    ) );
                    echo '<ul>';
                    foreach ( $deals as $deal ) {
                        echo '<li>';
                        echo '<a href="' . esc_url( get_permalink( $deal->ID ) ) . '">';
                        echo $deal->post_title;
                        echo '</a>';
                        echo '</li>';
                    }
                    echo '</ul>';
                    echo '</section>';
                }
                wp_reset_postdata();
            }
        } else {
            get_template_part( 'template-parts/content', 'none' );
        } ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar('posts');
get_footer();
