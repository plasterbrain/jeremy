<?php
/**
 * Customizer API: XProfile field control classs
 * 
 * Customizer control that displays a dropdown of every BuddyPress Xprofile field that is not
 * restricted by member type.
 * 
 * @author Plasterbrain
 * @version 1.0.0
 *
 * @see WP_Customize_Control
 */
if ( ! class_exists( 'Customize_XProfile_Control' ) && function_exists( 'buddypress' ) ) :
class Customize_XProfile_Control extends WP_Customize_Control {
	/**
     * Whether or not there are any defined XProfile fields.
     *
     * @since 1.0.0
     * @access private
     * @var bool
     */
    private $has_fields = false;
	/**
     * Limit the results to specific field types.
     *
     * @since 1.0.0
     * @access public
     * @var array
     */
    public $field_types = array();
    /**
     * Exclude certain field types from the results. If field_types is set, it will override
     * this value.
     * 
     * @since 1.0.0
     * @access public
     * @var array
 	 */
    public $exclude = array();
    public function __construct( $manager, $id, $args = array() ) {
        $options = array(
            'user_id' => false,
            'hide_empty_fields' => false,
            'fetch_field_data' => false,
            'fetch_visibility_level' => false
        );
        $this->has_fields = bp_has_profile( $options );
        if ( ! empty( $this->field_types ) ) {
            /* If the control is set to only show one field type, unset the exclude argument */
            $this->exclude = array();
        }
        parent::__construct( $manager, $id, $args );
    }
	/**
	 * Render the control's content.
	 *
	 * @since   1.0.0
	 */
	public function render_content() {
        if( ! $this->has_fields ) {
            return false;
        } ?>
		<label class="customize-control-title" for="<?php echo "customize-control-{$this->id}-select"; ?>"><?php echo esc_html( $this->label ); ?></label>
        
        <?php if ( ! empty( $this->description ) ) { ?>
            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        <?php } ?>

        <select id="<?php echo "customize-control-{$this->id}-select"; ?>" <?php $this->link(); ?>>
        <?php
        while ( bp_profile_groups() ) {
            bp_the_profile_group();
            while ( bp_profile_fields() ) {
                bp_the_profile_field();
                if ( ! empty( $this->exclude ) && in_array( bp_get_the_profile_field_type(), $this->exclude ) ) {
                    continue;
                }
                if ( empty( $this->field_types ) || in_array( bp_get_the_profile_field_type(), $this->field_types ) ) {
                    $field_name = esc_attr( bp_get_the_profile_field_name() );
                    printf('<option value="%s" %s>%s</option>',
                        $field_name,
                        selected( $this->value(), $field_name, false ),
                        $field_name
                    );
                }
            }
        } ?>
        </select>
	<?php
	}
}
endif;