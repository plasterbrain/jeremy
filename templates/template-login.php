<?php
/**
 * Template Name: Custom Login Page
 * 
 * A styled alternative to the default login page. The built-in login page is
 * left untouched by the theme due to the abundance of "login designer" plug-ins
 * that may conflict with such changes.
 *
 * @TODO Have theme handle entire WordPress login flow?
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0 - Content is now on the right side; major style changes.
 */

// Counter the margin added to accommodate the admin bar.
$adminbar_margin = 'style="margin-top: -32px !important;"';
if ( function_exists( 'bp_get_option' ) ) {
	if ( bp_get_option( 'hide-loggedout-adminbar' ) ) {
		$adminbar_margin = '';
	}
}

$logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
$logo = $logo ? ' login__title-logo" style="background-image: url(\'' . esc_url( $logo[0] ) . '\');"' : '"';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<body <?php body_class( 'flex flex-col' ); ?> <?php echo $adminbar_margin; ?>>
	<?php wp_body_open(); ?>
	
	<main id="content" class="login__main flex inner">
		<div class="login__section login__section-form" role="presentation">
			<?php the_title( '<h1 class="login__title' . $logo . '>', '</h1>' ); ?>
			
			<?php
			wp_login_form( array(
				'id-submit' => '',
				'label_username' => esc_html__( 'Email', 'jeremy' ),
				'label_remember' => esc_html__( 'Remember me', 'jeremy' ),
				'label_log_in' => esc_html__( 'Sign in', 'jeremy' ),
			) );
			?>
		</div>
		
		<?php while ( have_posts() ) : the_post();
			$image_style = esc_url( get_the_post_thumbnail_url() );
			$image_style = $image_style !== '' ? " style='background-image: url(\"{$image_style}\");'" : '';
			if ( get_the_content() !== '' || $image_style !== '' ) { ?>
				<div class="login__section login__section-content flex color1-bg"<?php echo $image_style; ?> role="presentation">
					<?php the_content(); ?>
				</div>
			<?php } ?>
		<?php endwhile; ?>
	</main>
			
	<?php get_template_part( 'partials/footer', 'mini' ); ?>