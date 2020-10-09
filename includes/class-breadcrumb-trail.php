<?php
/**
 * Breadcrumb Trail
 *
 * @package Jeremy
 * @subpackage Includes
 * @since 2.0.0
 */

if ( ! function_exists( 'jeremy_breadcrumbs' ) ) :
function jeremy_breadcrumbs() {
	$crumbs = new Jeremy_Breadcrumbs();
	$crumbs->accrue_items();
	$crumbs->render();
}
endif;

if ( ! class_exists( 'Jeremy_Breadcrumbs') ) :
class Jeremy_Breadcrumbs {
	public $items;
	public $query;
	public $post_page = null;
	public $buddypress;
	public $author_archive_posttypes;
	
	public function __construct() {
		$this->query = get_queried_object();
		
		// Name of the blog page
		if ( get_option( 'show_on_front' ) === 'page' ) {
			$this->post_page = get_the_title( get_option( 'page_for_posts' ) );
		}
		
		$this->buddypress = function_exists( 'buddypress' );
		
		$this->author_archive_posttypes = array(
			'jeremy_job',
			'jeremy_deal',
		);
	}
	
	public function accrue_items() {
		if ( ! is_front_page() ) {
			$this->items[__( 'Home', 'jeremy' )] = get_home_url();
		}
		switch ( true ) {
			case is_single():
				$this->post_items();
				break;
			case is_page():
				$this->page_items();
				break;
			case is_author():
			case is_archive():
				$this->archive_items();
				break;
			case is_search():
				$this->search_items();
				break;
			default:
				//TODO what's the title called
				$this->items[$this->query->post_title] = null;
		}
	}
	
	public function render() {
		if ( wp_is_mobile() ) {
			if ( count( $this->items ) > 1 ) {
				$items = array_keys( $this->items );
				$prev_page = esc_html( $items[count( $items ) - 2] );
				$prev_link = esc_url( $this->items[$prev_page] );
				$aria = esc_attr_x( 'Breadcrumbs: Parent page', 'Breadcrumbs on mobile', 'jeremy' );
				$output = "<nav id='nav-breadcrumbs-mobile' class='nav-breadcrumbs-mobile' aria-label='{$aria}'><a href='{$prev_link}' aria-describedby='nav-breadcrumbs-mobile'>{$prev_page}</a></nav>";
			} else {
				// ¯\_(ツ)_/¯
				$output = '';
			}
		} else {
			$output = '<nav class="nav-breadcrumbs" aria-label="' . esc_attr( 'Breadcrumbs', 'jeremy' ) . '">';
			$output .= '<ol class="nav__list nav__list-h">';
			
			$i = 1;
			foreach( $this->items as $label => $link ) {
				$esc_label = esc_html( $label );
				if ( $i === count( $this->items ) ) {
					$output .= '<li aria-current="page">' . $esc_label . '</li>';
				} else {
					$label = $esc_label;
					if ( $i === 1 ) {
						$label = jeremy_get_svg( array(
							'img' 	 => 'nav-home',
							'alt' 	 => esc_attr__( 'Home', 'jeremy' ),
							'inline' => true,
						) );
					}
					
					if ( $link ) {
						$esc_link = esc_url( $link );
						$label = "<a href='{$esc_link}'>{$label}</a>";
					}
					
					$class = 'breadcrumb-' . sanitize_title( strtolower( $esc_label ) );
					
					$output .= "<li class='{$class}'>{$label}</li>";
					$i++;
				}
			}
			$output .= '</nav>';
		}
		echo $output;
	}
	
	private function post_items() {
		$post_type = $this->query->post_type;
		$post_type_object = get_post_type_object( $post_type );
		$bread_archive_link = get_post_type_archive_link( $post_type );
		
		if ( $post_type === 'post' ) {
			// Single blog post
			$archive_name = $this->post_page;
			$taxonomy = 'category';
		} else {
			$archive_name = $post_type_object->labels->name;
			$taxonomy = get_object_taxonomies( $post_type )? get_object_taxonomies( $post_type )[0] : null;
		}
		
		$this->items[$archive_name] = get_post_type_archive_link( $post_type );
		
		$term_obj = $taxonomy ? get_the_terms( $this->query->ID, $taxonomy ): null;
		if ( $term_obj ) {
			$term_obj = $term_obj[0];
			
			if ( $taxonomy !== 'event-venue' ) {
				$this->term_items( $term_obj, $taxonomy );
			} else {
				$venue_link = jeremy_get_the_venue_link( $term_obj->name, true );
				if ( $venue_link ) {
					$this->items[$term_obj->name] = $venue_link;
        }
			}
		}
		
		if ( in_array( $post_type, $this->author_archive_posttypes ) ) {
			// e.g. Joe Cool > Events > Christmas Party
			$this->items[get_the_author_meta( 'display_name', $this->query->post_author )] = add_query_arg( 'post_type', $post_type, get_author_posts_url( $this->query->post_author ) );
		}
    
		$this->items[$this->query->post_title] = null;
	}
	
	private function archive_items() {
		// Post type archive link, e.g. "Movies"
		$post_type = get_query_var( 'post_type' ) ?: 'post';
		$pt_obj = get_post_type_object( $post_type );
		if ( is_a( $pt_obj, 'WP_Post_Type' ) ) {
			$this->items[get_post_type_object( $post_type )->label] = get_post_type_archive_link( $post_type );
		}
		
		if ( is_category() ) {
			$taxonomy = 'category'; // ? Can we hard-code these?
			$term = get_query_var( 'category_name' );
		} elseif ( is_tag() ) {
			$taxonomy = 'tag';
			$term = get_query_var( 'tag' );
		} else {
			$taxonomy = get_query_var( 'taxonomy' );
			$term = get_query_var( 'term' );
		}
		$term_obj = null;
		if ( $term ) {
			$term_obj = get_term_by( 'name', $term, $taxonomy );
			if ( ! $term_obj ) {
				$term_obj = get_term_by( 'slug', $term, $taxonomy );
			}
		}
		
		if ( is_a( $term_obj, 'WP_Term' ) ) {
			$this->term_items( $term_obj, $taxonomy );
			
			$tag_item_text = is_taxonomy_hierarchical( $taxonomy ) ? $term_obj->name : sprintf(__( 'Tagged "%s"', 'jeremy' ), $term_obj->name );
			
			$this->items[$tag_item_text] = get_term_link($term_obj->slug, $taxonomy);
		}
		
		
		if ( is_author() ) {
			$author_id = get_query_var( 'author' );
			$this->items[get_the_author_meta( 'display_name', $author_id )] = jeremy_get_author_link( $author_id );
		}
	}
	
	/**
	 * Adds hierarchical taxonomy terms to the breadcrumb trail.
	 * 
	 * @param  WP_Term $term_obj	Taxonomy object.
	 * @param  string $taxonomy		Taxonomy slug.
	 */
	private function term_items( $term_obj, $taxonomy ) {
		$parents = get_ancestors( $term_obj->term_id, $taxonomy, 'taxonomy' );
		
		array_unshift( $parents, $term_obj->term_id );

		foreach ( array_reverse( $parents ) as $id ) {
			$parent = get_term( $id, $taxonomy );
			$this->items[$parent->name] = get_term_link( $id, $taxonomy );
		}
	}
	
	private function page_items() {
		if( $this->query->post_parent ) {
			$ancestors = get_post_ancestors( $this->query->ID );
			foreach ( $ancestors as $ancestor ) {
				$this->items[get_the_title( $ancestor )] = get_permalink( $ancestor );
			}
		}
		
		if ( $this->buddypress && bp_is_user() ) {
			$this->bp_members_item(); // Members >
			
			$cat_field = get_theme_mod( 'bp_profile_category', false );
			if ( $cat_field ) {
				$cat_value = xprofile_get_field( xprofile_get_field_id_from_name( $cat_field ), bp_displayed_user_id() );
				
				if ( $cat_value && ! empty( $cat_value->data->value ) ) {
					$this->items[$cat_value->data->value] = add_query_arg( jeremy_bp_cat_query(), sanitize_title( $cat_value->data->value ), bp_get_members_directory_permalink() );
				}
			}
			
			if ( jeremy_bp_is_public_profile() ) {
				$this->items[get_the_title()] = null; // > Username
			} else {
				// BuddyPress profile sub-pages like Notifications
				$this->items[get_the_title()] = bp_displayed_user_domain();
				
				$bp_action = bp_current_action();
				/**
				 * Filters the overriding labels for BuddyPress actions in the
				 * breadcrumbs.
				 *
				 * @since 2.0.0
				 * 
				 * @param array $labels		An array of BuddyPress action slugs (e.g.
				 * 												"public" for "edit profile" pages) mapped to
				 * 												the preferred display name in the breadcrumbs
				 * 												(e.g. "Edit Profile").
				 * @return array 					The filtered label array.
				 */
				$bp_action_labels = apply_filters( 'jeremy_bp_action_labels', array(
					'change-cover-image' => __( 'Upload Cover Image', 'jeremy' ),
					'change-avatar' 		 => __( 'Upload Profile Image', 'jeremy' ),
				) );
				$bp_action = array_key_exists( $bp_action, $bp_action_labels ) ? $bp_action_labels[$bp_action] : $bp_action;
				
				$this->items[ucfirst( $bp_action )] = null;
			}
			return;
		}
		
		$this->items[$this->query->post_title] = null;
	}
	
	private function search_items() {
		// get_search_link returns the url with the current query appended.
		$this->items[__( 'Search', 'jeremy' )] =  home_url( '?s' );
		
		$search = get_search_query();
		if ( $search !== '' ) {
			/* translators: %s is the search term */
			$this->items[sprintf( _x( 'Results for "%s"', 'Breadcrumb item', 'jeremy' ), $search )] = null;
		}
	}
	
	private function bp_members_item() {
		$this->items[__( 'Members', 'jeremy' )] = bp_get_members_directory_permalink();
	}
}
endif;