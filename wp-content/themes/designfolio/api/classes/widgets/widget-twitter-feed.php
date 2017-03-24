<?php

// -------------------------
// Twitter Feed Widget Class
// -------------------------

class pc_twitter_feed_widget extends WP_Widget {

	// Constructor - process new widget
	function pc_twitter_feed_widget(){
        $widget_ops = array('classname' => 'pc_twitter_feed_widget', 'description' => __('Show your latest tweets.', 'presscoders' ) ); 
        $this->WP_Widget('pc_twitter_feed_widget_'.PC_THEME_NAME_SLUG, __( 'Twitter Feed', 'presscoders' ), $widget_ops);
	}

	// Build widget options form
	function form($instance){

        $defaults = array( 'title' => '', 'twitter_id' => '', 'num_tweets' => '4', 'show_timestamp' => true );
        $instance = wp_parse_args( (array) $instance, $defaults );
		$title = strip_tags($instance['title']);
		$twitter_id = strip_tags($instance['twitter_id']);

		if ( !isset($instance['num_tweets']) || !$num_tweets = (int) $instance['num_tweets'] )
			$num_tweets = 4;

        $show_timestamp = strip_tags($instance['show_timestamp']);
        ?>
			<p><?php _e('Title', 'presscoders' ) ?>: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>"  type="text" value="<?php echo esc_attr($title); ?>" /></p>
            <p><?php _e('Twitter ID', 'presscoders' ) ?>: <input class="widefat" name="<?php echo $this->get_field_name('twitter_id'); ?>" type="text" value="<?php echo esc_attr($twitter_id); ?>" /></p>
            <p><label><?php _e('Number of Tweets', 'presscoders' ) ?>: <input name="<?php echo $this->get_field_name('num_tweets'); ?>" type="text" value="<?php echo esc_attr($num_tweets); ?>" size="3" /></label></p>
			<p><label><input type="checkbox" value="1" <?php checked( $show_timestamp, '1' ); ?> name="<?php echo $this->get_field_name( 'show_timestamp' ); ?>" />&nbsp;<?php _e('Show Timestamp?', 'presscoders' ) ?></label></p>
        <?php
	}

	// Save widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['twitter_id'] = strip_tags($new_instance['twitter_id']);
		$instance['num_tweets'] = (int) ($new_instance['num_tweets']);
        $instance['show_timestamp'] = strip_tags($new_instance['show_timestamp']);
 
        return $instance;
    }
 
    // Display widget
    function widget($args, $instance) {

        extract($args);
		// url to images folder
 
        echo $before_widget;
		$title = $instance['title'];
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

		$twitter_id = $instance['twitter_id'];
		$num_tweets = $instance['num_tweets'];
        $show_timestamp = $instance['show_timestamp'];

		if($show_timestamp == '1') { $show_timestamp = 'true'; }
		else { $show_timestamp = 'false'; }
		if ( empty( $twitter_id ) ) {
			printf( __( 'Please enter a Twitter ID in the %s Twitter Feed widget options.', 'presscoders' ), PC_THEME_NAME );
			echo $after_widget; // close if returning here, or it will be left open, corrupting the sidebar
			return;
		};

		?>

		<div id="tweets">

			<script src="http://widgets.twimg.com/j/2/widget.js"></script>
			<script>
			try{
				new TWTR.Widget({
					version: 2,
					type: 'profile',
					rpp: <?php echo $num_tweets; ?>,
					interval: 6000,
					width: 'auto',
					features: {
						scrollbar: false,
						loop: false,
						live: false,
						hashtags: true,
						timestamp: <?php echo $show_timestamp; ?>,
						avatars: false,
						behavior: 'all'
					}
				}).render().setUser('<?php echo $twitter_id; ?>').start();
			}
			catch(error){
				document.write("Twitter not responding, please try again in a few moments.");		
			}
			</script>

		</div>

		<?php
        echo $after_widget;
    }
}

?>