<?php

/**
 * Framework hooks class.
 *
 * @since 0.1.0
 */
class PC_Hooks {

	/**
	 * PC_Hooks class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

	}

	/**
	 * Our version of the wp_head hook, but is placed directly after so guaranteed to run after wp_head hooked content.
	 *
	 * @since 0.1.0
	 */
	public static function pc_head() {
		do_action( 'pc_head' );
	}

	/**
	 * This hook fires inside the <head> tag, but before pc_head().
	 *
	 * @since 0.1.0
	 */
	public static function pc_head_top() {
		do_action( 'pc_head_top' );
	}

	/**
	 * This hook fires direclty before the opening <head> tag.
	 *
	 * @since 0.1.0
	 */
	public static function pc_before_head() {do_action( 'pc_before_head' );}

	/**
	 * This hook fires directly after the get_header() hook.
	 *
	 * @since 0.1.0
	 */
	public static function pc_after_get_header() {
		do_action( 'pc_after_get_header' );
	}

    /**
     * This hook fires directly after the opening <footer> tag.
	 *
	 * It also fires after the <section></section> tag. If defined in the theme this is
	 * immediately after the opening <footer> tag.
     *
     * @since 0.1.0
     */
    public static function pc_after_opening_footer_tag() {
        do_action( 'pc_after_opening_footer_tag' );
    }

    /**
     * This hook fires directly before the closing </footer> tag.
	 *
     * @since 0.1.0
     */
    public static function pc_before_closing_footer_tag() {
        do_action( 'pc_before_closing_footer_tag' );
    }

	/**
	 * This hook fires direclty after the closing </footer></div> tag, and directly before
	 * the wp_footer() hook.
	 *
	 * @since 0.1.0
	 */
	public static function pc_after_closing_footer_tag() {
		do_action( 'pc_after_closing_footer_tag' );
	}

	/**
	 * This hook fires directly before the opening <div class="content"> tag.
	 *
	 * @since 0.1.0
	 */
	public static function pc_before_content() {
		do_action( 'pc_before_content' );
	}

	/**
	 * This hook fires directly after the opening <div class="content"> tag.
	 *
	 * @since 0.1.0
	 */
	public static function pc_after_content_open() {
		do_action( 'pc_after_content_open' );
	}

	/**
	 * This hook fires directly after the closing <div class="content"> tag.
	 *
	 * @since 0.1.0
	 */
	public static function pc_after_content() {
		do_action( 'pc_after_content' );
	}

	/**
	 * This hook fires directly after the opening <div class="post-meta"> tag.
	 *
	 * @since 0.1.0
	 */
	public static function pc_post_meta() {
		do_action( 'pc_post_meta' );
	}

	/**
	 * This hook fires directly before the closing post-meta div tag: <div class="post-meta">
	 *
	 * @since 0.1.0
	 */
	public static function pc_after_post_meta() {
		do_action( 'pc_after_post_meta' );
	}

	/**
	 * This hook fires directly before the opening <div class="post-meta"> tag.
	 *
	 * @since 0.1.0
	 */
	public static function pc_pre_post_meta() {
		do_action( 'pc_pre_post_meta' );
	}

    /**
     * This hook fires in theme_options_form.php at a particular point in the form output.
     *
     * Use it to add one or more theme options at a specific point in the theme options page.
     *
     * @since 0.1.0
     */
    public static function pc_set_theme_option_fields_1() {
        do_action( 'pc_set_theme_option_fields_1' );
    }

    /**
     * This hook fires in theme_options_form.php in the header options section.
     *
     * Use it to quickly add theme options that are just a sinlge form field. i.e. check box etc.
     *
     * @since 0.1.0
     */
    public static function pc_set_header_theme_option_fields() {
        do_action( 'pc_set_header_theme_option_fields' );
    }

    /**
     * This hook fires in theme_options_form.php in the misc options section.
     *
     * Use it to quickly add theme options that are just a sinlge form field. i.e. check box etc.
     *
     * @since 0.1.0
     */
    public static function pc_set_theme_option_fields_misc() {
        do_action( 'pc_set_theme_option_fields_misc' );
    }

    /**
      * This hook fires in theme_options.php after the theme default settings have been defined.
      *
      * Use it to add custom default theme option settings. i.e. for theme specific options.
      *
      * @since 0.1.0
      */
     public static function pc_theme_option_defaults() {
        do_action( 'pc_theme_option_defaults' );
    }

    /**
      * This hook fires in theme_options_form.php after JS/jQuery has been added.
      *
      * Use it to add custom JS/jQuery. i.e. to manipulate theme options.
      *
      * @since 0.1.0
      */
     public static function pc_theme_option_js() {
        do_action( 'pc_theme_option_js' );
    }

    /**
     * This hook fires directly after the opening <div id="container"> tag in theme template files.
     *
     * @since 0.1.0
     */
    public static function pc_after_container() {
        do_action( 'pc_after_container' );
    }

    /**
     * This hook fires in theme_options_form.php in the custom colors section.
     *
     * Use it to add things like color picker options, or the color schemes drop down box.
     *
     * @since 0.1.0
     */
    public static function pc_set_theme_option_fields_custom_colors() {
        do_action( 'pc_set_theme_option_fields_custom_colors' );
    }

    /**
     * This filter hook allows you to add custom primary sidebars for archive pages.
     *
     * For example if you have a CPT archive page then you can use this filter to specify a
	 * custom sidebar for that page. Otherwise the default post loop will be used.
     *
     * @since 0.1.0
     */
    public static function pc_custom_primary_sidebar_archive($custom_archive_pages) {
        return apply_filters( 'pc_custom_primary_sidebar_archive', $custom_archive_pages );
    }

    /**
     * This filter hook allows you to add custom primary sidebars for archive custom post types.
     *
     * For example if you have a CPT defined then you can use this filter to specify a custom
	 * sidebar for that CPT.
     *
     * @since 0.1.0
     */
    public static function pc_custom_primary_sidebar_posts($custom_theme_posts) {
        return apply_filters( 'pc_custom_primary_sidebar_posts', $custom_theme_posts );
    }

    /**
     * This filter hook allows you to add custom primary sidebars for theme page templates.
     *
     * For example if you use specific theme page templates from theme to theme you can use
	 * this hook to easily add a custom sidebar for those page templates.
     *
     * @since 0.1.0
     */
    public static function pc_custom_primary_sidebar_pages($custom_theme_pages) {
        return apply_filters( 'pc_custom_primary_sidebar_pages', $custom_theme_pages );
    }
}

?>