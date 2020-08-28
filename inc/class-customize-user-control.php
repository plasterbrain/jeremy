<?php
/**
 * Customizer API: user dropdown control class
 * 
 * Customizer control that displays a dropdown of all users.
 * 
 * @package Jeremy
 * @subpackage Customizer
 * @since 1.0.0
 * 
 * @author Paul Underwood
 * @see WP_Customize_Control
 */
if ( ! class_exists( 'Customize_User_Control' ) ) :
class Customize_User_Control extends WP_Customize_Control {
    private $users = false;
    public function __construct( $manager, $id, $args = array(), $options = array() ) {
        $this->users = get_users( $options );
        parent::__construct( $manager, $id, $args );
    }
	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @since   1.0.0
	 */
	public function render_content() {
        if( empty( $this->users ) ) {
            return false;
        }
		?>
		<label>
			<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
			<select <?php $this->link(); ?>>
			<?php foreach( $this->users as $user ) {
                printf('<option value="%s" %s>%s</option>',
					$user->data->ID,
					selected($this->value(), $user->data->ID, false),
					$user->data->display_name
				);
            } ?>
			</select>
		</label>
		<?php
	}
}
endif;