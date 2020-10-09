<?php
/**
 * A somewhat whitelabeled version of the default cover image uploader.
 *
 * @since 1.0.0
 *
 * @package Jeremy
 * @subpackage Templates
 */
?>

<div class="bp-cover-image"></div>
<div class="bp-cover-image-status"></div>
<div class="bp-cover-image-manage"></div>

<?php bp_attachments_get_template_part( 'uploader' ); ?>

<script id="tmpl-bp-cover-image-delete" type="text/html">
	<# if ( 'user' === data.object ) { #>
		<p><a class="button edit" id="bp-delete-cover-image" href="#"><?php esc_html_e( 'Delete My Cover Image', 'buddypress' ); ?></a></p>
	<# } else if ( 'group' === data.object ) { #>
		<p><a class="button edit" id="bp-delete-cover-image" href="#"><?php esc_html_e( 'Delete Group Cover Image', 'buddypress' ); ?></a></p>
	<# } else { #>
		<?php do_action( 'bp_attachments_cover_image_delete_template' ); ?>
	<# } #>
</script>
