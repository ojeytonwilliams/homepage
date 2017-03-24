<?php

// ---------------------
// Info Box Widget Class
// ---------------------

class pc_info_box_widget extends WP_Widget {

	// Constructor
	function pc_info_box_widget(){
        $widget_ops = array('classname' => 'pc_info_widget', 'description' => __('Display phone number, and twitter/facebook links.', 'presscoders' ) ); 
        $this->WP_Widget('pc_info_widget_'.PC_THEME_NAME_SLUG, __( 'Info Box', 'presscoders' ), $widget_ops);
	}

	// Build widget options form
	function form($instance){
        $defaults = array(  'title' => '',
							'info_description' => '',
                            'phone_number' => '',
                            'twitter_id' => '',
                            'facebook_id' => '',
                            'youtube_id' => '',
                            'googleplus_id' => '',
                            'linkedin_id' => '',
                            'flickr_id' => '',
                            'pinterest_id' => '',
                            'custom_id_1' => '',
                            'custom_img_1' => '',
							'rss_id' => '',
                            'show_search' => true
                        );
        $instance = wp_parse_args( (array) $instance, $defaults );
		$title = strip_tags($instance['title']);
		$info_description = strip_tags($instance['info_description']);
        $phone_number = strip_tags($instance['phone_number']);
        $twitter_id = strip_tags($instance['twitter_id']);
        $facebook_id = strip_tags($instance['facebook_id']);
        $rss_id = strip_tags($instance['rss_id']);
        $youtube_id = strip_tags($instance['youtube_id']);
        $googleplus_id = strip_tags($instance['googleplus_id']);
        $linkedin_id = strip_tags($instance['linkedin_id']);
        $flickr_id = strip_tags($instance['flickr_id']);
        $pinterest_id = strip_tags($instance['pinterest_id']);
		$custom_id_1 = strip_tags($instance['custom_id_1']);
        $custom_img_1 = strip_tags($instance['custom_img_1']);
		$show_search = strip_tags($instance['show_search']);
        ?>

            <style type="text/css">
                <!--
                div.scroll {
                    height: 91px;
                    overflow: auto;
                    border: 1px solid #dfdfdf;
                    background-color: #f8f8f8;
                    padding: 1px 2px 1px 2px;
                }
                div.scroll table tr {
                    height: 30px;
                }
                -->
            </style>

            <p>
            <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title', 'presscoders' ) ?></label>
			<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>

            <p>
            <label for="<?php echo $this->get_field_name('info_description'); ?>"><?php _e('Description', 'presscoders' ) ?></label>
			<input class="widefat" name="<?php echo $this->get_field_name('info_description'); ?>" type="text" value="<?php echo esc_attr($info_description); ?>" />
            </p>

            <p>
            <label for="<?php echo $this->get_field_name('phone_number'); ?>"><?php _e('Phone Number', 'presscoders' ) ?></label>
			<input class="widefat" name="<?php echo $this->get_field_name('phone_number'); ?>" type="text" value="<?php echo esc_attr($phone_number); ?>" />
            </p>

            <p><label>Available Icons</label></p>

            <div class="scroll">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'facebook.png', 'images/' ); ?>" width="24" height="24" alt="Facebook" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('facebook_id'); ?>" type="text" value="<?php echo esc_attr($facebook_id); ?>" /></td>
                        <td style="width:73px;font-size:11px;">&nbsp;<?php _e('Facebook ID', 'presscoders' ) ?></td>
                    </tr>
                    <tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'twitter.png', 'images/' ); ?>" width="24" height="24" alt="Twitter" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('twitter_id'); ?>" type="text" value="<?php echo esc_attr($twitter_id); ?>" /></td>
                        <td style="font-size:11px;">&nbsp;<?php _e('Twitter ID', 'presscoders' ) ?></td>
                    </tr>
                    <tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'youtube.png', 'images/' ); ?>" width="24" height="24" alt="YouTube" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('youtube_id'); ?>" type="text" value="<?php echo esc_attr($youtube_id); ?>" /></td>
                        <td style="font-size:11px;">&nbsp;<?php _e('YouTube ID', 'presscoders' ) ?></td>
                    </tr>
                    <tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'flickr.png', 'images/' ); ?>" width="24" height="24" alt="Flickr" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('flickr_id'); ?>" type="text" value="<?php echo esc_attr($flickr_id); ?>" /></td>
                        <td style="font-size:11px;">&nbsp;<?php _e('Flickr ID', 'presscoders' ) ?></td>
                    </tr>
                    <tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'pinterest.png', 'images/' ); ?>" width="24" height="24" alt="Pinterest" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('pinterest_id'); ?>" type="text" value="<?php echo esc_attr($pinterest_id); ?>" /></td>
                        <td style="font-size:11px;">&nbsp;<?php _e('Pinterest ID', 'presscoders' ) ?></td>
                    </tr>
					<tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'googleplus.png', 'images/' ); ?>" width="24" height="24" alt="Google+" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('googleplus_id'); ?>" type="text" value="<?php echo esc_attr($googleplus_id); ?>" /></td>
                        <td style="font-size:11px;">&nbsp;<?php _e('Google+ URL', 'presscoders' ) ?></td>
                    </tr>
                    <tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'linkedin.png', 'images/' ); ?>" width="24" height="24" alt="Linked In" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('linkedin_id'); ?>" type="text" value="<?php echo esc_attr($linkedin_id); ?>" /></td>
                        <td style="font-size:11px;">&nbsp;<?php _e('Linked In URL', 'presscoders' ) ?></td>
                    </tr>
                    <tr>
                        <td><img src="<?php echo PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'rss.png', 'images/' ); ?>" width="24" height="24" alt="RSS" /></td>
                        <td><input style="width:100%;" name="<?php echo $this->get_field_name('rss_id'); ?>" type="text" value="<?php echo esc_attr($rss_id); ?>" /></td>
                        <td style="font-size:11px;">&nbsp;<?php _e('RSS URL', 'presscoders' ) ?></td>
                    </tr>
                </table>
            </div>

			<h4>Add Custom Icon</h4>
			
			<p>
			<label for="<?php echo $this->get_field_name('custom_id_1'); ?>"><?php _e('Link URL', 'presscoders' ) ?></label>
			<input class="widefat" name="<?php echo $this->get_field_name('custom_id_1'); ?>" type="text" value="<?php echo esc_attr($custom_id_1); ?>" />
			</p>

			<p>
			<label for="<?php echo $this->get_field_name('custom_img_1'); ?>"><?php _e('Image URL', 'presscoders' ) ?> <em>(32 x 32 pixels)</em></label>
			<input class="widefat" name="<?php echo $this->get_field_name('custom_img_1'); ?>" type="text" value="<?php echo esc_attr($custom_img_1); ?>" />	
			</p>
            
            <p>
            <label for="id_show_search"><?php _e('Show Search?', 'presscoders' ) ?></label>
            <input type="checkbox" value="1" <?php checked( $show_search, '1' ); ?> name="<?php echo $this->get_field_name( 'show_search' ); ?>" id="id_show_search" />
            </p>
            
        <?php
	}

	// Save widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['info_description'] = strip_tags($new_instance['info_description']);
		$instance['phone_number'] = strip_tags($new_instance['phone_number']);
        $instance['twitter_id'] = strip_tags($new_instance['twitter_id']);
        $instance['facebook_id'] = strip_tags($new_instance['facebook_id']);
        $instance['rss_id'] = strip_tags($new_instance['rss_id']);
        $instance['youtube_id'] = strip_tags($new_instance['youtube_id']);
        $instance['googleplus_id'] = strip_tags($new_instance['googleplus_id']);
        $instance['linkedin_id'] = strip_tags($new_instance['linkedin_id']);
        $instance['flickr_id'] = strip_tags($new_instance['flickr_id']);
        $instance['pinterest_id'] = strip_tags($new_instance['pinterest_id']);
		$instance['custom_id_1'] = strip_tags($new_instance['custom_id_1']);
        $instance['custom_img_1'] = strip_tags($new_instance['custom_img_1']);
		$instance['show_search'] = strip_tags($new_instance['show_search']);
 
        return $instance;
    }
 
	// Display widget
    function widget($args, $instance) {

        extract($args);
		echo $before_widget;

 		$title = $instance['title'];
 		$info_description = $instance['info_description'];
		$phone_number = $instance['phone_number'];
        $twitter_id = $instance['twitter_id'];
        $facebook_id = $instance['facebook_id'];
        $rss_id = $instance['rss_id'];
        $youtube_id = $instance['youtube_id'];
        $googleplus_id = $instance['googleplus_id'];
        $linkedin_id = $instance['linkedin_id'];
        $flickr_id = $instance['flickr_id'];
        $pinterest_id = $instance['pinterest_id'];
		$custom_id_1 = $instance['custom_id_1'];
        $custom_img_1 = $instance['custom_img_1'];

		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }

		if ( !empty( $info_description ) ) { echo '<span class="info_description">'.$info_description.'</span>'; }

		if ( !empty( $phone_number ) ) { echo '<span class="phone"><a href="tel:'.$phone_number.'">'.$phone_number.'</a></span>'; }
        
        /* Icons that need only an ID specified. */
		if ( !empty( $facebook_id ) ) { echo '<a href="http://www.facebook.com/'.$facebook_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'facebook.png', 'images/' ).'" width="32" height="32" alt="Facebook" /></a>'; }
		if ( !empty( $twitter_id ) ) { echo '<a href="http://twitter.com/'.$twitter_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'twitter.png', 'images/' ).'" width="32" height="32" alt="Twitter" /></a>'; }
		if ( !empty( $youtube_id ) ) { echo '<a href="http://www.youtube.com/user/'.$youtube_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'youtube.png', 'images/' ).'" width="32" height="32" alt="YouTube" /></a>'; }
        if ( !empty( $flickr_id ) ) { echo '<a href="http://www.flickr.com/photos/'.$flickr_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'flickr.png', 'images/' ).'" width="32" height="32" alt="Flickr" /></a>'; }
        if ( !empty( $pinterest_id ) ) { echo '<a href="http://pinterest.com/'.$pinterest_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'pinterest.png', 'images/' ).'" width="32" height="32" alt="Pinterest" /></a>'; }

        /* Icons that need a full URL specified. */
		if ( !empty( $googleplus_id ) ) { echo '<a href="'.$googleplus_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'googleplus.png', 'images/' ).'" width="32" height="32" alt="Google+" /></a>'; }
		if ( !empty( $linkedin_id ) ) { echo '<a href="'.$linkedin_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'linkedin.png', 'images/' ).'" width="32" height="32" alt="Linked In" /></a>'; }
		if ( !empty( $rss_id ) ) { echo '<a href="'.$rss_id.'" target="_blank" class="sm-icon"><img src="'.PC_Utility::theme_resource_uri( array('images/', 'api/images/icons/'), 'rss.png', 'images/' ).'" width="32" height="32" alt="RSS" /></a>'; }
		if ( !empty( $custom_id_1 ) ) { echo '<a href="'.$custom_id_1.'" target="_blank" class="sm-icon"><img src="'.$custom_img_1.'" width="32" height="32" alt="Custom Icon 1" /></a>'; }
		?>

		<?php if( $instance['show_search'] == "1" ) : ?>
			<div class="search">
			 <form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
				<span>
					<input type="text" placeholder="Search…" value="<?php echo get_search_query(); ?>" size="10" name="s" id="s"><input type="submit" id="searchsubmit" value="<?php echo esc_attr__( 'Search', 'presscoders' ); ?>">
				</span>
			 </form>
			</div>
		<?php endif;

		echo $after_widget;

    }
}

?>