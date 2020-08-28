<?php
/**
 * Menu API: nav menu walker
 * 
 * The core nav menu walker modified to omit excess classes, IDs and create
 * HTML5 output compatible with the theme's responsive menu features.
 * {@see Walker_Nav_Menu} for descriptions of the class methods.
 *
 * @package Jeremy
 * @subpackage Walkers
 * @since 1.0.0
 */
 class Jeremy_Walker_Nav_Menu extends Walker_Nav_Menu {
    private $submenu_count = 0;
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $classes = array( 'sub-menu' );
        $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        $id = 'id="sub-menu-' . $this->submenu_count . '" ';
        $output .= "<ul $id $class_names>";
    }
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= '</ul>';
    }
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
        $class = '';
        $aria = '';
        $id = '';
        if( in_array('current-menu-item', $classes) ) {
            $class .= 'active ';
        } else if( in_array('current-menu-parent', $classes) ) {
            $class .= 'active-parent ';
        }
        if( in_array('menu-item-has-children', $classes) ) {
            $this->submenu_count ++;
            $class .= 'parent';
            $id .= 'parent-menu-' . $this->submenu_count;
            $aria = ' aria-haspopup="true" aria-expanded="false" ';
        } else if( in_array('sub-menu', $classes) ) {
            $class .= 'sub-menu';
        }
        $class = empty( $class ) ? '' : ' class="' . $class . '"';
        $id = empty( $id ) ? '' : ' id="' . $id . '"';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
        
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth ); 

        if ( in_array('menu-item-has-children', $classes) ) {
            $aria .= sprintf( ' aria-label="' . __( 'Open %s sub-menu', 'jeremy' ) . '"', $title );
            $item_output = '<li><button' . $id . $class . $aria . '>' . $title . jeremy_get_svg(array('img'=>'toggle')) . '</button>';
        } else {
            $item_output = '<li' . $class . '><a href="' . $atts['href'] . '">' . $title . '</a>';
        }
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= '</li>';
    }
}