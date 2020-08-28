<?php
/**
 * Hero section
 *
 * @package Jeremy
 */
$hero_bg = get_theme_mod( 'hero_bg_img' ) ? "background-image:url('" . get_theme_mod( 'hero_bg_img' ) . "');" : '';
$hero_bg .= 'background-color:' . get_theme_mod( 'hero_bg_color', '#c2e164' ) . ';';
$hero_parallax = get_theme_mod( 'hero_bg_parallax', true ) && get_theme_mod( 'hero_bg_img' ) ? 'background-attachment:fixed;background-position:center;background-repeat:no-repeat;background-size:cover;' : 'background-size:contain;background-position:center bottom;';
$hero_toggle = get_theme_mod( 'show_hero', true ) ? '' : 'display:none;';
?>
<div class="hero hero-home" style="<?php echo $hero_toggle; ?><?php echo $hero_bg . $hero_parallax; ?>;">
	<?php
	if ( get_theme_mod( 'hero_h1') || is_customize_preview() ):
		echo '<h1 style="color:' . get_theme_mod( 'hero_text_color', '#141f36' ) . ';">' . get_theme_mod( 'hero_h1' ) . '</h1>';
	endif;
	if ( get_theme_mod( 'hero_h2') || is_customize_preview() ):
		echo '<h2 style="color:' . get_theme_mod( 'hero_text_color', '#141f36' ) . ';">' . get_theme_mod( 'hero_h2' ) . '</h2>';
	endif;
	if ( get_theme_mod( 'hero_button_text') ): ?>
		<a class="button hero-button"
			 style="background:<?php echo get_theme_mod( 'hero_button_color', '#e91f53' );?>;
					color:<?php echo get_theme_mod( 'hero_button_text_color', '#ffffff' );?>;"
			href="<?php echo get_theme_mod( 'hero_button_link' ); ?>">
			<?php echo get_theme_mod( 'hero_button_text' ); ?>
		</a>
	<?php
	endif;
	?>
</div>