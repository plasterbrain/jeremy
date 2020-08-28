<?php
/**
 * A barebones header partial to be used with minimalist page templates. Does
 * NOT include the opening body tag.
 * 
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head <?php do_action( 'add_head_attributes' ); ?>>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>