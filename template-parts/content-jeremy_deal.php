<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jeremy
 */
?>
<article class="flex single-deal">
    <?php
    if ( has_post_thumbnail() ) { ?>
        <div class="deal-img" style="background-color:white;border:1px solid #ccc;background-image:url('<?php the_post_thumbnail_url();?>')"></div>
    <?php } elseif ( get_post_meta( get_the_ID(), '_jeremydeal_code', true ) ) { ?>
        <div class="deal-img">
            <p class="deal-code">
                <?php printf( __( 'CODE:', 'jeremy' ) . '<br />%s', get_post_meta( get_the_ID(), '_jeremydeal_code', true ) ); ?>
            </p>
        </div>
    <?php }?>
	<div class="deal-content">
	    <header class="entry-header">
            <h2 class="entry-title">
                <a href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <section class="deal-meta flex">
                <?php
                $author_link = get_the_author_link();
                if ( function_exists( 'buddypress' ) ) {
                    if ( bp_is_user() ) {
                        $author_link = get_the_author();
                    } else {
                        $author_link = esc_url(bp_core_get_user_domain(get_the_author_meta('ID')));
                        echo '
                        <a href="' . $author_link . '">' .
                            bp_core_fetch_avatar(array('item_id'=>get_the_author_meta('ID'),'width'=>30,'height'=>30,)) .
                        '</a>';
                        $author_link = sprintf('<a href="%s">%s</a>',bp_core_get_user_domain(get_the_author_meta('ID')),get_the_author());
                    }
                } ?>
                <p>
                    <?php 
                    echo $author_link;
                    if ( get_post_meta( get_the_ID(), '_jeremydeal_expiry', true ) ) {
                        printf( ' <span class="deal-expires">' . _x( '(Expires %s)', 'Coupon/sale expiration date', 'jeremy' ) . '</span>', get_post_meta( get_the_ID(), '_jeremydeal_expiry', true ) );
                    } ?>
                </p>
            </section>
        </header><!-- .entry-header -->
        <?php the_content(); ?>
        <footer class="deal-footer flex">
            
            <?php
            if ( get_the_terms( get_the_ID(), 'jeremy_deal_tag' ) ) { ?>
                <p class="field-desc"><?php echo get_the_term_list(get_the_ID(),'jeremy_deal_tag',__('Tags:','jeremy').' ',',','');?></p>
            <?php } ?>
            <?php if ( get_post_meta( get_the_ID(), '_jeremydeal_link', true ) ) {
                printf( '<a href="%s" class="button deal-button">' . __( 'Get this deal', 'jeremy' ) . '</a>', get_post_meta( get_the_ID(), '_jeremydeal_link', true ) );
            } ?>
        </footer>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
