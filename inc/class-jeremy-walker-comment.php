<?php
/**
 * Comment API: comment walker
 * 
 * The core nav menu walker modified for styling purposes. {@see Walker_Comment}
 * for descriptions of the class properties and methods.
 *
 * @package Jeremy
 * @subpackage Walkers
 * @since 1.0.0
 */
class Jeremy_Walker_Comment extends Walker {
	public $tree_type = 'comment';
	public $db_fields = array ('parent' => 'comment_parent', 'id' => 'comment_ID');

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;
		switch ( $args['style'] ) {
			case 'div':
				break;
			case 'ol':
				$output .= '<ol class="children">' . "\n";
				break;
			case 'ul':
			default:
				$output .= '<ul class="children">' . "\n";
				break;
		}
	}
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;
		switch ( $args['style'] ) {
			case 'div':
				break;
			case 'ol':
				$output .= "</ol><!-- .children -->\n";
				break;
			case 'ul':
			default:
				$output .= "</ul><!-- .children -->\n";
				break;
		}
	}
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( !$element )
			return;
		$id_field = $this->db_fields['id'];
		$id = $element->$id_field;
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		if ( $max_depth <= $depth + 1 && isset( $children_elements[$id]) ) {
			foreach ( $children_elements[ $id ] as $child )
				$this->display_element( $child, $children_elements, $max_depth, $depth, $args, $output );
			unset( $children_elements[ $id ] );
		}
	}
	public function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
		$depth++;
		$GLOBALS['comment_depth'] = $depth;
		$GLOBALS['comment'] = $comment;
		if ( !empty( $args['callback'] ) ) {
			ob_start();
			call_user_func( $args['callback'], $comment, $args, $depth );
			$output .= ob_get_clean();
			return;
		}
		if ( ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) && $args['short_ping'] ) {
			ob_start();
			$this->ping( $comment, $depth, $args );
			$output .= ob_get_clean();
		} elseif ( 'html5' === $args['format'] ) {
			ob_start();
			$this->html5_comment( $comment, $depth, $args );
			$output .= ob_get_clean();
		} else {
			ob_start();
			$this->comment( $comment, $depth, $args );
			$output .= ob_get_clean();
		}
	}
	public function end_el( &$output, $comment, $depth = 0, $args = array() ) {
		if ( !empty( $args['end-callback'] ) ) {
			ob_start();
			call_user_func( $args['end-callback'], $comment, $args, $depth );
			$output .= ob_get_clean();
			return;
		}
		if ( 'div' == $args['style'] )
			$output .= "</div>\n";
		else
			$output .= "</li>\n";
	}
	protected function ping( $comment, $depth, $args ) {
		$tag = ( 'div' == $args['style'] ) ? 'div' : 'li';
		?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( '', $comment ); ?>>
			<div class="comment-body">
				<?php _e( 'Pingback:', 'jeremy' ); ?> <?php comment_author_link( $comment ); ?> <?php edit_comment_link( __( 'Edit', 'jeremy' ), '<span class="edit-link">', '</span>' ); ?>
			</div>
		<?php
	}
	protected function html5_comment( $comment, $depth, $args ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
		?>
		<<?php echo $tag; ?> <?php comment_class( !$this->has_children && $depth == 1? 'single-comment' : '', $comment ); ?>>
			<article id="comment-<?php comment_ID(); ?>">
				<div class="comment-avatar"> <?php
					if ( get_comment_author_url() !== '') echo '<a href="' . get_comment_author_url() . '" title="' . get_comment_author() . '" rel="external">';
						if ( 0 != $args['avatar_size'] ) {
							$avatar_size = $args['avatar_size'];
							if ( $depth > 1) $avatar_size = $avatar_size * .7;
							echo get_avatar( $comment, $avatar_size );
						}
					if ( get_comment_author_url() !== '') echo '</a>'; ?>
				</div>
				<div class="comment-body">
					<footer>
						<div class="comment-metadata">
							<address>
								<?php comment_author_link( $comment ); ?>
							</address>
							<time datetime="<?php comment_time( 'c' ); ?>"> <?php
								/* translators: 1: comment date, 2: comment time */
									printf( __( '%1$s at %2$s', 'jeremy' ), get_comment_date( '', $comment ), get_comment_time() ); ?>
							</time>
						</div>
						<div class="comment-controls">
							<?php jeremy_comment_links();?>
						</div>
					</footer><!-- .comment-metadata -->
					<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-moderation"><?php _e( '(Your comment is awaiting moderation.)', 'jeremy' ); ?></p>
					<?php endif; ?>
					<?php comment_text(); ?>
					<div class="comment-controls">
						<?php comment_reply_link( array_merge( $args, array(
							'add_below' => 'comment',
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
						) ) );
						?>
					</div>
				</div><!-- .comment-body -->
			</article><!-- .comment -->
			<?php
			if ( $this->has_children && $depth == 1 ) {
				echo '<hr>';
			}
	}
}