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

if ( ! class_exists( 'Jeremy_Walker_Main_Menu' ) ) :
class Jeremy_Walker_Main_Menu extends Walker_Nav_Menu {
	private $submenu_count = 0;
   
  /**
   * Starts the list before the elements are added.
   *
   * @param string   $output Used to append additional content.
   * @param int      $depth  Depth of menu item.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$class_names = join( ' ',
		apply_filters( 'nav_menu_submenu_css_class', array( 'nav-main__submenu__list nav__list nav__list-h inner' ), $args, $depth ) );
		$class_names = $class_names === '' ? '' : ' class="' . esc_attr( $class_names ) . '"';
		
		$id = 'id="sub-menu-' . esc_attr( $this->submenu_count ) . '" ';
		
		// Wrap it in a div so we can use the ".inner" styles
		$output .= "<div {$id} class='nav-main__submenu' role='presentation'><ul {$class_names} aria-hidden='true'>";
	}
	 
  /**
   * Ends the list of after the elements are added.
   * 
   * @param string   $output Used to append additional content.
   * @param int      $depth  Depth of menu item.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '</ul></div>';
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
		
		$aria = '';
		
		$id = '';
		
		if ( in_array( 'current-menu-item', $classes ) ) {
			$class[] = 'nav__item-active';
		} else if ( in_array( 'current-menu-parent', $classes ) ) {
			$class[] = 'nav__item-active';
			$class[] = 'nav__item-parent';
		}
		if( in_array( 'menu-item-has-children', $classes ) ) {
			$this->submenu_count ++;
			$id .= 'parent-menu-' . $this->submenu_count;
			
			$class[] = 'nav__item-parent';
			
			$aria = ' aria-haspopup="true" aria-expanded="false" ';
		} else if( in_array( 'sub-menu', $classes ) ) {
			$class[] = 'nav-main__submenu';
		}
		
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

		if ( in_array('menu-item-has-children', $classes) ) {
			/* translators: %s is the name of the sub-menu this button opens. */
			$aria .= ' aria-label="' . sprintf( esc_attr__( 'Open %s sub-menu', 'jeremy' ), $title ) . '"';
			$svg = jeremy_get_svg( array(
				'img' 	 => 'nav-toggle',
				'inline' => true,
			) );
			$item_output = "<li{$class}><button class='button-ignore' {$id}{$aria}><span class='nav__item-parent__text'>{$title}</span>{$svg}</button>";
		} else {
			if ( $item->current ) {
				// Make the current page a span rather than a redundant link.
				$item_output = "<li{$class}><span aria-current='page'>{$title}</span></li>";
			} else {
				$item_output = "<li{$class}><a {$attributes}>{$title}</a>";
			}
		}
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