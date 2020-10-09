<?php
/**
 * Jetpack Compatibility Layer
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 2.0.0
 */

if ( ! function_exists( 'jeremy_jetpack_setup' ) ) :
/**
 * Jetpack setup function.
 * 
 * @since 1.0.0
 *
 * @link https://jetpack.com/support/infinite-scroll/
 * @link https://jetpack.com/support/responsive-videos/
 * @link https://jetpack.com/support/content-options/
 */
function jeremy_jetpack_setup() {
	// Add theme support for Infinite Scroll.
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'render'    => 'jeremy_infinite_scroll_render',
		'footer'    => 'page',
	) );

	// Add theme support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );

	// Add theme support for Content Options.
	add_theme_support( 'jetpack-content-options', array(
		'post-details' => array(
			'stylesheet' => 'jeremy-style',
			'date'       => '.posted-on',
			'categories' => '.cat-links',
			'tags'       => '.tags-links',
			'author'     => '.byline',
			'comment'    => '.comments-link',
		),
	) );
}
add_action( 'after_setup_theme', 'jeremy_jetpack_setup' );
endif;

/**
 * Renders content for Jetpack's infinite scroll feature.
 */
function jeremy_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		if ( is_search() ) {
			get_template_part( 'partials/content', 'search' );
		} else {
			get_template_part( 'partials/content', get_post_format() );
		}
	}
}