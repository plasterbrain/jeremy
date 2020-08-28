<?php
/**
 * Jeremy Bootstrap File
 *
 * Child themes can override pluggable functions defined here by defining them
 * first in their own functions.php file.
 *
 * @version 2.0.0
 * @author Presto Bunny
 * @package Jeremy
 */

$jeremy_directory = get_template_directory();

/**
 * Hooks into WordPress to modify default output.
 * @since 1.0.0
 */
require $jeremy_directory . '/inc/template-functions.php';

/**
 * Defines functions used across the theme in various templates
 * @since 1.0.0
 */
require $jeremy_directory . '/inc/template-tags.php';

/**
 * Media functions to modify various built-in shortcodes.
 * @since 2.0.0
 */
require $jeremy_directory . '/inc/media-functions.php';

/**
 * Sets up the WordPress Customizer for our theme.
 * @since 1.0.0
 */
require $jeremy_directory . '/inc/customizer.php';

/**
 * Breadcrumb plug-in by Justin Tadlock. Remove this line to disable
 * this breadcrumb integration.
 * @since 1.0.0
 */
include $jeremy_directory . '/inc/class-breadcrumb-trail.php';

/**
 * Notifies users to install recommended plug-ins on theme activation.
 * @link http://tgmpluginactivation.com/
 */
require $jeremy_directory . '/inc/class-tgm-plugin-activation.php';

/**
 * Defines functions which integrate with BuddyPress and Event Organiser.
 */
require $jeremy_directory . '/inc/plugins.php';

/**
 * Adds custom walkers and widgets used to modify built-in HTML.
 */
require $jeremy_directory . '/inc/class-jeremy-walker-comment.php';
require $jeremy_directory . '/inc/class-jeremy-walker-nav-menu.php';
require $jeremy_directory . '/inc/class-jeremy-walker-bp-nav-menu.php';
require $jeremy_directory . '/inc/class-jeremy-widget-recent-comments.php';
require $jeremy_directory . '/inc/class-jeremy-widget-rss.php';

/** Site specific, oopsie. */
function ccc_filter_plugin_updates( $value ) {
	/* BP XProfile Location gets the job done, but it requires manual editing of the plug-in files to add your Google API code -- aka it stops working with every update *by design.* */
	if ( isset( $value ) && is_object( $value ) ) {
		unset( $value->response['bp-xprofile-location/loader.php'] );
    }
    return $value;
}
add_filter( 'site_transient_update_plugins', 'ccc_filter_plugin_updates' );

if ( ! function_exists( 'jeremy_setup' ) ) :
/**
 * Registers support for various WordPress features and plugins.
 *
 * @since 1.0.0
 */
function jeremy_setup() {
	load_theme_textdomain( 'jeremy', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form', ) );

	/**
	 * Jeremy has two menus by default, the main navigation and the top bar,
	 * which could be used for account or social media links.
	 */
	register_nav_menus( array(
		'mainmenu' => __( 'Primary', 'jeremy' ),
		'topbar' => __( 'Topbar', 'jeremy' ),
	) );

	/**
	 * Adds support for some basic Customizer features.
	 */
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support( 'custom-background', apply_filters( 'jeremy_custom_background_args', array(
		'default-color' => 'e7e7e7',
		'default-attachment' => 'fixed',
		'default-repeat' => 'no-repeat',
		'default-size' => 'cover',
	) ) );

	/**
	 * Sadly, giant banner images in the site header have gone out of style.
	 * You'll have to upload a logo instead. Or do whatever you want, since
	 * this function is pluggable.
	 */
	add_theme_support( 'custom-header', array(
		'default-text-color' => '000000',
		'uploads' => false,
	) );

	/**
	 * The logo in the site header is resized to a height of 150px.
	 */
	add_theme_support( 'custom-logo', array(
		'flex-width'  => true,
		'height' => 150,
	) );

	/**
	 * Disables the BuddyPress compatibility layer.
	 * @link https://codex.buddypress.org/themes/theme-compatibility-1-7/theme-compatibility-2/
	 */
	//add_theme_support( 'buddypress' );
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
 */
function jeremy_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Header Widget', 'jeremy' ),
		'id'            => 'sidebar-header',
		'description'   => esc_html__( 'Add widgets to the header.', 'jeremy' ),
		'before_widget' => '<section class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Page Sidebar', 'jeremy' ),
		'id'            => 'sidebar-main',
		'description'   => esc_html__( 'Sidebar for site pages.', 'jeremy' ),
		'before_widget' => '<section class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-name">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'jeremy' ),
		'id'            => 'sidebar-posts',
		'description'   => esc_html__( 'Sidebar for posts and archives.', 'jeremy' ),
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

if ( ! function_exists( 'jeremy_scripts' ) ) :
/**
 * Enqueues scripts and styles for our theme.
 *
 * @since 1.0.0
 */
function jeremy_scripts() {
	/**
	 * @todo use a minified version of the CSS
	 */
	wp_enqueue_style( 'jeremy-style', get_stylesheet_uri() );
	wp_enqueue_style( 'jeremy-fonts', 'https://fonts.googleapis.com/css?family=Quicksand:400,500,700|Lora:400i', array(), null );

	wp_enqueue_style( 'html5shiv', 'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js', array(), false, true );
	wp_enqueue_style( 'html5shiv-printshiv', 'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js', array(), false, true );

	/**
	 * Enqueues a custom dropdown menu script on all pages except the login.
	 */
	if ( has_nav_menu( 'mainmenu' ) && ! is_page_template( array( 'templates/template-login.php', 'templates/template-blank.php' ) ) ) {
		wp_enqueue_script( 'jeremy-menu', get_template_directory_uri() . '/assets/js/min/menu.min.js', array( 'jquery' ), null, true );
	}

	/* Gutenberg print styles */
	if ( get_theme_mod( 'use-printstyles', 0 ) ) {
		wp_enqueue_style( 'bafs-gutenberg', 'https://unpkg.com/gutenberg-css@0.4.7/dist/gutenberg.min.css', array(), null, 'print' );
	}

	/**
	 * Enqueues Lightcase.js if enabled in the theme settings and if it hasn't
	 * already been enqueued (e.g., by a plug-in) under the name "lightcase".
	 *
	 * @link http://cornel.bopp-art.com/lightcase/
	 */
	if ( ! wp_script_is( 'lightcase' ) && get_theme_mod( 'use_lightbox', true ) && is_singular() ) {
		wp_enqueue_script( 'lightcase', 'https://cdnjs.cloudflare.com/ajax/libs/lightcase/2.4.2/js/lightcase.min.js', array( 'jquery' ), null, true );
		wp_enqueue_style( 'lightcase', 'https://cdnjs.cloudflare.com/ajax/libs/lightcase/2.4.2/css/lightcase.min.css', array(), null );
	}

	/**
	 * Dequeues the comment reply AJAX script on pages that don't use comments.
	 */
	if ( ! is_singular() || ! comments_open() ) {
		wp_dequeue_script( 'comment-reply' );
	}
}
endif;
if ( ! is_admin() ) add_action( 'wp_enqueue_scripts', 'jeremy_scripts' );

if ( ! function_exists( 'jeremy_login_enqueue_scripts' ) ) :
/**
 * Replaces the default WordPress login page styles and adds the site name
 * or logo instead of the WordPress logo on the login page.
 *
 * @since 1.0.0
 */
function jeremy_login_enqueue_scripts() {
	wp_dequeue_style( 'login' );
	wp_enqueue_style( 'jeremy-style', get_stylesheet_uri() );

	/**
	 * Use the site's custom logo instead of the WordPress one.
	 */
	if ( has_custom_logo() ) {
        $image = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ) );
        ?>
        <style type="text/css">
            body.login h1 a {
                background-image: url('<?php echo esc_url( $image[0] ); ?>');
                -webkit-background-size: <?php echo absint( $image[1] )?>px;
                background-size: <?php echo absint( $image[1] ) ?>px;
                height: <?php echo absint( $image[2] ) ?>px;
                width: <?php echo absint( $image[1] ) ?>px;
            }
        </style>
        <?php
	}
}
endif;
add_action( 'login_enqueue_scripts', 'jeremy_login_enqueue_scripts', 10 );

if ( ! function_exists( 'jeremy_footer_event_schema' ) ) :
function jeremy_footer_event_schema() {
	global $post;
	if ( get_post_type( $post ) !== 'event' || ! function_exists ( 'eo_insert_event' ) ) {
		return;
	}
	echo '
	<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "Event",
		"name": "' . wp_kses( get_the_title(), array() ) . '",
		"description": "' . wp_kses( get_the_excerpt(), array() ) . '",
		"image": "' . get_the_post_thumbnail_url() . '",
		"startDate": "' . eo_get_the_start( 'c', $post->ID, $post->occurrence_id ) . '",
		"endDate": "' . eo_get_the_end( 'c', $post->ID, $post->occurrence_id ) . '",';
	$venue_id = eo_get_venue( $post->ID );
	if ( $venue_id !== false ) {
		$venue_name = eo_get_venue_name( $venue_id );
		$address = eo_get_venue_address( $venue_id );
		if ( is_string( $venue_name ) && is_array( $address ) ) {
			echo '
			"location": {
				"@type": "Place",
				"name": "' . esc_attr( $venue_name ) . '",
				"address": {
					"@type": "PostalAddress",
					"streetAddress": "' . esc_attr( $address['address'] ) . '",
					"addressLocality": "' . esc_attr( $address['city'] ) . '",
					"addressRegion": "' . esc_attr( $address['state'] ) . '",
					"postalCode": "' . esc_attr( $address['postcode'] ) . '",
					"addressCountry": "' . esc_attr( $address['country'] ) . '"
				}
			}';
		}
	}
	$performer = esc_attr( get_post_meta( $post->ID, 'performer', true ) );
	if ( ! empty( $performer ) ) {
		if ( $venue_id !== false ) {
			echo ',\n';
		}
		$performer_type = empty( get_post_meta( $post->ID, 'performer_type', true ) ) ? 'Person' : esc_attr( get_post_meta( $post->ID, 'performer_type', true ) );
		echo '
		"performer": {
			"@type": "' . $performer_type . '",
			"name": "' . $performer . '"
		}';
	}
	echo '
	}
	</script>';
}
add_action( 'wp_footer', 'jeremy_footer_event_schema' );
endif;

if ( ! function_exists( 'jeremy_footer_jquery' ) ) :
function jeremy_footer_jquery() {
	?>
	<script>
	jQuery( document ).ready( function($) {
		<?php
		if ( get_theme_mod( 'use_lightbox', true ) && is_singular() ) {
			?>
			$('a[data-rel^=lightcase]').lightcase();
			<?php
		}
		if ( function_exists( 'bp_is_user' ) ) {
			if ( bp_is_user() && jeremy_bp_is_editor() ) {
				?>
				$('.profile-avatar').hover(function() {
					$('.profile-avatar-edit').addClass('hover');
				}, function() {
					$('.profile-avatar-edit').removeClass('hover');
				});
				$('.profile-cover').hover(function() {
					$('.profile-cover-edit').addClass('hover');
				}, function() {
					$('.profile-cover-edit').removeClass('hover');
				});
				<?php
			}
		}
		?>
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'jeremy_footer_jquery' );
endif;
