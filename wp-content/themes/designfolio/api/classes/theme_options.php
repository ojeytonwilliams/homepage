<?php

/**
 * Theme options class.
 *
 * Handles all the functionality for theme options.
 *
 * @since 0.1.0
 */
class PC_Theme_Options {

	/* Handle to the theme options page */
	protected $_theme_options_page;

	/**
	 * Theme options class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		$this->default_theme_options();

		add_action( 'wp_head', array( &$this, 'theme_header_html' ) );
		add_action( 'wp_footer', array( &$this, 'theme_footer_html' ) );

		add_action( 'admin_init', array( &$this, 'register_theme_settings' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'theme_admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'theme_options_page_init' ) );

		/* Temporarily disabled as it isn't working 100% correctly yet. */
		//add_action( 'admin_head-media-upload-popup', array( &$this, 'theme_admin_head_media_upload_popup' ) );
	}

	/**
	 * Adds html code defined in theme options textarea to theme header.
	 *
	 * @since 0.1.0
	 */
	public function theme_header_html() {
		$options = get_option( PC_OPTIONS_DB_NAME );
		if($options['txtarea_header'] != "") { echo $options['txtarea_header']; }
	}

	/**
	 * Adds html code defined in theme options textarea to theme footer.
	 *
	 * @since 0.1.0
	 */
	public function theme_footer_html() {
		$options = get_option( PC_OPTIONS_DB_NAME );
		if($options['txtarea_footer'] != "") { echo $options['txtarea_footer']; }
	}

	/**
	 * Use solid background color.
	 *
	 * @since 0.1.0
	 */
	public function pc_solid_header_bg_color() {
		$options = get_option( PC_OPTIONS_DB_NAME );

		if ( isset($options[ 'chk_solid-header-bg' ]) ) { ?>

<style type="text/css">
	#header { background: transparent; }
</style>
			<?php
		} // endif
	}

	/**
	 * Register theme options with Settings API.
	 *
	 * @since 0.1.0
	 */
	public function register_theme_settings(){
		register_setting( PC_THEME_OPTIONS_GROUP, PC_OPTIONS_DB_NAME );
	}

	/**
	 * Register admin scripts and styles, ready for enqueueing on the theme options page
	 *
	 * @since 0.1.0
	 */
	public function theme_admin_init(){
		// Register theme option scripts
		wp_register_script('theme_colorpicker_script', PC_THEME_ROOT_URI.'/api/js/colorpickers/colorpicker/js/colorpicker.js', array('jquery-tools'));
		wp_register_script('theme_colorpicker_eye_script', PC_THEME_ROOT_URI.'/api/js/colorpickers/colorpicker/js/eye.js', array('theme_colorpicker_script'));

		// Register theme option style sheets
		wp_register_style('theme_jquery_custom_stylesheet', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/base/jquery-ui.css'); // @todo this might no longer be needed now
		wp_register_style('theme_admin_stylesheet', PC_THEME_ROOT_URI.'/includes/css/theme_admin.css');
		wp_register_style('theme_colorpicker_stylesheet', PC_THEME_ROOT_URI.'/api/js/colorpickers/colorpicker/css/colorpicker.css');
	}

	/**
	 * Register theme options page, and enqueue scripts/styles.
	 *
	 * @since 0.1.0
	 */
	public function theme_options_page_init() {

		/* @todo Add this in via a hook with cb function in theme specific utility cb class. */
		$options_menu_label = ( PC_THEME_NAME == 'Designfolio Pro' ) ? 'Designfolio Options' : PC_THEME_NAME.' Options';
		
		$this->_theme_options_page = add_theme_page( PC_THEME_NAME." Options", $options_menu_label, 'edit_theme_options', PC_THEME_MENU_SLUG, array( &$this, 'render_theme_form' ) );

		/* Enqueue scripts and styles for the theme option page */
		add_action( "admin_print_styles-$this->_theme_options_page", array( &$this,  'theme_admin_styles' ) );
		add_action( "admin_print_scripts-$this->_theme_options_page", array( &$this,  'theme_admin_scripts' ) );
	}

	/**
	 * Enqueue theme options page scripts.
	 *
	 * @since 0.1.0
	 */
	public function theme_admin_scripts() {
		/* Only show scripts on theme options page. */
		wp_enqueue_script( 'jquery-tools', 'http://cdn.jquerytools.org/1.2.5/full/jquery.tools.min.js', array('jquery'), null );

		wp_enqueue_script( 'theme_colorpicker_script' );
		wp_enqueue_script( 'theme_colorpicker_eye_script' );

		/* This is for the logo upload - delete if not working. */
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );

		/* Use built-in WordPress jQuery UI libraries for tabs, including tab cookie persistence. */
		//wp_enqueue_script( 'jquery-ui-core' ); /* jQuery UI core base, not the whole core. */
		//wp_enqueue_script( 'jquery-ui-widget' );
		//wp_enqueue_script( 'jquery-ui-tabs' );

		/* Cookie persistence jQuery Plugin. */
		//wp_enqueue_script( 'jquery-cookie-plugin', PC_THEME_ROOT_URI.'/api/js/misc/jquery.cookie.js', array('jquery-ui-tabs') );
	}

	/**
	 * Enqueue theme options page styles.
	 *
	 * @since 0.1.0
	 */
	public function theme_admin_styles() {
		// Only show styles on theme options page
		wp_enqueue_style('theme_jquery_custom_stylesheet');
		wp_enqueue_style('theme_admin_stylesheet');
		wp_enqueue_style('theme_colorpicker_stylesheet');

		// This is for the logo upload - delete if not working
		wp_enqueue_style('thickbox');
	}

	/**
	 * Display theme options page.
	 *
	 * @since 0.1.0
	 */
	public function render_theme_form() {
		/* Include the options form for now, as it is quite a long file */
		/* This should be generated in the future(?). At least add in hooks to let users extend the options form. */
		
		require_once( PC_THEME_ROOT_DIR.'/includes/admin/theme_options_form.php' );
	}

	/**
	 * Set default theme options.
	 * 
	 * This function updates the theme options with the specified defaults ONLY if they don't exist. The
	 * idea being that new theme options can be added by a developer and picked up by the theme without having
	 * to blank the existing theme options first. If some options exist then merge current options with defaults
	 * to set any new theme options that may have been added since last theme activation. This method does NOT
	 * overwrite any theme options if they already exist.
	 *
	 * Important: Any checkboxes you need to be ON by default just add them to an array, in addition to the
	 * $pc_default_options array, but with a "0" value. Otherwise when the theme is deactivated/reactivated the ON by
	 * default checkboxes will always be set to ON even if the user set to off in the meantime. This is because if
	 * a check box is turned off it is not stored in the options db at all, but to be efectively tested by the
     * array_merge($pc_default_options, $current_options) function, if it has been turned off by the user then it
     * needs to be set to zero manually in the code.
	 *
	 * @since 0.1.0
	 */
	public function default_theme_options() {

		/* Define as global to accessible anywhere (i.e. from within hook callbacks). */
		global $pc_default_options, $pc_footer_links;

		/* Define some theme specific option constants. */
		define( "PC_ADMIN_EMAIL_TEXTBOX", "txt_admin_email" );
		define( "PC_SEO_SETTINGS_CHECKBOX", "chk_seo_settings" );
		define( "PC_DEFAULT_LAYOUT_THEME_OPTION", "drp_default_layout" );
		define( "PC_LOGO_URL_OPTION_NAME", "txt_logo_url" ); // logo url text box
		define( "PC_LOGO_CHK_OPTION_NAME", "chk_custom_logo" ); // logo checkbox (to use/not use a custom logo)

		/* @todo Add footer links in via a (theme specific) hook cb only if the footer theme options are used. */
		$footer_theme_name = ( PC_THEME_NAME == 'Designfolio Pro' ) ? 'designfolio' : PC_THEME_NAME;
		$pc_footer_links = '<div id="site-info"><p class="copyright">&copy; [year] [site-url]</p><p class="pc-link">Powered by <a href="http://wordpress.org/" target="_blank" class="wp-link">WordPress</a> and the <a href="http://www.presscoders.com/'.$footer_theme_name.'/" target="blank" title="'.PC_THEME_NAME.' WordPress Theme">'.PC_THEME_NAME.' Theme</a>.</p></div><!-- #site-info -->'; 

		/* Defaults options array. */
		$pc_default_options = array(
					PC_LOGO_CHK_OPTION_NAME => null,
					PC_LOGO_URL_OPTION_NAME => "",
					"chk_hide_description" => null,
					PC_DEFAULT_LAYOUT_THEME_OPTION => "2-col-r",
					"chk_show_social_buttons" => "1",
					PC_SEO_SETTINGS_CHECKBOX => null,
					PC_ADMIN_EMAIL_TEXTBOX => get_bloginfo( 'admin_email' ),
					"txtarea_header" => "",
					"txtarea_footer" => "",
					"txtarea_footer_links" => $pc_footer_links,
                    "txt_favicon" => "",
					"txtarea_custom_css" => ""
					);

		/* Get a copy of the current theme options. */
		$current_options = get_option( PC_OPTIONS_DB_NAME);

		/* If theme options not set yet then don't bother trying to merge with the $pc_default_off_checkboxes. */
		if ( is_array($current_options)) {
            /* Define as global to accessible anywhere (i.e. from within hook callbacks). */
            global $pc_default_off_checkboxes;

			$pc_default_off_checkboxes = array(
									"chk_show_social_buttons" => "0",
									);
		}

        /* Add theme specific default settings vis this hook. */
        PC_Hooks::pc_theme_option_defaults(); /* Framework hook wrapper */

		/* Added this here rather inside the same 'if' statement above so we can add extra $pc_default_off_checkboxes via a hook. */
		if ( is_array($current_options)) {
			/* Manually set the checkboxes that have been unchecked, by the user, to zero. */
			$current_options = array_merge($pc_default_off_checkboxes, $current_options);
		}

		/* If there are no existing options just use defaults (no merge). */
		if ( !$current_options || empty($current_options) ) {
			// Update options in db
			update_option( PC_OPTIONS_DB_NAME, $pc_default_options);
		}
		/* Else merge existing options with current ones (new options are added, but none are overwritten). */
		else {
			/* Merge current options with the defaults, i.e. add any new options but don't overwrite existing ones. */
			$result = array_merge($pc_default_options, $current_options);

			/* Update options in db. */
			update_option( PC_OPTIONS_DB_NAME, $result);
		}
	}

	/**
	 * Add code to replace "Insert into Post" text on media uploads for theme logo image.
	 *
	 * @since 0.1.0
	 */
	public function theme_admin_head_media_upload_popup()
	{

		if($_GET[ PC_THEME_NAME_SLUG."_replace_text"] == "true") {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$("li#tab-type_url").css("display", "none"); // hide url tab
					$("li#tab-gallery").css("display", "none"); // hide gallery tab
					$("td input[type=button].button").val("Insert into <?php echo PC_THEME_NAME; ?> Theme");
					$("td.savesend input[type=submit]").val("Insert into <?php echo PC_THEME_NAME; ?> Theme");
					var fAct_library = $("form#library-form").attr("action"); // fAct_library = unpload tab form action
					var fAct_image = $("form#image-form").attr("action"); // fAct_image = media tab action
					$("form#library-form").attr("action", fAct_library + "&amp;<?php echo PC_THEME_NAME_SLUG; ?>_replace_text=true");
					$("form#image-form").attr("action", fAct_image + "&amp;<?php echo PC_THEME_NAME_SLUG; ?>_replace_text=true");
				});
			</script>
			<?php
		}
	}
}

?>