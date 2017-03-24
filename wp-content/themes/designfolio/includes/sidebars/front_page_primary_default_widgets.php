<?php
	
	// Front page primary default widget code

	$color_scheme_instance = array(
		'title' => 'Change Color Scheme',
		'description' => 'Try out a new color scheme. Select below to see an instant change!'
	);
	$color_scheme_args = array(
		'before_widget' => '<div class="widget pc_color_scheme_switcher_widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

	$primary_text_instance = array(
		'title' => 'Front Page: Primary',
		'text' => 'This widget area is currently empty. You can add widgets to this area via your <a href="'.get_admin_url( null, 'widgets.php' ).'" target="_blank">admin widgets</a> page.<br /><br />Examples of some widgets you can add are shown below.',
		'filter' => ''
	);
	$primary_text_args = array(
		'before_widget' => '<div class="widget widget_text">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

	$testimonial_instance = array(
		'title' => 'Customer Testimonials',
		'testimonial_shortcodes' => __('[testimonial name="Scott Bolinger" company="PressCoders.com"]'.PC_THEME_NAME.' is the most amazing website for business professionals on the planet! Your testimonials go here.[/testimonial] [testimonial name="Julie Sherman" company="Acme Inc."]Nam porta eros imperdiet nunc consectetur euismod. Sed vel nisl est, sit amet fermentum tellus. Fusce aliquet vestibulum tortor, ut iaculis nulla scelerisque sed. Quisque at nulla tortor.[/testimonial]', 'presscoders' )
	);
	$testimonial_args = array(
		'before_widget' => '<div class="widget '.PC_THEME_NAME_SLUG.'_pc_testimonial_widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

	$recent_posts_instance = array(
		'title' => 'Latest News',
		'recent_posts_category' => '1',
		'number_posts' => 2
	);
	$recent_posts_args = array(
		'before_widget' => '<div class="widget '.PC_THEME_NAME_SLUG.'_pc_recent_posts_widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

	the_widget( 'WP_Widget_Text', $primary_text_instance, $primary_text_args );
	the_widget( 'pc_color_scheme_switcher_widget', $color_scheme_instance, $color_scheme_args );

	the_widget( 'pc_testimonial_widget' , $testimonial_instance, $testimonial_args );
	the_widget( 'pc_recent_posts_widget' , $recent_posts_instance, $recent_posts_args );

?>