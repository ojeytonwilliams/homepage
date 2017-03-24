<?php

// Primary post generic default widget code

?>

<div class="widget widget_text">
	<h3 class="widget-title">Post: Primary</h3>
	<div class="textwidget">This widget area is currently empty. You can add widgets to this area via your <a href="<?php echo get_admin_url( null, 'widgets.php' ); ?>" target="_blank">admin widgets</a> page.<br /><br />Examples of some widgets you can add are shown below.</div>
</div>

<div id="search" class="widget widget_search">
	<?php get_search_form(); ?>
</div>

<div id="archives" class="widget">
	<h3 class="widget-title"><?php _e( 'Archives', 'presscoders' ); ?></h3>
	<ul>
		<?php wp_get_archives( 'type=monthly' ); ?>
	</ul>
</div>

<div id="meta" class="widget">
	<h3 class="widget-title"><?php _e( 'Meta', 'presscoders' ); ?></h3>
	<ul>
		<?php wp_register(); ?>
		<li><?php wp_loginout(); ?></li>
		<?php wp_meta(); ?>
	</ul>
</div>
