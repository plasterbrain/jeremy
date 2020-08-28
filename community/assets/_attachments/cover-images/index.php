<?php
/**
 * A somewhat whitelabeled version of the default cover image uploader.
 *
 * @since 1.0.0
 *
 * @package Jeremy
 * @subpackage Templates
 */

bp_attachments_get_template_part( 'uploader' ); ?>

<div id="profile-upload-status"></div>
<div id="profile-upload-cover"></div>
<div id="profile-upload-manage">
	<script id="tmpl-profile-cover-delete" type="text/html">
		<# if ( 'user' === data.object ) { #>
			<a class="button" id="delete-cover-image" href="#"><?php esc_html_e( 'Delete Cover Image', 'jeremy' ); ?></a>
		<# } else if ( 'group' === data.object ) { #>
			<a class="button" id="delete-cover-image" href="#"><?php esc_html_e( 'Delete Group Cover Image', 'jeremy' ); ?></a>
		<# } else { #>
			<?php do_action( 'bp_attachments_cover_image_delete_template' ); ?>
		<# } #>
	</script>
</div>

<?php do_action( 'bp_attachments_cover_image_main_template' );
