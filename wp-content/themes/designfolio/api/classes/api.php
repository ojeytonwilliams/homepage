<?php

/* Bootstrap file to load framework class dependencies. */
if( file_exists( get_template_directory().'/api/classes/bootstrap.php' ) ) {
	require_once( get_template_directory().'/api/classes/bootstrap.php' );
}

/**
 * Press Coders framework class.
 *
 * @since 0.1.0
 */
class PC_Framework {
	
	/**
	 * Framework class properties.
	 *
	 * @since 0.1.0
	 */

	/* Class type properties. */
	protected $_shortcodes_class;
	protected $_widgets_class;
	protected $_utility_callbacks_class;
    protected $_ts_utility_callbacks_class;
	protected $_ts_utility_class;
	protected $_meta_boxes_class;
	protected $_sidebar_commander_class;
	protected $_theme_options_class;
	protected $_deprecated_class;
	protected $_testimonials_cpt_class;
	protected $_slides_cpt_class;
	protected $_portfolio_cpt_class;

	/**
	 * Class constructor. Loads required framework files in the correct order.
	 *
	 * @since 0.1.0
	 */
	public function __construct($theme_name) {

		/* Core framework classes. */
		$this->_utility_callbacks_class = new PC_Utility_Callbacks();
        $this->_ts_utility_callbacks_class = new PC_TS_Utility_Callbacks();
        $this->_ts_utility_class = new PC_TS_Utility();
		$this->_meta_boxes_class = new PC_MetaBoxes();
		$this->_deprecated_class = new PC_Deprecated();

		/* Define framework constants */
		$this->constants($theme_name);

		/* Setup framework. */
		$this->setup();

		/* Enqueue required scripts. */
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_required_scripts' ) );

		/* After theme switched. Good place to run code that only needs executing ONCE after the theme is activated. */
		add_action( 'after_switch_theme', array( &$this, 'after_theme_activated' ) );

		/* Setup the themes text domain and .mo file for translation. */
		add_action( 'after_setup_theme', array( &$this, 'setup_locale' ), 3 );

		/* Load the core framework features.
		 *
		 * Priority set to 12 so the 'framework_features' callback fires AFTER the supported features have been specified in the extended class.
		 * This allows a call in a child theme such as: add_action( 'after_setup_theme', 'child_framework_features', 11 ) to easily remove/redefine added features.
		 */
		add_action( 'after_setup_theme', array( &$this, 'framework_features' ), 12 );
	}

	/**
	 * Setup framework.
	 *
	 * @since 0.1.0
	 */
	public function setup() {

		add_filter( 'template_include', array( &$this, 'requested_theme_template' ) );
		add_action( 'template_redirect', array( &$this, 'theme_redirect_cb' ) );

        /* Display front page before content widgets if any defined in 'Front Page: Before Content' widget area. */
		add_action( 'pc_after_container', array( &$this->_utility_callbacks_class, 'front_page_before_content' ) );

		add_action( 'template_redirect', array( &$this, 'enqueue_page_template_js' ) );
	}

	/**
	 * Defines the framework constants.
	 *
	 * @since 0.1.0
	 */
	public function constants($theme_name) {

		$theme_name = trim($theme_name);
		if( !isset($theme_name) || empty($theme_name) )
			wp_die('No theme name specified');
		
		/* Define main theme name label. */
		define( "PC_THEME_NAME", $theme_name );
		define( "PC_THEME_NAME_U", strtolower(str_replace(" ","_", PC_THEME_NAME) )); // Underscored lower case theme name
		define( "PC_THEME_NAME_H", strtolower(str_replace(" ","-", PC_THEME_NAME) )); // Hyphenated lower case theme name
		define( "PC_THEME_MENU_SLUG", PC_THEME_NAME_U."_admin_options_menu" );
		define( "PC_THEME_NAME_SLUG", PC_THEME_NAME_H ); /* Theme name slug label (used mainly in the options pages). */

		/* Theme paths. */
		define( "PC_THEME_ROOT_DIR", get_template_directory() );
		define( "PC_THEME_ROOT_URI", get_template_directory_uri() );
		define( "PC_CHILD_ROOT_DIR", get_stylesheet_directory() );
		define( "PC_CHILD_ROOT_URI", get_stylesheet_directory_uri() );
	}

	/**
	 * Setup the themes text domain and .mo file for translation.
	 *
	 * @since 0.1.0
	 */
	public function setup_locale() {
		$locale = get_locale();
		$locale_filename = '/languages/'.$locale.'.mo';

		// A parent theme
		if( get_stylesheet_directory() == get_template_directory() ) {
			// Use parent translation
			load_theme_textdomain( 'presscoders', get_template_directory().'/languages' );
		}
		// A child theme
		else {
			// Use child theme language file if exists
			if ( file_exists(get_stylesheet_directory().$locale_filename) ) {
				load_theme_textdomain( 'presscoders', get_stylesheet_directory().'/languages' );
			}
			// Else use parent
			else {
				load_theme_textdomain( 'presscoders', get_template_directory().'/languages' );
			}
		}

		$locale = get_locale();
		$locale_file = locate_template( array( "languages/{$locale}.php", "{$locale}.php" ) );

		if ( is_readable( $locale_file ) )
			require_once($locale_file);
	}

	/**
	 * Loads the core framework and optional features, specified in the extended class.
	 *
	 * @since 0.1.0
	 */
	public function framework_features() {

		/* Access the global theme features array. */
		global $_wp_theme_features;

        /* Add Modernizr library to handle cross browser HTML 5 and CSS 3. */
		if( current_theme_supports( 'modernizr' ) ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_modernizr' ) );
		}

		/* Add Superfish jQuery menu support. */
		if( current_theme_supports( 'superfish' ) ) {

            /* @todo Move the custom superfish jQuery to a custom file in the /includes/js/ folder. */
            add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_superfish' ) );
		}

		/* Initialise sidebar commander if supported. */
		if( current_theme_supports( 'sidebar-commander' ) ) {
			$this->_sidebar_commander_class = new PC_SidebarCommander();
		}

		/* Initialise theme widgets if supported. */
		if( current_theme_supports( 'pc_widgets' ) ) {
			
			/* @todo Add the code below into a method in the widgets class, and pass in the required arguments. */
			$widgets = $_wp_theme_features['pc_widgets'];

			/* Initialise framework widgets */
			$this->_widgets_class = new PC_Widgets( $widgets );
		}

		/* Initialise theme options if supported. */
		if( current_theme_supports( 'theme-options-page' ) ) {

            /* Define theme options constants only if an option page is required. */
            define( "PC_OPTIONS_DB_NAME", PC_THEME_NAME_U."_theme_options" );
            define( "PC_THEME_OPTIONS_GROUP", PC_THEME_NAME_U."_theme_options_group" );

            /* Add in theme features below that are dependent on 'theme-options-page' support. */

            /* @todo This is OK here for now, but will probably need to be moved later on. */
            add_action( 'pc_before_closing_footer_tag', array( &$this->_utility_callbacks_class, 'render_footer_links' ) );

            /* Include breadcrumb trail */
            if( current_theme_supports( 'breadcrumb-trail' ) ) {
                $options = get_option( PC_OPTIONS_DB_NAME );

                add_action( 'pc_theme_option_defaults', array( &$this->_utility_callbacks_class, 'set_breadcrumb_theme_option_defaults' ) );
                add_action( 'pc_set_theme_option_fields_misc', array( &$this->_utility_callbacks_class, 'set_breadcrumb_theme_option_fields' ) );
                if( isset($options[ 'chk_show_breadcrumbs' ]) && $options[ 'chk_show_breadcrumbs' ]) {
                    add_action( 'pc_before_content', array( &$this->_utility_callbacks_class, 'display_breadcrumb_trail' ) );
                }
            }

            /* Add Fancybox support. */
            if( current_theme_supports( 'fancybox' ) ) {
                $options = get_option( PC_OPTIONS_DB_NAME );

                add_action( 'pc_theme_option_defaults', array( &$this->_utility_callbacks_class, 'set_fancybox_theme_option_defaults' ) );
				add_action( 'pc_set_theme_option_fields_misc', array( &$this->_utility_callbacks_class, 'set_fancybox_theme_option_fields' ) );
                if( isset($options[ 'chk_enable_fancybox' ]) && $options[ 'chk_enable_fancybox' ]) {
                    add_action( 'wp_enqueue_scripts', array( &$this->_utility_callbacks_class, 'enqueue_fancybox' ) );
                }
            }

            /* Add callout bar. */
            if( current_theme_supports( 'callout-bar' ) ) {

                /* Theme options hook callbacks. */
                add_action( 'pc_theme_option_defaults', array( &$this->_utility_callbacks_class, 'set_calloutbar_theme_option_defaults' ) );
                add_action( 'pc_theme_option_js', array( &$this->_utility_callbacks_class, 'set_theme_option_js_callout_bar' ) );
                add_action( 'pc_set_theme_option_fields_1', array( &$this->_utility_callbacks_class, 'set_callout_theme_option_fields' ) );
                add_action( 'pc_after_opening_footer_tag', array( &$this->_utility_callbacks_class, 'render_callout_bar' ) );
            }

			/* @todo Should this go inside the code that enables features only if the theme options page is enabled? */
			if( current_theme_supports( 'color-schemes' ) ) {

				/* Defined as global so we can access it throughout the framework. */
				global $pc_color_schemes;

				/* Delete color scheme cookie if theme options are updated OR the customizer is used. */
				add_action( 'customize_register', array( &$this->_utility_callbacks_class, 'delete_color_scheme_cookie' ) );
				add_action( 'update_option', array( &$this->_utility_callbacks_class, 'delete_color_scheme_cookie' ) );

				add_action( 'pc_theme_option_defaults', array( &$this->_ts_utility_callbacks_class, 'set_color_scheme_default' ) );
				add_action( 'pc_set_theme_option_fields_custom_colors', array( &$this->_ts_utility_callbacks_class, 'set_color_scheme_theme_option_field' ) );
				add_action( 'customize_register', array( &$this->_utility_callbacks_class, 'theme_customizer_register_color_schemes' ) );
				$this->_utility_callbacks_class->set_color_scheme_cookie(); /* Set cookie if the color switcher widget has changed color scheme. */

				if( is_array( $_wp_theme_features['color-schemes'] ) ) {
						/* Get color scheme array. */
						$pc_color_schemes = $_wp_theme_features['color-schemes'][0];
						/* Using 'widgets_init' hook rather than 'wp_enqueue_scripts' as set_cookie() needs to run before any HTML ouptut is sent. */
						add_action( 'wp_enqueue_scripts', array( &$this->_utility_callbacks_class, 'enqueue_color_scheme' ) ); /* Enqueue the color scheme. */
				}
			}

			/* Color picker options. */
			if( current_theme_supports( 'color-pickers' ) ) {

				add_action( 'pc_theme_option_defaults', array( &$this->_ts_utility_callbacks_class, 'set_color_picker_defaults' ) );
				add_action( 'pc_theme_option_js', array( &$this->_ts_utility_callbacks_class, 'set_theme_option_js_color_picker' ) ); /* Insert JS into theme options page. */
				add_action( 'wp_head', array( &$this->_ts_utility_callbacks_class, 'theme_color_picker_css' ) ); /* Add color picker CSS to <head>. */
				add_action( 'pc_set_theme_option_fields_custom_colors', array( &$this->_ts_utility_callbacks_class, 'set_color_picker_theme_option_fields' ) );
			}

			/* Solid header bg color theme option. */
			if( current_theme_supports( 'solid-header' ) ) {

				add_action( 'pc_theme_option_defaults', array( &$this->_ts_utility_callbacks_class, 'set_solid_header_default' ) );
				add_action( 'wp_head', array( &$this->_theme_options_class, 'pc_solid_header_bg_color' ) );
				add_action( 'pc_set_theme_option_fields_custom_colors', array( &$this->_ts_utility_callbacks_class, 'set_solid_header_theme_option_field' ) );
			}

			/* Google web font options. */
			if( current_theme_supports( 'google-fonts' ) ) {
				
				global $pc_google_font_list;
				global $pc_default_google_font;

				$default_font_list = array( 'PT+Sans+Narrow:400,700', 'Droid+Sans:400,700', 'Droid+Serif:400,700,700italic,400italic', 'Oswald', 'Ubuntu:300,400,500,700,300italic,400italic,500italic,700italic', 'Anton', 'Arvo:400,700,400italic,700italic' );

				if( is_array( $_wp_theme_features['google-fonts'] ) ) {

					/* Check an array of fonts has been specified in functions.php. If not add some defaults. */
					if( !is_array( $_wp_theme_features['google-fonts'][0]['font_list'] ) ) {
						$_wp_theme_features['google-fonts'][0]['font_list'] = $default_font_list;
					}

					/* Make sure we have a default font. */
					if( isset($_wp_theme_features['google-fonts'][0]['default']) && !empty($_wp_theme_features['google-fonts'][0]['default']) ) {
						$pc_default_google_font = $_wp_theme_features['google-fonts'][0]['default'];
					}
					else {
						/* Set default to first in font list array. */
						$pc_default_google_font = $_wp_theme_features['google-fonts'][0]['font_list'][0];
						$_wp_theme_features['google-fonts'][0]['default'] = $pc_default_google_font; /* Maintain integrity of array. */
					}

					/* Add default font if it wasn't added to main font list. */
					if( $pc_default_google_font != "None" && !in_array($pc_default_google_font, $_wp_theme_features['google-fonts'][0]['font_list']) )
						$_wp_theme_features['google-fonts'][0]['font_list'][] = $pc_default_google_font;

					$pc_google_font_list = $_wp_theme_features['google-fonts'][0]['font_list'];
				}
				else {
					$pc_google_font_list = $default_font_list;
					$pc_default_google_font = $default_font_list[0]; 
				}

				add_action( 'pc_theme_option_defaults', array( &$this->_utility_callbacks_class, 'set_google_fonts_defaults' ) );
				add_action( 'pc_theme_option_js', array( &$this->_utility_callbacks_class, 'set_theme_option_js_google_fonts' ) );
				add_action( 'wp_print_styles', array( &$this->_utility_callbacks_class, 'enqueue_google_font' ) );
				add_action( 'pc_head', array( &$this->_utility_callbacks_class, 'theme_google_fonts_css' ), 11 );
				add_action( 'pc_set_theme_option_fields_1', array( &$this->_utility_callbacks_class, 'set_google_fonts_option_fields' ) );
			}

			/* Responsive video resizing. */
			if( current_theme_supports( 'fitvids' ) ) {
				$options = get_option( PC_OPTIONS_DB_NAME );

                add_action( 'pc_theme_option_defaults', array( &$this->_utility_callbacks_class, 'set_fitvids_theme_option_defaults' ) );
				add_action( 'pc_set_theme_option_fields_misc', array( &$this->_utility_callbacks_class, 'set_fitvids_theme_option_fields' ) );
                if( isset($options[ 'chk_enable_fitvids' ]) && $options[ 'chk_enable_fitvids' ]) {
                   add_action( 'wp_enqueue_scripts', array( &$this->_utility_callbacks_class, 'enqueue_fitvids' ) );
                }
			}

            /* PC_Theme_Options instantiated here so any callbacks for framework hooks in the class are registered before the hook definition. */
            $this->_theme_options_class = new PC_Theme_Options();
		}

		/* Initialise theme shortcodes if supported. */
		if( current_theme_supports( 'shortcodes' ) ) {

			if( is_array( $_wp_theme_features['shortcodes'] ) ) {
				/* Load specified shortcodes. */
				if( isset($_wp_theme_features['shortcodes'][1]) && !empty($_wp_theme_features['shortcodes'][1]) ) {
					/* Use specified mode of adding shortcodes. */
					$this->_shortcodes_class = new PC_Shortcodes($_wp_theme_features['shortcodes'][0], $_wp_theme_features['shortcodes'][1]);
				}
				else {
					$this->_shortcodes_class = new PC_Shortcodes($_wp_theme_features['shortcodes'][0]);
				}
			}
			else {
				/* Load all shortcodes. */
				$this->_shortcodes_class = new PC_Shortcodes();
			}
		}

		/* Register support for custom navigation menus */
		if( current_theme_supports( 'custom-menus' ) ) {

			if( is_array( $_wp_theme_features['custom-menus'] ) ) {
				$priority = array( 'primary', 'secondary', 'tertiary', 'quaternary', 'quinary', 'senary', 'septenary', 'octonary', 'nonary', 'denary' );
				$i = 0; /* Counter */
				foreach($_wp_theme_features['custom-menus'][0] as $nav_menu) {

					/*  If there are more than 10 custom menus defined, revert to a number suffix system. */
					if( $i >= 10 ) {
						define( "PC_CUSTOM_NAV_MENU_".($i + 1), PC_THEME_NAME_H."-theme-".($i + 1) );
					}
					else {
						define( "PC_CUSTOM_NAV_MENU_".($i + 1), PC_THEME_NAME_H."-theme-".$priority[$i] );
					}

					/*  Register each nav menu */
					register_nav_menus( array(
						constant( "PC_CUSTOM_NAV_MENU_".($i + 1) ) => $nav_menu
					) );

					$i++; /* Increment counter */
				}
			}
			else {
				/* Defaulting to one nav menu. */
				define( "PC_CUSTOM_NAV_MENU_1", PC_THEME_NAME_H."-theme-primary" );
				register_nav_menus( array(
					PC_CUSTOM_NAV_MENU_1 => __( 'Primary Navigation', 'presscoders' ),
				) );
			}
		}

		/* Add custom post types. */
        if( current_theme_supports( 'custom-post-types' ) ) {

            if( is_array( $_wp_theme_features['custom-post-types'] ) ) {
                /* Get custom post types. */
                $custom_post_types = $_wp_theme_features['custom-post-types'][0];

				if( is_array($custom_post_types) ) {
					
					/* Add Testimonials CTP if specified. */
					if( in_array( 'testimonials', $custom_post_types ) && class_exists('PC_Testimonial_CPT') ) $this->_testimonials_cpt_class = new PC_Testimonial_CPT();

					/* Add Slides CTP if specified. */
					if( in_array( 'slides', $custom_post_types ) && class_exists('PC_Slide_CPT') ) $this->_slides_cpt_class = new PC_Slide_CPT();

					/* Add Portfolio CTP if specified. */
					if( in_array( 'portfolio', $custom_post_types ) && class_exists('PC_Portfolio_CPT') ) {
						/* Add Portfolio CPT. */
						$this->_portfolio_cpt_class = new PC_Portfolio_CPT();

						/* Add default thumbnail sizes for portfolio image gallery. */
						add_image_size( 'large-pf-image', 466, 250, true );
						add_image_size( 'medium-pf-image', 303, 200, true );
						add_image_size( 'small-pf-image', 221, 150, true );
					}
					else { /* Check if Portfolio CPT added as key => value pair. */
						if ( array_key_exists( "portfolio",$custom_post_types ) && class_exists('PC_Portfolio_CPT') ) {
							/* Add Portfolio CPT and use thumbnail size specified. */
							$this->_portfolio_cpt_class = new PC_Portfolio_CPT();

							/* Add new thumbnail sizes for portfolio image gallery. */
							foreach( $custom_post_types['portfolio'] as $thumb => $size ) {
								add_image_size( $thumb, $size[0], $size[1], true );
							}
						}
					}
				}
            }
        }

		/* Add Cufon font support. */
		if( current_theme_supports( 'cufon' ) ) {
			
			if( is_array( $_wp_theme_features['cufon'] ) ) {
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_cufon' ) );
				add_action( 'pc_after_closing_footer_tag', array( &$this, 'add_footer_cufon' ) );
			}
		}

		/* Social buttons scripts for the blog page. */
		if( current_theme_supports( 'social-media-buttons' ) ) {

			/* Enqueue social media scripts. */
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_social_media_buttons' ) );

			if( is_array( $_wp_theme_features['social-media-buttons'] ) ) {				
				/* Use the hook specified to render social media buttons. */
				$meta_hook = $_wp_theme_features['social-media-buttons'][0];
				add_action( $meta_hook, array( &$this->_ts_utility_callbacks_class, 'render_social_media_buttons' ) );
			}
			else {
				/* Use the 'normal' pc_post_meta hook to render social media buttons. */
				add_action( 'pc_post_meta', array( &$this->_ts_utility_callbacks_class, 'render_social_media_buttons' ) );
			}
		}

		/* Show simple debug output. */
		if( current_theme_supports( 'simple-debug' ) ) {
			
			add_action( 'get_header', array( &$this->_utility_callbacks_class, 'pc_simple_debug_output' ) );
		}

		/* ADDITIONAL FRAMEWORK FEATURES */

		/* Show theme activation message (via 'theme_activated' callback). */
		/* @todo replace this with a callback function via the new 'after_switch_theme' hook. */
		global $pagenow;
		if ( is_admin() && isset($_GET['activated']) && $pagenow == "themes.php" ) {

			/* WordPress Administration Widgets API. */
			/* This is only loaded on widgets.php so we need it on themes.php to access certain Widgets API functions that we wouldn't otherwise be able to access. */
			require_once(ABSPATH . 'wp-admin/includes/widgets.php');

			/* Show theme activation message, and setup them option defaults. */
			add_action( 'admin_notices', array( &$this, 'theme_activated' ) );
		}
	}

	/**
	 * Register and enqueue front end social media button styles/scripts.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_social_media_buttons() {

		/* @todo Check that the theme supports this feature before trying to use in theme template files. */
		if( is_single() ) {
//			wp_register_script( 'blog_twitter_social_button', 'http://platform.twitter.com/widgets.js' );
//			wp_register_script( 'blog_facebook_social_button', 'http://connect.facebook.net/en_US/all.js#appId=144100152333902&amp;xfbml=1' );
			wp_register_script( 'social_media_buttons', PC_THEME_ROOT_URI.'/api/js/misc/social-media/social-media-buttons.js' );
			wp_enqueue_script( 'social_media_buttons' );
		}
	}

	/**
	 * Register and enqueue front end social media button styles/scripts.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_modernizr() {

		/* Don't enqueue these scripts on admin pages. */
		if(!is_admin()){
			//wp_register_script( 'pc_modernizr', PC_THEME_ROOT_URI.'/api/js/html5/modernizr/modernizr_dev.js', array('jquery') );
            //wp_register_script( 'pc_modernizr', PC_THEME_ROOT_URI.'/api/js/html5/modernizr/modernizr.custom.js' ); /* Scott added some CSS3 rules to fix some IE7 issues. */
			wp_register_script( 'pc_modernizr', PC_THEME_ROOT_URI.'/api/js/html5/modernizr/modernizr.custom.97935.js', array('jquery') ); // Load with jQuery dependency as many polyfills require this library.
			wp_register_script( 'pc_custom_modernizr', PC_THEME_ROOT_URI.'/api/js/html5/modernizr/pc_modernizr_custom.js', array('pc_modernizr') );

			wp_enqueue_script( 'pc_modernizr' );
			wp_enqueue_script( 'pc_custom_modernizr' );
		}
	}

    /**
     * Register and enqueue front end social media button styles/scripts.
     *
     * @since 0.1.0
     */
    public function enqueue_superfish() {

		/* Don't enqueue these scripts on admin pages. */
		if(!is_admin()){
            wp_register_script( 'pc_superfish', PC_THEME_ROOT_URI.'/api/js/misc/superfish-1.4.8/js/superfish.js', array('jquery') );
            wp_register_script( 'pc_superfish_init', PC_THEME_ROOT_URI.'/includes/js/pc_superfish_init.js', array('pc_superfish') );

			wp_enqueue_script( 'pc_superfish' );
			wp_enqueue_script( 'pc_superfish_init' );
		}
    }

	/**
	 * Register and enqueue front end Cufon scripts.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_cufon() {

		/* Don't enqueue these scripts on admin pages. */
		if(!is_admin()){
			/* Access the global theme features array. */
			global $_wp_theme_features;
			$font = $_wp_theme_features['cufon'][0];

			/* Register Cufon scripts */
			wp_register_script( 'cufon-yui', PC_THEME_ROOT_URI.'/api/js/misc/cufon/cufon-yui.js' );
			wp_register_script( 'cufon-'.$font, PC_Utility::theme_resource_uri( 'includes/fonts', $font.'.js' ), array('cufon-yui') );
			wp_register_script( 'cufon-custom', PC_THEME_ROOT_URI.'/includes/fonts/custom-cufon.js', array( 'cufon-'.$font ) );

			/* Enqueue Cufon scripts */
			wp_enqueue_script( 'cufon-yui' );
			wp_enqueue_script( 'cufon-'.$font );
			wp_enqueue_script( 'cufon-custom' );
		}
	}

	/**
	 * Add code to footer.php to initialze Cufon.
	 *
	 * @since 0.1.0
	 */
	public function add_footer_cufon() {
	?>

		<script type="text/javascript"> Cufon.now(); </script><!-- should be before other scripts to minimize font flash -->

	<?php
	}

	public function theme_activated() {

		/* Define some constants relevant to theme activation if not already set in functions.php. */

		if(!defined( 'PC_INSTALL_DEFAULT_CONTENT' )) {
			/* Installs default content if set to TRUE. */
			define( 'PC_INSTALL_DEFAULT_CONTENT', FALSE );
		}

		if(!defined( 'PC_INSTALL_CONTENT_PROMPT' )) {
			/* Installs default content automatically if FALSE. Otherwise prompts user first. */
			define( 'PC_INSTALL_CONTENT_PROMPT', TRUE );
		}

		if(!defined( 'PC_INSTALL_DEMO_CONTENT' )) {
			/* Installs site specific special default content for demo purposes. */
			define( 'PC_INSTALL_DEMO_CONTENT', FALSE );
		}

		if ( current_user_can('edit_theme_options') ) {

			/* Get rid of the default WordPress notice upon theme activation. */
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('#message2').css('display', 'none');
				});
			</script>

			<?php

			$theme_options_url = 'themes.php?page='.PC_THEME_MENU_SLUG;
			$install_content = 'themes.php?activated=true&install_default_content=true';

			/* If TRUE, install default content. */
			if( PC_INSTALL_DEFAULT_CONTENT ) {
				if(isset($_POST['install_default_content'])) {
					PC_Utility::install_default_content($theme_options_url);
				}
				else {
					/* If TRUE, prompt to install default content first. */
					if( PC_INSTALL_CONTENT_PROMPT ) {
						?>
						<div class="updated" style="margin-top: 10px;padding-bottom:10px;">
							<?php echo '<h3 style="margin: 0.7em 0;padding-top: 5px;">Congratulations, '.PC_THEME_NAME.' successfully activated!</h3>For new sites we have created a content starter kit which automatically installs some default content for you,  including:'; ?>
							<ul>
							<li><strong>Pages</strong> - Blog, About Us, Sitemap, Contact Us pages created.</li>
							<li><strong>Navigation Menu</strong> - Pages added to the main navigation menu, along with a home page link.</li>
							</ul>
							Note: If you have an established site you may want to just go straight to the theme options page.<br /><br />
							<form id="pc-install_content" method="post" action="<?php echo admin_url( $install_content ); ?>">
								<span>
									<input class="button-primary" type="submit" value="Install Default Content" name="create_content" onclick="return confirm('Are you sure? Site content will be modified.');">
									<a class="button" href="<?php echo admin_url( $theme_options_url ); ?>"><?php echo PC_THEME_NAME; ?> Theme Options</a>
									<input type="hidden" value="true" name="install_default_content">
								</span>
							</form>
						</div>
						<?php
					}
					/* Install default content automatically. */
					else {
						PC_Utility::install_default_content($theme_options_url);
					}
				}
			}
			/* Else just show theme activation notice, and link to theme options. */
			else {
				PC_Utility::theme_activation_message($theme_options_url);
			}
		}
		else { ?>
			<div class="updated"><p><?php printf( PC_THEME_NAME.' theme activated! <a href="%s">Visit Site</a>', home_url() ); ?></p></div><?php
		}			
	}

	/**
	 * One of the PHP magic methods.
	 *
	 * Used for retrieving values of private and protected variables from outside of the class.
	 *
	 * @since 0.1.0
	 */
	public function __get($var) {
		return $this->$var;
	}

	/**
	 * Get the theme template file name used to load the requested page.
	 *
	 * @since 0.1.0
	 */
	public function requested_theme_template( $template ) {

        /* Requested theme template filename. */
		global $pc_template;
		$pc_template = basename($template);

		return $template;
	}

	/**
	 * Callback function for the 'template_redirect' hook.
     *
     * Set some framework global variables.
	 *
	 * @since 0.1.0
	 */
	public function theme_redirect_cb() {

        /* WordPress global post and wp_query objects. */
		global $post, $wp_query;

	    /* Framework globals. */

	    global $pc_post_object;				/* Current post object. */
		global $pc_is_front_page;			/* Front page status. */
		global $pc_home_page;				/* Home page status. */
		global $pc_post_id;					/* Post ID. */
        global $pc_show_on_front;			/* Store the front page reading setting. */
		global $pc_page_template;			/* Store the page template used for the current page. */
		global $pc_global_column_layout;	/* Correct column layout to use for current page. */
		global $pc_page_on_front;

		/* Get the template for the current page if one defined (i.e. won't be defined for category archive pages or 404 error pages). */
		if( !is_archive() && !is_404() ) {
			$pc_page_template = basename(get_page_template());
		}

		$pc_post_object = $post;
		$pc_is_front_page = is_front_page();
		$pc_home_page = is_home();

		/* Test $wp-query->post property, and that it has a valid ID, as it may not always exist (i.e. on 404.php page, or search.php). */
		if( property_exists( $wp_query, 'post') && isset($wp_query->post->ID) ) {
			$pc_post_id = $wp_query->post->ID;
		}

		/* Modify the post ID if necessary. i.e. if 'A static page' has been selected on reading settings. In this case the post ID for the static page set for the blog posts will be invalid. */
        $pc_show_on_front = get_option( 'show_on_front' );
		$pc_page_on_front = get_option( 'page_on_front' );
		if( $pc_show_on_front == 'page' ) {
			/* If the 'Posts page'. */
			if ( is_home() ) {
				/* Use the 'page_for_posts' WP option to get the 'correct' ID for this page.
				 * Otherwise all other attempts at getting the post ID results in the first ID
				 * in the posts loop. */
				$pc_post_id = get_option( 'page_for_posts' );
			}
		}

		/* Get the correct column layout to use for current page. */
		$options = get_option( PC_OPTIONS_DB_NAME );

		/* If current page is an archive OR front page with 'Your latest posts' OR front page
		 * with reading settings set to static page but the front page drop down not set, then
		 * use the default theme column layout. */
		if( is_archive() || ($pc_is_front_page && $pc_show_on_front == 'posts') || ($pc_home_page && $pc_page_on_front == 0) ) {
			$pc_global_column_layout = $options[PC_DEFAULT_LAYOUT_THEME_OPTION];
		}
		else {
			$pc_global_column_layout = get_post_meta($pc_post_id, '_'.PC_THEME_NAME_SLUG.'_column_layout',true);
		}

		if( empty($pc_global_column_layout) || $pc_global_column_layout == 'default' ) $pc_global_column_layout = $options[PC_DEFAULT_LAYOUT_THEME_OPTION];

		/* Set the global WordPress $content_width variable. */
		PC_Utility::set_content_width( $pc_global_column_layout );
	}

	/**
	 * Enqueue JS libraries based on page template.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_page_template_js() {

		global $pc_page_template;

		/* Check for the portfolio-page.php page template. */
		if( $pc_page_template == 'portfolio-page.php' ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_portfolio_quicksand' ) );
		}
	}

	/**
	 * Enqueue Quicksand JS library.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_portfolio_quicksand() {

		/* Don't enqueue these scripts on admin pages. */
		if(!is_admin()){
			wp_register_script( 'quicksand-js', PC_THEME_ROOT_URI.'/api/js/misc/jquery.quicksand.js', array('jquery') );
			wp_register_script( 'custom-quicksand-js', PC_THEME_ROOT_URI.'/api/js/presscoders/custom-quicksand.js', array('quicksand-js') );

			wp_enqueue_script( 'quicksand-js' );
			wp_enqueue_script( 'custom-quicksand-js' );
		}
	}

	/**
	 * Enqueue required scripts.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_required_scripts() {
		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
	}

	/**
	 * This function is executed once after theme activation.
	 *
	 * @since 0.1.0
	 */
	public function after_theme_activated() {

		/* Flush permalinks only ONCE after theme activation and custom taxonomies registered (these will have been registered before this function executes). */
		PC_Utility::flush_permalink_rules();
	}

} /* End of class definition */

?>