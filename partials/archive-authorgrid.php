<?php
/**
 * The template for displaying archive pages
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 2.0.0
 */
 
$post_type = get_query_var( 'post_type' );
if ( is_array( $post_type ) ) {
	$post_type = $post_type[0];
}
$post_type_obj = get_post_type_object( $post_type );
?>

<div class="page__content archive archive-grid" role="presentation">
  <?php if ( have_posts() ) {
    $authors = get_users( array(
      'orderby' => 'post_count',
      'order' => 'DESC',
      'has_published_posts' => array( $post_type ),
    ) );
    
    if ( $authors ) { ?>
      <?php foreach ( $authors as $author ) { ?>
        <?php $posts = get_posts( array(
          'post_type' => $post_type,
          'author' => $author->ID,
        ) ); ?>
        <section class="archive-grid__section">
          <header class="archive-grid__section__header flex">
            <?php //echo get_avatar( $author->ID, 28 ); ?>
            <h3 id="<?php echo esc_attr( $post_type . '-' . $author->ID ); ?>" class="archive-grid__section__title">
              <?php echo esc_html( $author->display_name ); ?>
            </h3>
          </header>
          
          <ul class="archive-grid__section__list">
            <?php foreach ( $posts as $post ) {
              $link = get_permalink( $post->ID );
              if ( $post->post_type === 'jeremy_job' && get_post_format( $post ) === 'link' ) {
                $link = get_post_meta( $post->ID, '_jeremysfriend_link', true ) ?: $link;
              } ?>
              <li><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $post->post_title ); ?></a></li>
            <?php } ?>
          </ul>
        </section>
      <?php } ?>
      
      <?php wp_reset_postdata(); ?>
    <?php } else { ?>
      <p class="archive__none">
        <?php /* translators: %s is the plural name of the post type */
        echo esc_html( sprintf( __( 'Sorry, there are no %s to show.', 'jeremy' ), strtolower( $post_type_obj->labels->name ) ) ); ?>
      </p>
    <?php } ?>
  <?php } // endif post exists ?>
</div><!-- .page__content -->