<?php
/**
 * Template Name: Minimalist
 * 
 * A skinny page with no header or footer, ideal for use with modals, forms, and
 * legal information.
 *
 * @package Jeremy
 * @subpackage Templates
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0
 * 	- Fixed h1 class typo :)
 * 	- Inlined header/footer template partials.
 */

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'inner flex' ); ?>>
	<?php wp_body_open(); ?>
	<header class="screen-reader-text">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php bloginfo( 'name' ); ?>
		</a>
	</header>
	<main class="page-blank__content" id="content">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php the_title(
					'<h1 class="entry__title entry-blank__title">', '</h1>' ); ?>

			<div class="entry__content" role="presentation">
				<?php the_content(); ?>
				
				<?php wp_link_pages(); ?>
			</div><!-- .entry__content -->
		<?php endwhile; ?>
	</main>
	<?php get_template_part( 'partials/footer', 'mini' ); ?>