<?php
	global $pc_is_front_page, $pc_home_page, $pc_show_on_front, $pc_page_on_front, $pc_post_id, $pc_template, $wp_registered_sidebars;
?>

	<div id="primary-sidebar" class="sidebar-container <?php echo PC_Utility::content_layout_classes_primary( true ); ?>" role="complementary">

		<?php

		/* If the page is singular we'll have a valid post/page ID to check for custom widget areas. */
		$primary_custom_widget_areas = is_singular() ? get_post_meta( $pc_post_id, '_'.PC_THEME_NAME_SLUG.'_primary_sort', true ) : null;

		/* Show global widget area if it contains widgets and we are NOT on a post/page that has custom widget areas set. */
		if ( is_active_sidebar( 'global-widget-area' ) && empty($primary_custom_widget_areas) )
			PC_Utility::render_widget_area( 'global-widget-area' );
		
		/* FRONT PAGE SIDEBARS. */
		if( ($pc_is_front_page && $pc_show_on_front == 'posts') || ($pc_home_page && $pc_page_on_front == 0) ) {
			/* If 'Your latest posts' OR 'A static page'  set in Settings -> Reading (and 'Front page' drop down blank) show default post widget area. */
			PC_Utility::render_widget_area( 'primary-post-widget-area', true, 'primary_post_generic_default_widgets.php', true );
		}
		/* ARCHIVE PAGE SIDEBARS. */
		elseif( is_archive() ) {
			/* Check for specific archive pages via filter hook. */
			PC_Utility::custom_widget_area_loop( 'primary-archive' );
		}
		/* SINGULAR PAGE SIDEBARS. */
		elseif( is_singular() ) {
			if( !empty($primary_custom_widget_areas) ) {
				PC_Utility::render_custom_widget_areas( $primary_custom_widget_areas );
			}
			elseif( is_single() ) {
				/* Check for custom posts type pages via filter hook. */
				PC_Utility::custom_widget_area_loop( 'primary-posts' );
			}
			elseif( is_page() ) {
				/* Check for custom pages via filter hook. */
				PC_Utility::custom_widget_area_loop( 'primary-pages', 'primary-page-widget-area', 'primary_page_generic_default_widgets.php' );
			}
		}
		/* CATCH-ALL PAGE. */
		else {
			/* Catch all case. Show primary post widget area. */
			PC_Utility::render_widget_area( 'primary-post-widget-area', true,		'primary_post_generic_default_widgets.php', true );
		}

		?>

	</div><!-- .sidebar-container -->