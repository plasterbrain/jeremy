<?php
/**
 * Jeremy Plugin Compatibility
 *
 * Setup for compatibility with, and extra features for, BuddyPress, Event
 * Organiser, BP xProfile Location, Jetpack, and Soil.
 *
 * @package Jeremy
 * @subpackage Jeremy/Includes
 * @since 1.0.0
 */

if ( ! function_exists( 'jeremy_register_required_plugins' ) ) :
/**
 * TGM Plugin Activation
 *
 * Contains the recommended plug-ins users are prompted to download and
 * activate when using this theme.
 *
 * @link http://tgmpluginactivation.com/
 *
 * @package Jeremy
 * @subpackage Jeremy/Includes
 * @since 1.0.0
 */
function jeremy_register_required_plugins() {
	$plugins = array(
		array(
			'name'      => 'BuddyPress',
			'slug'      => 'buddypress',
			'required'  => false,
		),
		array(
			'name'      => 'BP xProfile Location',
			'slug'      => 'bp-xprofile-location',
			'required'  => false,
		),
		array(
			'name'      => 'Event Organiser',
			'slug'      => 'event-organiser',
			'required'  => false,
		),
	);

	$config = array(
		'id'           => 'jeremy',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'parent_slug'  => 'themes.php',
		'capability'   => 'edit_theme_options',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'jeremy_register_required_plugins' );
endif;

if ( ! is_admin() ) add_action( 'wp_enqueue_scripts', 'jeremy_plugin_scripts' );
/**
 * Dequeue some unnecessary plug-in scripts and enqueue some new ones.
 *
 * @package Jeremy
 * @subpackage Jeremy/BuddyPress
 * @since 1.0.0
 *
 * @see jeremy_directory_scripts()
 * @todo assign dqing of buddypress scripts to a theme option
 */
function jeremy_plugin_scripts() {
	wp_dequeue_style('bp-legacy-css');
	wp_dequeue_style('bp-admin-bar');
	wp_dequeue_script('bp-jquery-query');
	wp_dequeue_script('bp-confirm');
	wp_dequeue_script('bp-legacy-js');
	wp_dequeue_script('bp-moment');
	wp_dequeue_script('bp-livestamp');
}

add_action( 'after_setup_theme', 'jeremy_jetpack_setup' );
/**
 * Jetpack setup function.
 *
 * @package Jeremy
 * @subpackage Plug-ins
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

/**
 * Custom render function for Infinite Scroll.
 */
function jeremy_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		if ( is_search() ) :
			get_template_part( 'template-parts/content', 'search' );
		else :
			get_template_part( 'template-parts/content', get_post_format() );
		endif;
	}
}
add_action( 'bp_setup_nav', 'jeremy_bp_setup_nav', 999 );
/**
 * Since the theme shows xprofile details on the front, this function changes
 * the name of 'Home' to 'About' and turns the 'Profile' tab into a link for
 * the user to edit their profile.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.0
 */
function jeremy_bp_setup_nav() {
	$front_slug = bp_get_root_slug('front');
	$profile_slug = bp_get_profile_slug();
	buddypress()->members->nav->edit_nav( array( 'name' => __( 'About', 'jeremy' ) ), $front_slug );

	buddypress()->members->nav->delete_nav( 'edit', $profile_slug );
	if ( jeremy_bp_is_editor() ) {
		buddypress()->members->nav->edit_nav( array( 'name' => __( 'Edit Profile', 'jeremy' ) ), $profile_slug );
		buddypress()->members->nav->edit_nav( array( 'name' => _x( 'Edit', 'Edit your profile', 'jeremy' ) ), 'public', $profile_slug );
 	} else {
		buddypress()->members->nav->delete_nav( $profile_slug );
	}
}
add_filter( 'bp_current_action', 'jeremy_bp_profile_edit_filter' );
function jeremy_bp_profile_edit_filter( $action ) {
	if ( $action === 'public' ) {
		$action = 'edit';
	}
	return $action;
}

add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'jeremy_bp_set_cover_settings', 10, 1 );
/**
 * Set custom width and height for the BuddyPress cover image.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.0
 *
 * @link https://codex.buddypress.org/themes/buddypress-cover-images/
 */
function jeremy_bp_set_cover_settings( $settings = array() ) {
    $settings['width']  = 1000;
    $settings['height'] = 250;

    return $settings;
}
/**
 * Returns the default cover image set in the Customizer. If none has been
 * set, returns a blank 1x1 gif instead.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.0
 *
 * @return string url of the default cover image
 */
function jeremy_bp_get_default_cover() {
	$default = get_theme_mod( 'community-default-cover' ) ?: get_template_directory_uri() . '/assets/gif/blank.gif';
	return esc_url( $default );
}
add_action( 'wp_enqueue_scripts', 'jeremy_bp_scripts' );
function jeremy_bp_scripts() {
	if ( ! function_exists( 'buddypress' ) ) {
		return;
	}
	if ( is_page_template( 'templates/template-directory.php' ) ) {
		jeremy_directory_scripts();
	}
	if ( bp_is_directory() ) {
		wp_enqueue_script( 'bp-jquery-cookie' );
		wp_enqueue_script( 'jeremy-bp-legacy', get_template_directory_uri() . '/assets/js/min/buddypress.min.js', array( 'jquery' ), null, true );
	}
	wp_register_script( 'jeremy-bp-cover-image', get_template_directory_uri() . '/assets/js/min/cover-image.min.js', array(), null, true );

	$cover_images = array(
		'default' => jeremy_bp_get_default_cover()
	);
	if ( bp_is_profile_component() && bp_is_current_action( 'change-cover-image' ) ) {
		wp_dequeue_script( 'bp-cover-image' );
		wp_localize_script( 'jeremy-bp-cover-image', 'jeremyCoverImages', $cover_images );
		wp_enqueue_script( 'jeremy-bp-cover-image' );
	}
}
add_action( 'bp_init', 'jeremy_bp_options_filter' );
/**
 * The bp_get_options_nav_ filter exists for each possible subnav item.
 * This function iterates over and adds the filter for each one.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.0
 *
 * @see jeremy_bp_options_nav
 */
function jeremy_bp_options_filter() {
	$component_index = ! empty( $bp->displayed_user ) ? bp_current_component() : bp_get_root_slug( bp_current_component() );

	$options = buddypress()->members->nav->get_secondary( array(
        'parent_slug' => $component_index,
	) );
	if ( bp_is_user() && is_array( $options ) ) {
		// If the item has no secondary menu and $options isn't an array, don't bother
		foreach( $options as $nav ){
			add_filter( 'bp_get_options_nav_' . $nav['css_id'], 'jeremy_bp_options_nav', 10, 3 );
		}
	}
};
/**
 * Removes the id from list items and changes the default 'current selected'
 * class attribute of the current menu item to 'active', which matches our
 * other menus.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.0
 *
 * @see bp_get_options_nav_ filter
 * @link http://buddypress.wp-a2z.org/oik_api/bp_get_options_nav/
 *
 * @param string $html     The li item html output.
 * @param array $subnav    The subnav item.
 * @param string $selected The slug of the current menu item.
 */
function jeremy_bp_options_nav( $html, $subnav, $selected ) {
	$class = ( $subnav->slug === $selected ) ? ' class="active"' : '';
	$html = '<li' . $class . '><a href="' . esc_url( $subnav->link ) . '">' . $subnav->name . '</a></li>';
	return $html;
}
add_filter( 'bp_get_the_profile_field_required_label', 'jeremy_bp_required_label' );
/**
 * @see bp_get_the_profile_field_required_label()
 */
function jeremy_bp_required_label( $label ) {
	$label = '<span class="required">*</span>';
	return $label;
}

/**
 * Returns the url of the BuddyPress user's cover image.
 *
 * @package Jeremy
 * @subpackage Plug-ins
 *
 * @param int $id  The user id, default the user you're looking at.
 */
function jeremy_get_bp_cover_image( $id = '' ) {
	if ( bp_disable_cover_image_uploads() ) {
		return;
	}

	$id = empty( $id ) ? bp_displayed_user_id() : $id;

	$cover_url = bp_attachments_get_attachment( 'url', array(
		'item_id'=> $id,
	) );
	return $cover_url;
}

/**
 * Check whether the current user is a moderator/admin or is viewing their own profile.
 * Keepin that code DRY. Use it in a conditional statement!
 *
 * @package Jeremy
 * @subpackage Plug-ins
 *
 * @return bool true if can edit/is looking at own profile, false otherwise
 */
function jeremy_bp_is_editor() {
	if ( bp_is_my_profile() || bp_current_user_can( 'bp_moderate' ) || current_user_can( 'edit_users' ) ) {
		return true;
	} else {
		return false;
	}
}
add_action( 'init', 'jeremy_bp_setup_nav_init' );
/**
 * Adds new items to the BuddyPress profile menu. Hooked into init because the default
 * bp_setup_nav is too early to recognize custom post types. This will probably bite me
 * in the ass later, but who knows?
 *
 * @package Jeremy
 * @subpackage Plug-ins
 * @since 1.0.0
 *
 * @todo Allow this to recognize more potential sources of "job," "event," and "deal"
 * post types than the default supported ones.
 */
function jeremy_bp_setup_nav_init() {
	if ( post_type_exists( 'event' ) ) {
		$event_slug = __( 'events', 'jeremy' );
		bp_core_new_nav_item(
			array(
				'name' => __( 'Events', 'jeremy' ),
				'slug' => $event_slug,
				'position' => 50,
				'screen_function' => 'jeremy_bp_events_template',
				'show_for_displayed_user' => true,
				'default_subnav_slug' => $event_slug,
		) );
	}
}
function jeremy_bp_events_template() {
	add_action( 'bp_template_title', 'jeremy_bp_events_title' );
	add_action( 'bp_template_content', 'jeremy_bp_events_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function jeremy_bp_events_title() {
	echo '<h2>' . __( 'Events', 'jeremy' ) . '</h2>';
}
function jeremy_bp_events_content() {
	if ( ! function_exists( 'eo_get_events' ) )
		return;
	// Attempt to get the matching Event Organiser venue slug for this user.
	$user_slug = jeremy_get_the_venue_slug( bp_get_displayed_user_fullname() );
	// If none exists, get their author id instead.
	$id = $user_slug ? '' : bp_displayed_user_id();
	// Show past events if the users have enabled it.
	$past = eventorganiser_get_option( 'showpast' );
	if ( $past ) {
		$description = sprintf( __( 'Showing past and upcoming events for %s.', 'jeremy' ), bp_get_displayed_user_fullname() );
	} else {
		$description = sprintf( __( 'Showing upcoming events for %s.', 'jeremy' ), bp_get_displayed_user_fullname() );
	}
	echo '<p class="archive-description"><em>' . $description . '</em></p>';
    $events = new WP_Query( array(
		'event-venue' => $user_slug,
		'author' => $id,
		'post_type' => 'event',
		'suppress_filters' => false,
		'showpastevents' => $past,
		) );
    if ( $events->have_posts() ) {
		echo '<div class="archive-event">';
        while ( $events->have_posts() ) {
            $events->the_post();
            //display title
            get_template_part( 'events/eo-loop-single-event' );
		}
		echo '</div>';
    } else {
		echo '<p>' . __( 'There are no events to show right now.', 'jeremy' ) . '</p>';
	}
	if ( current_user_can( 'administrator' ) ) { ?>
		<a class="button" href="<?php echo admin_url( 'post-new.php?post_type=event' )?>"><?php _e( 'Add new', 'jeremy-cpt' ); ?></a>
	<?php }
	wp_reset_postdata();
}
/**
 * Renders XProfile field name and data, using different methods to escape/sanitize
 * the data depending on the field type.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.1
 *
 * @param string $name  The name of the field to display
 * @param bool $heading Whether to show a heading with the field name.
 */
function jeremy_bp_display_field( $name, $heading = true, $user_id = null ) {
	$id = xprofile_get_field_id_from_name( $name );
	$user_id = $user_id ?: bp_displayed_user_id();
	$field = xprofile_get_field( $id, $user_id );
	if ( $field == null || empty( $field->data->value ) ) {
		return;
	}

	if ( $heading ) echo '<h3>' . sanitize_text_field( $field->name ) . '</h3>';

	switch ( $field->type ) {
		case 'textarea' :
			echo wpautop( wp_kses_data( $field->data->value ) );
			break;
		case 'url' :
			$url = esc_url( $field->data->value );
			$link = rtrim( $url, '/' );
			$link = substr( $link, strpos( $link, ":" ) + 3 );
			$link_slash = strpos( $link, '/' );
			$link = $link_slash !== false ? substr( $link, 0, $link_slash ) : $link;
			echo '<p class="field-value"><a href="' . $url . '">' . $link . '</a></p>';
			break;
		case 'selectbox' :
			echo '<p class="field-value">';
			jeremy_bp_the_search_link( esc_attr( $field->data->value ) );
			echo '</p>';
			break;
		case 'location' :
			$address = sanitize_text_field( $field->data->value );
			if ( strpos( $address, ',' ) !== false ) {
				/* Remove the country since these addresses are local */
				$address = substr( $address, 0, strrpos( $address, ',' ) );
				if ( strpos( $address, ',' ) !== false ) {
					/* Remove the statesince these addresses are local */
					$address = substr( $address, 0, strrpos( $address, ',' ) );
				}
			}
			echo '<p class="field-value"><address class="field-value">' . $address . '</address></p>';
			break;
		default:
			echo '<p class="field-value">' . wp_kses_data( $field->data->value ) . '</p>';
			break;
	}
}
/**
 * Attempts to render cleaner html for generic xprofile fields on the front end
 * Edit Profile page. If there's no match for the xprofile field type, it will
 * call the edit_field_html function BuddyPress uses by default.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * @see BP_XProfile_Field_Type_{type}
 * @see bp_get_form_field_attributes
 */
function jeremy_bp_xprofile() {
	$field_type = bp_get_the_profile_field_type();
	$field = bp_xprofile_create_field_type( $field_type );
	$field_name = sanitize_text_field( bp_get_the_profile_field_name() );
	$field_id = bp_get_the_profile_field_input_name();
	$user_id = bp_displayed_user_id();
	$required = bp_get_the_profile_field_is_required() ? ' aria-required="true"' : '';
	switch ( $field_type ) {
		case 'textbox' : ?>
			<section>
			<label for="<?php echo $field_name; ?>">
				<?php bp_the_profile_field_name(); ?>
				<?php bp_the_profile_field_required_label(); ?>
			</label>

			<?php if ( bp_get_the_profile_field_description() ) { ?>
				<p class="field-desc" id="<?php echo $field_name . '-description'; ?>"><?php bp_the_profile_field_description(); ?></p>
			<?php }
			/** This action is documented in bp-xprofile/bp-xprofile-classes */
			do_action( bp_get_the_profile_field_errors_action() );
			?>
			<input aria-describedby="<?php echo $field_name.'-description'; ?>" id="<?php echo $field_name; ?>" type="text" value="<?php echo bp_get_the_profile_field_edit_value(); ?>" name="<?php echo $field_id; ?>" <?php echo bp_get_form_field_attributes( $field_name ); echo $required; ?> >
			</section>
			<?php
			break;
		case 'url' :
			?>
			<section>
			<label for="<?php echo $field_name; ?>">
				<?php bp_the_profile_field_name(); ?>
				<?php bp_the_profile_field_required_label(); ?>
			</label>

			<?php if ( bp_get_the_profile_field_description() ) { ?>
				<p class="field-desc" id="<?php echo $field_name . '-description'; ?>"><?php bp_the_profile_field_description(); ?></p>
			<?php }
			/** This action is documented in bp-xprofile/bp-xprofile-classes */
			do_action( bp_get_the_profile_field_errors_action() ); ?>

			<input type="text" inputmode="url" id="<?php echo $field_name; ?>" aria-describedby="<?php echo $field_name.'-description'; ?>" value="<?php echo esc_url( bp_get_the_profile_field_edit_value() ); ?>" name="<?php echo $field_id; ?>" <?php echo $required; ?>>
			</section>
			<?php
			break;
		case 'textarea' :
			$richtext_enabled = bp_xprofile_is_richtext_enabled_for_field(); ?>
			<section class="textbox-field">
			<label for="<?php echo $field_name; ?>">
				<?php bp_the_profile_field_name(); ?>
				<?php bp_the_profile_field_required_label(); ?>
			</label>

			<?php
			if ( bp_get_the_profile_field_description() ) { ?>
				<p class="field-desc" id="<?php echo $field_name . '-description'; ?>"><?php bp_the_profile_field_description(); ?></p>
			<?php }
			/** This action is documented in bp-xprofile/bp-xprofile-classes */
			do_action( bp_get_the_profile_field_errors_action() );
			if ( ! $richtext_enabled ) {
				?>
				<textarea aria-describedby="<?php echo $field_name . '-description';?>" id="<?php echo $field_name; ?>" name="<?php echo $field_id; ?>" cols="40" rows="5" <?php echo $required; ?>><?php bp_the_profile_field_edit_value(); ?></textarea>
				<?php
			} else {
				/** This filter is documented in BP_XProfile_Field_Type_Textarea */
				$editor_args = apply_filters( 'bp_xprofile_field_type_textarea_editor_args', array(
					'teeny'         => true,
					'media_buttons' => false,
					'quicktags'     => true,
					'textarea_rows' => 10,
				), 'edit' );
				wp_editor(
					bp_get_the_profile_field_edit_value(),
					bp_get_the_profile_field_input_name(),
					$editor_args
				);
			} ?>
			</section>
			<?php
			break;
		case 'selectbox' : ?>
			<section>
			<label for="<?php echo $field_name; ?>">
				<?php bp_the_profile_field_name(); ?>
				<?php bp_the_profile_field_required_label(); ?>
			</label>

			<?php if ( bp_get_the_profile_field_description() ) { ?>
				<p class="field-desc" id="<?php echo $field_name.'-description'; ?>"><?php bp_the_profile_field_description(); ?></p>
			<?php }
			/** This action is documented in bp-xprofile/bp-xprofile-classes */
			do_action( bp_get_the_profile_field_errors_action() ); ?>

			<div class="select">
			<select id="<?php echo $field_name; ?>" aria-describedby="<?php echo $field_name.'-description'; ?>" name="<?php echo $field_id; ?>" <?php echo $required; ?>>
				<?php bp_the_profile_field_options( array( 'user_id' => $user_id ) ); ?>
			</select>
			</div>
			</section>
			<?php
			break;
		default: ?>
			<section>
			<?php $field->edit_field_html(); ?>
			</section>
			<?php
	}
}

/**
 * Echoes a list of linked svg social media icons for any specified XProfile
 * field names that have data for the displayed user.
 *
 * You can add support for more social media sites by calling the function with
 * an array of field names and including svg files in your child theme under
 * the same name if a matching icon does not already exist.
 *
 * NOTE: If a LinkedIn XProfile value is not a url, the function will append
 * the value as a slug to LinkedIn's structure for company profiles
 * (linkedin.com/company) rather than personal ones (linkedin.com/in).
 *
 * @package Jeremy
 * @subpackage Jeremy/BuddyPress
 * @since 1.0.0
 *
 * @see xprofile_get_field
 * @see jeremy_get_svg
 *
 * @param array $sites  Optional array of strings for each social media
 * 						XProfile field name. Default includes 'Facebook',
 * 						'Twitter', 'Instagram', 'LinkedIn', and 'YouTube'. The
 * 						array order is used as order of icon appearance.
 */
function jeremy_bp_social_links( $sites = null ) {
	if ( ! $sites ) {
		$sites = array(
			'Website',
			'Email',
			'Facebook',
			'Twitter',
			'Instagram',
			'LinkedIn',
			'YouTube',
		);
	}
	foreach( $sites as $network ) {
		$id = xprofile_get_field_id_from_name( $network );
		if ( $id ) { // Only continue if a field exists for this site.
			$field = xprofile_get_field( $id, bp_displayed_user_id() );
			$field_data = $field->data->value;
			if ( ! empty( $field_data ) ) {
				$link = filter_var( $field_data, FILTER_VALIDATE_URL ) ? esc_url( $field_data ) : null;
				// If it doesn't pass as a url, assume it's a username.
				// Remove possibly @ symbol from username
				$username = urlencode( str_replace( '@', '', $field_data ) );
				/* translators: alt text for the social link svgs. */
				$alt = sprintf( esc_html__( 'Follow %s on %s', 'jeremy' ), bp_get_displayed_user_fullname(), $network );
				switch( $network ) {
					case 'Facebook':
						// File name, e.g., jeremy/assets/svg/facebook.svg
						$svg = 'facebook';
						break;
					case 'Twitter':
						$svg = 'twitter';
						break;
					case 'Instagram':
						$svg = 'instagram';
						break;
					case 'LinkedIn':
						$link = $link ?: 'https://linkedin.com/company/' . $username;
						$svg = 'linkedin';
						break;
					case 'YouTube':
						$link = $link ?: 'https://youtube.com/channel/' . $username;
						$svg = 'youtube';
						break;
					default:
						$svg = sanitize_key( $network );
				}
				if ( $network == 'Website' ) {
					if ( $link ) {
						/* translators: %s is username */
						$alt = sprintf( esc_html__( "%s's website", 'jeremy' ), bp_get_displayed_user_fullname() );
						$svg = jeremy_get_svg( array( 'img'=>'website', 'alt'=>$alt ) );
						printf( '<a href="%s">%s</a>', $link, $svg );
					}
				} elseif ( $network == 'Email' ) {
					$link = filter_var( $field_data, FILTER_VALIDATE_EMAIL ) ? 'mailto:' . sanitize_email( $field_data ) : null;
					if ( $link ) {
						/* translators: %s is username */
						$alt = sprintf( esc_html__( 'Email %s', 'jeremy' ), bp_get_displayed_user_fullname() );
						$svg = jeremy_get_svg( array( 'img'=>'email', 'alt'=>$alt ) );
						printf( '<a href="%s">%s</a>', $link, $svg );
					}
				} else {
					// example.com/username
					$link = $link ?: 'https://' . sanitize_key( $network ) . '.com/' . $username;
					// Use site name if no icon exists for it.
					$svg = $svg ? jeremy_get_svg( array( 'img'=>$svg, 'alt'=>$alt ) ) : esc_html( $network );
					printf( '<a href="%s">%s</a>', $link, $svg );
				}
			}
		}
	}
}

/**
 * Retrieves the categories associated with the Category field set in the
 * Customizer. This function assumes the Category field is a selectbox, and
 * returns the array of its options as objects.
 *
 * @package Jeremy
 * @subpackage Jeremy/BuddyPress
 * @since 1.0.0
 *
 * @see xprofile_get_field
 * @see BP_XProfile_Field->get_children()
 *
 * @return array|bool 	Array of children of the category field on success,
 * 						false on failure.
 */
function jeremy_xprofile_get_categories() {
	$id = xprofile_get_field_id_from_name( get_theme_mod( 'bp_profile_category' ) );
	$field = xprofile_get_field( $id );

	if ( $field == null || $field->type !== 'selectbox' ) {
		return false;
	}

	return $field->get_children();
}

/**
 * Echoes the BuddyPress member directory search link for the given term.
 *
 * @package Jeremy
 * @subpackage Jeremy/BuddyPress
 * @since 1.0.0
 *
 * @param string $value  The search term, aka the value of the field.
 */
function jeremy_bp_the_search_link( $value ) {
	$search_url = bp_get_members_directory_permalink() . '?s=' . urlencode( $value );
	/* translators: %s is search term */
	$label = sprintf( esc_html__( 'Search all members in &quot;%s&quot;', 'jeremy' ), $value );
	printf( '<a href="%s" aria-label="%s">%s</a>', $search_url, esc_attr( $label ), sanitize_text_field( $value ) );
}

/**
 * Returns the corresponding geocode field created by the BP xProfile
 * Location plug-in for the main Address field set in theme options.
 *
 * @package Jeremy
 * @subpackage Jeremy/BuddyPress
 * @since 1.0.0
 *
 * @return string|bool The geocode meta key, or false if field does not exist.
 */
function jeremy_xprofile_get_geocode() {
    $field = get_theme_mod( 'bp_profile_address' );
	if ( ! xprofile_get_field_id_from_name( $field ) ) {
		return false;
	}
	// Grab the BP xProfile Location geocode field
	$id = xprofile_get_field_id_from_name( $field );
	return 'geocode_' . $id;
}

/**
 * Loads Google Maps scripts and localizes a list of users/addresses to map.js
 * in order to show member addresses on the directory map. Requires Buddypress
 * and BP xProfile Location to work. The latter plug-in must have the save
 * geocode option enabled. Admin can set the field used for map addresses via
 * the Customizer.
 *
 * @package Jeremy
 * @subpackage Jeremy/Maps
 * @since 1.0.0
 *
 * @var array $addresses   Contains arrays with display name, geocode, and
 * 						   profile link
 *
 */
function jeremy_directory_scripts() {
	wp_register_script( 'jeremy-mapinit', get_template_directory_uri() . '/assets/js/map.js', array(), null, true );
	$key = esc_attr( get_theme_mod( 'gmaps-api' ) );
	$geocode = jeremy_xprofile_get_geocode();
	if ( ! $geocode || ! $key )
		return;

	// Run a query to find every user/geocode entry we have.
	$user_query = new WP_User_Query( array(
		'meta_key' => $geocode,
		'fields' => array( 'ID', 'display_name' ),
	) );
	$addresses = array();
	if ( ! empty( $user_query->results ) ) {
		foreach ( $user_query->results as $user ) {
			$page = bp_core_get_user_domain( $user->ID );
			$addresses[] = array(
				'name' => $user->display_name,
				'geocode' => get_user_meta( $user->ID, $geocode ),
				'page' => $page,
			);
		}
	}
	// markers is the name of the JSON object we'll reference in the js.
    wp_localize_script( 'jeremy-mapinit', 'markers', $addresses );
	$gmaps = 'https://maps.googleapis.com/maps/api/js?key=' . $key . '&callback=initMap';
	wp_enqueue_script( 'jeremy-mapinit');
	wp_enqueue_script( 'jeremy-googlemaps', $gmaps, array('jeremy-mapinit'), null, true );
}

/**
 * Returns an Event Organiser venue slug for the given name.
 *
 * @package Jeremy
 * @subpackage Plugins/Events
 *
 * @see eo_get_venue_by
 *
 * @param string $name  The venue name
 * @return string       The venue slug
 */
function jeremy_get_the_venue_slug( $name ) {
	$venue_obj = eo_get_venue_by( 'name', $name );
	if ( ! $venue_obj )
		return false;
	$venue_slug = $venue_obj->slug;
	return $venue_slug;
}

/**
 * Check if the given search query (an event venue name) matches any existing
 * user display names. That way we can link an event venue to a user profile
 * instead of the generic venue archive page.
 *
 * @package Jeremy
 * @subpackage Jeremy/BuddyPress
 * @since 1.0.0
 *
 * @see WP_User_Query
 * @see bp_core_get_user_domain
 * @see jeremy_get_the_venue_slug
 *
 * @param string $venue   The venue name to be used as a search query
 * @param bool $force_bp  Whether to force the function to return BuddyPress
 * 						  link instead of falling back to the EO venue link.
 * 						  Default false.
 * @return string|bool    The URL of the first matching user's Buddypress
 * 						  profile on success. False if BuddyPress isn't active
 * 						  and $force_bp is true.
 */
function jeremy_get_the_venue_link( $venue, $force_bp = false ) {
	if ( $force_bp && ! function_exists( 'buddypress' ) )
		return false;
	$user_query = new WP_User_Query( array(
		'search'         => $venue,
		'search_columns' => array( 'display_name' ),
		'fields' => 'ID',
	) );
	$users = $user_query->get_results();
	if ( ! empty( $users ) ) {
		$first_user = $users[0];
		$link = bp_core_get_user_domain( $first_user );
	} else {
		$venue_slug = jeremy_get_the_venue_slug( $venue );
		// Return the EO venue page if no matching profile is found.
		$link = eo_get_venue_link( $venue_slug );
	}
	return esc_url( $link );
}

/**
 * Prints a link to Google Maps directions for an event venue.
 *
 * @package Jeremy
 * @subpackage Jeremy/Events
 * @since 1.0.0
 *
 * @param string $venue  The venue name.
 */
function jeremy_the_directions( $venue = null ) {
	$venue_urlname = $venue? urlencode( $venue ) : urlencode( bp_get_displayed_user_fullname() );
	$directions = 'https://www.google.com/maps/dir/?api=1&destination=' . $venue_urlname;
	printf( '<p class="get-directions"><a href="%s">' . __( 'Get Directions', 'jeremy' ) . '</a></p>', esc_url( $directions ) );
}

function jeremy_the_profile_map( $use_address = false ) {
	$key = get_theme_mod( 'gmaps-api' );
	$field = xprofile_get_field_id_from_name( get_theme_mod( 'community-address-field', 'Address' ) );
	if ( $field && $use_address ) {
		$address = xprofile_get_field_data( $field ) ? xprofile_get_field_data( $field, '', 'comma' ) : bp_get_displayed_user_fullname();
	} else {
		$address = bp_get_displayed_user_fullname();
	}
	$address = urlencode( $address );
	if ( ! $key || ! $address )
		return;
	$url = 'https://www.google.com/maps/embed/v1/place?key=' . esc_attr( $key ) . '&q=' . $address;
	?>
	<iframe
		width="600"
		height="350"
		frameborder="0" style="border:0; width:100%;"
		src="<?php echo $url; ?>">
  	</iframe>
<?php
}
function jeremy_the_venue_map() {
	if ( ! function_exists( 'eo_get_venue_address' ) )
		return;
	$address = eo_get_venue_address()['address'];
	$key = get_theme_mod( 'gmaps-api' );
	$address = urlencode( $address );
	if ( ! $key || ! $address )
		return;
	$url = 'https://www.google.com/maps/embed/v1/place?key=' . esc_attr( $key ) . '&q=' . $address;
	?>
	<iframe
		width="600"
		height="350"
		frameborder="0" style="border:0; width:100%;"
		src="<?php echo $url; ?>">
  	</iframe>
<?php }

add_filter( 'formidable_paypal_url', 'jeremy_formidable_paypal_url', 10, 3 );
function jeremy_formidable_paypal_url( $paypal_url, $entry_id, $form_id ){
	$paypal_url .= '&no_shipping=1&image_url='. urlencode( 'https://www.countrysidechamber.org/app/uploads/2018_paypal-logo.png' );
	return $paypal_url;
}
