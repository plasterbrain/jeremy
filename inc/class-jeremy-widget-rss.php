<?php
/**
 * Widgets API: RSS Widget
 * 
 * The core bundled RSS widget with identical functionality and some design changes.
 * {@see WP_Widget_RSS} for descriptions of the methods.
 *
 * @package Jeremy
 * @subpackage Widgets
 * @since 1.0.0
 */
class Jeremy_Widget_RSS extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Entries from an RSS or Atom feed.', 'jeremy' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array( 'width' => 400, 'height' => 200 );
		parent::__construct( 'rss', __( 'RSS' ), $widget_ops, $control_ops );
    }
	public function widget( $args, $instance ) {
		if ( isset($instance['error']) && $instance['error'] )
			return;
		$url = ! empty( $instance['url'] ) ? $instance['url'] : '';
		while ( stristr($url, 'http') != $url )
			$url = substr($url, 1);
		if ( empty($url) )
			return;
		if ( in_array( untrailingslashit( $url ), array( site_url(), home_url() ) ) )
			return;
		$rss = fetch_feed($url);
		$title = $instance['title'];
		$desc = '';
		$link = '';
		if ( ! is_wp_error($rss) ) {
			$desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
			if ( empty($title) )
				$title = strip_tags( $rss->get_title() );
			$link = strip_tags( $rss->get_permalink() );
			while ( stristr($link, 'http') != $link )
				$link = substr($link, 1);
		}
		if ( empty($title) )
			$title = empty($desc) ? __( 'Unknown Feed', 'jeremy' ) : $desc;
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$url = strip_tags( $url );
		$icon = jeremy_get_svg( array( 'img'=>'rss', 'alt'=>__( 'RSS Feed', 'jeremy' ) ) );
		$title = '<a class="widget-rss flex" href="' . esc_url( $url ) . '">' . $icon . esc_html( $title ) . '</a>';
		
		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
		$this->jeremy_widget_rss_output( $rss, $instance );
		echo $args['after_widget'];

		if ( ! is_wp_error($rss) )
			$rss->__destruct();
		unset($rss);
	}
	public function jeremy_widget_rss_output( $rss, $args = array() ) {
		if ( is_string( $rss ) ) {
			$rss = fetch_feed($rss);
		} elseif ( is_array($rss) && isset($rss['url']) ) {
			$args = $rss;
			$rss = fetch_feed($rss['url']);
		} elseif ( !is_object($rss) ) {
			return;
		}
		if ( is_wp_error($rss) ) {
			if ( is_admin() || current_user_can('manage_options') )
				echo '<p><strong>' . __( 'Error:', 'jeremy' ) . '</strong> ' . $rss->get_error_message() . '</p>';
			return;
		}
		$default_args = array( 'show_author' => 0, 'show_date' => 0, 'show_summary' => 0, 'items' => 0 );
		$args = wp_parse_args( $args, $default_args );

		$items = (int) $args['items'];
		if ( $items < 1 || 20 < $items )
			$items = 10;
		$show_summary  = (int) $args['show_summary'];
		$show_author   = (int) $args['show_author'];
		$show_date     = (int) $args['show_date'];

		if ( !$rss->get_item_quantity() ) {
			echo '<ul><li>' . __( 'An error has occurred, which probably means the feed is down. Try again later.', 'jeremy' ) . '</li></ul>';
			$rss->__destruct();
			unset($rss);
			return;
		}
		echo '<ul>';
		foreach ( $rss->get_items( 0, $items ) as $item ) {
			$link = $item->get_link();
			while ( stristr( $link, 'http' ) != $link ) {
				$link = substr( $link, 1 );
			}
			$link = esc_url( strip_tags( $link ) );

			$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
			if ( empty( $title ) ) {
				$title = __( 'Untitled', 'jeremy' );
			} else {
				$title = wp_trim_words( $title, 6, '&hellip;' );
			}
			$desc = @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) );
			$desc = esc_attr( wp_trim_words( $desc, 10, '&hellip;' ) );
			$summary = '';
			if ( $show_summary ) {
				$summary = $desc;
				if ( '...' == substr( $summary, -3 ) ) {
					$summary = substr( $summary, 0, -3 ) . '&hellip;';
				}
				$summary = '<p class="rss-summary">' . esc_html( $summary ) . '</p>';
			}

			$date = '';
			if ( $show_date ) {
				$date = $item->get_date( 'U' );

				if ( $date ) {
					$date = ' <p><em><time class="rss-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</time></em></p>';
				}
			}
			$author = '';
			if ( $show_author ) {
				$author = $item->get_author();
				if ( is_object($author) ) {
					$author = $author->get_name();
					$author = ' <cite>' . esc_html( strip_tags( $author ) ) . '</cite>';
				}
			}
			if ( $link == '' ) {
				echo "<li>$title{$date}{$summary}{$author}</li>";
			} elseif ( $show_summary ) {
				echo "<li><a class='rss-title' href='$link'>$title</a>{$date}{$summary}{$author}</li>";
			} else {
				echo "<li><a class='rss-title' href='$link'>$title</a>{$date}{$author}</li>";
			}
		}
		echo '</ul>';
		$rss->__destruct();
		unset($rss);
	}
	public function update( $new_instance, $old_instance ) {
		$testurl = ( isset( $new_instance['url'] ) && ( !isset( $old_instance['url'] ) || ( $new_instance['url'] != $old_instance['url'] ) ) );
		return wp_widget_rss_process( $new_instance, $testurl );
	}
	public function form( $instance ) {
		if ( empty( $instance ) ) {
			$instance = array( 'title' => '', 'url' => '', 'items' => 10, 'error' => false, 'show_summary' => 0, 'show_author' => 0, 'show_date' => 0 );
		}
		$instance['number'] = $this->number;
		wp_widget_rss_form( $instance );
	}
}