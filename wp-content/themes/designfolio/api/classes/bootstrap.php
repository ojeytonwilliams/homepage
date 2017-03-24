<?php

/**
 * Bootstrap file to load all classes used by the framework, as well theme specific classes.
 *
 * @todo Try to auto load these classes in the future rather than manually load them.
 *
 * @since 0.1.0
 */

/* Load shortcodes class. */
if( file_exists( get_template_directory().'/api/classes/shortcodes.php' ) ) {
	require_once( get_template_directory().'/api/classes/shortcodes.php' );
}

/* Load hooks class. */
if( file_exists( get_template_directory().'/api/classes/hooks.php' ) ) {
	require_once( get_template_directory().'/api/classes/hooks.php' );
}

/* Load widgets class. */
if( file_exists( get_template_directory().'/api/classes/widgets.php' ) ) {
	require_once( get_template_directory().'/api/classes/widgets.php' );
}

/* Load utility callbacks class. */
if( file_exists( get_template_directory().'/api/classes/utility_callbacks.php' ) ) {
	require_once( get_template_directory().'/api/classes/utility_callbacks.php' );
}

/* Load utility class. */
if( file_exists( get_template_directory().'/api/classes/utility.php' ) ) {
	require_once( get_template_directory().'/api/classes/utility.php' );
}

/* Load deprecated class. */
if( file_exists( get_template_directory().'/api/classes/deprecated.php' ) ) {
	require_once( get_template_directory().'/api/classes/deprecated.php' );
}

/* Load meta boxes class. */
if( file_exists( get_template_directory().'/api/classes/meta_boxes.php' ) ) {
	require_once( get_template_directory().'/api/classes/meta_boxes.php' );
}

/* Load sidebar commander class. */
if( file_exists( get_template_directory().'/api/classes/sidebar_commander.php' ) ) {
	require_once( get_template_directory().'/api/classes/sidebar_commander.php' );
}

/* Load theme options class. */
if( file_exists( get_template_directory().'/api/classes/theme_options.php' ) ) {
	require_once( get_template_directory().'/api/classes/theme_options.php' );
}

/* Load template parts class. */
if( file_exists( get_template_directory().'/api/classes/template_parts.php' ) ) {
	require_once( get_template_directory().'/api/classes/template_parts.php' );
}

/* Load Testimonials custom post type class. */
if( file_exists( get_template_directory().'/api/classes/custom-post-types/testimonials.php' ) ) {
	require_once( get_template_directory().'/api/classes/custom-post-types/testimonials.php' );
}

/* Load Slides custom post type class. */
if( file_exists( get_template_directory().'/api/classes/custom-post-types/slides.php' ) ) {
	require_once( get_template_directory().'/api/classes/custom-post-types/slides.php' );
}

/* Load Portfolio custom post type class. */
if( file_exists( get_template_directory().'/api/classes/custom-post-types/portfolio.php' ) ) {
	require_once( get_template_directory().'/api/classes/custom-post-types/portfolio.php' );
}

/* Theme specific classes. */

/* Load theme specific utility callbacks class. */
if( file_exists( get_template_directory().'/includes/classes/theme_specific_utility_callbacks.php' ) ) {
	require_once( get_template_directory().'/includes/classes/theme_specific_utility_callbacks.php' );
}

/* Load theme specific utility class. */
if( file_exists( get_template_directory().'/includes/classes/theme_specific_utility.php' ) ) {
	require_once( get_template_directory().'/includes/classes/theme_specific_utility.php' );
}

/* Load Platinum theme features if they exist. */
if( file_exists( get_template_directory().'/includes/modules/bootstrap.php' ) ) {
	require_once( get_template_directory().'/includes/modules/bootstrap.php' );
}

?>