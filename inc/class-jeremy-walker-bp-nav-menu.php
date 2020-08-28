<?php
/**
 * Budypress nav menu walker
 * 
 * The BuddyPress nav menu walker modified to remove unnecessary IDs.
 * {@see BP_Walker_Nav_Menu} for descriptions of the class properties and methods.
 *
 * @package Jeremy
 * @subpackage Plugins
 * @since 1.0.0
 */
defined( 'ABSPATH' ) || exit;
class Jeremy_Walker_BP_Nav_Menu extends Walker_Nav_Menu {
	var $db_fields = array( 'id' => 'css_id', 'parent' => 'parent' );
	var $tree_type = array();

	public function walk( $elements, $max_depth, ...$args ) {
		$args   = array_slice( func_get_args(), 2 );
		$output = '';
		if ( $max_depth < -1 )
			return $output;

		if ( empty( $elements ) )
			return $output;
		$parent_field = $this->db_fields['parent'];
		if ( -1 == $max_depth ) {

			$empty_array = array();
			foreach ( $elements as $e )
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );

			return $output;
		}

		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e ) {
			if ( 0 === $e->$parent_field )
				$top_level_elements[] = $e;
			else
				$children_elements[$e->$parent_field][] = $e;
		}
		if ( empty( $top_level_elements ) ) {

			$first              = array_slice( $elements, 0, 1 );
			$root               = $first[0];
			$top_level_elements = array();
			$children_elements  = array();

			foreach ( $elements as $e ) {
				if ( $root->$parent_field == $e->$parent_field )
					$top_level_elements[] = $e;
				else
					$children_elements[$e->$parent_field][] = $e;
			}
		}
		foreach ( $top_level_elements as $e )
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
		if ( ( $max_depth == 0 ) && count( $children_elements ) > 0 ) {
			$empty_array = array();

			foreach ( $children_elements as $orphans )
				foreach ( $orphans as $op )
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
		 }
		 return $output;
	}
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = $depth ? str_repeat( "\t", $depth ) : '';

		$class_names = join( ' ', apply_filters( 'bp_nav_menu_css_class', array_filter( $item->class ), $item, $args ) );
		$class_names = ! empty( $class_names ) ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= $indent . '<li' . $class_names . '>';
		$attributes = ! empty( $item->link ) ? ' href="' . esc_url( $item->link ) . '"' : '';

		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->name, 0 ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		$output .= apply_filters( 'bp_walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
