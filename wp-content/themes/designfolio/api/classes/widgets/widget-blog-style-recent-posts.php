<?php

// --------------------------------------------
//  Theme Blog Style Recent Posts Widget Class
// --------------------------------------------

class pc_blog_style_recent_posts_widget extends WP_Widget {

	function pc_blog_style_recent_posts_widget() {
		$widget_ops = array('classname' => 'pc_blog_style_recent_posts_widget', 'description' => __( 'Recent posts similar to your main blog page. Add to the front page main content widget area.', 'presscoders' ) );
		$this->WP_Widget('pc_blog_style_recent_posts_widget_'.PC_THEME_NAME_SLUG, __( 'Blog Style Recent Posts', 'presscoders' ), $widget_ops);
	}

	function form( $instance ) {
        $defaults = array( 'title' => '', 'pc_blog_style_recent_posts_category' => '1', 'number_posts' => 2, 'show_post_thumbs' => false );
        $instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags($instance['title']);
		if ( !isset($instance['number_posts']) || !$number_posts = (int) $instance['number_posts'] )
			$number_posts = 2;

        $show_post_thumbs = strip_tags($instance['show_post_thumbs']);
		$category = $instance['pc_blog_style_recent_posts_category'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'presscoders' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>Post Category: <?php $args = array(
			'show_option_all' => 'All Categories',
			'id' => $this->get_field_id('pc_blog_style_recent_posts_category'),
			'hide_empty' => 0,
			'hierarchical' => 0,
			'show_count' => 0,
			'name' => $this->get_field_name('pc_blog_style_recent_posts_category'),
			'selected' => $category);
  wp_dropdown_categories( $args ); ?>
		</p>
		<p><label for="<?php echo $this->get_field_id('number_posts'); ?>"><?php _e( 'Number of posts to show:', 'presscoders' ); ?></label>
		<input id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" type="text" value="<?php echo $number_posts; ?>" size="3" /></p>
		<p><label><input type="checkbox" value="1" <?php checked( $show_post_thumbs, '1' ); ?> name="<?php echo $this->get_field_name( 'show_post_thumbs' ); ?>" />&nbsp;<?php _e('Show Post Thumbnails?', 'presscoders' ) ?></label></p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['pc_blog_style_recent_posts_category'] = $new_instance['pc_blog_style_recent_posts_category'];
		$instance['number_posts'] = (int) $new_instance['number_posts'];
		$instance['show_post_thumbs'] = $new_instance['show_post_thumbs'];

		return $instance;
	}

	function widget($args, $instance) {

		extract($args);

		$title = $instance['title'];
        $number_posts = (int) $instance['number_posts'];
		$pc_blog_style_recent_posts_category = $instance['pc_blog_style_recent_posts_category'];
		$show_post_thumbs = isset( $instance['show_post_thumbs'] );

		// START - ** RECENT POSTS **
		// ** Show recent posts **
		
		$args = array(
			'cat' => $pc_blog_style_recent_posts_category,
			'showposts' => $number_posts,
			'nopaging' => 0,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1
		);
		$r = new WP_Query( $args );

		if ($r->have_posts()) :
		
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		while ($r->have_posts()) : $r->the_post();

		$args1 = array(	'read_more'         =>  __( 'Read More', 'presscoders' ),
						'spt'				=>	$show_post_thumbs
		);

		if( method_exists( 'PC_TS_Utility', 'blog_style_recent_posts_widget_loop' ) ) {
			PC_TS_Utility::blog_style_recent_posts_widget_loop($args1);
		}
		else {
			echo 'No function exists to handle the blog style recent posts. Please add a blog_style_recent_posts_widget_loop() method to the PC_TS_Utility class.';
			break;
		}

		?>
		
		<?php endwhile; ?>

		<?php echo $after_widget; ?>

		<?php wp_reset_postdata(); ?>

		<?php endif; ?>

	<?php // END - ** RECENT POSTS ** ?>

<?php
	}
}

?>