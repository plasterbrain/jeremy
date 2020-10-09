<?php
/**
 * Template Tags
 * 
 * Defines various tags used by other templates to output content.
 *
 * @TODO Function to unify phone number formatting
 *
 * @package Jeremy
 * @subpackage Includes
 * @since 1.0.0
 */

/**
 * Shortens given text to the given number of characters or words, attempting
 * to cut off at the nearest whole word and remove any trailing whitespace or
 * interrupted characters (e.g. "(" but not ")" ).
 *
 * @TODO Syllabic languages not separated by spaces probably need thir own logic
 * and handling for cutting off "by nearest word" to work.
 * 
 * @since 2.0.0
 *
 * @param string $text	The text to shorten.
 * @param int $length		The number of characters or words to shorten to.
 * @param bool $words		Whether $length is a number of words (true) or
 * 											characters (false, default).
 * @return string 			The shortened text.
 */
function jeremy_truncate( $text, $length, $words = false ) {
	$text = wp_strip_all_tags( $text );
	$length = absint( $length );
	$words = (bool) $words;
	
	$trimmed = false;
	if ( $words === true ) {
		$text = explode( ' ', $text );
		$trimmed = count( $text ) > $length;
		$text = implode( ' ', array_slice( 	$text, 0, $length ) );
	} elseif ( strlen( $text ) > $length ) {
		$trimmed = true;
		$text = substr( $text, 0, $length );
		$space = strrpos( $text, ' ' );
		if ( $space !== false ) {
			// Try to avoid cutting off in the middle of a word.
			$text = substr( $text, 0, $space );
		}
	}
	
	if ( $trimmed ) {
		$bad_chars = _x( '�,;-–_—([<{',
			'Characters to be stripped from the end of truncated menu titles.', 'jeremy' );
		$text = html_entity_decode( $text );
		
		do {
			$text = rtrim( $text, $bad_chars );
		} while ( strpos( $bad_chars, $text[-1] ) !== false );

		// Remove any trailing whitespace.
		$text = rtrim( $text );
		$text .= "&hellip;";
	}
	return $text;
}

if ( ! function_exists( 'jeremy_the_title' ) ) :
/**
 * A wrapper for {@see the_title} that outputs the post title with a link in
 * archive displays, but not singular ones.
 *
 * @since 2.0.0
 * 
 * @param  array  $args {
 * 		Optional.
 * 		@type string $tag_single		The non-link tag to wrap the title in on
 * 																singular displays, default "h1".
 * 		@type string $tag_archive		The non-link tag to wrap the title in on
 * 																archive displays, default "h2".
 * 		@type string $link					Where the title links to in archive displays,
 * 																default {@see get_the_permalink}.
 * 		@type string $class					The class attribute for the outer element.
 * 																Default "entry-title".
 * }
 */
function jeremy_the_title( $args = array() ) {
	$args = wp_parse_args( $args,
	array(
		'tag_single' => 'h1',
		'tag_archive' => 'h2',
		'link' => get_the_permalink(),
		'class' => 'entry__title',
	) );
	
	$args['class'] = esc_attr( $args['class'] );
	
	if ( is_singular() ) {
		$title_before = '';
		$title_after= '';
		
		$args['tag'] = esc_attr( $args['tag_single'] );
	} else {
		// Archive displays
		$args['link'] = esc_url( $args['link'] );
		$title_before = "<a href='{$args['link']}'>";
		$title_after = '</a>';
		
		$args['tag'] = esc_attr( $args['tag_archive'] );
	}
	
	$title_before = "<{$args['tag']} class='{$args['class']}'>" . $title_before;
	$title_after .= "</{$args['tag']}>";
	
	the_title( $title_before, $title_after );
}
endif;

if ( ! function_exists( 'jeremy_paginate_links' ) ) :
/**
 * Returns a customized version of WordPress's {@see paginate_links}, with SVG
 * arrows for the 'next' and 'previous' links. The arrows are always visible,
 * but are grayed out if there is no next or previous page. Wrap it in a div
 * with the class 'pagination' to take advantage of theme styling.
 * 
 * @since 1.0.0
 *
 * @param array   $args 	{@see paginate_links}
 * @return string 				The HTML for loop pagination.
 */
function jeremy_paginate_links( $args = array() ) {
	global $wp_query, $wp_rewrite;
	
	$url_parts = explode( '?', html_entity_decode( get_pagenum_link() ) );
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';
	
	$format = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';
	
	$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	
	$args = wp_parse_args( $args, array(
		'base' => $pagenum_link,
		'format' => $format,
		'current' => $current,
		'total' => $total,
		'show_all' => true,
		'add_args' => false,
	) );
	
	$args['prev_next'] = false;

	$id = wp_unique_id();
	$output = '<nav class="nav-pagination" id="pagination-' . esc_attr( $id ) . '" aria-label="' . esc_attr__( 'Pagination', 'jeremy' ) . '">';
	
	$svg_args = array(
		'alt'    => esc_attr__( 'Previous page', 'jeremy' ),
		'img'    => 'nav-previous',
		'class'  => 'nav-pagination__prev',
		'inline' => true,
	);
	
	if ( 1 < $args['current'] ) {
		$prev_svg = jeremy_get_svg( $svg_args );
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $args['current'] - 1, $link );
		$link = esc_url( apply_filters( 'paginate_links', $link ) );
		$output .= "<a class='page-numbers' href='{$link}'>{$prev_svg}</a>";
	} else {
		$svg_args['alt'] = '';
		$svg_args['class'] .= ' nav-pagination__disabled';
		$prev_svg = jeremy_get_svg( $svg_args );
		$output .= "<span role='presentation' class='page-numbers'>{$prev_svg}</span>";
	}
	
	$output .= paginate_links( $args );
	
	$svg_args = array(
		'alt' 	 => esc_attr__( 'Next page', 'jeremy' ),
		'img'    => 'nav-next',
		'class'  => 'nav-pagination__next',
		'inline' => true,
	);

	if ( $args['current'] < $args['total'] ) {
		$next_svg = jeremy_get_svg( $svg_args );
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $args['current'] + 1, $link );
		$link = esc_url( apply_filters( 'paginate_links', $link ) );
		$output .= "<a class='page-numbers' href='{$link}'>{$next_svg}</a>";
	} else {
		$svg_args['alt'] = '';
		$svg_args['class'] .= ' nav-pagination__disabled';
		$next_svg = jeremy_get_svg( $svg_args );
		$output .= "<span role='presentation' class='page-numbers'>{$next_svg}</span>";
	}
	
	$output .= '</nav>'; 
	return $output;
}
endif;

/**
 * Prints pagination for an archive/search view.
 *
 * @since 2.0.0
 */
function jeremy_posts_navigation() {
	if ( is_singular() ) {
		return;
	}
 
	global $wp_query;
	
	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}
	
	$current_page = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );
 
	if ( $current_page >= 1 ) {
		$pages[] = $current_page;
	}

	if ( $current_page >= 3 ) {
		$pages[] = $current_page - 1;
		$pages[] = $current_page - 2;
	}

	if ( ( $current_page + 2 ) <= $max ) {
		$pages[] = $current_page + 1;
		$pages[] = $current_page + 2;
	}
	
	if ( ! in_array( 2, $pages ) && $max !== 2 ) {
		array_unshift( $pages, '&hellip;' );
	}
	
	if ( ! in_array( 1, $pages ) ) {
		array_unshift( $pages, 1 );
	}
	
	if ( ! in_array( $max, $pages ) ) {
		if ( ! in_array( $max - 1, $pages ) ) {
			$pages[] = '&hellip;';
		}
		$pages[] = $max;
	}
	
 	$prev = get_previous_posts_link( jeremy_get_svg( array(
		'img' 	 => 'nav-previous',
		'alt' 	 => __( 'Previous page', 'jeremy' ),
		'inline' => true,
	) ) );
	$next = get_next_posts_link( jeremy_get_svg( array(
		'img' 	 => 'nav-next',
		'alt' 	 => __( 'Next page', 'jeremy' ),
		'inline' => true,
	) ) );
	?>
	<nav class="nav-pagination">
		<ul class="nav__list nav__list-h">
			<?php if ( $prev ) {
				echo "<li>{$prev}</li>";
			}
			foreach ( $pages as $page ) {
				if ( $page === $current_page ) {
					echo '<li class="current">
						<span aria-disabled="true">' . esc_html( $page ) . '</span>
					</li>';
				} elseif ( ! is_int( $page ) ) {
					echo '<li><span>' . esc_html( $page ) . '</span></li>';
				} else {
					echo '<li>
						<a href="' . esc_url( get_pagenum_link( $page ) ) . '">' .
							esc_html( $page ) . '
						</a>
					</li>';
				}
			}
			if ( $next ) {
				echo '<li>' . $next . '</li>';
			} ?>
		</ul>
	</nav>
	<?php 
}

if ( ! function_exists( 'jeremy_sharemy' ) ) :
/**
 * Outputs sharing buttons and an ability to copy the link to he current post.
 *
 * @TODO copy button js
 * @TODO customizer option that skips shortcode re-mapping maybe?
 * @TODO QR CODE!!
 *
 * @since 2.0.0
 * 
 * @param string $heading  Heading to show above the share links. Optional.
 * @param bool   $after		 Whether this is going after content, default true.
 */
function jeremy_sharemy( $heading = '', $after = true ) {
	$copy_id = get_the_ID() . "-permalink";
	$post_type_slug = get_post_type();
	$post_type = get_post_type_object( $post_type_slug );
	$post_type_label = $post_type->labels->singular_name;
	if ( empty( $heading ) ) {
		/* translators: %s is the human-readable name of the post type. */
		$heading = sprintf( __( "Share this %s", 'jeremy' ), $post_type_label );
	}
	
	if ( function_exists( 'shariff3uu_render' ) ) {
		// WordPress Shariff Wrapper
		if ( array_key_exists( 'shariff3uu', $GLOBALS ) ) {
			$post_type_slug = $post_type_slug === 'post' ? 'posts' : $post_type_slug;
			
			if ( $after ) {
				$s_key = 'add_after';
				$s_meta = 'shariff_metabox_after';
			} else {
				$s_key = 'add_before';
				$s_meta = 'shariff_metabox_before';
			}
			
			$s_key = $GLOBALS['shariff3uu'][$s_key][$post_type_slug] ?? 0;
			
			if ( $s_key === 1 || get_post_meta( get_the_ID(), $s_meta, true ) ) {
				$use_shariff = true;
			}
		}
	}
	
	if ( is_page() && empty( $use_shariff ) ) {
		return;
	}
	?>

	<div class="entry__share">
		<h3 class="entry__share__title">
			<?php echo esc_html( $heading ); ?>
		</h3>
		
		<?php
		// Share buttons
		if ( ! empty( $use_shariff ) ) {
			echo shariff3uu_render( array( 'headline' => '' ) );
		} ?>
		
		<?php if ( ! is_page() ) { ?>
			<div class="entry__share-link flex" role="presentation">
				<p class="entry__share-link__label"><?php esc_html_e( 'Permalink:', 'jeremy' ); ?></p>
				<div class="entry__share-link__form" role="presentation">
					<input class="entry__share-link__input" id="<?php echo esc_attr( $copy_id ); ?>" type="text" readonly value="<?php echo esc_url( wp_get_shortlink( get_the_id() ) ); ?>">
					<?php /*<button data-clipboard-target="#<?php echo esc_attr( $copy_id ); ?>" aria-label="<?php esc_attr_e( 'Copy permalink to clipboard', 'jeremy' ); ?>"><?php esc_html_e( 'Copy', 'jeremy' ); ?></button> */ ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'jeremy_get_svg' ) ) :
/**
 * Generates an html img tag for a given svg file, with optional png fallback.
 * This function returns the string. Use echo to show its output in a template.
 *
 * @since 1.0.0
 * 
 * @param array $args {
 * 		Required.
 * 		@type string $img   The file name of the image, without an extension.
 * 		@type string $alt   The image alt text/aria-label, if any. Default blank,
 * 												which will cause it to be ignored by screenreaders.
 * 		@type string $class The class attribute for the image, if any.
 * }
 * @return string      String with the html output.
 */
function jeremy_get_svg( $args ) {
	$args = wp_parse_args( $args, array(
		'img' 	 => '',
		'alt' 	 => '',
		'class'  => '',
		'inline' => false,
	) );
	
	$svg = '/assets/svg/' . esc_attr( $args['img'] ) . '.svg';
	
	if ( $args['inline'] ) {
		$output = get_theme_file_path( $svg );
		if ( ! file_exists( $output ) ) {
			return;
		}
		$output = file_get_contents( $output );
		$class = ! empty( $args['class'] ) ? 'class="' . esc_attr( $args['class'] ) . '"' : '';
		$aria = empty( $args['alt'] ) ? 'role="presentation"' : 'aria-label="' . esc_attr( $args['alt'] ) . '"';
		
		$start = '<svg ';
		$output = str_replace( $start, $start . $class, $output );
		return str_replace( $start, $start . $aria, $output );
	}
	
	$svg = esc_url( get_theme_file_uri( $svg ) );
	
	$png = '/assets/png/' . esc_attr( $args['img'] ) . '.png';
	
	$alt = esc_attr( $args['alt'] );
	$class = esc_attr( $args['class'] );
	$class = ! empty( $class ) ? "class='{$class}'" : '';
	
	if ( is_file( get_theme_file_path( $png ) ) ) {
		$png = esc_url( get_theme_file_uri( $png ) );
		$src = "src='{$png}' srcset='{$svg}'";
	} else {
		$src = "src='{$svg}'";
	}

	$output = "<img {$src} alt='{$alt}' {$class}>";
	/**
	 * Filters the svg output im tired document later
	 * 
	 * @since 2.0.0
	 */
	$output = apply_filters( 'jeremy_svg', $output );
	return $output;
}
endif;

if ( ! function_exists( 'jeremy_get_the_excerpt' ) ) :
/**
 * Returns the post excerpt, shortened to the specified number of words and
 * followed by an ellipsis, with an optional "Read more" link afterwards.
 * 
 * @since 1.0.0
 * 
 * @param array $args {
 * 		Optional.
 * 		@type int    				$length   Number of words in the excerpt, default 30.
 * 																	Note that the default-returned WordPress
 * 																	excerpt is a max of 55 words.
 * 		@type string 				$excerpt  Custom excerpt, default is the post excerpt.
 * 		@type string|bool   $link		 	Whether to add a "Read more" link. Set to
 * 																	a string to specify the read more URL
 * 																	(default {@see get_the_permalink}) or false
 * 																	to disable the link entirely.
 * 		@type string				$readmore	The label for the "read more" link, default
 * 																	"Read more," which gets filtered.
 * 		@type string				$full			Name of the full post, used for the
 * 																	screenreader portion of the "read more"
 * 																	label (e.g. "Read more about [x]"). Default
 * 																	the post title.
 * }
 * @return string The shortened excerpt with the optional "read more" link.
 */
function jeremy_get_the_excerpt( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'length' => 30,
		'excerpt' => '',
		'link' => get_the_permalink(),
		'readmore' => '',
		'full' => '',
	) );
	
	if ( empty( $args['excerpt'] ) ) {
		$content = get_extended( get_post()->post_content );
		if ( ! empty( $content['extended'] ) ) {
			$args['excerpt'] = $content['main'];
			$args['readmore'] = $content['more_text'];
		} else {
			$args['excerpt'] = get_the_excerpt();
		}
	}

	// HTML tags are stripped and length is converted to int within this function.
	$args['excerpt'] = jeremy_truncate( $args['excerpt'], $args['length'], true );
	
	$output = "<p class='entry__excerpt'>{$args['excerpt']}";

	$args['link'] = esc_url( $args['link'] );
	if ( $args['link'] ) {
		$args['full'] = wp_strip_all_tags( $args['full'] );
		$args['readmore'] = wp_kses_post( $args['readmore'] );
		
		// Make sure we have a full post title for the screenreader tag.
		if ( empty( $args['full'] ) ) {
			$args['full'] = wp_strip_all_tags( get_the_title() );
		}
		
		// Don't allow an empty "read more" label if we have a link.
		if ( empty( $args['readmore'] ) ) {
			// This gets sanitized after running through a filter.
			/* translators: %s is the name of the full post. */
			$args['readmore'] = sprintf( __( 'Read more <span class="screen-reader-text">about %s</span>', 'jeremy' ), $args['full'] );
		
			/**
			 * Filters the value of the default "read more" link text. It will not
			 * work in cases where the {@see jeremy_get_the_excerpt} is supplied with
			 * a custom "read more" label. The result is wrapped in a paragraph tag.
			 * 
			 * @since 2.0.0
			 *
			 * @param string $readmore	The read more text, default "Read more".
			 * @param string $full			The name of the full post, which you should
			 * 													use to supply screenreader text.
			 */
			$args['readmore'] = wp_kses_post( apply_filters(
				'jeremy_readmore_default_label', $args['readmore'], $args['full']
			) );
		}

		$output .= "&nbsp;<a class='entry__excerpt-more' href='{$args['link']}'>{$args['readmore']}</a>";
	}
	$output .= '</p>';
	return $output;
}
endif;

/**
 * Returns either the author archive url on regular WordPress installs or the
 * profile link if BuddyPress is active. Use it in the loop!
 *
 * @since 2.0.0
 * 
 * @param int $id			The author ID, default the current post's author ID.
 * @return string			The unescaped author profile or archive URL.
 */
function jeremy_get_author_link( $id = null ) {
	if ( ! $id || is_int( $id ) ) {
		$id = get_the_author_meta( 'ID' );
	}
	return function_exists( 'buddypress' ) ? bp_core_get_user_domain( $id ) : get_author_posts_url( $id );
}

/**
 * Prints the post author and date (for non-event posts) in a cool box.
 * 
 * @since 1.0.0
 * 
 * @param bool $show_image	Whether to show the author's avatar. Default false.
 */
if ( ! function_exists( 'jeremy_entry_byline' ) ) :
function jeremy_entry_byline( $show_img = false ) {
	$post_type = get_post_type();
	$is_deal = $post_type === 'jeremy_deal';
	$is_event = $post_type === 'event';

	$img_size = 36;
	$time_string = '';
	if ( $is_deal ) {
		$show_img = true;
		$img_size = 96;
	} elseif ( ! $is_event ) {
		$time_string = sprintf( '<time datetime="%1$s">%2$s</time>', esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
		
		// $time_url = get_month_link( get_the_date( 'Y' ), get_the_date( 'n' ) );
	}
	
	$author_id = get_the_author_meta( 'ID' );
	$author_link = esc_url( jeremy_get_author_link( $author_id ) );
	$author = get_the_author();
	$author_text_link = "<a href='{$author_link}'>{$author}</a>";
	
	$avatar = get_avatar( $author_id, $img_size, 'mystery', '', array(
		'class' => 'entry__meta-avatar'
	) );
	
	$box_class = is_archive() || is_home() ? '' : ' coolbox';
	
	$address = function_exists( 'jeremy_bp_get_address' ) ? jeremy_bp_get_address( $author_id ) : false;
	?>
	<div class="entry__meta flex<?php echo esc_attr( $box_class );?>">
		<?php if ( $show_img && is_single() ) { ?>
			<a aria-hidden="true" href="<?php echo $author_link; ?>">
				<?php echo $avatar; ?>
			</a>
		<?php } ?>
		<?php if ( $is_deal ) { ?>
			<div id="entry-byline" class="entry__meta__inner">
				<?php /* translators: %s is the name of a business or venue */ ?>
				<p class="entry__meta-author">
					<?php echo wp_kses_post( sprintf( __( '@ %s', 'jeremy' ), $author_text_link ) ); ?>
				</p>
				<?php if ( $address ) { ?>
					<p class="entry__meta-address">
						<?php echo esc_html(jeremy_bp_get_address( $author_id ) ); ?>
					</p>
				<?php } ?>
			</div>
		<?php } else { ?>
			<p id="entry-byline" class="entry__meta-byline">
				<?php if ( $time_string ) {
					/* translators: %2$s is the author name, %3$s is the post date. */
					echo wp_kses_post( sprintf( __( 'Posted by %1$s on %2$s', 'jeremy' ), $author_text_link, $time_string ) );
				} else {
					/* translators: %s is the author name. */
					echo wp_kses_post( sprintf( __( 'Posted by %s', 'jeremy' ), $author_text_link ) );
				} ?>
			</p>
		<?php } ?>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'jeremy_post_banner' ) ) :
/**
 * Prints a nice little banner that shows the name of the currently viewed
 * post type, for use on singular view pages.
 *
 * @since 2.0.0
 *
 * @param string $post_type_slug		The post type or "sticky" for sticky posts.
 * 																	Default is the current post type object.
 */
function jeremy_post_banner( $post_type_slug = null ) {
	$post_type_slug = $post_type_slug ?: get_post_type();

	/**
	 * Filters the array of labels shown in the banner for each post type.
	 *
	 * @since 2.0.0
	 * 
	 * @param array $names	The array of post type slugs (e.g. "post") to their
	 * 											custom banner labels. If a post type is missing from
	 * 											this array, the singular post type label (e.g. "Post")
	 * 											will be used. The array also includes "__sticky" for
	 * 											featured (sticky) posts.
	 * @return array				The filtered array of labels.
	 */
	$post_type_labels = wp_parse_args( apply_filters( 'jeremy_post_banner_labels', array() ), array(
		'post' 					=> __( 'Blog Post', 'jeremy' ),
		'__sticky'			=> __( 'Featured', 'jeremy' ),
		'jeremy_deal' 	=> __( 'Special Offer', 'jeremy' ),
		'__past_event'	=> __( 'Past Event', 'jeremy' ),
	) );
	
	if ( $post_type_slug === 'event' && defined( 'EVENT_ORGANISER_VER' ) && ! is_single() ) {
		$id = get_the_ID();
		$post_type_slug = jeremy_eo_is_past( $id ) ? '__past_event' : $post_type_slug;
	}
	
	if ( array_key_exists( $post_type_slug, $post_type_labels ) ) {
		$post_type_label = $post_type_labels[$post_type_slug];
		if ( $post_type_slug === '__past_event' ) {
			$post_type_slug = 'event';
		}
	} else {
		$post_type = get_post_type_object( $post_type_slug );
		if ( $post_type ) {
			$post_type_label = $post_type->labels->singular_name;
		} else {
			$post_type_label = ucwords( $post_type_slug );
		}
	}
	
	$svg =  jeremy_get_svg( array(
		'img' 	 => "post-{$post_type_slug}",
		'inline' => true,
	) );
	?>
	<p id="post__banner-<?php echo esc_attr( get_the_ID() ); ?>" class="post__banner post__banner-<?php echo esc_attr( $post_type_slug ); ?>">
		<?php echo $svg; ?><?php echo esc_html( $post_type_label ); ?>
	</p>
	<?php
}
endif;

if ( ! function_exists( 'jeremy_entry_meta' ) ) :
/**
 * Prints the post tags and categories.
 * 
 * @since 2.0.0
 */
function jeremy_entry_meta() {
	$post_type = get_post_type();
	$post_id = get_the_ID();
	
	$before = '<ul class="nav__list nav__list-h nav__list-terms"><li class="entry__meta-terms__li">';
	$sep = '</li><li class="entry__meta-terms__li">';
	$after = '</li></ul>';
	
	if ( $post_type === 'post' ) {
		$terms = get_the_category_list();
		$tags = get_the_tag_list( $before, $sep, $after );
	} else {
		if ( $post_type === 'event' ) {
			return;
		}
		$taxonomy = null;
		//@TODO tags
		$tags = null;
		
		$terms = get_the_terms( $post_id, $taxonomy );
		$terms = is_wp_error( $terms ) ? false : get_the_term_list(
			get_the_ID(), $taxonomy, $before, $sep, $after
		);
	}
	
	if ( $terms || $tags ) { ?>
		<?php if ( $terms ) { ?>
			<div class="entry__meta-terms flex" aria-label="<?php esc_attr_e( 'Post categories' ); ?>">
				<span class="entry__meta-terms__label">
					<?php esc_html_e( 'Posted in:', 'jeremy' ); ?>
				</span>
				<?php echo $terms; ?>
			</div>
		<?php } ?>
		<?php if ( $tags ) { ?>
			<div class="entry__meta-terms flex" aria-label="<?php esc_attr_e( 'Post tags' ); ?>">
				<span class="entry__meta-terms__label">
					<?php esc_html_e( 'Tagged:', 'jeremy' ); ?>
				</span>
				<?php echo $tags; ?>
			</div>
		<?php } ?>
	<?php }
}
endif;

if ( ! function_exists( 'jeremy_entry_edit_links' ) ) :
/**
 * Prints the links to edit and delete the current post as an unordered list.
 *
 * @since 2.0.0
 *
 * @param bool $delete		Whether to include the delete post link, default true.
 */
function jeremy_author_tools( $delete = true, $bp = false ) {
	$post_id = get_the_ID();
	$post_title = get_the_title();
	if ( is_customize_preview() || ! current_user_can( 'delete_post', $post_id ) || ! current_user_can( 'edit_posts', $post_id ) ) {
		return;
	}
	
	$link_args = array(
		'edit' => array(
			'class' 		 => 'nav-edit__item-edit',
			'svg'				 => 'ui-pencil',
			'label' 		 => sprintf( /* translators: %s is the post title. */
						__( 'Edit %s', 'jeremy' ), $post_title ),
		),
	);
	
	if ( $bp && function_exists( 'buddypress' ) ) {
		$link_args['edit']['permission'] = jeremy_bp_is_editor();
		$link_args['edit']['link'] = bp_get_members_component_link( 'profile', 'edit' );
	} else {
		$link_args['edit']['permission'] = current_user_can( 'edit_post', $post_id );
		$link_args['edit']['link'] = get_edit_post_link( $post_id );
	}
		
	if ( $delete ) {
		$link_args['delete'] = array(
			'class' 		 => 'nav-edit__item-delete',
			'permission' => current_user_can( 'delete_post', $post_id ),
			'svg'				 => 'ui-trash',
			'link' 			 => get_delete_post_link( $post_id ),
			'label' 		 => sprintf( /* translators: %s is the post title. */
						__( 'Delete<span class="screen-reader-text"> %s</span>', 'jeremy' ),
						$post_title ),
		);
	}
	?>
	
	<ul class="nav__list nav__list-h nav-edit" aria-label="<?php esc_attr_e( 'Post author tools', 'jeremy' ); ?>">
		<?php foreach ( $link_args as $button ) { if ( $button['permission'] ) { ?>
			<li class="nav-edit__item">
				<a class="<?php echo esc_attr( $button['class'] ); ?>" href="<?php echo esc_url( $button['link'] ); ?>">
					<?php echo jeremy_get_svg( array(
						'img' 	 => $button['svg'],
						'alt' 	 => $button['label'],
						'inline' => true,
					) ); ?>
				</a>
			</li>
		<?php } } ?>
	</ul>
	<?php
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
 * Echoes links to edit, mark as spam, and delete the current comment. Must be
 * used in the comment loop.
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

/**
 * Attempts to generate "create event" URLs for popular services. If you are
 * using the Event Organiser, use {@see jeremy_eo_get_addtocal_links}.
 * 
 * @link https://github.com/InteractionDesignFoundation/add-event-to-calendar-docs
 *
 * @since 2.0.0
 *
 * @param array $args {
 * 		Not strictly required, but you probably want to fill it out, yo.
 * 		
 * 		@type DateTime $start		An object with the first event start time.
 * 		@type DateTime $end			An object with the first event end time.
 * 		@type bool $allday			Whether the event lasts all day, default false.
 * 		@type string $venue			The name of the event location, if any.
 * 		@type string $uid				A unique event ID to be passed to the calendars.
 * }
 * @param array $links {
 * 		Add links if your event solution already handles them. Optional.
 *
 * 		@type string $ics							Link to an ics file for this event.
 * 		@type string $outlook_query 	Query string for this event used for both
 * 																	outlook.live.com and outlook.office.com.
 * 		@type string $google_cal			Google Calendar link for this event.
 * 		@type string $yahoo						Yahoo Calendar link for this event.
 * }
 * @return array|bool		{
 * 		An array with the various "add to calendar" links on success, false if
 * 		there was an issue getting the event post and/or start and end times. The
 * 		ics link (or lackthereof) appears multiple times so you can make a list
 * 		out of this using a foreach loop without much trouble.
 *
 * 		@type string $apple						The link to the ics file if one was given.
 * 		@type string $google_cal			The Google Calendar link if one was given.
 * 		@type string $outlook					The link to the ics file if one was given.
 * 		@type string $outlook_web			The Outlook.com Calendar link.
 * 		@type string $office365				The Office365 Calendar link.
 * 		@type string $yahoo						The Yahoo Calendar link.
 * 		@type string $other						The ics file again. Don't tell anybody...
 * }
 */
function jeremy_get_addtocal_links($post = null, $args=array(), $links=array()){
	$post = get_post( $post );
	if ( ! $post ) { return false; }

	$args = wp_parse_args( $args, array(
		'start' 				=> new DateTime('now'),
		'end' 					=> false,
		'allday'				=> false,
		'rrule'					=> false,
		'venue' 				=> false,
		//'address1' 			=> false,
		//'address2' 			=> false,
		'uid' 					=> false,
	) );
	
	/* == Basic Event Info == */
	$title = wp_strip_all_tags( get_the_title() );
	$desc = wp_strip_all_tags( jeremy_get_the_excerpt( array( 'link' => false) ));

	/* == Location Info == */
	$venue = wp_strip_all_tags( $args['venue'] );
	
	/* == Date/Time == */
	$start = $args['start'];
	// You get a minute-long event if you didn't supply any info.
	$end = empty( $args['end'] ) ?
		( new DateTime('now') )->modify( '+1 minute' ) : $args['end'];
	
	if ( ! is_a( $start, 'DateTime' ) || ! is_a( $end, 'DateTime' ) ) {
		return false; }
		
	$tz_utc = new DateTimeZone( 'UTC' );
	$start->setTimezone( $tz_utc );
	$end->setTimezone( $tz_utc );
	
	/**
	 * Filters the order in which the "Add to Calendar" links are returned for
	 * easy use in for loops. You can remove elements to prevent them from being
	 * generated here.
	 *
	 * @since 2.0.0
	 * 
	 * @param array $link_order		The default link order: 'apple', 'google_cal',
	 * 														'outlook', 'outlook_web', 'office365', 'yahoo',
	 * 														'other'.
	 */
	$link_order = apply_filters( 'jeremy_addtocal_link_order', array( 'apple', 'google_cal', 'outlook', 'outlook_web', 'office365', 'yahoo', 'other' ) );
	
	if ( empty( $link_order ) ) {
		return false; }
	
	/* == Calendar Links ==*/
	$ics = array_key_exists( 'ics', $links ) ? $links['ics'] : '';
	$result = array(
		'apple' 	=> $ics,
		'outlook' => $ics,
		'other' 	=> $ics
	);
	
	/* == Google == */
	if ( in_array( 'google_cal', $link_order ) ) {
		if ( array_key_exists( 'google_cal', $links ) ) {
			$result['google_cal'] = $links['google_cal'];
		} else {
			//@TODO Google link is handled by Event Organiser, so...
		}
	}
	
	/* == Outlook.com Calendar, Office 365 Calendar == */
	$outlook_url = 'https://outlook.live.com/calendar/0/action/compose';
	$office_url  = 'https://outlook.office.com/calendar/0/deeplink/compose';

	if ( in_array( 'outlook_web', $link_order ) || in_array( 'office365', $link_order ) ) {
		if ( array_key_exists( 'outlook_query', $links ) ) {
			$result['outlook_web'] = $outlook_url . $links['outlook_query'];
			$result['office365'] 	 = $outlook_url . $links['outlook_query'];
		} else {
			$format_outlook = 'Y-m-d\TH:i:s\Z';
			$format_outlook_ad = 'Y-m-d';
			$outlook = array(
				'subject' => $title,
				'body' 		=> $desc,
			);
			
			if ( (bool) $args['allday'] ) {
				$outlook['startdt'] = $start->format( $format_outlook_ad );
				$outlook['enddt'] = $end->format( $format_outlook_ad );
				$outlook['allday'] = 'true';
			} else {
				$outlook['startdt'] = $start->format( $format_outlook );
				$outlook['enddt'] = $end->format( $format_outlook );
			}
			
			if ( ! empty( $venue ) ) {
				$outlook['location'] = $venue;
			}
			
			if ( ! empty( $args['uid'] ) ) {
				$outlook['uid'] = $args['uid'];
			}
			
			$outlook_query = http_build_query( $outlook );
			$result['outlook_web'] = $outlook_url . '?' . $outlook_query;
			$result['office365']   = $office_url . '?' . $outlook_query;
		}
	} // endif check for outlook_web/office365 keys in link order
	
	/* == Yahoo Calendar == */
	if ( in_array( 'yahoo', $link_order ) ) {
		if ( array_key_exists( 'yahoo', $links ) ) {
			$result['yahoo'] = $links['yahoo'];
		} else {
			$format_yahoo = 'Ymd\THis\Z';
			$yahoo = array(
				'v' => '60',
				'title' => $title,
				'desc' => $desc,
			);
			
			if ( (bool) $args['allday'] ) {
				$yahoo['st'] = $start->format( 'Ymd' );
				$yahoo['dur'] = 'allday';
			} else {
				$yahoo['st'] = gmdate( $format_yahoo, $start->getTimestamp() );
				$yahoo['et'] = $end->format( $format_yahoo );
			}
			
			if ( $args['rrule'] ) {
				$yahoo = array_merge( $yahoo, jeremy_get_yahoo_rrule( $args['rrule'] ) );
			}
			
			if ( ! empty( $venue ) ) {
				$yahoo['in_loc'] = $venue;
			}
			
			if ( ! empty( $args['uid'] ) ) {
				$yahoo['uid'] = $args['uid'];
			}
			
			$result['yahoo'] = add_query_arg( $yahoo, 'https://calendar.yahoo.com' );
		}
	} // endif check for yahoo key in link order
	
	$ordered_result = array();
	foreach ( $link_order as $service ) {
		if ( array_key_exists( $service, $result ) ) {
			$ordered_result[$service] = $result[$service];
		}
	}

	return $ordered_result;
}

/**
 * Attempts to convert a RRULE-formatted string into an array of recurrence
 * rule parameters you can add as query arguments to a Yahoo Calendar URL. It
 * assumes that your string follows RFC 5545 specifications.
 * 
 * Yahoo's recurrence patterns are very basic. The following are not supported:
 * COUNT, BYSECOND, BYMINUTE, BYHOUR, BYWEEKNO, BYMONTHDAY, BYYEARDAY, BYSETPOS
 *
 * @link https://github.com/InteractionDesignFoundation/add-event-to-calendar-docs/blob/master/services/yahoo.md
 *
 * @since 2.0.0
 * 
 * @param string $rrule_string	An RRULE string for your event.
 * @return array {
 * 		Array with the following keys on success, emtpy array on failure.
 * 		
 * 		@type string $RPAT		The Yahoo-formatted RPAT parameter.
 * 		@type stirng $REND		The Yahoo-formatted REND parameter, RRULE "UNTIL".
 * }
 */
function jeremy_get_yahoo_rrule( $rrule_string ) {
	$rrule_pairs = explode( ';', $rrule_string );
	$rrules = array();
	foreach ( $rrule_pairs as $rrule_pair ) {
		$pair = explode( '=', $rrule_pair );
		// e.g., "FREQ" => "DAILY"
		$rrules[$pair[0]] = $pair[1];
	}
	
	$unsupported = array( 'COUNT', 'BYSECOND', 'BYMINUTE', 'BYHOUR', 'BYWEEKNO', 'BYMONTHDAY', 'BYYEARDAY', 'BYSETPOS' );
	/* If any of the unsupported keys are here, it's better to cancel then to make a date link with the wrong recurring format. */
	if ( array_intersect_key( $rrules, $unsupported ) ) {
		return array(); }
	
	// Yahoo needs a number -- 1 or even 0 is fine.
	$rrules['INTERVAL'] = array_key_exists( 'INTERVAL', $rrules ) ? $rrules['INTERVAL'] : '1';
	
	// RRULE keyword => (Yahoo keyword, max allowed interval)
	$yahoo_freqs = array(
		'DAILY' 	=> array( 'Dy', 99 ),
		'WEEKLY' 	=> array( 'Wk', 52 ),
		'MONTHLY' => array( 'Mh', 20 ),
		'YEARLY' 	=> array( 'Yr', 20 ),
	);
	
	// No "SECONDLY" and the like.
	if ( ! array_key_exists( $rrules['FREQ'], $yahoo_freqs ) ) {
		return array(); }
		
	// Yahoo links will not work at all if the interval is too high.
	if ( $rrules['INTERVAL'] > $yahoo_freqs[$rrules['FREQ']] ) {
		return array(); }
	
	$yahoo_rrules = array(
		// e.g., "1Dy" or "2Yr"
		'RPAT' => strval( $rrules['INTERVAL'] ) . $rrules['FREQ'],
	);
	
	if ( array_key_exists( 'BYDAY', $rrules ) ) {
		// e.g. "MO", "WE", "FR"
		$days = explode( ',', $rrules['BYDAY'] );

		if ( $rrules['FREQ'] === 'MONTHLY' ) {
			// Yahoo only supports picking one BYDAY for monthly events.
			if ( count( $days ) > 1 ) {
				return array(); }
			
			/* ...And you can't use the 5th week or a relative occurence. You should
			be able to use "last" but I don't know how to trigger it via URL. */
			if ( preg_match( '~(-?[0-9]+)~', $days[0], $weeknum ) ) {
				if ( $weeknum[0] < 0 || $weeknum[0] > 4 ) {
					return array(); }
			}
		}
		
		$yahoo_rrules['RPAT'] .= implode( '', $days );
	}
	
	if ( array_key_exists( 'UNTIL', $rrules ) ) {
		$yahoo_rrules['REND'] = $rrules['UNTIL'];
	}
	
	return $yahoo_rrules;
}

/**
 * Prints some "Add to Calendar" links for an event listing.
 *
 * @since 2.0.0
 * 
 * @param array $links The array of links from {@see jeremy_get_addtocal_links}.
 */
function jeremy_addtocal_button( $links ) {
	if ( ! is_array( $links ) ) {
		return;
	}
	
	$labels = array(
		'google_cal' 		=> __( 'Google Calendar', 'jeremy' ),
		'yahoo' 				=> __( 'Yahoo Calendar', 'jeremy' ),
		'office365' 		=> __( 'Office 365', 'jeremy' ),
		'outlook_web' 	=> __( 'Outlook.com', 'jeremy' ),
		'apple' 				=> __( 'Apple Calendar', 'jeremy' ),
		'outlook' 			=> __( 'Outlook', 'jeremy' ),
		'other' 				=> __( 'Other', 'jeremy' ),
	);
	
	?>
	<div class="dropdown__container" role="presentation">
		<button class="button-ignore button-dropdown button-secondary button-addtocal">
			<?php esc_html_e( 'Add to Calendar', 'jeremy' ); ?>
			<?php echo jeremy_get_svg( array(
				'img' 	 => 'nav-toggle',
				'inline' => true,
			) ); ?>
		</button>
		<ul class="nav-dropdown nav__list hidden">
			<?php foreach ( $links as $service => $link ) {
				if ( ! empty( $link ) ) { ?>
					<li class="nav-dropdown__item nav-dropdown__item-<?php echo esc_attr( $service ); ?>">
						<a class="nav-dropdown__link nav-dropdown__link-<?php echo esc_attr( $service ); ?>" href="<?php echo $link; ?>">
							<?php echo esc_html( $labels[$service] ); ?>
						</a>
					</li>
				<?php }	
			} ?>
		</ul>
	</div>
	<?php
}

if ( ! function_exists( 'jeremy_copyright' ) ) :
/**
 * Prints the site copyright line, either a custom line of text set by the user
 * in the Customizer, or the default "(C) 2020 So-and-so. All rights reserved."
 *
 * @since 2.0.0
 */
function jeremy_copyright() {
	echo esc_html( get_theme_mod( 'copyright',
		/* translators: %1$s is the year, %1$s is the site name */
		sprintf( __( '&copy; %1$s %2$s. All rights reserved.', 'jeremy' ),
		date( 'Y' ), get_bloginfo( 'name' ) ) ) );
}
endif;

if ( ! function_exists( 'jeremy_rss_output' ) ) :
/**
 * Prints entries from an RSS url or feed object. It's identical to
 * {@see wp_widget_rss_output}, but with cleaner HTML markup.
 *
 * @since 2.0.0
 * 
 * @param string|array|object $rss  RSS url or object.
 * @param array               $args Widget arguments.
 */
function jeremy_rss_output( $rss, $args = array() ) {
	if ( is_string( $rss ) ) {
		$rss = fetch_feed( $rss );
	} elseif ( is_array( $rss ) && isset( $rss['url'] ) ) {
		$args = $rss;
		$rss  = fetch_feed( $rss['url'] );
	} elseif ( ! is_object( $rss ) ) {
		return;
	}

	if ( is_wp_error( $rss ) ) {
		if ( is_admin() || current_user_can( 'manage_options' ) ) {
			echo '<p><strong>' . esc_html__( 'RSS Error:', 'jeremy' ) . '</strong> ' . $rss->get_error_message() . '</p>';
		}
		return;
	}
	
	$rss_args = wp_parse_args( $args, array(
		'show_author'  => 0,
		'show_date'    => 0,
		'show_summary' => 0,
		'items'        => 0,
	) );

	$items = (int) $rss_args['items'];
	if ( $items < 1 || 20 < $items ) {
		$items = 10;
	}
	
	$show_summary = (int) $rss_args['show_summary'];
	$show_author  = (int) $rss_args['show_author'];
	$show_date    = (int) $rss_args['show_date'];

	if ( ! $rss->get_item_quantity() ) {
		echo '<p>' . esc_html__( 'Unable to fetch RSS feed. Please try again later.', 'jeremy' ) . '</p>';
		$rss->__destruct();
		unset( $rss );
		return;
	}

	echo '<ul>';
	
	foreach ( $rss->get_items( 0, $items ) as $item ) {
		$link = $item->get_link();
		while ( stristr( $link, 'http' ) !== $link ) {
			$link = substr( $link, 1 );
		}
		$link = esc_url( strip_tags( $link ) );

		$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
		if ( empty( $title ) ) {
			$title = esc_html__( 'Untitled', 'jeremy' );
		}
		if ( $link !== '' ) {
			$title = "<a href='{$link}'>{$title}</a>";
		}

		$author = '';
		if ( $show_author ) {
			$author = $item->get_author();
			if ( is_object( $author ) ) {
				$author = esc_html( strip_tags( $author->get_name() ) );
				if ( $show_date ) {
					$author .= ' &mdash; ';
				}
				$author = "<span class='widget-rss__item__author'>{$author}</span>";
			}
		}

		$date = '';
		if ( $show_date ) {
			$date = $item->get_date( 'U' );
			if ( $date ) {
				$nice_date = date_i18n( get_option( 'date_format' ), $date );
				$date = "<time datetime='{$date}' class='widget-rss__item__date'>{$nice_date}</time>";
			}
		}

		$summary = '';
		if ( $show_summary ) {
			$summary = html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) );
			if ( has_filter( 'jeremy_rss_widget_excerpt' ) ) {
				/**
				 * Filters the RSS widget entry excerpt text. If no functions are
				 * hooked to this filter, by default the text will be limited to the
				 * first 10 words using {@see jeremy_truncate}. It's wrapped in a
				 * paragraph tag after this filter is called.
				 *
				 * @since 2.0.0
				 * 
				 * @param string $summary		The full RSS item description.
				 * @return string						The filtered RSS item description.
				 */
				$summary = apply_filters( 'jeremy_rss_widget_excerpt', $summary );
			} else {
				$summary = esc_attr( jeremy_truncate( $summary, 10, true ) );
			}
			$summary = "<p class='widget-rss__item__text'>{$summary}</p>";
		}

		?>
		<li>
			<article class="widget-rss__item">
				<h4 class='widget-rss__item__title'><?php echo $title; ?></h4>
				<div class="widget-rss__item__meta entry__meta-postdate" role="presentation">
					<?php echo $author; ?>
					<?php echo $date; ?>
				</div>
				<?php echo $summary; ?>
			</article>
		</li>
		<?php
	}
	echo '</ul>';

	if ( ! is_wp_error( $rss ) ) {
		$rss->__destruct();
	}
	unset( $rss );
}
endif;

/**
 * Adds a query string to get the RSS2 or Event Organiser feed for a page.
 *
 * @since 2.0.0
 * 
 * @param  boolean $event 			 Whether to use the Event Organiser feed type.
 * 															 If false (the default) or Event Organiser is
 * 															 not active, it will use the built-in RSS2.
 * @param  string|boolean $link  A link to get the feed for, default the current
 * 															 page with all its existing query strings. 
 * @return string								 The requested feed link.
 */
function jeremy_get_feed_link( $event = false, $link = false ) {
	$link = $link ?: '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$feed = $event && defined( 'EVENT_ORGANISER_VER' ) ? 'eo-events' : 'rss2';
	return add_query_arg( 'feed', $feed, $link );
}

if ( ! function_exists( 'jeremy_feed_button' ) ) :
/**
 * This is a wrapper for {@see jeremy_get_feed_link} that prints a styled link.
 *
 * @since 2.0.0
 *
 * @param bool 				$event Whether this is an Event Organiser feed button. If
 * 													 false (the default) or the Event Organiser plug-in
 * 													 is not being used, the text will say "Subscribe to
 * 													 RSS" and link to an RSS feed. Otherwise, it will
 * 													 say "Download iCal" and link to the ICS file.
 * @param string|bool $link	 A link to pass to {@see jeremy_get_feed_link}. If
 * 													 false (default), the current page is used.
 * @param string			$alt	 Text to use for the link's aria-label, if any.
 */
function jeremy_feed_button( $event = false, $link = false, $alt = false ) {
	$feed = jeremy_get_feed_link( $event, $link );
	$text = $event && defined( 'EVENT_ORGANISER_VER' ) ? __( 'Download iCal', 'jeremy' ) : __( 'Subscribe to RSS', 'jeremy' );
	$alt = $alt ? 'aria-label="' . esc_attr( $alt ) . '" ' : '';
	?>
	<a <?php echo $alt; ?>class="button-secondary button-feed" href="<?php echo esc_url( $feed ); ?>">
		<?php
		if ( ! $event || ! defined( 'EVENT_ORGANISER_VER' ) ) {
			echo jeremy_get_svg( array(
				'img' 		=> 'social-rss',
				'inline'	=> true,
			) );
		} ?>
		<?php echo esc_html( $text ); ?></a>
	<?php
}
endif;