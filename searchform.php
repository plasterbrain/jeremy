<?php
/**
 * Search Form template
 *
 * Used to generate WordPress's built-in search form. This template is based on
 * the one from TwentyTwenty.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 2.0.0
 */

$search_id = wp_unique_id( 'form-search-' );

$aria = ! empty( $args['label'] ) ? esc_attr( $args['label'] ) : esc_attr__( 'Search Form', 'jeremy' );

if ( is_404() ) {
	// Populate search with a readable version of the requested URL.
	global $wp;
	$search = sanitize_text_field( urldecode( $wp->request ) );
	$search = str_replace( array( '-', '/', '_', ), ' ', $search );
} else {
	$search = get_search_query();
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<form role="search" aria-label="<?php echo $aria; ?>" method="get" class="form-search flex" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>">
		<?php esc_html_e( 'Search term', 'jeremy' ); ?>
	</label>
	<input type="search" id="<?php echo esc_attr( $search_id ); ?>" class="search__input" placeholder="<?php echo esc_attr__( 'Search&hellip;', 'jeremy' ); ?>" value="<?php echo esc_attr( $search ); ?>" name="s" />
	<button type="submit" class="button button-search" />
  	<?php echo jeremy_get_svg( array(
			'img' 	=> 'form-search',
			'alt' 	=> esc_attr__( 'Submit', 'jeremy' ),
			'class' => 'button-search__icon',
			'inline'=> true,
		) ); ?>
  </button>
</form>