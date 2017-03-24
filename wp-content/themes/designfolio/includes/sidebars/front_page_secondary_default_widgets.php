<?php

// Front page secondary default widget code

?>

<div class="widget widget_text">
	<h3 class="widget-title">Front Page: Secondary</h3>
	<div class="textwidget">This widget area is currently empty. You can add widgets to this area via your <a href="<?php echo get_admin_url( null, 'widgets.php' ); ?>" target="_blank">admin widgets</a> page.<br /><br />Examples of some widgets you can add are shown below.</div>
</div>

<?php the_widget('WP_Widget_Calendar'); ?>

<?php

$args = array(
		'before_widget' => '<div class="widget widget_text">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

the_widget('WP_Widget_Categories', 'title=Post Categories&count=1&hierarchical=0&dropdown=0', $args);

?>