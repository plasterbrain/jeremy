<?php
/**
 * Jeremy Theme Customizer
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 */
if ( class_exists( 'WP_Customize_Control' ) ) {
	/**
	 * The third party controls used by the Customizer for this theme.
	 */
	require_once( get_template_directory() . '/inc/class-customize-blank-control.php' );
	require_once( get_template_directory() . '/inc/class-customize-toggle-control.php' );
	require_once( get_template_directory() . '/inc/class-customize-user-control.php' );
	require_once( get_template_directory() . '/inc/class-customize-xprofile-control.php' );
}

if ( ! function_exists( 'jeremy_customize_preview_js' ) ) :
/**
 * Enqueues the javascript used to generate a live preview of Customizer settings.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 */
add_action( 'customize_preview_init', 'jeremy_customize_preview_js' );
function jeremy_customize_preview_js() {
	wp_enqueue_script( 'jeremy-customize-preview', get_template_directory_uri() . '/assets/js/customizer/customize-preview.js', array( 'customize-preview' ), null, true );
}
endif;

if ( ! function_exists( 'jeremy_customize_controls_js' ) ) :
/**
 * Enqueues the javascript used to conditionally show settings in the Customizer.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 */
add_action( 'customize_controls_enqueue_scripts', 'jeremy_customize_controls_js');
function jeremy_customize_controls_js() {
	wp_enqueue_script( 'jeremy-customize-controls', get_template_directory_uri() . '/assets/js/customizer/customize-controls.js', array( 'jquery' ), null, true );
}
endif;

if ( ! function_exists( 'jeremy_customize_register' ) ) :
add_action( 'customize_register', 'jeremy_customize_register' );
/**
 * Registers theme settings and controls for the Customizer, and adds postMessage
 * support for some of the default controls.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 * 
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function jeremy_customize_register( $wp_customize ) {
	/* Site Identity */
	$wp_customize->add_setting( 'footer_controls_start', array() );
	// Footer copyright text
	$wp_customize->add_setting( 'copyright', array(
		'default' => '&copy; ' . date('Y') . ' ' . get_bloginfo('name'),
		'sanitize_callback' => 'wp_filter_nohtml_kses',
		'transport' => 'postMessage',
	) );
	$wp_customize->add_setting( 'site_facebook', array(
		'sanitize_callback' => 'esc_url_raw',
		'transport' => 'postMessage',
	) );
	$wp_customize->add_setting( 'site_twitter', array(
		'sanitize_callback' => 'esc_url_raw',
		'transport' => 'postMessage',
	) );
	// Whether to show theme credits in the footer
	$wp_customize->add_setting( 'show_theme_credits', array(
		'default' => false,
		'transport' => 'postMessage',
	) );
	// Whether to show "Proudly powered by WordPress" in footer
	$wp_customize->add_setting( 'show_powered_by', array(
		'default' => false,
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( new Customize_Blank_Control( $wp_customize, 'footer_controls_start', array(
		'label' => __( 'Footer', 'jeremy' ),
		'description' => __( 'Control what appears in the site footer.', 'jeremy' ),
		'priority' => 100,
		'section' => 'title_tagline',
	) ) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'copyright', array(
		'label' => __( 'Copyright text', 'jeremy' ),
		'priority' => 100,
		'section' => 'title_tagline',
		'type' => 'text',
	) ) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'site_facebook', array(
		'label' => sprintf( _x( '%s link', 'Facebook link', 'jeremy' ), 'Facebook' ),
		'priority' => 100,
		'section' => 'title_tagline',
		'type' => 'text',
	) ) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'site_twitter', array(
		'label' => sprintf( _x( '%s link', 'Twitter link', 'jeremy' ), 'Twitter' ),
		'priority' => 100,
		'section' => 'title_tagline',
		'type' => 'text',
	) ) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'show_theme_credits', array(
		'label' => __( 'Show theme credits', 'jeremy' ),
		'priority' => 100,
		'section' => 'title_tagline',
		'type' => 'toggle',
	) ) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'show_powered_by', array(
		'label' => __( 'Show WordPress credits', 'jeremy' ),
		'priority' => 100,
		'section' => 'title_tagline',
		'type' => 'toggle',
	) ) );

	/* Static Front Page */
	$wp_customize->add_setting( 'hero_controls_start', array() );
	// Whether to show the hero image section on the front page
	$wp_customize->add_setting( 'show_hero', array(
		'default' => true,
		'transport' => 'postMessage',
	) );
	// The hero image to use
	$wp_customize->add_setting( 'hero_bg_img', array() );
	// Whether or not to use a fixed scrolling effect with the image
	$wp_customize->add_setting( 'hero_bg_parallax', array( 'default' => true, ) );
	// The hero section background color if no image is given
	$wp_customize->add_setting( 'hero_bg_color', array(
		'default' => '#c2e164',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport' => 'postMessage',
	) );
	// The hero section text color
	$wp_customize->add_setting( 'hero_text_color', array(
		'default' => '#141f36',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport' => 'postMessage',
	) );
	// The hero section main heading
	$wp_customize->add_setting( 'hero_h1', array(
		'default' => __( 'Title text here.', 'jeremy' ),
		'sanitize_callback' => 'wp_filter_nohtml_kses',
		'transport' => 'postMessage',
	) );
	// The hero section subheading
	$wp_customize->add_setting( 'hero_h2', array(
		'default' => __( 'Subtitle text here.', 'jeremy' ),
		'sanitize_callback' => 'wp_filter_nohtml_kses',
		'transport' => 'postMessage',
	) );
	// The hero section button color
	$wp_customize->add_setting( 'hero_button_color', array(
		'default' => '#e91f53',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport' => 'postMessage',
	) );
	// The hero section button text color
	$wp_customize->add_setting( 'hero_button_text_color', array(
		'default' => '#ffffff',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport' => 'postMessage',
	) );
	// The hero section button text
	$wp_customize->add_setting( 'hero_button_text', array(
		'default' => __( 'Call to action', 'jeremy' ),
		'sanitize_callback' => 'wp_filter_nohtml_kses',
		'transport' => 'postMessage',
	) );
	// The hero section button link
	$wp_customize->add_setting( 'hero_button_link', array(
		'sanitize_callback' => 'esc_url_raw',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( new Customize_Blank_Control( $wp_customize, 'hero_controls_start', array(
		'label' => __( 'Hero Image', 'jeremy' ),
		'description' => __( 'Display a hero image with an optional call to action button on the front page.', 'jeremy' ),
		'section' => 'static_front_page',
	) ) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'show_hero', array(
		'label' => __( 'Show the hero section', 'jeremy' ),
		'section' => 'static_front_page',
		'type' => 'toggle',
	) ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_bg_img', array(
		'label'   => __( 'Hero image', 'jeremy' ),
		'sanitize_callback' => 'jeremy_sanitize_image',
		'section' => 'static_front_page',
	) ) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'hero_bg_parallax', array(
		'label' => __( 'Parallax scrolling', 'jeremy' ),
		'section' => 'static_front_page',
		'type' => 'toggle',
	) ) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'hero_h1', array(
		'label' => __( 'Title text', 'jeremy' ),
		'section' => 'static_front_page',
		'type' => 'text',
	) ) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'hero_h2', array(
		'label' => __( 'Subtitle text', 'jeremy' ),
		'section' => 'static_front_page',
		'type' => 'text',
	) ) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'hero_button_text', array(
		'label' => __( 'Button text', 'jeremy' ),
		'section' => 'static_front_page',
		'type' => 'text',
	) ) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'hero_button_link', array(
		'label' => __( 'Button link', 'jeremy' ),
		'section' => 'static_front_page',
		'type' => 'url',
	) ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hero_bg_color', array(
		'label'   => __( 'Hero section color', 'jeremy' ),
		'description' => __( 'Used if no image is selected.', 'jeremy' ),
		'section' => 'static_front_page',
	) ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hero_text_color', array(
		'label'   => __( 'Hero text color', 'jeremy' ),
		'section' => 'static_front_page',
	) ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hero_button_color', array(
		'label'   => __( 'Button color' ),
		'section' => 'static_front_page',
	) ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hero_button_text_color', array(
		'label'   => __( 'Button text color' ),
		'section' => 'static_front_page',
	) ) );

	/* Posts & Pages */
	$wp_customize->add_section( 'singles' , array(
		'title' => __( 'Posts & Pages', 'jeremy' ),
		'description' => __( 'Control how elements appear on single posts and pages.', 'jeremy' ),
	) );
	$wp_customize->add_setting( 'default-featured', array(
		'default' => esc_url( get_template_directory_uri() . '/assets/jpg/default-featured.jpg' ),
		'transport' => 'postMessage',
	) );
	$wp_customize->add_setting( 'breadcrumb_index_name', array(
		'default' => get_bloginfo( 'name' ),
		'transport' => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'breadcrumb_index_name', array(
		'label' => __( 'Breadcrumb Index', 'jeremy' ),
		'description' => __( 'What to call the first page in the breadcrumb trail.', 'jeremy' ),
		'section' => 'singles',
		'type' => 'text',
	) ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'default-featured', array(
		'label' => __( 'Default Featured Image', 'jeremy' ),
		'description' => __( "The featured image to use if your post doesn't have one.", 'jeremy' ),
		'section' => 'singles',
	) ) );

	$wp_customize->add_setting( 'use_post_nav', array(
		'default' => true,
		'transport' => 'postMessage',
	) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'use_post_nav', array(
		'label' => __( 'Show next/previous entries at the bottom of each post.', 'jeremy' ),
		'section' => 'singles',
		'type' => 'toggle',
	) ) );

	/* BuddyPress */
	$wp_customize->add_section( 'community', array(
		'title' => __( 'Community', 'jeremy' ),
		'description' => __( 'Settings for BuddyPress integration', 'jeremy' ),
	) );
	// Intended for a "featured business" function.
	$wp_customize->add_setting( 'bp_featured_member', array() );
	// Default cover image to use if members haven't set one.
	$wp_customize->add_setting( 'bp_default_cover', array() );

	/* Because the theme uses a customized template to display only certain fields on the front
	page of user profiles, we'll include settings to customize which fields are used. */
	// The category field
	$wp_customize->add_setting( 'bp_profile_category', array() );

	// Heading and description
	$wp_customize->add_setting( 'bp_profile_controls_start', array() );
	// The field that should be used for the profile map/address functionality
	$wp_customize->add_setting( 'bp_profile_address', array() );
	// The main front page field, ideally a user bio. Appears on the left.
	$wp_customize->add_setting( 'bp_profile_about', array() );
	// The first front page field to feature on the right-hand side, next to the bio.
	$wp_customize->add_setting( 'bp_profile_1', array() );
	// The second field to show to the right of the bio.
	$wp_customize->add_setting( 'bp_profile_2', array() );
	// The third field to show to the right of the bio.
	$wp_customize->add_setting( 'bp_profile_3', array() );

	if ( function_exists( 'buddypress' ) ) {
		$wp_customize->add_control( new Customize_User_Control( $wp_customize, 'bp_featured_member', array(
			'label' => __( 'Featured member', 'jeremy' ),
			'description' => __( 'Select a member to feature on the site.', 'jeremy' ),
			'section' => 'community',
		) ) );
		if ( bp_displayed_user_use_cover_image_header() ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bp_default_cover', array(
				'label' => __( 'Default User Cover Image', 'jeremy' ),
				'description' => __( "The cover image to use if a user hasn't uploaded one.", 'jeremy' ),
				'section' => 'community',
			) ) );
		}
		/* XProfile Customization */
		if ( bp_is_active( 'xprofile' ) ) {
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_category', array(
				'label' => __( 'Category Field', 'jeremy' ),
				'description' => __( 'This will generate a list of categories on the member directory page.', 'jeremy' ),
				'section' => 'community',
				'field_types' => array( 'selectbox' ),
			) ) );

			$wp_customize->add_control( new Customize_Blank_Control( $wp_customize, 'bp_profile_controls_start', array(
				'label' => __( 'Customize Profiles', 'jeremy' ),
				'description' => __( 'Choose which XProfile fields are used to show information on user profiles.', 'jeremy' ),
				'section' => 'community',
			) ) );

			/* Exclude the address field from other options if the BP XProfile Location plug-in
			is set, since it will appear on profiles automatically. */
			$location = array();
			if ( function_exists( 'pp_loc_init' ) ) {
				$location = array( 'location' );
				$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_address', array(
					'label' => __( 'Address Field', 'jeremy' ),
					'description' => __( 'The XProfile field to use for member addresses.', 'jeremy' ),
					'section' => 'community',
					'field_types' => $location,
				) ) );
			}

			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_about', array(
				'label' => __( 'About Field', 'jeremy' ),
				'description' => __( 'The XProfile field to use for member bios.', 'jeremy' ),
				'section' => 'community',
				'exclude' => $location,
			) ) );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_1', array(
				'label' => __( 'Featured Field 1', 'jeremy' ),
				'section' => 'community',
				'exclude' => $location,
			) ) );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_2', array(
				'label' => __( 'Featured Field 2', 'jeremy' ),
				'section' => 'community',
				'exclude' => $location,
			) ) );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_3', array(
				'label' => __( 'Featured Field 3', 'jeremy' ),
				'section' => 'community',
				'exclude' => $location,
			) ) );
		}

	}

	$wp_customize->add_section( 'modules', array(
		'title' => __( 'Modules', 'jeremy' ),
		'description' => __( "Control which external scripts are loaded. If you aren't sure what these are, leave them on the default settings.", 'jeremy' ),
	) );
	// Whether to use lightboxes
	$wp_customize->add_setting( 'use_lightbox', array(
		'default' => true,
	) );
	// Whether to load the WP-Embed script to embed one WordPress post into another.
	$wp_customize->add_setting( 'use_wp_embed', array(
		'default' => false,
	) );
	// If user set the Google Maps API key in Event Organiser already, grab it from there.
	$key = '';
	if ( function_exists( 'eventorganiser_get_google_maps_api_key' ) )
		$key = eventorganiser_get_google_maps_api_key();
	
	$wp_customize->add_setting( 'gmaps-api', array(
		'default' => $key,
	) );
	
	$wp_customize->add_setting( 'use_maps', array(
		'default' => true,
	) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'use_lightbox', array(
		'label'	      => __( 'Lightbox2', 'jeremy' ),
		/* Translators: %s is the placeholder for the name of the Lightbox script. */
		'description' => sprintf( __( 'View images in a responsive lightbox using %s.', 'jeremy' ), '<a href="http://cornel.bopp-art.com/lightcase/">Lightcase</a>' ),
		'section'     => 'modules',
		'type'        => 'toggle',
	) ) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'use_wp_embed', array(
		'label'	      => __( 'WP Embed', 'jeremy' ),
		'description' => __( 'Load the WP Embed script that allows you to embed another WordPress post in your post.', 'jeremy' ),
		'section'     => 'modules',
		'type'        => 'toggle',
	) ) );
	
	if ( function_exists( 'buddypress' ) ) {
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'gmaps-api', array(
			'label' => __( 'Google Maps API Key', 'jeremy' ),
			'description' => __( "You'll need an API key in order to use Google Maps", 'jeremy' ),
			'section' => 'modules',
		) ) );
		$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'use_maps', array(
			'label'	      => __( 'Show maps on profiles', 'jeremy' ),
			'description' => __( 'Show an embedded Google Map on profile pages.', 'jeremy' ),
			'section'     => 'modules',
			'type'        => 'toggle',
		) ) );
	}
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	$wp_customize->remove_control( 'header_image' );

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'jeremy_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.tagline',
			'render_callback' => 'jeremy_customize_partial_blogdescription',
		) );
	}
}
endif;
/**
 * Renders the site title for the selective refresh partial.
 */
function jeremy_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Renders the site tagline for the selective refresh partial.
 */
function jeremy_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Sanitizes an image filename to ensure it is an image file type. For use with Image
 * Controls in the Customizer.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 * 
 * @link https://divpusher.com/blog/wordpress-customizer-sanitization-examples#file
 * 
 * @param string $file    	   			The image filename.
 * @param WP_Customize_Setting $setting The setting instance.
 * @return string		  	   			The image filename if it passes, or the default
 * 										image filename if it doesn't.
 */
if ( ! function_exists( 'jeremy_sanitize_image' ) ) :
function jeremy_sanitize_image( $file, $setting ) {
	$mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif'          => 'image/gif',
		'png'          => 'image/png'
	);
	$file_ext = wp_check_filetype( $file, $mimes );
	return ( $file_ext['ext'] ? $file : $setting->default );
}
endif;