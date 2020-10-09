<?php
/**
 * The template partial displaying the document header and everything up until
 * the div #content.
 * 
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0
 * - Title tag is now linked and is a <p> on non-home pages.
 * - Topbar menu removed
 */

// A "blank" value means to hide the title/tagline.
$title_color = get_header_textcolor();
if ( $title_color === 'blank' ) {
	$title_style = 'style="display:none;"';
} else if ( $title_color !== get_theme_support( 'custom-header', 'default-text-color' ) ) {
	$title_style = 'style="color:#' . esc_attr( $title_color ) . ';"';
} else {
	$title_style = '';
}

// For accessibility purposes, the site title is only H1 on the homepage.
$title_tag = ( is_front_page() ) ? 'h1' : 'p';

// Check if we should show the tagline and if one is even set.
$tagline = false;
if ( is_customize_preview() || $title_color !== 'blank' ) {
	$tagline = get_bloginfo( 'description', 'display' );
	if ( ! empty( $tagline ) ) {
		$tagline = "<p {$title_style} class='site-tagline'>{$tagline}</p>";
	}
}

$main_class = function_exists( 'bp_is_directory' ) && bp_is_directory() ? '' : 'site__content ';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'jeremy' ); ?></a>
	
	<header id="masthead" class="site__header" aria-label="<?php esc_attr_e( 'Site header'); ?>">
		<div class="header__branding flex inner" role="presentation">
			<?php the_custom_logo(); ?>
			
			<?php if ( $tagline !== false ) {?>
				<div class="site__meta" role="presentation">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo "<{$title_tag} {$title_style} class='site-title'>" ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo "</{$title_tag}>" ?>
					<?php echo wp_kses_post( $tagline ); ?>
				</div><!-- .site-meta -->
			<?php } ?>
		</div><!-- .header__branding -->
		
		<?php if ( has_nav_menu( 'mainmenu' ) ) : ?>
			<button id="nav-main__toggle" aria-controls="nav-main" class="button-ignore nav-main__toggle">
				<?php echo jeremy_get_svg( array(
					'img' 	 => 'nav_hamburger',
					'alt' 	 => __( 'Toggle main menu' ),
					'inline' => true,
				) ); ?>
			</button>
			<nav id="nav-main" class="nav-main" aria-label="<?php esc_attr_e( 'Main menu', 'jeremy' ); ?>">
				<div class="nav-main__bg" role="presentation">
				<?php
					wp_nav_menu( array(
						'menu_class' 		 => 'nav__list nav__list-h nav-collapse inner',
						'menu_id' 			 => 'site-navigation',
						'container' 		 => '',
						'depth' 				 => 2,
						'walker' 				 => new Jeremy_Walker_Main_Menu(),
						'theme_location' => 'mainmenu',
						'fallback_cb' 	 => false,
					) );
				?></div>
			</nav><!-- #site-navigation -->
		<?php endif; ?>	
	</header><!-- #masthead -->
	<main id="content" class="<?php echo esc_attr( $main_class ); ?>inner">