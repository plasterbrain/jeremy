<?php
/**
 * Jeremy Bootstrap File
 *
 * Child themes can override pluggable functions defined here by defining them
 * first in their own functions.php file.
 *
 * @author Presto Bunny
 * @license GPL-3+
 * @package Jeremy
 * @version 2.0.0
 */

/* == Required Scripts == */

$jeremy_directory = get_template_directory();

/**
 * Hooks into WordPress to modify default output.
 * @since 1.0.0
 */
require $jeremy_directory . '/includes/template-functions.php';

/**
 * Defines functions used across the theme in various templates
 * @since 1.0.0
 */
require $jeremy_directory . '/includes/template-tags.php';

/**
 * Media functions to modify various built-in shortcodes.
 * @since 2.0.0
 */
require $jeremy_directory . '/includes/media-functions.php';

/**
 * Sets up the WordPress Customizer for our theme.
 * @since 1.0.0
 */
require $jeremy_directory . '/includes/customizer.php';

/**
 * Breadcrumb plug-in by Justin Tadlock. Remove this line to disable
 * this breadcrumb integration.
 * @since 1.0.0
 */
include $jeremy_directory . '/includes/class-breadcrumb-trail.php';

/**
 * Defines functions which integrate with BuddyPress and Event Organiser.
 * @since 1.0.0
 */
require $jeremy_directory . '/includes/plugins.php';

/**
 * Adds custom walkers and widgets used to modify built-in HTML.
 */
require $jeremy_directory . '/includes/class-jeremy-walker-comment.php';
require $jeremy_directory . '/includes/class-jeremy-walker-nav-menu.php';
require $jeremy_directory . '/includes/class-jeremy-walker-nav-footer-menu.php';
require $jeremy_directory . '/includes/class-jeremy-widget-recent-comments.php';
require $jeremy_directory . '/includes/class-jeremy-widget-rss.php';

/* == Theme Hooks == */

if ( ! function_exists( 'jeremy_setup' ) ) :
/**
 * Registers support for various WordPress features and plugins.
 *
 * @since 1.0.0
 */
function jeremy_setup() {
	load_theme_textdomain( 'jeremy', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array(
		'caption',
		'comment-form',
		'comment-list',
		'gallery',
		'search-form',
		'style',
		'script',
	) );
	add_theme_support( 'post-formats', array( 'link' ) );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );

	register_nav_menus( array(
		'mainmenu' 	=> esc_html__( 'Main Menu', 'jeremy' ),
		'footer' 		=> esc_html__( 'Footer Menu', 'jeremy' ),
	) );
	
	add_theme_support( 'customize-selective-refresh-widgets' );

 	add_theme_support( 'custom-header', array(
 		'default-text-color' => '000000',
 		'uploads' => false,
 	) );

 	add_theme_support( 'custom-logo', array(
 		'flex-width'  => true,
 		'height' => 150,
 	) );

	add_theme_support( 'custom-background', apply_filters( 'jeremy_custom_background_args', array(
		'default-color' => 'fff',
		'default-attachment' => 'fixed',
		'default-repeat' => 'no-repeat',
		'default-size' => 'cover',
	) ) );
	
	// The theme is built for BuddyPress's Legacy layout. Sorta.
	add_theme_support('buddypress-use-legacy');
	
	add_theme_support('event-organiser');
}
endif;
add_action( 'after_setup_theme', 'jeremy_setup' );

if ( ! function_exists( 'jeremy_widgets_init' ) ) :
/**
 * Registers the four main widget areas: header, footer, and two sidebars,
 * one for posts/archives and one for pages. You may want to override this
 * function if you're already using a multi-sidebar plug-in.
 *
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Removed header widget.
 */
function jeremy_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'jeremy' ),
		'id'            => 'sidebar',
		'description'   => esc_html__( 'Sidebar for posts and pages.', 'jeremy' ),
		'before_widget' => '<section class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-name">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widgets', 'jeremy' ),
		'id'            => 'sidebar-footer',
		'description'   => esc_html__( 'Add widgets to the footer.', 'jeremy' ),
		'before_widget' => '<section class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-name">',
		'after_title'   => '</h4>',
	) );

	/**
	 * Replaces the default RSS and Recent Comments widgets with custom versions
	 * that change some of the HTML markup.
	 */
	unregister_widget( 'WP_Widget_RSS' );
	unregister_widget( 'WP_Widget_Recent_Comments' );
	register_widget( 'Jeremy_Widget_RSS' );
	register_widget( 'Jeremy_Widget_Recent_Comments' );
}
endif;
add_action( 'widgets_init', 'jeremy_widgets_init', 11 );

/**
 * Returns a file path for a script or style based on the value of WP_DEBUG.
 * If WP_DEBUG is false or not set (production environment), it returns the
 * path to the minified version of the file. Otherwise, it returns the full
 * version for easier debugging.
 *
 * @since 2.0.0
 * 
 * @param  string $file	The name of the file to retrieve without an extension.
 * @param  string $ext	The extension to use, default "js".
 * @return string				The path to the file to enqueue. The template directory
 * 											URI is part of this path.
 */
function jeremy_get_script_path( $file, $ext = 'js' ) {
	$path = get_template_directory_uri() . "/assets/{$ext}";
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$path .= "/{$file}.{$ext}";
		
		if ( $ext === 'css' ) {
			// Cache-buster
			$path = add_query_arg( 't', time(), $path );
		}
	} else {
		$path .= "/min/{$file}.min.{$ext}";
	}
	return $path;
}

if ( ! function_exists( 'jeremy_register_scripts' ) ) :
/**
 * Registers scripts for use across various functions.
 *
 * @TODO RTL?
 *
 * @since 2.0.0
 */
function jeremy_register_scripts() {
	$rtl = is_rtl() ? '-rtl' : '';
	
 	wp_register_style(
		'jeremy-style',
		get_template_directory_uri() . '/assets/css/style' . $rtl . '.min.css',
		array(), null );
}
endif;
add_action( 'wp_loaded', 'jeremy_register_scripts' );

if ( ! function_exists( 'jeremy_enqueue_scripts' ) ) :
/**
 * Enqueues scripts and styles for our theme. If WP_DEBUG is set to true in
 * wp-config, the non-minified versions are used instead.
 *
 * @since 1.0.0
 */
function jeremy_enqueue_scripts() {
	/* Stylesheet */
	wp_enqueue_style( 'jeremy-style' );
	
	
	global $is_IE;
	
	if ( $is_IE ) {
	/* HTML5 Shiv */
		wp_enqueue_script(
			'html5shiv-printshiv',
			'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js',
			array(), false, true );
		
		/** CSS Custom Properties IE 11 Polyfill
		@link https://github.com/nuxodin/ie11CustomProperties */
		wp_enqueue_script(
			'ie11-custom-properties',
			'https://cdn.jsdelivr.net/gh/nuxodin/ie11CustomProperties@4.1.0/ie11CustomProperties.min.js',
			array(), null, true );
	}

	/* Main Nav/Dropdowns */
	if ( has_nav_menu( 'mainmenu' ) && ! is_page_template( array( 'templates/template-login.php', 'templates/template-blank.php' ) ) ) {
		wp_register_script(
			'jeremy-menu',
			jeremy_get_script_path( 'menu' ),
			array( 'jquery' ), null, true );

		wp_localize_script( 'jeremy-menu', 'jeremy', array(
			'icon' 	=> get_theme_file_uri( 'assets/svg/nav_hamburger.svg' ),
			'label' => esc_html__( 'Toggle nav menu', 'jeremy' ),
		) );
		
		wp_enqueue_script( 'jeremy-menu' );
	}

	/* Cleanup */
	if ( ! is_singular() || ! comments_open() ) {
		// Dequeues the comment reply AJAX script on pages that don't use comments.
		wp_dequeue_script( 'comment-reply' );
	}
	
	#wp_deregister_style( 'mediaelement' );
	#wp_deregister_style( 'wp-mediaelement' );
}
endif;
add_action( 'wp_enqueue_scripts', 'jeremy_enqueue_scripts' );