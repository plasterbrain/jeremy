<?php
/**
 * Theme customizations which modify default WordPress beavhior. You can safely
 * remove this file if you are a WordPress purist or something, because it seems
 * if that's the case we may never understand each other...
 *
 * @package Jeremy
 * @since 1.0.0
 */
define( 'BP_ENABLE_ROOT_PROFILES', true );
define ( 'BP_DISABLE_ADMIN_BAR', true );

add_filter( 'body_class', 'jeremy_clean_body_classes', 10, 2 );
/**
 * Cleans up most of the classes added to body by WordPress.
 *
 * @package Jeremy
 * @since 1.0.0
 * 
 * @link https://developer.wordpress.org/reference/functions/get_body_class/
 * 
 * @param array $classes  List of classes WordPress adds to body by default
 * @return array          The filtered list of classes
 */
function jeremy_clean_body_classes( $classes ) {
	global $wp_query;
	$whitelist = array(
		'rtl',
		'home',
		'blog',
		'archive',
		'search',
		'single',
		'admin-bar',
		'no-customize-support',
		'custom-background',
		'page'
	);

	if ( is_singular() ) { // Adds single-{post_type}
		$post_id = $wp_query->get_queried_object_id();
		$post = $wp_query->get_queried_object();
		if ( is_single() )
			$whitelist[] = 'single-' . sanitize_html_class( $post->post_type, $post_id );
	}

    $classes = array_intersect( $classes, $whitelist );
	return $classes;
}

add_filter( 'comment_class', 'jeremy_clean_comment_classes' );
/**
 * Cleans up most of the classes added to comments by WordPress.
 * 
 * @package Jeremy
 * @since 1.0.0
 * 
 * @param array $classes  List of classes WordPress adds to comments
 * @var array $whitelist  List of classes to keep
 * @return array          The approved classes to add
 */
function jeremy_clean_comment_classes( $classes ) {
	$whitelist = array(
		'comment',
		'single-comment',
		'odd',
		'even',
		'depth-1',
		'depth-2'
	);

	$classes = array_intersect( $classes, $whitelist );
	return $classes;
}

add_action('login_head', 'jeremy_remove_login_shake');
/**
 * Removes the default WordPress shaking behavior.
 * 
 * @package Jeremy
 * @since 1.0.0
 */
function jeremy_remove_login_shake() {
	remove_action('login_head', 'wp_shake_js', 12);
}
