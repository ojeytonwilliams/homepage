<?php
	global $pc_is_front_page, $pc_home_page, $pc_show_on_front, $pc_page_on_front, $pc_post_id, $pc_template, $wp_registered_sidebars;
?>

	<div id="secondary-sidebar" class="sidebar-container <?php echo PC_Utility::content_layout_classes_secondary( true ); ?>" role="complementary">

		<?php

		/* If the page is singular we'll have a valid post/page ID to check for custom widget areas. */
		$secondary_custom_widget_areas = is_singular() ? get_post_meta( $pc_post_id, '_'.PC_THEME_NAME_SLUG.'_secondary_sort', true ) : null;

		/* FRONT PAGE SIDEBARS. */
		if( ($pc_is_front_page && $pc_show_on_front == 'posts') || ($pc_home_page && $pc_page_on_front == 0) ) {
			/* If 'Your latest posts' OR 'A static page'  set in Settings -> Reading (and 'Front page' drop down blank) show default post widget area. */
			PC_Utility::render_widget_area( 'secondary-post-widget-area', true, 'secondary_post_generic_default_widgets.php', false );
		}
		/* ARCHIVE PAGE SIDEBARS. */
		elseif( is_archive() ) {
			PC_Utility::render_widget_area( 'secondary-post-widget-area', true, 'secondary_post_generic_default_widgets.php', false );
		}
		/* SINGULAR PAGE SIDEBARS. */
		elseif( is_singular() ) {
			if( !empty($secondary_custom_widget_areas) ) {
				PC_Utility::render_custom_widget_areas( $secondary_custom_widget_areas );
			}
			elseif( is_single() ) {
				PC_Utility::render_widget_area( 'secondary-post-widget-area', true, 'secondary_post_generic_default_widgets.php', false );
			}
			elseif( is_page() ) {
				PC_Utility::render_widget_area( 'secondary-page-widget-area', true, 'secondary_page_generic_default_widgets.php', false );
			}
		}
		/* CATCH-ALL PAGE. */
		else {
			/* Catch all case. Show secondary post widget area. */
			PC_Utility::render_widget_area( 'secondary-post-widget-area', true,		'secondary_post_generic_default_widgets.php', false );
		}

		?>

	</div><!-- .sidebar-container -->