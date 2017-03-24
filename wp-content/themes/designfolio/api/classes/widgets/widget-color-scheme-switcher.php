<?php

// -----------------------
//  Color Scheme Switcher
// -----------------------

class pc_color_scheme_switcher_widget extends WP_Widget {

	// Constructor - process new widget
	function pc_color_scheme_switcher_widget(){

		/* @todo Check theme supports color schemes or don't instantiate the color scheme switcher class. */

		$widget_ops = array('classname' => 'pc_color_scheme_switcher_widget', 'description' => __('Allow your site visitors to change the theme color scheme dynamically!', 'presscoders' ) );
        $this->WP_Widget('pc_color_scheme_switcher_widget_'.PC_THEME_NAME_SLUG, __( 'Color Switcher', 'presscoders' ), $widget_ops);
	}

	// Build widget options form
	function form($instance){

        $defaults = array( 'title' => __('Change Color Scheme', 'presscoders' ), 'description' => __('Try out a new color scheme. Select below to see an instant change!', 'presscoders' ) );
        $instance = wp_parse_args( (array) $instance, $defaults );
		$title = strip_tags($instance['title']);
		$description = $instance['description'];
		?>
			<p><?php _e('Title', 'presscoders' ) ?>: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
            <p><?php _e('Description', 'presscoders' ) ?>: <textarea class="widefat" name="<?php echo $this->get_field_name('description'); ?>" rows="4"><?php echo esc_attr($description); ?></textarea></p>
		<?php
	}

	// Save widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['description'] = $new_instance['description'];
       
		return $instance;
    }
 
    // Display widget
    function widget($args, $instance) {
        extract($args);
        echo $before_widget;
		$title = $instance['title'];
        $description = $instance['description'];
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

// =====================================================
// START - Handle the submitted color-switcher form code
// =====================================================

	// Add in the color scheme from theme options
	// If color switcher cookie set then use it in preference to the theme color option
	if (isset($_COOKIE[PC_THEME_NAME_SLUG."_color_scheme"])) {
		// Set color scheme from cookie value
		$col_scheme = $_COOKIE[PC_THEME_NAME_SLUG."_color_scheme"];
	}
	else {
		// set color scheme from theme options
		$options = get_option( PC_OPTIONS_DB_NAME );
		$col_scheme = $options[ PC_COLOR_SCHEME_DROPDOWN ];
	}

// ===================================================
// END - Handle the submitted color-switcher form code
// ===================================================

		?>

		<div class="color-switcher-container">
		<?php if( !empty($description) ) { ?>
			<p class="color-switcher-description"><?php echo $description; ?></p>
		<?php } ?>
		<form action="<?php echo PC_Utility::currURL(); // current page url ?>" method="post">
			<?php _e( 'Color Scheme', 'presscoders' ); ?>:&nbsp;
			<select name='<?php echo PC_THEME_NAME_SLUG; ?>_color_scheme_widget_dropdown' onchange="this.form.submit();">
				<?php
				// Grab global color scheme array
				global $pc_color_schemes;

				foreach($pc_color_schemes as $color_scheme => $value){
					echo "<option value='".$value."' ".selected($value, $col_scheme).">".$color_scheme."&nbsp;</option>";
				}
				?>
			</select>
			<input type="hidden" name="color_scheme_submitted" value="true" />
		</form>
		</div>

		<?php
        echo $after_widget;
    }
}

?>