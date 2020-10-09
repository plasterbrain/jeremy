<?php
/**
 * BuddyPress Compatibility Layer
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 2.0.0
 */

if ( ! function_exists( 'jeremy_bp_dequeue_scripts' ) ) :
/**
* Dequeue some unnecessary plug-in scripts and enqueue some new ones.
*
* @since 1.0.0
*/
function jeremy_bp_dequeue_scripts() {
	if ( ! is_admin() ) {
	 	wp_dequeue_style('bp-legacy-css');
		wp_dequeue_style( 'bp-member-block' );
	 	wp_dequeue_style('bp-admin-bar');
	 	wp_deregister_script('bp-legacy-js');
	 	wp_deregister_script('bp-moment');
	 	wp_deregister_script('bp-livestamp');
		if ( ! bp_is_user_settings() ) {
		 	wp_deregister_script('bp-jquery-query');
		 	wp_deregister_script('bp-confirm');
		 	wp_dequeue_script('bp-jquery-query');
		 	wp_dequeue_script('bp-confirm');
		}
	 	wp_dequeue_script('bp-legacy-js');
	 	wp_dequeue_script('bp-moment');
	 	wp_dequeue_script('bp-livestamp');
	}
	
	if ( bp_is_directory() ) {
		wp_enqueue_script( 'bp-jquery-cookie' );
		wp_enqueue_script(
			'jeremy-bp-legacy',
			jeremy_get_script_path( 'buddypress' ),
			array( 'jquery' ), null, true );
		
		if ( get_theme_mod( 'use_maps', true ) ) {
			jeremy_bp_init_map();
		}
	} elseif ( jeremy_bp_is_public_profile() ) {
		if ( get_theme_mod( 'use_maps', true ) ) {
			jeremy_bp_init_map( false );
		}
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'jeremy_bp_dequeue_scripts' );

if ( ! function_exists( 'jeremy_bp_set_cover_image_size' ) ) :
/**
 * Adds some cover image dimensions so the theme doesn't show "0px wide and
 * 225px tall."
 *
 * @link https://codex.buddypress.org/themes/buddypress-cover-images/
 *
 * @since 2.0.0
 * 
 * @param array $settings BP cover image settings.
 */
function jeremy_bp_set_cover_image_size( $settings = array() ) {
    $settings['width']  = 1170;
    $settings['height'] = 250;
 
    return $settings;
}
endif;
add_filter( 'bp_before_members_cover_image_settings_parse_args', 'jeremy_bp_set_cover_image_size', 10, 1 );

if ( ! function_exists( 'jeremy_bp_init_map' ) ) :
/**
 * Loads Google Maps scripts and localizes a list of users/addresses to map.js
 * in order to show member addresses on the directory map. Requires Buddypress
 * and BP xProfile Location to work. The latter plug-in must have the save
 * geocode option enabled. Admin can set the field used for map addresses via
 * the Customizer.
 * 
 * @since 1.0.0
 *
 * @var array $addresses   Contains arrays with display name, geocode, and
 * 												 profile link
 *
 */
function jeremy_bp_init_map( $directory = true ) {
	$api_key = esc_js( get_theme_mod( 'gmaps_js_key', false ) );
	$geocode_key = jeremy_xprofile_get_geocode_key();
	if ( ! $geocode_key || ! $api_key ) {
		return;
	}

	wp_register_script(
		'jeremy-map',
		jeremy_get_script_path( 'map' ),
		array(), null, true );
	
	// Find some marker data to send to the js
	$addresses = array();
	if ( $directory ) {
		// Find the geocode value for every user on the site.
		$user_query = new WP_User_Query( array(
			'meta_key' => $geocode_key,
			'fields' => array( 'ID', 'display_name' ),
		) );
		
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$addresses[] = array(
					'name' 		=> esc_js( $user->display_name ),
					'geocode' => get_user_meta( $user->ID, $geocode_key ),
					'link' 		=> esc_js( bp_core_get_user_domain( $user->ID ) ),
				);
			}
		}
	} else {
		// Get the geocode for the currently displayed user.
		$id = bp_displayed_user_id();
		$geocode = get_user_meta( $id, $geocode_key );
		if ( $geocode ) {
			$addresses[] = array(
				'name' 	  => esc_js( bp_get_displayed_user_fullname() ),
				'geocode' => $geocode,
				'link'		=> false,
			);
		}
	}

	if ( ! $addresses ) {
		return;
	} 
	// markers is the name of the JSON object we'll reference in the js.
	wp_localize_script( 'jeremy-map', 'markers', $addresses );
	wp_enqueue_script( 'jeremy-map' );
	wp_enqueue_script( 'jeremy-gmaps', 'https://maps.googleapis.com/maps/api/js?key=' . esc_js( $api_key ) . '&callback=initMap', array( 'jeremy-map' ), null, true );
}
endif;

/**
 * The "Edit Member" link in the admin toolbar just goes to the front page of
 * the profile, which is useless to us.
 * 
 * @since 1.0.0
 */
function j_bp_admin_bar() {
	global $wp_admin_bar;
	
	if ( bp_is_user_profile() && jeremy_bp_is_editor() ) {
		$wp_admin_bar->remove_node( 'user-admin' );
		
		$wp_admin_bar->add_node( array(
			'id'    => 'user-admin',
			'title' => __( 'Edit Member', 'jeremy' ),
			'href'  => get_edit_user_link( bp_displayed_user_id() ),
		) );	
	}
}
add_action( 'admin_bar_menu', 'j_bp_admin_bar', 100 );

if ( ! function_exists( 'jeremy_bp_required_label' ) ) :
/**
 * @see bp_get_the_profile_field_required_label()
 */
function jeremy_bp_required_label( $label ) {
	$label = '<span class="required">*</span>';
	return $label;
}
endif;
add_filter( 'bp_get_the_profile_field_required_label', 'jeremy_bp_required_label' );

/**
 * @link https://codex.buddypress.org/developer/loops-reference/the-members-loop/
 *
 * @since 2.0.0
 * 
 * @param  [type] $category [description]
 * @return [type]           [description]
 */
function jeremy_bp_cat_filter( $category ) {
  global $wpdb;
  
  $field_id = xprofile_get_field_id_from_name( get_theme_mod( 'bp_profile_category' ) ); 
	
	$query = $wpdb->prepare( "
		SELECT user_id
		FROM {$wpdb->prefix}bp_xprofile_data
		WHERE field_id = %s
		AND value = %s",
		$field_id,
		htmlspecialchars( $category )
	);
  return $wpdb->get_col( $query ); 
}

/* ===== Template Tags ===== */

/**
 * Returns the name of the query parameter string to search for members in a
 * category, default "category" (e.g. ?category=animals).
 *
 * @since 2.0.0
 * 
 * @return string		The category query parameter string.
 */
function jeremy_bp_cat_query() {
	$default = _x( 'category', 'Category query parameter string', 'jeremy' );
	/**
	 * Filters the name of the category query parameter. If the result is not a
	 * string, it will be replaced with the default, "category."
	 *
	 * @since 2.0.0
	 * 
	 * @param string $cat		The category query parameter string.
	 */
	$cat = apply_filters( 'jeremy_bp_cat_query', $default );
	$cat = is_string( $cat ) ? $cat : $default;
	return sanitize_title( urlencode( $cat ) );
}

if ( ! function_exists( 'jeremy_bp_get_search_link' ) ) :
/**
 * Echoes the BuddyPress member directory search link for the given term.
 *
 * @since 1.0.0
 *
 * @param string $value  The search term, aka the value of the field.
 * @return string				 The linked field value.
 */
function jeremy_bp_get_search_link( $value ) {
	$search_url = add_query_arg( jeremy_bp_cat_query(), urlencode( $value ), bp_get_members_directory_permalink() );
	
	/* translators: %s is search term, usually a member category */
	$label = sprintf( esc_html__( 'Search all members in &quot;%s&quot;', 'jeremy' ), $value );
	
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return sprintf( '<a class="entry__meta-terms__item" href="%s" aria-label="%s">%s</a>', esc_url( $search_url ), esc_attr( $label ), esc_html( $value ) );
}
endif;

if ( ! function_exists( 'jeremy_bp_is_public_profile' ) ) :
/**
 * Returns whether the given BuddyPress profile subpage is "[edit] profile,"
 * "settings," "notifications," or "messages."
 *
 * @since 2.0.0
 * 
 * @return boolean I literally just explained this oh my GOD
 */
function jeremy_bp_is_public_profile() {
	if ( bp_is_user_profile() ) {
		return bp_current_action() === 'public';
	}
	return ! ( bp_is_user_messages() || bp_is_user_notifications() || bp_is_user_settings() );
}
endif;

/**
 * Redirects users without editing access who try to access "member/profile"
 * URL to the front page of the profile. The template hides the editing forms
 * already, so this is just for SEO. Part of Operation: Consolidate Profile.
 *
 * @link https://github.com/buddypress/BuddyPress/blob/60666dcd287bdc1fe6bbd0d63e77c519acadd0e1/src/bp-core/bp-core-catchuri.php
 * 
 * @since 2.0.0
 */
function jeremy_bp_redirect_noneditors() {
	if ( class_exists( 'BuddyPress') ) {
		if ( bp_is_user_profile() && ! jeremy_bp_is_editor() ) {
			$bp = buddypress();
      bp_core_redirect( $bp->canonical_stack['base_url'] );
		}
	}
}
add_action( 'template_redirect', 'jeremy_bp_redirect_noneditors' );

if ( ! function_exists( 'jeremy_bp_is_editor' ) ) :
/**
 * Whether the current user is an admin or is viewing their own profile.
 *
 * @since 1.0.0
 *
 * @return bool true if can edit/is looking at own profile, false otherwise
 */
function jeremy_bp_is_editor() {
	return ( bp_is_my_profile() || bp_current_user_can( 'bp_moderate' ) || current_user_can( 'edit_users' ) );
}
endif;

if ( ! function_exists( 'jeremy_bp_get_default_cover' ) ) :
/**
 * Returns the default BuddyPress profile cover image set in the Customizer, r
 * a blank 1x1 gif if none is set. It does not escape the URL.
 * 
 * @since 1.0.0
 *
 * @return string URL of the default cover image, or a blank gif if none set
 */
function jeremy_bp_get_default_cover() {
	return get_theme_mod( 'community-default-cover' ) ?: get_template_directory_uri() . '/assets/gif/blank.gif';
}
endif;

if ( ! function_exists( 'jeremy_bp_get_cover_image' ) ) :
/**
 * Given a BuddyPress user id, returns the URL of the user's cover image. It
 * does not escape the URL. There is, astonishingly, no built-in way to do this
 * in BuddyPress.
 *
 * @since 1.0.0
 *
 * @param int $id	 The user id, default the user you're looking at.
 */
function jeremy_bp_get_cover_image( $id = '' ) {
	if ( bp_disable_cover_image_uploads() ) {
		return;
	}

	$id = empty( $id ) ? bp_displayed_user_id() : $id;

	$cover_url = bp_attachments_get_attachment( 'url', array(
		'item_id'=> $id,
	) );

	return $cover_url;
}
endif;

/**
 * "Public profile" and "front" are the same thing in this theme. So, we'll
 * catch any references to "username/public" and point them back to "username."
 *
 * @since 2.0.0
 * 
 * @param  string $url	The canonical URL BuddyPress wants to use.
 * @param  array $args	Contains a bool of whether to use query vars, unused.
 * @return string				The canonical URL to use.
 */
function jeremy_bp_canonical_url( $url, $args ) {
	$bp = buddypress();
	if ( bp_current_component() === 'profile' ) {
		if ( bp_current_action() === 'public' ) {
			return $bp->canonical_stack['base_url'];
		}
	}
	return $url;
}
add_filter( 'bp_get_canonical_url', 'jeremy_bp_canonical_url', 10, 2 );

/**
 * Looks up the field set for "Address Field" in the theme settings and then
 * tries to find the current user's value for that field. Good if you just need
 * the address value returned.
 *
 * @since 2.0.0
 * 
 * @param int $id			The author ID, default the current post author ID or the
 * 										current BuddyPress profile ID.
 * @return string			The address, as a string.
 */
function jeremy_bp_get_address( $id = null ) {
	if ( ! $id || is_int( $id ) ) {
		$id = bp_is_user_profile() ? bp_displayed_user_id() : get_the_author_meta( 'ID' );
	}
	
	$field = xprofile_get_field_id_from_name( esc_attr(
		get_theme_mod( 'bp_profile_address', 'Address' )
	) );
	
	return xprofile_get_field_data( $field, $id, 'comma' );
}

if ( ! function_exists( 'jeremy_bp_field' ) ) :
/**
 * Echoes XProfile field name and data, using different methods to escape
 * sanitize the data depending on the field type.
 * 
 * @TODO Removing country/state from address as a Customizer option
 * @TODO Could possibly link addresses to e.g. Google Maps
 * 
 * @since 1.0.1
 *
 * @param array $args {
 * 		@type string $field			The name of the xProfile field to show.
 * 		@type string $class			The class to wrap the field value in, default
 * 														"profile__field__value".
 * 		@type bool 	 $heading		Whether to show the field name, default false.
 * 		@type string $htag			The heading tag to wrap the field name in. Accepts
 * 														"h1" through "h6", with "h3" as default.
 * 		@type string $hclass		The class for the heading element, default
 * 														"profile__field__title".
 * 		@type string $hid				The ID for the heading element, or no ID to omit
 * 														the ID attribute (the default).
 * 		@type bool	 $aria			If the field name heading is false, whether to
 * 														include the field name as a screen reader span
 * 														element before the field value, default true.
 * 		@type bool	 $link			Whether the field value should be linked, default
 * 														true. Currently only applies to URL fields and
 * 														select box fields (which link to a directory
 * 														search for members with the field value).
 * }
 * @param int 	 $id			The user ID to get a field for. Optional, default the
 * 												currently viewed user.
 */
function jeremy_bp_field( $args = array(), $user_id = null ) {
	$args = wp_parse_args( $args, array(
		'field' 	=> null,
		'class' 	=> 'profile__field__value',
		'heading' => false,
		'htag'		=> 'h3',
		'hclass'  => 'profile__field__title',
		'hid'			=> '',
		'aria'		=> true,
		'link' 		=> true,
	) );
	
	$user_id = $user_id ?: bp_displayed_user_id();
	
	$field = xprofile_get_field( xprofile_get_field_id_from_name( $args['field'] ), $user_id );
	
	if ( $field === null || ! $user_id || empty( $field->data->value ) ) {
		return;
	}
	
	if ( $args['heading'] ) {
		$h = esc_attr( $args['htag'] );
		$h = in_array( $h, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' )) ? $h : 'h3';
		$name = esc_html( $field->name );
		$class = esc_attr( $args['hclass'] );
		$id = esc_attr( $args['hid'] );
		$id = $id && $id !== '' ? " id='{$id}'" : '';
		echo "<{$args['htag']} {$id} class='{$args['hclass']}'>{$name}</{$args['htag']}>";
	}

	$wrapper = '<p class="' . esc_attr( $args['class'] ) . '">';
	if ( $args['aria'] && ! $args['heading'] ) {
		$wrapper .= '<span class="screen-reader-text">' . esc_html( $field->name ) . ': </span>';
	}
	$wrapper2 = '</p>';
	switch ( $field->type ) {
		case 'textarea': ?>
			<div class="<?php echo esc_attr( $args['class'] ); ?>">
				<?php echo wp_kses_post( wpautop( $field->data->value ) ); ?>
			</div>
			<?php break;
		case 'url':
			// Remove protocol (e.g. "https://") from the displayed link.
			$link_display = parse_url( $field->data->value );
			$link_display = $link_display['host'];
			if ( $args['link'] ) { 
				$wrapper = $wrapper . '<a href="' . esc_url( $field->data->value ) . '">';
				$wrapper2 = '</a>' . $wrapper2;
			}
			echo $wrapper . esc_html( $link_display ) . $wrapper2;
			break;
		case 'selectbox':
			if ( $args['link'] ) {
				$field = jeremy_bp_get_search_link( esc_attr( $field->data->value ) );
			} else {
				$field = sanitize_text_field( $field->data->value );
			}
			echo $wrapper . $field . $wrapper2;
			break;
		case 'location':
			$field = sanitize_text_field( $field->data->value );
			if ( strpos( $field, ',' ) !== false ) {
				// Remove the country since these addresses are local.
				$field = substr( $field, 0, strrpos( $field, ',' ) );
				if ( strpos( $field, ',' ) !== false ) {
					// Remove the state
					$field = substr( $field, 0, strrpos( $field, ',' ) );
				}
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $wrapper . esc_html( $field ) . $wrapper2;
			break;
		default:
			echo $wrapper . wp_kses_post( $field->data->value ) . $wrapper2;
			break;
	}
}
endif;

if ( ! function_exists( 'jeremy_bp_edit_field' ) ) :
/**
 * Echoes cleaner html for generic xprofile fields on the front-end Edit
 * Profile page. If there's no match for the xprofile field type, it will
 * call the {@see edit_field_html} function BuddyPress uses by default.
 * 
 * @since 1.0.0
 *
 * @TODO Filter
 * @TODO Test field type named "location" w/o xProfile Location active
 * @TODO Checkbox field
 *
 * @see BP_XProfile_Field_Type_{type}
 * @see bp_get_form_field_attributes
 */
function jeremy_bp_edit_field() {
	$field_type = bp_get_the_profile_field_type();
	
	$field_title = bp_get_the_profile_field_name();
	$field_name = esc_attr( bp_get_the_profile_field_input_name() );
	
	$nameid = "id='{$field_name}' name='{$field_name}'";
	$described = '';
	$required = bp_get_the_profile_field_is_required() ? 'required="required" aria-required="true"' : '';
	?>
	
	<div class="profile__edit__field profile__edit__field-<?php esc_attr_e( $field_type ); ?>" role="presentation">
		<label
			class="profile__edit__field__title"
			for="<?php echo $field_name; ?>">
				<?php echo jeremy_get_svg( array(
					'img' 	 => 'social-' . sanitize_file_name( $field_title ),
					'inline' => true,
				) ); ?>
				<?php echo esc_html( $field_title ); ?>
				<?php bp_the_profile_field_required_label(); ?>
		</label>
		
		<?php if ( bp_get_the_profile_field_description() ) {
			$desc_id = esc_attr( $field_name . "-" . wp_unique_id() . "__desc" );
			$described = "aria-describedby='{$desc_id}'";
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p
				class="profile__edit__field__desc"
				id="<?php echo $desc_id; ?>">
					<?php bp_the_profile_field_description(); ?>
			</p>
		<?php } ?>
		
		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>
		
		<?php switch ( $field_type ) {
			case 'textbox' :
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<input
					<?php echo $nameid; ?>
					type="text"
					value="<?php echo bp_get_the_profile_field_edit_value(); ?>"
					<?php bp_form_field_attributes( $field_name ); ?>
					<?php echo $described; ?>
					<?php echo $required; ?> >
				<?php break;
			case 'url' :
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<input
					type="url"
					id="<?php echo $field_name; ?>"
					name="<?php echo $field_name; ?>"
					value="<?php bp_the_profile_field_edit_value(); ?>"
					<?php bp_form_field_attributes( $field_name ); ?>
					<?php echo $described; ?>
					<?php echo $required; ?> >
				<?php break;
			case 'textarea' :
				if ( ! bp_xprofile_is_richtext_enabled_for_field() ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<textarea
						<?php echo $nameid; ?>
						cols="40"
						rows="5"
						<?php echo $described; ?>
						<?php echo $required; ?> >
							<?php bp_the_profile_field_edit_value(); ?>
					</textarea>
				<?php } else {
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
				<?php break;
			case 'selectbox' : ?>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<select
					<?php echo $nameid; ?>
					<?php echo $described; ?>
					<?php echo $required; ?> >
						<?php bp_the_profile_field_options(); ?>
				</select>
				<?php break;
			case 'radio' :
				bp_the_profile_field_options();
				break;
			case 'location' :
				if ( class_exists( 'PP_Field_Type_Location' ) ) {
					$save_geocode = bp_xprofile_get_meta( bp_get_the_profile_field_id(), 'data', 'geocode' );
					$save_geocode = empty( $save_geocode ) ? '0' : $save_geocode;
					$field_id = bp_get_the_profile_field_id();
					$geocode_id = 'pp_' . $field_id . '_geocode';
					?>
					<div class="flex profile__edit__field-location__inner" role="presentation">
						<?php echo jeremy_get_svg( array(
							'img' 	 => 'social-google_maps',
							'inline' => true,
						) ); ?>
						<input
							<?php echo $nameid; ?>
							type="text"
							value="<?php echo bp_get_the_profile_field_edit_value(); ?>" placeholder="<?php
								esc_attr_e( 'Start typing an address...', 'jeremy' ); ?>"
							autocomplete="false"
							<?php echo $described; ?>
							<?php echo $required; ?> />
					</div>
					<script>
						function pp_<?php echo esc_js( $field_id ); ?>_geo_initialize() {
							var save_geocode = '<?php echo $save_geocode ?>';
							var input = '<?php echo $field_name ?>';
	
							google.maps.event.addDomListener( document.getElementById( input ), 'keydown', function( event ) {
								if ( event.keyCode === 13 ) {
									event.preventDefault();
								}
							} );
	
							if ( document.getElementById( input ).value == 'a:0:{}' ) {
								document.getElementById( input ).value = '';
							}
	
							var ppx_autocomplete = new google.maps.places.Autocomplete( ( document.getElementById( input ) ), { types: ['geocode'] } );
							ppx_autocomplete.setFields( ['geometry', 'formatted_address'] );
	
							google.maps.event.addListener( ppx_autocomplete, 'place_changed', function () {
	
								var place = ppx_autocomplete.getPlace();
	
								document.getElementById( input ).value = place.formatted_address;
	
								if ( save_geocode == '1' ) {
									var lat = place.geometry.location.lat();
									var lng = place.geometry.location.lng();
									var latlng = lat + ',' + lng;
									document.getElementById( 'pp_<?php echo $field_name; ?>_geocode' ).value = latlng;
								}
							} );
						}
						google.maps.event.addDomListener( window, 'load', pp_<?php echo esc_js( $field_id ); ?>_geo_initialize );
					</script>
					
					<?php if ( '1' == $save_geocode ) { ?>
						<input type="hidden" id="pp_<?php echo $field_name; ?>_geocode" name="pp_<?php echo $field_id; ?>_geocode" />
					<?php }
					break;
				}
			default:
				$field = bp_xprofile_create_field_type( $field_type );
				$field->edit_field_html();
			} ?>
	</div>
<?php
}
endif;

if ( ! function_exists( 'jeremy_bp_social_links' ) ) :
/**
 * Prints a list of linked svg social media icons for any specified XProfile
 * field names that have data for the displayed user.
 *
 * You can add support for more social media sites by calling the function with
 * an array of field names and including svg files in your child theme under
 * the same name if a matching icon does not already exist.
 *
 * NOTE: If a LinkedIn XProfile value is not a url, the function will append
 * the value as a slug to LinkedIn's structure for company profiles
 * (linkedin.com/company) rather than personal ones (linkedin.com/in/person).
 *
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Reduced a lot of redundant code
 *
 * @see xprofile_get_field
 * @see jeremy_get_svg
 *
 * @param array $sites  Optional array of strings for each social media
 * 											XProfile field name. Default includes 'Facebook',
 * 											'Twitter', 'Instagram', 'LinkedIn', and 'YouTube'. The
 * 											array order is used as order of icon appearance.
 */
function jeremy_bp_social_links( $sites = null ) {
	if ( ! $sites ) {
		$sites = array(
			'Email',
			'Facebook',
			'Twitter',
			'Instagram',
			'LinkedIn',
			'YouTube',
		);
	}
	
	$formatted_links = array();
	foreach( $sites as $network ) {
		$network = sanitize_key( $network );
		$id = xprofile_get_field_id_from_name( $network );
		if ( ! $id ) {
			continue;
		}

		$field = xprofile_get_field( $id, bp_displayed_user_id() );
		$field_data = $field->data->value;
		if ( empty( $field_data ) ) {
			continue;
		}

		// If null, then we probably got a username instead of a full link.
		$link = filter_var( $field_data, FILTER_VALIDATE_URL ) ? esc_url( $field_data ) : null;

		// Remove possible @ symbol from username
		$username = urlencode( str_replace( '@', '', $field_data ) );
		// example.com/username
		$link = $link ?: 'https://' . $network . '.com/' . $username;
		
		$network = ucfirst( $network );
		
		/* translators: "Follow [member] on [social network]" */
		$alt = sprintf( __( 'Follow %1$s on %2$s', 'jeremy' ), bp_get_displayed_user_fullname(), $network );
		
		$svg = 'social-' . strtolower( $network );

		switch( $network ) {
			case 'LinkedIn':
				$link = $link ?: 'https://linkedin.com/company/' . $username;
				break;
			case 'YouTube':
				$link = $link ?: 'https://youtube.com/channel/' . $username;
				break;
			case 'Website':
				/* translators: "[Member]'s website" */
				$alt = sprintf( __( "%s's website", 'jeremy' ), bp_get_displayed_user_fullname() );
				break;
			case 'Email':
				$link = filter_var( $field_data, FILTER_VALIDATE_EMAIL ) ? 'mailto:' . sanitize_email( $field_data ) : null;
				/* translators: "Email [member]" */
				$alt = sprintf( __( 'Email %s', 'jeremy' ), bp_get_displayed_user_fullname() );
				break;
		}
		
		$svg_tag = jeremy_get_svg( array(
			'img' 	 => $svg,
			'alt' 	 => $alt,
			'inline' => true
		) ) ?: esc_html( $network );
		
		if ( $link ) {
			$formatted_links[$svg] = '<a href="' . esc_url( $link ) . '">' . $svg_tag . '</a>';
		}
	}
	
	if ( ! empty( $formatted_links ) ) {
		/* translators: %s is the name of user we're showing profile links for. */
		$nav_alt = sprintf( __( 'Social media links for %s', 'jeremy' ), bp_get_displayed_user_fullname() );
		?>
		<nav class="nav-social" aria-label="<?php echo esc_attr( $nav_alt ); ?>">
			<ul class="nav__list nav__list-h">
				<?php foreach( $formatted_links as $link ) { ?><li class="nav-social__item nav-social__item-<?php echo esc_attr( $svg ); ?>">
						<?php echo $link; ?>
					</li><?php } ?>
			</ul>
		</nav>
		<?php
	}
}
endif;

if ( ! function_exists( 'jeremy_xprofile_get_categories' ) ) :
/**
 * Retrieves the categories associated with the Category field set in the
 * Customizer. This function assumes the Category field is a selectbox, and
 * returns the array of its options as objects.
 *
 * @since 1.0.0
 *
 * @see xprofile_get_field
 * @see BP_XProfile_Field->get_children()
 *
 * @return array|bool 	Array of children of the category field on success,
 * 						false on failure.
 */
function jeremy_xprofile_get_categories() {
	$field = xprofile_get_field( xprofile_get_field_id_from_name( get_theme_mod( 'bp_profile_category' ) ) );

	if ( $field == null || $field->type !== 'selectbox' ) {
		return false;
	}

	return $field->get_children();
}
endif;

/**
 * Returns the corresponding geocode field for the Address field set in theme
 * options. This requires the xProfile Location plug-in.
 *
 * @since 1.0.0
 *
 * @return string|bool The geocode meta key, or false if field does not exist.
 */
function jeremy_xprofile_get_geocode_key() {
	$field = get_theme_mod( 'bp_profile_address' );
	if ( ! xprofile_get_field_id_from_name( $field ) ) {
		return false;
	}

	// Grab the BP xProfile Location geocode field
	$id = xprofile_get_field_id_from_name( $field );
	return 'geocode_' . $id;
}

/**
 * Returns the unescaped setting of the RSS xProfile field for the current user.
 * The field searched for this purpose can be set in the Customizer.
 *
 * @since 2.0.0
 * 
 * @return string		The RSS feed URL for the current user.
 */
function jeremy_xprofile_get_rss() {
	$field = xprofile_get_field_id_from_name( get_theme_mod( 'bp_profile_rss' ) );
	if ( ! $field ) {
		return false;
	}
	
	return xprofile_get_field( $field, bp_displayed_user_id() )->data->value;
}

/**
 * Returns the permalink of the given BuddyPress component.
 *
 * @since 2.0.0
 * 
 * @param  string $component The component to get a page link for, default
 * 													 "members." Also accepts "groups" and probably some
 * 													 others!
 * @return string						 The permalink.
 */
function jeremy_bp_get_component_link( $component = 'members' ) {
	$pages = bp_get_option( 'bp-pages' );
	if ( ! array_key_exists( $component, $pages ) ) {
		return false;
	}
	return get_page_link( $pages[$component] );
}