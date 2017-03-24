<?php

// --------------------------------
//  Theme News Ticker Widget Class
// --------------------------------

class pc_news_ticker_widget extends WP_Widget {

	function pc_news_ticker_widget() {
		$widget_ops = array('classname' => 'pc_news_ticker_widget', 'description' => __( 'A news ticker to display your most recent content.', 'presscoders' ) );
		$this->WP_Widget('pc_news_ticker_widget_'.PC_THEME_NAME_SLUG, 'News Ticker', $widget_ops);
	}

	function form( $instance ) {
        $defaults = array( 'title' => '', 'news_ticker_category' => '1', 'number_posts' => 2, 'pause_time' => 3000 );
        $instance = wp_parse_args( (array) $instance, $defaults );

		$title = substr( trim(strip_tags($instance['title'])), 0, 13 );
		if( empty($title) ) {
			$title = 'Recent News';
		}

		if ( !isset($instance['number_posts']) || !$number_posts = (int) $instance['number_posts'] )
			$number_posts = 2;
		if ( !isset($instance['pause_time']) || !$pause_time = (int) $instance['pause_time'] )
			$pause_time = 3000;

?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'presscoders' ); ?></label>&nbsp;&nbsp;<small style="font-style:italic;color:#777;">(max 13 chars)</small>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p>Post Category: <?php $args = array('show_option_all' => 'All Categories', 'id' => $this->get_field_id('news_ticker_category'), 'hide_empty' => 0, 'hierarchical' => 0, 'show_count' => 0, 'name' => $this->get_field_name('news_ticker_category'), 'selected' => $instance['news_ticker_category']);
			  wp_dropdown_categories( $args ); ?></p>

		<p><label for="<?php echo $this->get_field_id('number_posts'); ?>"><?php _e( 'Number of posts to show:', 'presscoders' ); ?></label>&nbsp;&nbsp;
		<input id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" type="text" value="<?php echo $number_posts; ?>" size="4" /></p>

		<p><label for="<?php echo $this->get_field_id('pause_time'); ?>"><?php _e( 'Pause time between items:', 'presscoders' ); ?></label>
		<input id="<?php echo $this->get_field_id('pause_time'); ?>" name="<?php echo $this->get_field_name('pause_time'); ?>" type="text" value="<?php echo $pause_time; ?>" size="4" /></p>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['news_ticker_category'] = $new_instance['news_ticker_category'];
		$instance['number_posts'] = (int) $new_instance['number_posts'];
		$instance['pause_time'] = (int) $new_instance['pause_time'];

		return $instance;
	}

	function widget($args, $instance) {

		extract($args);

		$title = substr( trim(strip_tags($instance['title'])), 0, 13 );
		if( empty($title) ) {
			$title = 'Recent News';
		}

		$number_posts = (int) $instance['number_posts'];
        $pause_time = $instance['pause_time'];
		$news_ticker_category = $instance['news_ticker_category'];

		$r = new WP_Query(array('cat' => $news_ticker_category, 'showposts' => $number_posts, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1));

		// START - ** NEWS TICKER **
		?>

		<!-- add-in the reference to the news ticker js/css libraries -->
		<link href="<?php echo PC_THEME_ROOT_URI.'/includes/css/ticker-style.css'; ?>" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?php echo PC_THEME_ROOT_URI.'/api/js/misc/news-ticker/jquery.ticker.js'; ?>"></script>

		<!-- news ticker js config -->
		<script language="javascript">
			jQuery(document).ready(function($) {

				$('#js-news').ticker({
					speed: 0.10,
					htmlFeed: true,
					controls: false,
					debugMode: false,
					displayType: 'fade',
					pauseOnItems: <?php echo $pause_time; ?>,
					fadeInSpeed: 900,
					fadeOutSpeed: 800,
					titleText: '<?php echo $title; ?>'
				});

				$('#ticker-wrapper').removeClass('left');

			});
		</script>
		
		<?php if ($r->have_posts()) : ?>

		<?php echo $before_widget; ?>

		<div id="ticker-wrapper" class="no-js">
			<ul id="js-news" class="js-hidden">
				<?php while ($r->have_posts()) : $r->the_post(); ?>
				<li class="news-item"><?php echo "<a href=".get_permalink()."><span class=\"ticker-post-title\">".get_the_title()."</span>: ".get_the_excerpt(); ?></a></li>
				<?php endwhile; ?>
			</ul>
		</div>

		<?php echo $after_widget; ?>

		<?php
			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();
			endif;
		?>

	<?php // END - ** NEWS TICKER ** ?>

<?php
	}
}

?>