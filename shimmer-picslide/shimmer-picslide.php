<?php
/*
Plugin Name: PicSlide
Description: Show a slideshow with JS effects. Uses images uploaded to the current post. Available as widget and shortcode [picslide].
Author: Pasi Lallinaho // Shimmer Project
Version: 0.2
Author URI: http://knome.fi/
Plugin URI: http://shimmerproject.org/
*/

/*  On plugin activation, create options for default values if needed
 *
 */

register_activation_hook( __FILE__, 'JSPicSliderActivatePlugin' );

function JSPicSliderActivatePlugin( ) {
	add_option( 'picslide_default_size', 'medium' );
}

/*  Add new image size
 *
 */

if( function_exists( 'add_image_size' ) ) {
	add_image_size( 'picslide', 600, 9999 );
}

/*  Include default CSS
 *
 */

add_action( 'wp_head', 'JSPicSliderHead' );

function JSPicSliderHead( ) {
	print "<link rel=\"stylesheet\" href=\"" . plugins_url( 'shimmer-picslide' ) . "/js-pic-slider.css\" />\n";
}

/*  Include scripts
 *
 */

add_action( 'init', 'JSPicSliderInit' );

function JSPicSliderInit( ) {
	$x = plugins_url( 'shimmer-picslide' );

	wp_enqueue_script( 'jquery', $x . "/jquery-1.5.2.min.js", false, "1.5.2" );
	wp_enqueue_script( 'js-pic-slider', $x . "/js-pic-slider.js", array( "jquery" ), "0.1" );
}

/*  Add widget
 *
 */

add_action( 'widgets_init', create_function( '', 'return register_widget("JSPicSliderWidget");' ) );

class JSPicSliderWidget extends WP_Widget {
	/** constructor */
	function JSPicSliderWidget() {
		$widget_ops = array( "description" => __( 'Show a slideshow on your blog, built from uploaded images to the current post.', 'shimmer-picslide' ) );
		$control_ops = array( "width" => 200 );
		parent::WP_Widget( false, $name = 'PicSlide', $widget_ops, $control_ops );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;

		// Print all the items here...
		if( is_front_page( ) ) {
			$post_id = get_option( 'page_on_front' );
		} else {
			global $post;
			$post_id = $post->ID;
		}

		$args = array(
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order_by' => 'menu_order'
		);

		$attachments = get_children( $args );

		if( $title && $attachments ) { echo $before_title . $title . $after_title; }
		print '<div class="picslide-wrap">';
		print '<ul class="picslide">';

		foreach( $attachments as $a ) {
			if( $i == 0 ) {
				print "<li class=\"first active\">"; $i = 1;
			} else {
				print "<li>";
			}

			if( strpos( $instance['size'], "," ) ) {
				list( $sw, $sh ) = split( ",", $instance['size'] );
				$instance['size'] = array( $sw, $sh );
			}

			/* http://codex.wordpress.org/Function_Reference/wp_get_attachment_image */
			if( !$instance['size'] ) { $size = get_option( 'picslide_default_size' ); } else { $size = $instance['size']; }
			print wp_get_attachment_image( $a->ID, $size );
			print "<p>" . $a->post_title . "</p>";
			if( $a->post_content ) {
				print "<p>" . $a->post_content  . "</p>";
			}
			print "</li>";
		}

		print '</ul>';
		print '<a href="#" class="control-left">«</a>';
		print '<a href="#" class="control-right">»</a>';
		print '</div>';
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['size'] = strip_tags( $new_instance['size'] );
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$title = esc_attr( $instance['title'] );
		$size = esc_attr( $instance['size'] );

		if( !$size ) { $size = get_option( 'picslide_default_size' ); }
		?>

		<p>
			<label style="display: inline;" for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'shimmer-picslide' ); ?><br />
				<input style="width: 220px;" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		<p>
			<label style="display: inline;" for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size:', 'shimmer-picslide' ); ?><br />
				<input style="width: 220px;" id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" type="text" value="<?php echo $size; ?>" />
				<p style="font-size: 85%;"><?php _e( 'Size should be either a string keyword (thumbnail, medium, large or full) or a 2-item array representing width and height in pixels, e.g. array(32,32).', 'shimmer-picslide' ); ?></p>
			</label>
		</p>

		<?php 
	}
}

/*  Add shortcode
 *
 */

add_shortcode( 'picslide', 'JSPicSliderShortCode' );

function JSPicSliderShortcode( $atts, $content, $code ) {
	extract( shortcode_atts( array(
		'size' => '',
		'parent' => ''
	), $atts ) );

	if( strpos( $size, "," ) ) {
		list( $sw, $sh ) = split( ",", $size );
		$size = array( $sw, $sh );
	}

	if( !$parent ) {
		global $post;
		if( is_front_page( ) ) {
			$id = get_option( 'page_on_front' );
		} else {
			$id = $post->ID;
		}
	} else {
		$id = $parent;
	}

	$args = array(
		'post_parent' => $id,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order_by' => 'menu_order'
	);

	$attachments = get_children( $args );

	$out .= '<div class="picslide">';
	$out .= '<ul class="picslide">';

	foreach( $attachments as $a ) {
		if( $i == 0 ) {
			$out .= "<li class=\"first active\">"; $i = 1;
		} else {
			$out .= "<li>";
		}

		/* http://codex.wordpress.org/Function_Reference/wp_get_attachment_image */
		if( !$size ) { $size = get_option( 'picslide_default_size' ); }
		$out .= wp_get_attachment_image( $a->ID, $size );
		$out .= "<p>" . $a->post_title . "</p>";
		if( $a->post_content ) {
			$out .= "<p>" . $a->post_content  . "</p>";
		}
		$out .= "</li>";
	}

	$out .= '</ul>';
	$out .= '<a href="#" class="control control-left"></a>';
	$out .= '<a href="#" class="control control-right"></a>';
	$out .= '</div>';

	return $out;
}

?>
