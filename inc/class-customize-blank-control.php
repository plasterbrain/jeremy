<?php
/**
 * Customizer API: blank control class
 * 
 * A blank Customizer control that allows arbitrary HTML output.
 *
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 * 
 * @see WP_Customize_Control
 */
if ( ! class_exists( 'Customize_Blank_Control' ) ) :
class Customize_Blank_Control extends WP_Customize_Control {
	public $type = 'blank';
	/**
	 * Render the control content.
	 *
	 * @since   1.0.0
	 */
	public function render_content() {
		if ( isset( $this->label ) ) {
			echo '<h4>' . esc_html( $this->label ) . '</h4>';
		}
		echo $this->description;
		echo '<hr />';
	}
}
endif;