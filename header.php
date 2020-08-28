<?php
/**
 * The template partial displaying the document header and everything up until the
 * div #content.
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
<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'jeremy' ); ?></a>
	<?php if ( has_nav_menu( 'topbar' ) ) : ?>
	<nav class="topbar" role="navigation" aria-labelledby="topbar_label">
		<h4 id="topbar_label" class="screen-reader-text"><?php _e( 'Top Bar Menu', 'jeremy' ); ?></h4>
		<?php
			wp_nav_menu( array(
				'menu_class' => 'menu',
				'menu_id' => '',
				'container' => '',
				'depth' => 0,
				'theme_location' => 'topbar',
				'fallback_cb' => false,
			) );
		?>
	</nav>
	<?php endif; ?>
	<header id="masthead" class="masthead flex">
		<div class="header-branding flex">
			<?php
			the_custom_logo();
			// Show the site title/description
			$titletag_color = get_header_textcolor();
			$titletag_css = "style=";
			if ( $titletag_color === 'blank' ) {
				$titletag_css .= "display:none;";
			} else if ( $titletag_color !== get_theme_support( 'custom-header', 'default-text-color' ) ) {
				// Add CSS if the user has chosen a non-default color.
				$titletag_css .= "color:'#" . esc_attr( $titletag_color ) . "';";
			} else {
				$titletag_css = '';
			}
			if ( is_customize_preview() || $titletag_color !== 'blank'  ) {
				$description = get_bloginfo( 'description', 'display' );
				if ( is_customize_preview() || ! empty( $description ) ) $description = "<h2 {$titletag_css} class='site-tagline'>{$description}</h2>";
				echo '
				<div class="site-meta">
					<h1 ' . $titletag_css . ' class="site-title">' . get_bloginfo( 'name' ) . '</h1>' .
					$description .
				'</div>';
			} ?>
		</div><!-- .header-branding -->
		<?php if ( is_active_sidebar( 'sidebar-header' ) ) : ?>
		<div class="header-widget">
			<section>
				<?php dynamic_sidebar( 'sidebar-header' ); ?>
			</section>
		</div><!-- .header-widget -->
		<?php endif; ?>
	</header><!-- #masthead -->

	<?php if ( has_nav_menu( 'mainmenu' ) ) : ?>
	<nav class="main-nav" role="navigation" aria-labelledby="mainnav_label">
		<h4 id="mainnav_label" class="screen-reader-text"><?php _e( 'Main menu', 'jeremy' ); ?></h4>
		<?php
			wp_nav_menu( array(
				'menu_class' => 'menu nav-collapse',
				'menu_id' => 'site-navigation',
				'container' => '',
				'depth' => 2,
				'walker' => new Jeremy_Walker_Nav_Menu(),
				'theme_location' => 'mainmenu',
				'fallback_cb' => false,
			) );
		?>
	</nav><!-- #site-navigation -->
	<?php endif; ?>

	<?php if ( is_page_template( 'templates/template-directory.php' ) ) {
		get_template_part( 'community/members/map');
	} elseif ( is_front_page() ) {
		if ( get_theme_mod( 'show_hero', true ) || is_customize_preview() ) {
			get_template_part( 'template-parts/header-hero' );
		}
	} ?>
	
	<div id="content" class="site-content">