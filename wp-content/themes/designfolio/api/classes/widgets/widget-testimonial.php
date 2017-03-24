<?php

// -------------------------
//  Testimonial Widget Class
// -------------------------

class pc_testimonial_widget extends WP_Widget {

	// Constructor - process new widget
	function pc_testimonial_widget(){
        $widget_ops = array('classname' => 'pc_testimonial_widget', 'description' => __('Showcase customer testimonials. e.g. [testimonial name="John Smith" company="Acme Inc."]..[/testimonial]', 'presscoders' ) );
		$control_ops = array( 'width' => 270);
        $this->WP_Widget('pc_testimonial_widget_'.PC_THEME_NAME_SLUG, __( 'Testimonials', 'presscoders' ), $widget_ops, $control_ops);
	}

	// Build widget options form
	function form($instance){
        $defaults = array( 'title' => '', 'testimonial_shortcodes' => '' );
        $instance = wp_parse_args( (array) $instance, $defaults );
		$title = strip_tags($instance['title']);
        $testimonial_shortcodes = $instance['testimonial_shortcodes'];
        ?>
			<p><?php _e( 'Title', 'presscoders' ) ?>: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
            <p><?php _e('Testimonial Shortcodes', 'presscoders' ) ?>: <textarea class="widefat" name="<?php echo $this->get_field_name('testimonial_shortcodes'); ?>" rows="8" cols="12"><?php echo esc_attr($testimonial_shortcodes); ?></textarea></p>
        <?php
	}

	// Save widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['testimonial_shortcodes'] = $new_instance['testimonial_shortcodes'];
 
        return $instance;
    }
 
    // Display widget
    function widget($args, $instance) {
        extract($args);
 
        echo $before_widget;
		$title = $instance['title'];
		$testimonial_shortcodes = apply_filters( 'widget_text', $instance['testimonial_shortcodes'], $instance );

		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
        echo '<div class="theme-testimonial-widget">'.$testimonial_shortcodes.'</div>';
        echo $after_widget;
    }
}

?>