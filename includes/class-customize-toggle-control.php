<?php
/**
 * Customizer API: Toggle Control Class
 * 
 * This Customizer control looks like an on/off switch.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 *
 * @author Per Søderlind
 * @link https://github.com/soderlind/class-customizer-toggle-control
 * @version 1.2.0
 * @license GPL-2+
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
	 * Renders the control's content.
	 *
	 * @author soderlind
	 * @version 1.2.0
	 */
	public function render_content() {
		?>
		<label>
			<div class="customize-control-toggle">
				<div class="customize-control__row">
					<span class="customize-control-title" style="vertical-align: middle;">
						<?php echo esc_html( $this->label ); ?>
					</span>
					<input id="cb<?php echo $this->instance_number ?>" type="checkbox" class="tgl tgl-skewed" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
					<label data-tg-off="<?php esc_attr_e( 'OFF', 'jeremy' ); ?>" data-tg-on="<?php esc_attr_e( 'ON', 'jeremy' ); ?>" for="cb<?php echo $this->instance_number ?>" class="tgl-btn"></label>
				</div>
			</div>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description">
					<?php echo $this->description; ?>
				</span>
			<?php endif; ?>
		</label>
		<?php
	}
}
endif;