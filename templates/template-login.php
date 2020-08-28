<?php
/**
 * Template Name: Login
 * Description: Shows a stylish login form. Page content appears on the left side.
 *
 * @package Jeremy
 * @since 1.0.0
 */

get_header( 'minimal' ); ?>
<body id="page" class="page-login flex">
	<main class="login-content-area flex">
		<header class="login-header flex">
			<?php the_custom_logo();
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/content', 'minimal' );
			endwhile; ?>
		</header>
		<div class="login-form">
			<?php echo '<h1>' . __( 'Login', 'jeremy' ) . '</h1>';
			// No redirect because it's handled in another function
			wp_login_form( array(
				'id-submit' => '',
				'label_username' => __( 'Username or Email', 'jeremy' ),
				'label_remember' => __( 'Remember me', 'jeremy' ),
				'label_log_in' => __( 'Sign in', 'jeremy'),
			) );
			printf( '<p class="login-forgot"><a href="%s">' . __( 'Forgot your password?', 'jeremy' ) . '</a></p>', wp_lostpassword_url() );
			?>
		</div>
	</main>

<?php get_footer( 'minimal' );