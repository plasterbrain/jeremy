<?php
/**
 * Template Name: Blank
 * Description: A skinny blank page that's good for forms and mobile display.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */

get_header( 'minimal' ); ?>
<body id="page" class="page page-blank">
<?php while ( have_posts() ) : the_post(); ?>
    <header class="entry-header">
        <?php the_title( '<h1 class="screen-reader-text">', '</h1>' ); ?>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php
            the_content();
            wp_link_pages( array(
                'before' => '<div class="pagination">' . esc_html__( 'Pages:', 'jeremy' ),
                'after'  => '</div>',
            ) );
        ?>
    </div><!-- .entry-content -->
    <?php
    // If comments are open or we have at least one comment, load up the comment template.
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;
endwhile; // End of the loop.
get_footer('minimal');
