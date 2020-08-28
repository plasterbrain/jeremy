<?php
/**
 * Template Functions
 *
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package Jeremy
 * @subpackage Jeremy/Includes
 * @since 1.0.0
 */

if ( ! function_exists( 'jeremy_comment_form_fields' ) ) :
add_filter( 'comment_form_fields', 'jeremy_comment_form_fields' );
/**
 * Removes the comment textarea field from the array and adds it to the end. This
 * has the effect of ensuring the name/email/website fields show up on the comment
 * form first.
 *
 * @package Jeremy
 * @since 1.0.0
 *
 * @link https://developer.wordpress.org/reference/hooks/comment_form_fields/
 *
 * @param array $fields The array of default comment fields. Contains 'comment',
 * 						'author', 'email', and 'url' by default.
 */
function jeremy_comment_form_fields( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}
endif;

if ( ! function_exists( 'jeremy_truncate_menu_items' ) ) :
add_filter( 'nav_menu_item_title', 'jeremy_truncate_menu_items', 10, 2 );
/**
 * Truncate nav menu item names over 40 characters to the nearest whole word,
 * if possible, to prevent overly long names from taking an excessive amount of
 * space and RUINING MY LAYOUT.
 *
 * @package Jeremy
 * @since 1.0.0
 *
 * @link https://developer.wordpress.org/reference/hooks/nav_menu_item_title/
 *
 * @param string $title The menu item name.
 * @return string 		The shortened menu item name.
 */
function jeremy_truncate_menu_items( $title ) {
	$length = 40;
	if ( strlen( $title ) > $length ) {
		$space = strrpos(substr($title, 0, $length), ' ');
		if ( $space ) {
			/* Try to avoid cutting off in the middle of a word. */
			$title = substr($title, 0, strrpos($space));
		} else {
			$title = substr($title, 0, $length);
		}
		$title .= '...';
	}
	return $title;
}
endif;

if ( ! function_exists( 'jeremy_login_errors' ) ) :
add_filter('login_errors', 'jeremy_login_errors');
/**
 * Replaces the default WordPress error message with a more generic one.
 *
 * @package Jeremy
 * @since 1.0.0
 */
function jeremy_login_errors() {
    $error = __( "We couldn't log you in. Check your credentials and try again.", 'jeremy' );
	return $error;
}
endif;

add_filter( 'login_headertitle', 'jeremy_login_headertitle');
/**
 * Replaces the default WordPress login header title attribute with your
 * site name and tagline.
 *
 * @package Jeremy
 * @since 1.0.0
 */
function jeremy_login_headertitle( $title ) {
	$site = get_bloginfo( 'name' );
	return $site;
}

add_filter( 'login_headerurl', 'jeremy_login_headerurl' );
/**
 * Changes the WordPress login logo link to the homepage instead of WordPress.org
 *
 * @package Jeremy
 * @since 1.0.0
 */
function jeremy_login_headerurl( $url ) {
    return home_url();
}

add_action( 'template_redirect', 'jeremy_login_redirect' );
function jeremy_login_redirect() {
	if ( is_page_template( 'templates/template-login.php' ) && is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( user_can( $user, 'manage_options' ) ) {
			wp_redirect( admin_url() );
		} elseif ( function_exists( 'buddypress' ) ) {
			wp_safe_redirect( bp_loggedin_user_domain() );
		} else {
			wp_redirect( home_url() );
		}
		exit;
	}
}
