<?php
/**
 * BuddyPress email template.
 *
 * @since 1.0.0
 *
 * @package Jeremy
 * @subpackage Templates
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings = bp_email_get_appearance_settings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
	<link href="https://fonts.googleapis.com/css?family=Quicksand:400,500" rel="stylesheet" type="text/css">
	<!-- CSS Reset -->
	<style type="text/css">
		html,
		body {
			font-family: 'Quicksand', sans-serif;
		}

		/* Stop email clients resizing small text. */
		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}
		/* Force Outlook.com to display emails full width. */
		.ExternalClass {
			width: 100%;
		}
		/* Center email on Android 4.4 */
		div[style*="margin: 16px 0"] {
			margin: 0 !important;
		}
		/* Stop Outlook from adding extra spacing to tables. */
		table,
		td {
			mso-table-lspace: 0pt !important;
			mso-table-rspace: 0pt !important;
		}
		/* Fis webkit padding issue. Fix for Yahoo mail table alignment bug.*/
		table {
			border-spacing: 0 !important;
			border-collapse: collapse !important;
			table-layout: fixed !important;
			Margin: 0 auto !important;
		}
		/* Apply table-layout to the first 2 tables then removes for anything nested deeper. */
		table table table {
			table-layout: auto;
		}
		/* Use a better rendering method when resizing images in IE. */
		/* Ensure content body images don't exceed template width. */
		img {
			-ms-interpolation-mode:bicubic;
			height: auto;
			max-width: 100%;
		}

		/* Override styles added when Yahoo auto-senses a link. */
		.yshortcuts a {
			border-bottom: none !important;
		}
		/* A work-around for iOS meddling in triggered links. */
		a[x-apple-data-detectors] {
			color: inherit !important;
			text-decoration: underline !important;
		}
	</style>
</head>
<body class="email_bg" width="100%" height="100%" bgcolor="<?php echo esc_attr( $settings['email_bg'] ); ?>" style="Margin: 0;">
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="<?php echo esc_attr( $settings['email_bg'] ); ?>" style="border-collapse:collapse;" class="email_bg"><tr><td valign="top">
	<center style="width: 100%;">
		<!-- Visually Hidden Preheader Text : BEGIN -->
		<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
			{{email.preheader}}
		</div>
		<!-- Visually Hidden Preheader Text : END -->
		<div style="max-width: 600px;">
			<!--[if (gte mso 9)|(IE)]>
			<table cellspacing="0" cellpadding="0" border="0" width="600" align="center">
			<tr>
			<td>
			<![endif]-->
			<!-- Email Header : BEGIN -->
			<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px; border-top: 7px solid <?php echo esc_attr( $settings['highlight_color'] ); ?>" bgcolor="<?php echo esc_attr( $settings['header_bg'] ); ?>" class="header_bg">
				<tr>
					<td style="text-align: center; padding: 15px 0; mso-height-rule: exactly; font-weight: bold; color: <?php echo esc_attr( $settings['header_text_color'] ); ?>; font-size: <?php echo esc_attr( $settings['header_text_size'] . 'px' ); ?>" class="header_text_color header_text_size">
						<?php
						/**
						 * Fires before the display of the email template header.
						 *
						 * @since 2.5.0
						 */
						do_action( 'bp_before_email_header' );

						$custom_logo_id = get_theme_mod( 'custom_logo' );
						$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
						?>
						<img src="<?php echo $image[0]; ?>" style="height: 100px; width: auto;" />
						
						<?php
						/**
						 * Fires after the display of the email template header.
						 *
						 * @since 2.5.0
						 */
						do_action( 'bp_after_email_header' );
						?>
					</td>
				</tr>
			</table>
			<!-- Email Header : END -->
			<!-- Email Body : BEGIN -->
			<table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="<?php echo esc_attr( $settings['body_bg'] ); ?>" width="100%" style="max-width: 600px; border-radius: 5px;" class="body_bg">
				<!-- 1 Column Text : BEGIN -->
				<tr>
					<td>
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
						  <tr>
								<td style="padding: 20px; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['body_text_size'] * 1.618 ) . 'px' ) ?>; color: <?php echo esc_attr( $settings['body_text_color'] ); ?>; font-size: <?php echo esc_attr( $settings['body_text_size'] . 'px' ); ?>" class="body_text_color body_text_size">
									<span style="font-weight: bold; font-size: <?php echo esc_attr( floor( $settings['body_text_size'] * 1.35 ) . 'px' ); ?>" class="welcome"><?php bp_email_the_salutation( $settings ); ?></span>
								</td>
						  </tr>
						  <tr>
								<td style="border: 2px solid <?php echo esc_attr( $settings['email_bg'] ); ?>; padding: 20px; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['body_text_size'] * 1.618 ) . 'px' ) ?>; color: <?php echo esc_attr( $settings['body_text_color'] ); ?>; font-size: <?php echo esc_attr( $settings['body_text_size'] . 'px' ); ?>" class="body_text_color body_text_size">
									{{{content}}}
								</td>
						  </tr>
						</table>
					</td>
				</tr>
				<!-- 1 Column Text : BEGIN -->
			</table>
			<!-- Email Body : END -->
			<!-- Email Footer : BEGIN -->
			<table cellspacing="0" cellpadding="0" border="0" align="left" width="100%" style="max-width: 600px; border-radius: 5px;" bgcolor="<?php echo esc_attr( $settings['footer_bg'] ); ?>" class="footer_bg">
				<tr>
					<td style="text-align:center; padding: 20px; width: 100%; font-size: <?php echo esc_attr( $settings['footer_text_size'] . 'px' ); ?>; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['footer_text_size'] * 1.618 ) . 'px' ) ?>; color: <?php echo esc_attr( $settings['footer_text_color'] ); ?>;" class="footer_text_color footer_text_size">
						<?php
						/**
						 * Fires before the display of the email template footer.
						 *
						 * @since 2.5.0
						 */
						do_action( 'bp_before_email_footer' );
						?>

						<span class="footer_text"><?php echo nl2br( stripslashes( $settings['footer_text'] ) ); ?></span>
						<br><br>
						<a href="{{{unsubscribe}}}" style="text-decoration: underline;"><?php _ex( 'Unsubscribe', 'email', 'jeremy' ); ?></a>

						<?php
						/**
						 * Fires after the display of the email template footer.
						 *
						 * @since 2.5.0
						 */
						do_action( 'bp_after_email_footer' );
						?>
					</td>
				</tr>
			</table>
			<!-- Email Footer : END -->
			<!--[if (gte mso 9)|(IE)]>
			</td>
			</tr>
			</table>
			<![endif]-->
		</div>
	</center>
</td></tr></table>
<?php if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) wp_footer(); ?>
</body>
</html>
