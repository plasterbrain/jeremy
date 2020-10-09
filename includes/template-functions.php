<?php
/**
 * Template Functions
 *
 * Filters and actions which hook into WordPress.
 *
 * @package Jeremy
 * @subpackage Includes
 * @since 1.0.0
 */

if ( ! function_exists( 'jeremy_body_class' ) ) :
/**
 * Modifies the default class names added to the page body by WordPress based
 * on the query.
 *
 * @since 2.0.0
 * 
 * @param  [type] $classes      Classes added by WordPress as well as classes
 * 															supplied to {@see body_class}.
 * @param  [type] $user_classes Classes supplied to {@see body_class} (unused).
 * @return array 								Modified array of classes to add.
 */
function jeremy_body_class( $classes, $user_classes ) {
	$new_classes = array();
	foreach ( $classes as $class=>$name ) {
		/* Having page template files prefixed with "template-" in the "templates" folder results in some downright garbage generated class names. */
		$name = str_replace( 'template-template', 'template', $name );
		$name = str_replace( 'templatestemplate', 'template', $name );
		$new_classes[$class] = $name;
	}
	return $new_classes;
}
endif;
add_filter( 'body_class', 'jeremy_body_class', 10, 2 );

/**
 * Custom titles for various forms of archives. We're eschewing WP's built-in
 * colon-based format in favor of something more readable, e.g. "Alice's Posts"
 * instead of "Author: Alice."
 *
 * These titles are used on the page and as a page/RSS feed title.
 *
 * @since 1.0.0
 *
 * @param string $title	The starting archive title, not really used.
 * @return string				The custom archive title.
 */
if ( ! function_exists( 'jeremy_archive_title' ) ) :
function jeremy_archive_title( $title ) {
	$post_type = get_query_var( 'post_type' );
	$post_type_label = $post_type ? get_post_type_object( $post_type )->label : _x( 'Posts', 'noun', 'jeremy' );
	
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() || is_tax() ) {
		/* translators: %1$s is plural post type label, %2$s is tag name */
		$title = sprintf( __( '%1$s tagged "%2$s"', 'jeremy' ), $post_type_label, single_tag_title( '', false ) );
	} elseif ( is_author() ) {
		/* translators: e.g. %1$s is the author, %2$s is plural post type label */
		$title = sprintf( __( '%1$s\'s %2$s', 'jeremy'), get_the_author_meta( 'display_name', get_query_var( 'author' ) ), $post_type_label );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_year() ) {
		/* translators: %1$s is the archive year, %2$s is post type label */
		$title = sprintf( __( '%1$s %2$s', 'jeremy' ), get_the_date( 'Y' ), $post_type_label );
	} elseif ( is_month() ) {
		/* translators: %1$s is the archive year & month, %2$s is post type label */
		$title = sprintf( __( '%1$s %2$s', 'jeremy' ), get_the_date( _x( 'F Y', 'PHP date format for month/year archives', 'jeremy' ) ), $post_type_label );
	} elseif ( is_day() ) { // Seriously?!
		$title = sprintf( __( '%1$s %2$s', 'jeremy' ), get_the_date( _x( 'F j, Y', 'PHP date format for single date archives', 'jeremy' ) ), $post_type_label );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title();
	} else {
		$post_page  = get_option( 'page_for_posts' );
		if ( get_option( 'show_on_front' ) === 'page' && $post_page !== false ) {
			$title = get_the_title( $post_page );
		} else {
			$title = __( 'Home', 'jeremy' );
		}
	}
	return $title;
}
endif;
add_filter( 'get_the_archive_title', 'jeremy_archive_title' );

/**
 * Sets the page title to match {@see jeremy_archive_title}, since otherwise we
 * get some interesting broken garbage for author-based custom post archives.
 *
 * This function is used directly with a filter for The SEO Framework and called
 * by a function used with {@see document_title_parts} to achieve the same
 * effect even if The SEO Framework is messing with title output.
 *
 * @TODO Integration with other SEO solutions?
 * 
 * @since 2.0.0
 * 
 * @param  string $title	The page title.
 * @return string					The new page title.
 */
function jeremy_filter_page_title( $title ) {
	if ( is_archive() ) {
		$title = jeremy_archive_title( $title );
	}
	return $title;
}
if ( defined( 'THE_SEO_FRAMEWORK_VERSION' ) ) {
	add_filter( 'the_seo_framework_title_from_generation', 'jeremy_filter_page_title' );
} else{
	add_filter( 'document_title_parts', function( $title ) {
		$title['title'] = jeremy_filter_page_title( $title['title'] );
		return $title;
	} );
}

if ( ! function_exists( 'jeremy_truncate_menu_items' ) ) :
/**
 * This is a wrapper for {@see jeremy_truncate} to hook into WordPress. It
 * truncates nav menu item names over a certain number of characters to the
 * nearest whole word, to prevent overly long names from taking an excessive
 * amount of space and RUINING MY LAYOUT.
 *
 * @TODO make $length a customizer option
 *
 * @since 1.0.0
 *
 * @param string $title		The nav menu item title to possibly truncate.
 * @return string					The maybe shortened title.
 */
function jeremy_truncate_menu_items( $title ) {
	$length = 40;
	return jeremy_truncate( $title, $length );
}
endif;
add_filter( 'nav_menu_item_title', 'jeremy_truncate_menu_items', 10, 2 );

/* == wp-login.php == */

if ( ! function_exists( 'jeremy_login_errors' ) ) :
/**
 * Replaces the default WordPress error message with a more generic one.
 * 
 * @since 1.0.0
 *
 * @return string 	The login error message.
 */
function jeremy_login_errors() {
	return esc_html__( "There was a problem logging you in. Check your credentials and try again.", 'jeremy' );
}
endif;
add_filter('login_errors', 'jeremy_login_errors');

if ( ! function_exists( 'jeremy_login_headertext' ) ):
/**
 * Sets the login page heading (seen by screen readers) to say "Sign in to
 * {site name}" instead of the default "Powered by WordPress."
 *
 * @since 1.0.0
 *
 * @param string $text		The default heading text. Not used.
 * @return string					The new, superior heading text.
 */
function jeremy_login_headertext( $text ) {
	/* translators: %s is the site name */
	return sprintf(
		esc_html__( 'Sign in to %s', 'jeremy' ), get_bloginfo( 'name' ) );
}
endif;
add_filter( 'login_headertext', 'jeremy_login_headertext');

if ( ! function_exists( 'jeremy_login_headerurl') ) :
/**
 * Changes the WordPress login logo link to the homepage instead of
 * WordPress.org.
 * 
 * @since 1.0.0
 *
 * @param string $url		The login URL, I suppose. Not used.
 * @return string				The site homepage.
 */
function jeremy_login_headerurl( $url ) {
	return home_url();
}
endif;
add_filter( 'login_headerurl', 'jeremy_login_headerurl' );

/**
 * Redirects non-admin users to their BuddyPress profile when they log in.
 *
 * @TODO This might be plug-in territory... nyoron :)
 * 
 * @since 1.0.0
 */
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
add_action( 'template_redirect', 'jeremy_login_redirect' );

/**
 * Changes the 'post-categories' class added to the <ul> element and adds
 * 'entry__meta-terms__item' to <li> elements in the output of
 * {@see get_the_category_list}.
 *
 * @since 2.0.0
 * 
 * @param  string $thelist	The HTML output of the list as a string.
 * @return string						The list with new classes applied.
 */
function jeremy_category_list_classes( $thelist ) {
	$thelist = str_replace( 'class="post-categories"', 'class="nav__list nav__list-h"', $thelist );
	
	$thelist = str_replace( '<li>', '<li class="entry__meta-terms__li">', $thelist );
	
	return $thelist;
}
add_filter( 'the_category', 'jeremy_category_list_classes' );

/* == Widgets == */

/**
 * Alters the default tag cloud arguments.
 *
 * @since 2.0.0
 * 
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function jeremy_modify_tagcloud( $args ) {
	$args['unit'] = 'rem';
	$args['smallest'] = 0.9;
	$args['largest'] = 1.2;
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'jeremy_modify_tagcloud' );

/* == RSS Feeds == */

if ( ! function_exists( 'jeremy_filter_feed_title' ) ) :
/**
 * Uses {@see jeremy_archive_title} for RSS feed titles.
 *
 * @since 2.0.0
 * 
 * @param  string $title Default RSS feed title.
 * @return string        Updated RSS feed title.
 */
function jeremy_filter_feed_title( $title ) {
	if ( is_archive() ) {
		$title = jeremy_archive_title( $title ) . ' - ' . get_bloginfo( 'name' );
	}
	return $title;
}
endif;
add_filter( 'wp_title_rss','jeremy_filter_feed_title' );

if ( ! function_exists( 'jeremy_set_rss_timeout' ) ) :
/**
 * RSS feed display is pretty non-essential, so this sets the timeout when
 * fetching one to 2 seconds rather than 10.
 *
 * @since 2.0.0
 * 
 * @param SimplePie $feed		The SimplePie feed object.
 */
function jeremy_set_rss_timeout( $feed ) {
	$feed->set_timeout( 3 );
}
endif;
add_action( 'wp_feed_options', 'jeremy_set_rss_timeout' );

/* == Comments == */

if ( ! function_exists( 'jeremy_comment_form_fields' ) ) :
/**
* Removes the comment textarea field from the array and adds it to the end.
* This has the effect of ensuring the name/email/website fields show up on the
* comment form first.
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
add_filter( 'comment_form_fields', 'jeremy_comment_form_fields' );