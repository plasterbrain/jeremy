<?php
/**
 * This template is used to create the BuddyPress Uploader Backbone views.
 *
 * @since 1.0.0
 *
 * @package Jeremy
 * @subpackage Templates
 */

?>
<script type="text/html" id="tmpl-upload-window">
	<?php if ( ! _device_can_upload() ) : ?>
		<h3 class="upload-instructions"><?php esc_html_e( 'The web browser on your device cannot be used to upload files.', 'jeremy' ); ?></h3>
	<?php elseif ( is_multisite() && ! is_upload_space_available() ) : ?>
		<h3 class="upload-instructions"><?php esc_html_e( 'Upload Limit Exceeded', 'jeremy' ); ?></h3>
	<?php else : ?>
		<div id="{{data.container}}">
			<div id="{{data.drop_element}}">
				<div class="drag-drop-inside">
					<p class="drag-drop-info"><?php _ex( 'Drop a file here or', 'Drop a file here or browse for file', 'jeremy' ); ?></p>
					<p class="drag-drop-buttons"><label for="{{data.browse_button}}" class="screen-reader-text"><?php
						/* translators: accessibility text */
						esc_html_e( 'Select your File', 'jeremy' );
					?></label><input id="{{data.browse_button}}" type="button" value="<?php esc_attr_e( 'Browse for file', 'jeremy' ); ?>" class="button" /></p>
				</div>
			</div>
		</div>
	<?php endif; ?>
</script>

<script type="text/html" id="tmpl-progress-window">
	<div id="{{data.id}}">
		<div class="bp-progress">
			<div class="bp-bar"></div>
		</div>
		<div class="filename">{{data.filename}}</div>
	</div>
</script>
