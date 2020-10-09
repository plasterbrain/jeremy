<?php
/**
 * RSS Widget
 * 
 * The markup produced by the built-in RSS widget is... yikes. Somehow the
 * HTML5 version is even worse?
 * 
 * @see WP_Widget_RSS
 *
 * @package Jeremy
 * @subpackage Widgets
 * @since 1.0.0
 */

if ( ! class_exists( 'Jeremy_Widget_RSS' ) ) :
class Jeremy_Widget_RSS extends WP_Widget {
	public function __construct() {
		$widget_ops  = array(
			'description' => esc_attr__( 'Entries from an RSS feed.', 'jeremy' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
		 'width'  => 400,
		 'height' => 200,
		);
		parent::__construct( 'rss', __( 'RSS', 'jeremy' ), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {
		if ( isset( $instance['error'] ) && $instance['error'] ) {
			return;
		}
 
		$feed_link = ! empty( $instance['url'] ) ? $instance['url'] : '';
		while ( stristr( $feed_link, 'http' ) !== $feed_link ) {
			$feed_link = substr( $feed_link, 1 );
		}
 
		if ( empty( $feed_link ) ) {
			return;
		}
 
		// Self-URL destruction sequence.
		if ( in_array( untrailingslashit( $feed_link ), array( site_url(), home_url() ), true ) ) {
			return;
		}
 
		$rss   = fetch_feed( $feed_link );
		$title = array_key_exists( 'title', $instance ) ? $instance['title'] : '';
		$desc  = '';
		$link  = '';
 
		if ( is_wp_error( $rss ) ) {
			if ( current_user_can( 'edit_theme_options' ) ) {
				jeremy_rss_output( $rss, $instance );
			}
			return;
		}
		
		$desc = esc_attr( strip_tags( html_entity_decode( $rss->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) ) ) );
		
		if ( empty( $title ) ) {
			$title = strip_tags( $rss->get_title() );
		}
		
		// Link of the feed origin site, not the feed itself.
		$link = strip_tags( $rss->get_permalink() );
		while ( stristr( $link, 'http' ) !== $link ) {
			$link = substr( $link, 1 );
		}
		
		if ( empty( $title ) ) {
			$title = ! empty( $desc ) ? $desc : __( 'Unknown Feed' );
		}
		
		/* This widget is echoed in the profile sidebar. */
		if ( ! array_key_exists( 'bp', $instance ) || ! $instance['bp'] ) {
			// Y'all are breaking my title :T
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		}
		
		$feed_link  = esc_url( strip_tags( $feed_link ) );
		if ( $title ) {
			$rss_title_id = $this->id . '_title';
			$rss_icon = jeremy_get_svg( array(
				'img' 	 => 'social-rss',
				'alt' 	 => esc_attr__( 'RSS Feed', 'jeremy' ),
				'class'  => 'widget-rss__icon__svg',
				'inline' => true,
			) );
			$title = wp_kses_post( $title );
			$link = esc_url( $link );
			
			if ( array_key_exists( 'bp', $instance ) && $instance['bp'] ) {
				$title_text = $title;
				$title = $rss_icon . $title;
			} else {
				$title_text = $title;
				$title = "{$rss_icon}<a id='{$rss_title_id}' class='widget-rss__link-title' href='{$link}'>{$title}</a>";
			}
		}
 
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
 
		jeremy_rss_output( $rss, $instance );
		
		/* translators: %s is the name of an external website, "something.com" */
		echo '<a alt="' . sprintf( esc_attr__( 'RSS feed at %s', 'jeremy' ), $title_text ) . '" class="button-ignore button-secondary button-feed" href="' . esc_url( $feed_link ) . '">' .
			jeremy_get_svg( array(
				'img' 		=> 'social-rss',
				'inline'	=> true,
			) ) .
			esc_html__( 'Download RSS Feed', 'jeremy' ) .
		'</a>';
		
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$testurl = ( isset( $new_instance['url'] ) &&
							 ( ! isset( $old_instance['url'] ) ||
							 ( $new_instance['url'] !== $old_instance['url'] ) ) );
		return wp_widget_rss_process( $new_instance, $testurl );
	}

	public function form( $instance ) {
		if ( empty( $instance ) ) {
			$instance = array(
				'title'        => '',
				'url'          => '',
				'items'        => 5,
				'error'        => false,
				'show_summary' => 0,
				'show_author'  => 0,
				'show_date'    => 0,
			);
		}
		$instance['number'] = $this->number;
 
		wp_widget_rss_form( $instance );
	}
}
endif;