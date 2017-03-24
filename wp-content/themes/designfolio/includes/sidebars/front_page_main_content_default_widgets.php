<?php

	// Front page main content default widget code

	$blog_style_recent_posts_instance = array(
		'title' => 'From The Blog',
		'pc_blog_style_recent_posts_category' => '1',
		'number_posts' => 2,
		'show_post_thumbs'
	);
	$blog_style_recent_posts_args = array(
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div>',
		'before_title' => '<div class="title widgettitle">',
		'after_title' => '</div>'
	);

	$text_widget_content = "<div id=\"bs-main-content\"><h2 style=\"clear: none;\" id=\"bs-posts-main-header-bar\" class=\"page-title\">The Premium Website Solution</h2><p><strong>".PC_THEME_NAME." is the premier turn-key website solution for business professionals.</strong></p><p>You can add some teaser text here to make your visitors want to buy your services.</p><a class=\"button defaultbtn\" href=\"#\">View Our Services</a></div>";
	$mc_text_widget_instance = array(
		'title' => '',
		'text' => $text_widget_content,
		'filter' => ''
	);
	$top_text_widget_instance = array(
		'title' => '',
		'text' => 'The content below is 100% widgetized. Yes, the '.PC_THEME_NAME.' front page is now completely built with widgets! This means you have total control over your front page content and layout. Edit widgets via your <a href="'.get_admin_url( null, 'widgets.php' ).'" target="_blank">admin widgets</a> page.',
		'filter' => ''
	);
	$top_text_widget_args = array(
		'before_widget' => '<div class="widget widget_text">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	$text_widget_args = array(
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => ''
	);

	the_widget('WP_Widget_Text', $top_text_widget_instance, $top_text_widget_args);
	the_widget('WP_Widget_Text', $mc_text_widget_instance, $text_widget_args);
	the_widget( 'pc_blog_style_recent_posts_widget' , $blog_style_recent_posts_instance, $blog_style_recent_posts_args );

?>
