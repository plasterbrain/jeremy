<?php
/**
 * Menu API: Custom Nav Menu Walker
 * 
 * The core nav menu walker modified to omit excess classes, IDs and create
 * HTML5 output compatible with the theme's responsive menu features.
 * {@see Walker_Nav_Menu} for descriptions of the class methods.
 *
 * @package Jeremy
 * @subpackage Includes
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Currently viewed page now has an aria-current attribute.
 */

if ( ! class_exists( 'Jeremy_Walker_Footer_Menu' ) ) :
class Jeremy_Walker_Footer_Menu extends Walker_Nav_Menu {
	private $submenu_count = 0;
   
  /**
   * Starts the list before the elements are added.
   *
   * @param string   $output Used to append additional content.
   * @param int      $depth  Depth of menu item.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {		
		// Wrap it in a div so we can use the ".inner" styles
		$output .= "<ul>";
	}
	 
  /**
   * Ends the list of after the elements are added.
   * 
   * @param string   $output Used to append additional content.
   * @param int      $depth  Depth of menu item.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '</ul>';
	}
	
	/**
   * Starts the element output.
   *
   * @param string   $output Used to append additional content.
   * @param WP_Post  $item   Menu item data object.
   * @param int      $depth  Depth of menu item.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   * @param int      $id     Current item ID.
   */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
		
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class = array( 'nav__item' );
		
		$id = '';
		
		$class = ' class="' . esc_attr( implode( ' ', $class ) ) . '"';
		$id = empty( $id ) ? '' : ' id="' . esc_attr( $id ) . '"';

		$atts = array();
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= " {$attr}='{$value}'";
			}
		}
		
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth ); 

		$social_links = array(
			'facebook' 	=> 'facebook.com',
			'twitter' 	=> 'twitter.com',
			'instagram' => 'instagram.com',
		);
		
		$rss_links = array(
			'/feed',
			'?feed='
		);
		
		if ( ! empty( $item->url ) ) {
			foreach ( $social_links as $network=>$link ) {
				if ( strpos( $item->url, $link ) !== false ) {
					$svg = jeremy_get_svg( array(
						'img' 	 => 'footer__social-' . $network,
						'alt' 	 => esc_attr( $title ),
						'inline' => true,
					) );
					if ( $svg ) {
						$title = $svg;
					}
					break;
				}
			}
			
			if ( empty( $svg ) ) {
				foreach ( $rss_links as $link ) {
					if ( strpos( $item->url, $link ) !== false ) {
						$svg = jeremy_get_svg( array(
							'img' 	 => 'social-rss',
							'alt' 	 => esc_attr( $title ),
							'inline' => true,
						) );
						if ( $svg ) {
							$title = $svg;
						}
						break;
					}
				}
			}
		}

		$item_output = "<li{$class}><a {$attributes}>{$title}</a>";
		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	
  /**
   * Ends the element output, if needed.
   * 
   * @param string   $output Used to append additional content.
   * @param WP_Post  $item   Page data object. Not used.
   * @param int      $depth  Depth of page. Not Used.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= '</li>';
	}
}
endif;