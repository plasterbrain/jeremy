<?php

?>
<article class="flex" <?php if ( is_single() ) echo ' itemtype="http://data-vocabulary.org/Event"'; ?>>
    <?php
    if ( has_post_thumbnail() ) {
        the_post_thumbnail( 'post-thumbnail', ['class' => 'event-img'] );
    } else {
        printf( '<div class="event-img">%s<br />%s</div>', eo_get_the_start( 'M' ), eo_get_the_start( 'j' ) );
    }?>
    <div class="event-content">
        <header class="entry-header">
            <h2 class="entry-title">
                <a href="<?php echo eo_get_permalink(); ?>" itemprop="url"><?php the_title() ?></a>
            </h2>
            <p class="entry-footer"><em>
            <?php
            $date = eo_get_the_start( 'F jS' );
            $venue = eo_get_venue_name();
            if ( $venue ) {
                printf( '%s' . __( ' @ ', 'jeremy' ) . '%s', $date, $venue );
            } else {
                echo $date;
            } ?>
            </em></p>
        </header><!-- .entry-header -->
        <?php echo jeremy_get_the_excerpt( array( 'length' => 20, 'readmore' => true ) ); ?>
 </div><!-- .entry-content -->
</article>