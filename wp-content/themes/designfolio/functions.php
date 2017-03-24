<?php

/* Load Press Coders theme framework class */
if( file_exists( get_template_directory().'/api/classes/api.php' ) ) {
	require_once( get_template_directory().'/api/classes/api.php' );
}

class PC_Main_Theme extends PC_Framework {

	public function __construct($theme_name) {
		/* Call parent construtor manually to make both constructors fire. */
		parent::__construct($theme_name);

		/* Add theme support for framework features. */
		add_action( 'after_setup_theme', array( &$this, 'theme_support' ) );
	}

	/* Add support for theme features. */
	public function theme_support() {

		/** WORDPRESS BUILT-IN SUPPORTED THEME FEATURES **/

		add_theme_support( 'automatic-feed-links' );	/* Add posts and comments RSS feed links to head. */
		add_theme_support( 'post-thumbnails' );			/* Use the post thumbnails feature. */
		add_theme_support( 'custom-background' );		/* A simple uploader to change the site background image. */
		add_editor_style();								/* Post/page editor style sheet to match site styles. */

		/** FRAMEWORK SUPPORTED THEME FEATURES **/

		add_theme_support( 'theme-options-page' );							/* Display a theme options page. */
		add_theme_support( 'shortcodes' );									/* Include all framework shortcodes. */
		add_theme_support( 'social-media-buttons', 'pc_pre_post_meta' );	/* Include the social media buttons in single.php. */
		add_theme_support( 'fancybox' );									/* Include Fancybox lightbox. */
        add_theme_support( 'superfish' );									/* Load Superfish jQuery menu. */
        add_theme_support( 'modernizr' );									/* Load Modernizr library. */
        add_theme_support( 'fitvids' );										/* Responsive video resizing. */
		
        /* Include specified framework widgets. */
		add_theme_support( 'pc_widgets',	'twitter-feed',
											'theme-recent-posts',
											'blog-style-recent-posts',
											'color-scheme-switcher',
											'info-box'
		);

		/* Add array of menu location labels, or leave 2nd parameter blank for a single default menu. */
		add_theme_support( 'custom-menus', array( 'Primary Navigation', 'Top Menu' ) );

		/* Add array of theme color schemes. */
		add_theme_support( 'color-schemes',  array( __( 'Navy', 'presscoders' ) => 'default',
													__( 'Black', 'presscoders' ) => 'black' ) );

		/* ADDITIONAL THEME FEATURES */

		/* Default thumbnail size for post thumbnails. */
		set_post_thumbnail_size( 580, 200, true );

		/* Example adding an extra custom thumbnail size. */
		// add_image_size( 'blog-thumb', 620, 300, true );
	}

} /* End class definition */

/* Create theme class instance */
global $pc_theme_object;
$pc_theme_object = new PC_Main_Theme( 'Designfolio' );

?>