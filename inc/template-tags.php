<?php
/**
 * Template tags and shortcodes
 * 
 * Defines various tags used by other templates. Eventually, some of
 * the functionality here could be replaced by core features.
 *
 * @package Jeremy
 * @since 1.0.0
 */

/**
 * Returns a customized version of WordPress's paginattion html markup, with svg
 * arrows for the 'next' and 'previous' links. The arrows are always visible, but
 * are grayed out if there is no next or previous page. Wrap it in a div with the
 * class 'pagination' to take advantage of theme styling.
 * 
 * @see paginate_links
 * 
 * @package Jeremy
 * @subpackage Jeremy/includes
 * @since 1.0.0
 * 
 * @return string The HTML markup for the pagination, to be used in the Loop.
 */
function jeremy_paginate_links( $args = null ) {
	global $wp_query, $wp_rewrite;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';
	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';
	$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	$defaults = array(
		'base' => $pagenum_link,
		'format' => $format,
		'current' => $current,
		'total' => $total,
		'show_all' => true,
		'add_args' => false,
	);
	$r = wp_parse_args( $args, $defaults );
	$r['prev_next'] = false;

	$output = '<nav aria-label="' . _x( 'Pagination', 'Pagination links ARIA label', 'jeremy' ) . '">';
	
	if ( 1 < $r['current'] ) {
		$prev_text = jeremy_get_svg( array(
			'alt' => _x( 'Previous page', 'Pagination previous page arrow alt text', 'jeremy' ),
			'img' => 'previous',
			'class' => 'prev',
		) );
		$link = str_replace( '%_%', $r['format'], $r['base'] );
		$link = str_replace( '%#%', $r['current'] - 1, $link );
		/** This filter is documented in wp-includes/general-template.php */
		$output .= '<a class="page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $prev_text . '</a>';
	} else {
		$prev_text = jeremy_get_svg( array(
			'img' => 'previous-disabled'
		) );
		$output .= '<span class="page-numbers disabled">' . $prev_text . '</span>';
	}
	$output .= paginate_links( $r );

	if ( $r['current'] < $r['total'] ) {
		$next_text = jeremy_get_svg( array(
			'alt' => _x( 'Next page', 'Pagination next page arrow alt text', 'jeremy' ),
			'img' => 'next',
			'class' => 'next',
		) );
		$link = str_replace( '%_%', $r['format'], $r['base'] );
		$link = str_replace( '%#%', $r['current'] + 1, $link );
		/** This filter is documented in wp-includes/general-template.php */
		$output .= '<a class="page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $next_text . '</a>';
	} else {
		$next_text = jeremy_get_svg( array(
			'img' => 'next-disabled'
		) );
		$output .= '<span class="page-numbers disabled">' . $next_text . '</span>';	
	}
	
	$output .= '</nav>'; 
	return $output;
}

/**
 * Generates an html img tag for a given svg file, with optional png fallback.
 * This function returns the string. Use echo to show its output in a template.
 *
 * @package Jeremy
 * @since 1.0.0
 * 
 * @see shortcode_atts()
 * 
 * @param array $args {
 * 		Required.
 * 
 * 		@type string $img   (Required) The file name of the image without an extension.
 * 		@type string $alt   (Optional) The image alt text. Default blank. Note that a
 * 							blank alt attribute will cause the image to be ignored by
 * 							screenreaders.
 * 		@type string $class (Optional) The class attribute for the image. Default blank.
 * }
 * @return string      String with the html output.
 */
function jeremy_get_svg( $args ) {
	$atts = shortcode_atts( array(
    	'img' => '',
		'alt' => '',
		'class' => '',
	), $args );
	$svg = '/assets/svg/' . esc_attr( $atts['img'] ) . '.svg';
	$png = '/assets/png/' . esc_attr( $atts['img'] ) . '.png';
	$output = '';

	if ( ! is_file( get_template_directory() . $svg ) && ! is_file( get_stylesheet_directory() . $svg ) )
		return false;
	// If a child theme is active and the file exists in it, use that one
	$path = ( is_file( get_stylesheet_directory() . $svg ) ) ? get_stylesheet_directory() : get_template_directory();
	$url = ( is_file( get_stylesheet_directory() . $svg ) ) ? get_stylesheet_directory_uri() : get_template_directory_uri();

	$output = '<img ';
	//If there's an equivalent png in the png directory, set svg to srcset instead of src
	$output .= is_file( $path . $png ) ? 'src="'.$url.$png.'" srcset="'.$url.$svg.'"' : 'src="'.$url.$svg.'"';

	$output .= ' alt="' . esc_attr( $atts['alt'] ) . '"';
	$output .= empty( $atts['class'] ) ? '>' : ' class="' . esc_attr( $atts['class'] ) . '">';
	
	return $output;
}

/**
 * Generates and echoes a trail of breadcrumbs based on the current page. The
 * genral structure is: $opt_home > $bread_archive > $bread_terms > $bread_current
 * Which might look like: Index > Blog > Category > Post Title. Example use:
 * 
 *     <?php jeremy_breadcrumbs( 'Portfolio' ); ?>
 *
 * @package Jeremy
 * @since 1.0.0
 * 
 * @link https://codex.wordpress.org/Option_Reference#Reading
 * 
 * @param string $opt_archive_name  Name for post archive, to be used with custom
 * 									post type archives. Default blank.
 */
function jeremy_breadcrumbs( $opt_archive_name = '' ) {
	if ( is_front_page() ) {
		// No breadcrumbs on the homepage.
		return;
	}
	// What to show between breadcrumbs.
	$opt_sep = get_theme_mod( 'bread-sep', '>' );

	// What to show for the first breadcrumb.
	$opt_home = get_theme_mod( 'breadcrumb_index_name', get_bloginfo( 'name' ) );

	$post_page  = get_option( 'page_for_posts' );
	if ( get_option( 'show_on_front' ) === 'page' && $post_page !== false ) {
		$post_page = get_the_title( $post_page );
	}
	$query_obj = get_queried_object();

	$output = '<nav class="breadcrumbs"><a href="' . home_url() . '">';
	if ( is_customize_preview() ) {
		// Wrap $opt_home and $opt_sep in a span so they can be dynamically changed
		// in the Customizer preview.
		$opt_sep = ' <span class="bread-sep">' . $opt_sep . '</span> ';
		$output .= '<span class="bread-index">' . $opt_home . '</span></a>' . $opt_sep;
	}
	else {
		$opt_sep = ' ' . $opt_sep . ' ';
		$output .= $opt_home . '</a>' . $opt_sep;
	}
	if ( function_exists( 'buddypress' ) ) {
		if ( bp_is_user() ) {
			$output .= sprintf( '<a href="%s">' . __( 'Members' ) . '</a>%s', bp_get_members_directory_permalink(), $opt_sep );
		}
	}
	if ( is_single() || is_category() || is_tag() || is_author() || is_tax() ) { // Post or archive pages
		if ( is_single() ) { // Individual posts
			$id = $query_obj->ID;
			$post_type = $query_obj->post_type;
			$post_type_object = get_post_type_object( $post_type );
			$bread_archive_link = get_post_type_archive_link( $post_type );
			if ( $post_type == 'post' ) { // Single blog post
				$bread_archive = $post_page;
				$taxonomy = 'category';
			} else { // Single custom post type
				$bread_archive = $post_type_object->labels->name;
				if ( $post_type == 'jeremy_deal' || $post_type == 'jeremy_job' ) {
					$author = $query_obj->post_author;
					$author_link = function_exists( 'buddypress' ) ? bp_core_get_user_domain( $author ) : get_author_posts_url( $author );
					$author = get_the_author_meta( 'display_name', $author );
					$bread_terms = sprintf( '<a href="%s">%s</a>', $author_link, $author ) . $opt_sep;
					$taxonomy = false;
				} else {
					// Pulls the first taxonomy specified for custom post type.
					$taxonomy = get_object_taxonomies($post_type)? get_object_taxonomies($post_type)[0] : False;
				}
							}
			if ( get_the_terms( $id, $taxonomy ) ) {
				/** @var int $term_obj ID of post's first category or first term of the $taxonomy */
				$term_obj = get_the_terms( $id, $taxonomy )[0];
				if ( $taxonomy === 'event-venue' && jeremy_get_the_venue_link( $term_obj->name, true ) ) {
					$bread_terms = sprintf( '<a href="%s">%s</a>', jeremy_get_the_venue_link( $term_obj->name, true ), $term_obj->name ) . $opt_sep;
				}
			}
			/** @var boolean $inclusive  Include the current category when listing its parents */
			$inclusive = True;
			/** @var string $bread_current  The current post title */ 
			$bread_current = $query_obj->post_title;
		} else { // Archive pages
			if ( is_author() ) {
				$bread_archive = $post_page; // Get blog page
				$bread_current = get_the_author(); // Author's name
				$bread_terms = __( 'Authors', 'jeremy' ) . $opt_sep;
			} else {
				$taxonomy = $query_obj->taxonomy;
				$term_obj = $query_obj->term_obj;
				$inclusive = False;
				if ( is_tax() ) { // Custom taxonomies
					$taxonomy_obj = get_taxonomy( $taxonomy );
					$post_type = $taxonomy_obj->object_type[0];
					$bread_archive = get_post_type_object( $post_type )->labels->name;
					$bread_archive_link = get_post_type_archive_link( $post_type );
					$bread_current = is_taxonomy_hierarchical( $taxonomy_obj->name ) ? $query_obj->name : sprintf( _x( 'Filed under "%s"', 'Breadcrumb link text for posts with the given tag', 'jeremy' ), $query_obj->name );
				} else {
					// Categories and tags
					$bread_archive = $post_page;
					$bread_archive_link = get_post_type_archive_link( 'post' );
					$bread_current = $query_obj->name; // Term name
				}
			}
		}
		if ( ! isset( $bread_terms ) ) {
			if ( isset( $term_obj ) ) {
				$bread_terms = get_term_parents_list( $term_obj->term_id, $taxonomy,
					array(
						'inclusive' => $inclusive,
						'separator' => $opt_sep,
				) );
			} else {
				$bread_terms = '';
			}
		}
		$bread_archive_link = isset( $bread_archive_link ) ? $bread_archive_link : $bread_archive_link = get_post_type_archive_link( 'posts' );
		$bread_archive = empty( $opt_archive_name ) ? $bread_archive : $opt_archive_name;
		$output .= '<a href="' . $bread_archive_link . '"> ' . $bread_archive . '</a>' . $opt_sep . $bread_terms . $bread_current;
	} elseif ( is_page() ) {
		if( $query_obj->post_parent ) {
			$anc = get_post_ancestors( $query_obj->ID );
			foreach ( $anc as $ancestor ) {
				$output .= '<a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a>' . $opt_sep;
			}
		}
		$output .= get_the_title();
	} elseif ( is_search() ) {
		$output .= __( 'Search results', 'jeremy' );
	} elseif ( is_404() ) {
		$output .=  __( 'Not found', 'jeremy' );
	} else {
		$output .=  get_the_archive_title();
	}
	$output .=  '</nav>';
	echo $output;
}

if ( ! function_exists( 'jeremy_get_the_excerpt' ) ) :
/**
 * Returns the post excerpt wrapped in p tags, shortened to the specified
 * number of words and followed by an ellipsis, with an optional
 * "Read more" link wrapped in p tags.
 * 
 * @package Jeremy
 * @since 1.0.0
 * 
 * @see get_the_excerpt()
 * @see get_the_permalink()
 * 
 * @param array $args {
 * 		Optional.
 * 
 * 		@type int    $length   Desired number of words in the excerpt.
 * 							   Default 30.
 * 		@type string $excerpt  Custom excerpt. Default is the post excerpt.
 * 		@type bool   $readmore Whether to add a 'Read more' link to the end.
 * 							   Default false. String is translatable.
 * 		@type string $link     The href attribute for the "Read more" link.
 * 							   Default get_the_permalink().
 * }
 * @return string The shortened excerpt with the optional 'read more' link.
 */
function jeremy_get_the_excerpt( $args = null ) {
	$defaults = array(
		'length' => 30,
		'excerpt' => '',
		'readmore' => false,
		'link' => '',
	);
	$r = wp_parse_args( $args, $defaults );

	if ( empty( $r['excerpt'] ) )
		$r['excerpt'] = get_the_excerpt();
	// Turn the excerpt string into an array of words in the string.
	$excerpt = explode( ' ', $r['excerpt'] );
	// Cut that down to the desired number of words...
	$short_excerpt = implode(' ', array_slice( $excerpt, 0, $r['length'] ) );
	// If $short_excerpt cut anything off, attach an ellipsis to the excerpt.
	if ( count( $excerpt ) > $r['length'] )
		$short_excerpt .= '...';
	// Paragraph tags.
	$short_excerpt = '<p>' . $short_excerpt . '</p>';

	if ( $r['readmore'] || ! empty( $r['link'] ) ) {
		$link = $r['link'] ? esc_url( $r['link'] ) : get_the_permalink();
		$short_excerpt .= sprintf( '<p class="read-more"><a href="%s">' . __( 'Read more', 'jeremy' ) . '</a></p>', $link );
	}
	return $short_excerpt;
}
endif;

add_filter( 'get_the_archive_title', 'jeremy_archive_title' );
/**
 * Eliminates the redundant language of WordPress's default archive titles.
 *
 * @link https://developer.wordpress.org/reference/functions/get_the_archive_title/#comment-1807
 */
if ( ! function_exists( 'jeremy_archive_title' ) ) :
function jeremy_archive_title( $title ) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() || is_tax() ) {
        $title = sprintf( __( 'Showing posts under "%s"', 'jeremy' ), single_tag_title( '', false ) );
    } elseif ( is_author() ) {
        $title = get_the_author() . __("'s Posts", 'jeremy');
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    } elseif ( is_year() ) {
        $title = get_the_date( 'Y' );
    } elseif ( is_month() ) {
        $title = get_the_date( 'F Y' );
    } elseif ( is_day() ) { // Seriously?!
        $title = get_the_date( 'F j, Y' );
    } elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title();
	} else {
		$post_page  = get_option( 'page_for_posts' );
		if ( get_option( 'show_on_front' ) === 'page' && $post_page !== false ) {
			$title = get_the_title( $post_page );
		} else {
			$title = __( 'Home', 'jeremy' );
		}
	}
    return $title;
}
endif;

/**
 * Echoes html output indicating the post author and date.
 * 
 * @package Jeremy
 * @since 1.0.0
 */
if ( ! function_exists( 'jeremy_entry_meta' ) ) :
function jeremy_entry_meta() {
	$time_string = sprintf( '<time datetime="%1$s">%2$s</time>', esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date( 'M j, Y' ) ) );
	$time_url = get_month_link( get_the_date( 'Y' ), get_the_date( 'n' ) );

	$id = get_the_author_meta( 'ID' );
	$author_url = function_exists( 'buddypress' ) ? bp_core_get_user_domain( $id ) : get_author_posts_url( $id );
	$author = get_the_author();
	/** Translators: This reads as, "Posted by Author on Date" */
	printf( '<p>' . __( 'Posted by <a href="%s">%s</a> on <a href="%s">%s</a>', 'jeremy' ) . '</p>', $author_url, $author, $time_url, $time_string );
}
endif;

if ( ! function_exists( 'jeremy_entry_footer' ) ) :
/**
 * Echoes html output showing the post edit link, tags, and categories.
 * 
 * @package Jeremy
 * @since 1.0.0
 */
function jeremy_entry_footer() {
	if ( 'post' === get_post_type() ) {
		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'jeremy' ) );
		if ( $tags_list ) {
			printf( '<p>' . esc_html__('Tagged: %s', 'jeremy' ) . '</p>', $tags_list );
		}
	}
	edit_post_link( sprintf( __( 'Edit<span class="screen-reader-text"> %s</span>', 'jeremy' ), get_the_title() ), '<p class="edit-links">', '' );
	if ( current_user_can( 'delete_post', get_the_id() ) ) {
		if ( 'post' !== get_post_type() ) {
			$del_link = get_delete_post_link( get_the_id() );
			printf( ' / <a href="%s">' . __( 'Delete<span class="screen-reader-text"> %s</span>', 'jeremy' ) . '</a>', $del_link, get_the_title() );	
		}
		echo '</p>';
	}
}
endif;

if ( ! function_exists( 'jeremy_comment_form' ) ):
function jeremy_comment_form() {
	$user = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';
	
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$args = array(
		'title_reply'       => __( 'Add your thoughts', 'jeremy' ),
		'cancel_reply_link' => __( 'Cancel', 'jeremy' ),
		'label_submit'      => __( 'Submit', 'jeremy' ),
		'format'            => 'xhtml',
	  
		'fields' => apply_filters( 'comment_form_default_fields', array(
			'author' =>
			'<div class="comment-form-grid"><p><label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' .
			'<input id="author" name="author" type="text" placeholder="John Doe" value="' . esc_attr( $commenter['comment_author'] ) .
			'" size="30"' . $aria_req . ' /></p>',
	
			'email' =>
			'<p><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' .
			'<input id="email" name="email" type="text" placeholder="john@example.com" value="' . esc_attr(  $commenter['comment_author_email'] ) .
			'" size="30"' . $aria_req . ' /></p></div>',
	
			'url' =>
			'<p><label for="url">' . __( 'Website (Optional)', 'jeremy' ) . '</label>' .
			'<input id="url" name="url" type="text" placeholder="example.com" value="' . esc_attr( $commenter['comment_author_url'] ) .
			'" size="30" /></p>',
		) ),
		'comment_field' =>  '<p><label for="comment">' . _x( 'Comment', 'noun' ) .
		  '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">' .
		  '</textarea></p>',
		
		'logged_in_as' => '<div class="logged-in-as">' . get_avatar( $user, 36 ) .
		sprintf( __( '<p>Logged in as <a href="%1$s">%2$s</a> <a href="%3$s" title="Log out of this account">(Logout)</a></p>' ),
		admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
		) . '</div>',

		'must_log_in' => '<p class="must-log-in">' .
		sprintf(
		__( 'You must be <a href="%s">logged in</a> to comment.', 'jeremy' ),
		wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
		) . '</p>',
	  
		'comment_notes_before' => '<p class="comment-notes">' .
		__( 'Your email address will not be shown.' ) .
		'</p>',
	);
	comment_form( $args );
}
endif;

if ( ! function_exists( 'jeremy_comment_links' ) ) :
/**
 * Echoes links to edit, mark as spam, and delete the current comment. Must be used
 * in the comment loop.
 * 
 * @since 1.0.1
 */
function jeremy_comment_links() {
	$comment = get_comment();
	if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
		return;
	}
	$edit = admin_url('comment.php?action=editcomment&amp;c=') . $comment->comment_ID;
	$spam = admin_url('comment.php?action=cdc&dt=spam&c=') . $comment->comment_ID;
	$del = admin_url('comment.php?action=cdc&c=') . $comment->comment_ID;
	echo '
	<a href="' . $edit . '">' . __( 'Edit', 'jeremy' ) . '</a>
	<a href="' . $spam . '">' . __( 'Spam', 'jeremy' ) . '</a>
	<a href="' . $del . '">' . __( 'Delete', 'jeremy' ) . '</a>';
}
endif;

if ( ! function_exists( 'jeremy_footer_credits' ) ) :
/**
 * Displays the copyright text, theme credits, and social media links.
 * 
 * @since 1.1.0
 * 
 * @param bool $show_icons	Whether to show the social media icons. Default true.
 */
function jeremy_footer_credits( $show_icons = true ) {
	if ( $show_icons ) {
		echo '
		<nav class="social-links" role="navigation" aria-labelledby="socialmenu_label">
			<h4 id="socialmenu_label" class="screen-reader-text" >' . __( 'Social Media Links', 'jeremy' ) . '</h4>
			<ul class="menu">
				<li><a href="' . get_feed_link() . '">' . jeremy_get_svg( array( 'alt' => __( 'RSS Feed', 'jeremy' ), 'img' => 'footer-rss' ) ) . '</a></li>';
			if ( get_theme_mod( 'site_facebook' ) ) {
				echo '
				<li><a href="' . esc_url( get_theme_mod( 'site_facebook' ) ) . '">' . jeremy_get_svg( array( 'alt' => __( 'Visit our Facebook page', 'jeremy' ), 'img' => 'footer-facebook' ) ) . '</a></li>';
			} if ( get_theme_mod( 'site_twitter' ) ) {
				echo '
				<li><a href="' . esc_url( get_theme_mod( 'site_twitter' ) ) . '">' . jeremy_get_svg( array( 'alt' => __( 'Visit our Twitter', 'jeremy' ), 'img' => 'footer-twitter' ) ) . '</a></li>';
			}
			echo '
			</ul>
		</nav>
		<div>';
	}
	$preview = is_customize_preview();
	$tag = ( get_theme_mod( 'show_theme_credits' ) || get_theme_mod( 'show_powered_by' ) || $preview ) ? 'span' : 'p';
	$sep = ' | ';
	echo '<'.$tag.' class="copyright">' . get_theme_mod('copyright', '&copy; '.date('Y').' '.get_bloginfo('name')) . '</'.$tag.'>';
	if ( get_theme_mod( 'show_theme_credits' ) || $preview ) {
		$credits_toggle = get_theme_mod( 'show_theme_credits', true ) ? '' : 'style="display:none;"';
		if ( get_theme_mod( 'copyright' ) || $preview ) echo $sep;
		printf( '<span class="theme-credits" %s>' . __( 'Jeremy theme by <a href="%s">plasterbrain</a>', 'jeremy' ) . '</span>', $credits_toggle, esc_url( 'https://plasterbrain.com/' ) );
	}
	if ( get_theme_mod( 'show_powered_by' ) || $preview ) {
		$poweredby_toggle = get_theme_mod( 'show_powered_by', true ) ? '' : 'style="display:none;"';
		if ( get_theme_mod( 'show_theme_credits' ) || $preview ) echo $sep;
		printf( '<a class="powered-by" href="https://wordpress.org/" %s>' . __( 'Proudly powered by %s', 'jeremy' ) . '</a>', $poweredby_toggle, 'WordPress' );
	}
	echo '
	</div>';
}
endif;
