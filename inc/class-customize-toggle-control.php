<?php
/**
 * Customizer API: toggle control class
 * 
 * Customizer control that displays an on/off toggle to replace single checkbox controls.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 *
 * @author Per Søderlind
 * @see WP_Customize_Control
 */
if ( ! class_exists( 'Customize_Toggle_Control' ) ) :
class Customize_Toggle_Control extends WP_Customize_Control {
    /**
     * The type of customize control being rendered.
     *
     * @since  1.0.0
     * @access public
     * @var    string
     */
	public $type = 'toggle';
	/**
	 * Enqueue scripts/styles.
	 * 
	 * @since 3.4.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'customize-control-toggle-css', get_template_directory_uri() . '/assets/css/customize-control-toggle.css', array(), false );
	}
	/**
	 * Render the control's content.
	 *
	 * @author soderlind
	 * @version 1.2.0
	 */
	public function render_content() {
		?>
		<label>
			<div style="display:flex;flex-direction:row;justify-content:flex-start;">
				<span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php echo esc_html( $this->label ); ?></span>
				<input id="cb<?php echo $this->instance_number ?>" type="checkbox" class="tgl" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
				<label for="cb<?php echo $this->instance_number ?>" class="tgl-btn"></label>
			</div>
			<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>
		<?php
	}
}
endif;