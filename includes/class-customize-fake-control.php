<?php
/**
 * Customizer API: Display-Only Control Class
 * 
 * This Customizer control doesn't process or display user-submitted data.
 * Instead it shows the label as a heading (<h4>), which lets us use it as a
 * subdivider in Customizer sections.
 * 
 * @see WP_Customize_Control
 *
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 */

if ( ! class_exists( 'Customize_Fake_Control' ) ) :
class Customize_Fake_Control extends WP_Customize_Control {
	public $type = 'blank';
	/**
	 * Renders the control content.
	 *
	 * @since   1.0.0
	 */
	public function render_content() {
		if ( isset( $this->label ) ) {
			echo '<h4>' . esc_html( $this->label ) . '</h4>';
		}
		echo wp_kses_post( $this->description );
		echo '<hr />';
	}
}
endif;