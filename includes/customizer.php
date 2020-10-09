<?php
/**
 * Jeremy Theme Customizer
 *
 * Implementation of theme options in the WordPress Customizer API.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
	/**
	 * The third party controls used by the Customizer for this theme.
	 */
	require_once(
		get_template_directory() . '/includes/class-customize-fake-control.php' );
	require_once(
		get_template_directory() . '/includes/class-customize-toggle-control.php' );
	require_once(
		get_template_directory() . '/includes/class-customize-user-control.php' );
	require_once(
		get_template_directory() . '/includes/class-customize-xprofile-control.php' );
}

/**
 * Enqueues the javascript used to generate a live preview of Customizer
 * settings on the website.
 * 
 * @since 1.0.0
 */
function jeremy_customize_preview_js() {
	wp_enqueue_script(
		'jeremy-customize-preview',
		get_template_directory_uri() . '/assets/js/customizer/customize-preview.js',
		array( 'customize-preview' ), null, true );
}
add_action( 'customize_preview_init', 'jeremy_customize_preview_js' );

/**
 * Enqueues the javascript used to conditionally show settings in the
 * Customizer based on the property of other settings.
 * 
 * @since 1.0.0
 */
function jeremy_customize_controls_js() {
	wp_enqueue_script(
		'jeremy-customize-controls_scripts',
		get_template_directory_uri() . '/assets/js/customizer/customize-controls.js',
		array( 'jquery' ), null, true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'jeremy_customize_controls_js');

function jeremy_customize_controls_css() {
	wp_enqueue_style(
		'jeremy_customize_controls_styles',
		get_template_directory_uri() . '/assets/css/customize-controls.css',
		array(), false
	);
}
add_action( 'customize_controls_init', 'jeremy_customize_controls_css');

if ( ! function_exists( 'jeremy_customize_register' ) ) :
/**
 * Registers theme settings and controls for the Customizer and adds
 * postMessage support for some of the default controls.
 * 
 * @since 1.0.0
 * 
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function jeremy_customize_register( $wp_customize ) {
	/* == Site Identity ==*/
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
	
	/* == Site Identity - Footer == */
	// (Heading)
	$wp_customize->add_setting( 'footer_controls_start', array() );
	$wp_customize->add_control( new Customize_Fake_Control( $wp_customize, 'footer_controls_start', array(
		'label' 			=> __( 'Footer', 'jeremy' ),
		'description' => __( 'Control what appears in the site footer.', 'jeremy' ),
		'priority' 		=> 100,
		'section' 		=> 'title_tagline',
	) ) );

	// Copyright text
	$wp_customize->add_setting( 'copyright', array(
		/* translators: "(c) [year] [Site Name]" */
		'default' 					=> sprintf(
														__( '&copy; %1$s %2$s', 'jeremy' ),
														date('Y'), get_bloginfo('name') ),
		'sanitize_callback' => 'wp_filter_kses',
		'transport'					=> 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'copyright', array(
		'label' 	 => __( 'Copyright text', 'jeremy' ),
		'priority' => 100,
		'section'  => 'title_tagline',
		'type' 		 => 'text',
	) ) );

	// Toggle Presto theme credit in the footer
	$wp_customize->add_setting( 'show_theme_credits', array(
		'default' 	=> false,
		'transport' => 'postMessage',
	) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'show_theme_credits', array(
		'label' 	 => __( 'Show theme credits', 'jeremy' ),
		'priority' => 100,
		'section'  => 'title_tagline',
		'type' 		 => 'toggle',
	) ) );

	// Toggle "Proudly powered by WordPress" in the footer
	$wp_customize->add_setting( 'show_powered_by', array(
		'default' 	=> false,
		'transport' => 'postMessage',
	) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'show_powered_by', array(
		'label' 		=> __( 'Show WordPress credits', 'jeremy' ),
		'priority' 	=> 100,
		'section' 	=> 'title_tagline',
		'type' 			=> 'toggle',
	) ) );

	/* == Posts & Pages == */
	$wp_customize->add_section( 'singles' , array(
		'title' 			=> esc_html__( 'Posts & Pages', 'jeremy' ),
		'description' => __( 'Control how single posts and pages look.', 'jeremy' ),
	) );

	// Toggle next/previous post box under posts.
	$wp_customize->add_setting( 'use_post_nav', array(
		'default' 					=> true,
		'transport' 				=> 'postMessage',
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'use_post_nav', array(
		'label' 			=> __( 'Show "Read More" Section', 'jeremy' ),
		'description' => __(' Show links to the next and previous posts.', 'jeremy' ),
		'section' 		=> 'singles',
		'type'				=> 'toggle',
	) ) );
	
	/* == BuddyPress == */
	$wp_customize->add_section( 'community', array(
		'title' 					=> __( 'Member Directory', 'jeremy' ),
		'description' 		=> __( 'Settings for BuddyPress integration', 'jeremy' ),
		'active_callback' =>  function() { return function_exists( 'buddypress' ); }
	) );
	
	if ( function_exists( 'buddypress' ) ) {
		// Intended for a "featured business" function.
		$wp_customize->add_setting( 'bp_featured_member', array() );
		$wp_customize->add_control( new Customize_User_Control( $wp_customize, 'bp_featured_member', array(
			'label' 			=> __( 'Featured member', 'jeremy' ),
			'section' 		=> 'community',
		) ) );
		
		if ( bp_displayed_user_use_cover_image_header() ) {
			// Default cover image to use if members haven't set one.
			$wp_customize->add_setting( 'bp_default_cover', array() );
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bp_default_cover', array(
				'label' 			=> __( 'Default Profile Cover Image', 'jeremy' ),
				'section' 		=> 'community',
			) ) );
		}
		
		/* === XProfile Customization === */
		if ( bp_is_active( 'xprofile' ) ) {
			$location = array();
			if ( function_exists( 'pp_loc_init' ) ) {
				$location = array( 'location' );
			}
			
			// The category field
			$wp_customize->add_setting( 'bp_profile_category', array() );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_category', array(
				'label' 			=> __( 'Category Field', 'jeremy' ),
				'section' 		=> 'community',
				'field_types' => array( 'selectbox' ),
				'exclude' 		=> $location,
			) ) );
			
			if ( $location ) {
				// The address field
				$wp_customize->add_setting( 'bp_profile_address', array() );
				$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_address', array(
					'label' 			=> __( 'Address Field', 'jeremy' ),
					'description' => __( "The profile field to use for member maps. It's also shown on the profile sidebar.", 'jeremy' ),
					'section' 		=> 'community',
					'field_types' => $location,
				) ) );
				
				
				$wp_customize->add_setting( 'bp_address_omit', array() );
				$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'bp_address_omit', array(
					'label' 	 		=> __( 'Exclude from address', 'jeremy' ),
					'description' => __( 'Text (such as a state or country code) to remove from addresses shown in profiles.', 'jeremy' ),
					'section'  		=> 'community',
					'type' 		 		=> 'text',
				) ) );
			}
			
			// The RSS feed field
			$wp_customize->add_setting( 'bp_profile_rss', array() );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_rss', array(
				'label' 			=> __( 'RSS Feed Field', 'jeremy' ),
				'section' 		=> 'community',
				'field_types' => array( 'url' ),
				'exclude' 		=> $location,
			) ) );
			
			// (Heading)
			$wp_customize->add_setting( 'bp_profile_controls_start', array() );
			$wp_customize->add_control( new Customize_Fake_Control( $wp_customize, 'bp_profile_controls_start', array(
				'label' 			=> __( 'Customize Profiles', 'jeremy' ),
				'description' => __( 'Choose which fields to feature on user profiles.', 'jeremy' ),
				'section' 		=> 'community',
			) ) );
			
			// The about field
			$wp_customize->add_setting( 'bp_profile_about', array() );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_about', array(
				'label' 			=> __( 'About Field', 'jeremy' ),
				'description' => __( 'The field to use for member bios.', 'jeremy' ),
				'section' 		=> 'community',
				'exclude' 		=> $location,
			) ) );
			
			// Contact Field 1
			$wp_customize->add_setting( 'bp_profile_contact1', array() );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_contact1', array(
				'label' 	=> __( 'Contact Field 1', 'jeremy' ),
				'description' => __( 'Shown in the sidebar on profiles.', 'jeremy'),
				'section' => 'community',
				'exclude' => $location,
			) ) );
			
			// Contact Field 2
			$wp_customize->add_setting( 'bp_profile_contact2', array() );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_contact2', array(
				'label' 	=> __( 'Contact Field 2', 'jeremy' ),
				'description' => __( 'Shown in the sidebar on profiles.', 'jeremy'),
				'section' => 'community',
				'exclude' => $location,
			) ) );
			
			// Misc. Sidebar Field for listing business hours etc.
			$wp_customize->add_setting( 'bp_profile_misc', array() );
			$wp_customize->add_control( new Customize_XProfile_Control( $wp_customize, 'bp_profile_misc', array(
				'label' 	=> __( 'Sidebar Field', 'jeremy' ),
				'description' => __( 'An extra field to show in the sidebar. The name will be used as a heading with the content underneath.', 'jeremy'),
				'section' => 'community',
				'exclude' => $location,
			) ) );
		} // endif xProfile active
	} // endif BuddyPress active

	/* == Modules/External Libraries == */
	$wp_customize->add_section( 'modules', array(
		'title' 			=> __( 'Modules', 'jeremy' ),
		'description' => __( "Control how libraries are loaded.", 'jeremy' ),
	) );
	
	if ( defined( 'EVENT_ORGANISER_VER' ) ) {
		$wp_customize->add_setting( 'use_calendar', array(
			'default' => true,
		) );
		$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'use_calendar', array(
			'label'	      => __( 'Show Calendar', 'jeremy' ),
			'description' => __( 'Whether to show a full-sized calendar on the event archive pages.', 'jeremy' ),
			'section'     => 'modules',
			'type'        => 'toggle',
		) ) );
	}
	
	if ( defined( 'EVENT_ORGANISER_VER' ) || function_exists( 'buddypress' ) && ( function_exists( 'pp_loc_init' ) || get_theme_mod( 'bp_profile_address', false ) ) ) {
		// Enable Google Maps
		$wp_customize->add_setting( 'use_maps', array(
			'default' => true,
		) );
		$wp_customize->add_control( new Customize_Toggle_Control( $wp_customize, 'use_maps', array(
			'label'	      => __( 'Use Google Maps', 'jeremy' ),
			'description' => __( 'Whether to use maps in the theme.', 'jeremy' ),
			'section'     => 'modules',
			'type'        => 'toggle',
		) ) );
		
		// Maps JS API Key
		if ( function_exists( 'eventorganiser_get_google_maps_api_key' ) ) {
			$key = eventorganiser_get_google_maps_api_key();
		}
		$wp_customize->add_setting( 'gmaps_js_key', array(
			'default' => $key,
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'gmaps_js_key', array(
			'label' 			=> __( 'Google Maps Javascript API Key', 'jeremy' ),
			'description' => __( "You'll need an API key in order to use Google Maps.", 'jeremy' ),
			'section' 		=> 'modules',
		) ) );
	}
}
endif;
add_action( 'customize_register', 'jeremy_customize_register' );

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
 * Sanitizes an image filename to ensure it is an image file type. It allows for
 * various image types not supported by WordPress core provided they've been
 * enabled by external plug-ins. SVG files are only allowed if the user can
 * edit theme options, which probably should be true anyway, but why not.
 *
 * @TODO test
 * 
 * @since 1.0.0
 * 
 * @param string 							 $file    The image filename.
 * @param WP_Customize_Setting $setting The setting instance.
 * @return string		  	   							The image filename if it passes, or the
 * 																			default image filename if it doesn't.
 */
function jeremy_sanitize_image( $file, $setting ) {
	$all_mimes = get_allowed_mime_types();
	$image_mimes = array(
		'apng', 'bmp', 'ico', 'gif', 'jpg|jpeg|jpe', 'png', 'tifF|tif', 'webp' );
	if ( current_user_can( 'edit_posts' ) ) {
		$image_mimes[] = ['svg'];
	}
	$allowed_mimes = array();
	foreach ( $image_mimes as $mime ) {
		if ( array_key_exists( $mime, $all_mimes ) ) {
			$allowed_mimes[$mime] = $all_mimes[$mime];
		}
	}
	$file_ext = wp_check_filetype( $file, $allowed_mimes );
	
	$file = $file_ext['ext'] !== false ? $file : $setting->default;
	return esc_url_raw( $file );
}