<?php

// ----------------------------------
//  Theme Recent Posts Widget Class
// ----------------------------------

class pc_recent_posts_widget extends WP_Widget {

	function pc_recent_posts_widget() {
		$widget_ops = array('classname' => 'pc_recent_posts_widget', 'description' => __( 'An alternative way to display recent posts. The post title, excerpt, and read more link are shown.', 'presscoders' ) );
		$this->WP_Widget('pc_recent_posts_widget_'.PC_THEME_NAME_SLUG, __( 'Recent Posts Excerpt', 'presscoders' ), $widget_ops);
	}

	function form( $instance ) {
        $defaults = array( 'title' => '', 'recent_posts_category' => '1', 'number_posts' => 2 );
        $instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags($instance['title']);
		if ( !isset($instance['number_posts']) || !$number_posts = (int) $instance['number_posts'] )
			$number_posts = 2;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'presscoders' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>Post Category: <?php $args = array('show_option_all' => 'All Categories', 'id' => $this->get_field_id('recent_posts_category'), 'hide_empty' => 0, 'hierarchical' => 0, 'show_count' => 0, 'name' => $this->get_field_name('recent_posts_category'), 'selected' => $instance['recent_posts_category']);
			  wp_dropdown_categories( $args ); ?>
		</p>
		<p><label for="<?php echo $this->get_field_id('number_posts'); ?>"><?php _e( 'Number of posts to show:', 'presscoders' ); ?></label>
		<input id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" type="text" value="<?php echo $number_posts; ?>" size="3" /></p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['recent_posts_category'] = $new_instance['recent_posts_category'];
		$instance['number_posts'] = (int) $new_instance['number_posts'];

		return $instance;
	}

	function widget($args, $instance) {

		extract($args);

		$title = $instance['title'];
        $number_posts = $instance['number_posts'];
		$recent_posts_category = $instance['recent_posts_category'];

		$r = new WP_Query(array('cat' => $recent_posts_category, 'showposts' => $number_posts, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1));
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul class="theme-rpw">
		<?php while ($r->have_posts()) : $r->the_post(); ?>
		<li>
			<h3><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></h3>
			<p class="excerpt"><?php the_excerpt(); ?></p>
			<p class="read-more"><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php _e( 'Read more', 'presscoders' ); ?></a></p>
		</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;
	}
}

?>