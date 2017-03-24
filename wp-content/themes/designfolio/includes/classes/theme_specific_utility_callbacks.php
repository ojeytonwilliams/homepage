<?php

/**
 * Theme specific utility callbacks class.
 * 
 * Contains theme specific hook callback functions.
 *
 * @since 0.1.0
 */

class PC_TS_Utility_Callbacks {

	/**
	 * PC_TS_Utility callbacks class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'admin_head', array( &$this, 'cpt_icons' ) );
		add_action( 'pc_head', array( &$this, 'pc_ts_custom_wp_head' ) );
	}

    /**
     * Add default theme option settings for the color scheme drop down field.
     *
     * @since 0.1.0
     */
    public function set_color_scheme_default() {

        global $pc_default_options;
		define( "PC_COLOR_SCHEME_DROPDOWN", "drp_color_scheme" );

        $pc_default_options[PC_COLOR_SCHEME_DROPDOWN] = "default";
	}

    /**
     * Add custom form fields for theme specific options.
     *
     * Color scheme drop down option field.
     *
     * @since 0.1.0
     */
    public function set_color_scheme_theme_option_field() {

        $options = get_option(PC_OPTIONS_DB_NAME);
		?>
					<div class="box">
						<select name='<?php echo PC_OPTIONS_DB_NAME; ?>[<?php echo PC_COLOR_SCHEME_DROPDOWN; ?>]'>

							<?php
							global $pc_color_schemes; /* Grab global color scheme array. */

							foreach($pc_color_schemes as $color_scheme => $value){
								echo "<option value='".$value."' ".selected($value, $options[ PC_COLOR_SCHEME_DROPDOWN ]).">".$color_scheme."&nbsp;</option>";
							}
							?>
						
						</select>&nbsp;&nbsp;Color Scheme 
						<img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/tooltip.png'; ?>" width="17" height="16" class="tooltipimg" title="Choose a pre-made color stylesheet (there are an extra 5 color schemes included in Designfolio Pro)" />
					</div>
		<?php
    }

	/**
     * Add default theme option settings for the solid header field.
     *
     * @since 0.1.0
     */
    public function set_solid_header_default() {

        global $pc_default_options;

        $pc_default_options["txtarea_custom_css"] = "";
	}

    /**
     * Add custom form fields for theme specific options.
     *
     * Solid Header option field.
     *
     * @since 0.1.0
     */
    public function set_solid_header_theme_option_field() {

        $options = get_option(PC_OPTIONS_DB_NAME);
		?>
					<div class="box">
						<label><input name="<?php echo PC_OPTIONS_DB_NAME; ?>[chk_solid-header-bg]" type="checkbox" value="1" class="alignleft" <?php if (isset($options[ 'chk_solid-header-bg' ])) { checked('1', $options[ 'chk_solid-header-bg' ]); } ?> /> Use solid header bg color
						<img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/tooltip.png'; ?>" width="17" height="16" class="tooltipimg" title="Check to remove the transparent image in header" />
						</label>
					</div>
		<?php
    }
	
	/**
	 * Render front end social media buttons.
	 *
	 * @since 0.1.0
	 */
	public function render_social_media_buttons() {

		/* Only show on single post */
		if( is_single() ) {
			$options = get_option( PC_OPTIONS_DB_NAME );
			if( isset($options['chk_show_social_buttons']) && $options[ 'chk_show_social_buttons' ] == "1" ) : ?>

			<div class="social-btns">
				<a href="https://twitter.com/share" class="twitter-share-button" data-count="vertical">Tweet</a>
				<br />
				<div id="fb-root"></div><fb:like send="false" layout="box_count" width="60" show_faces="false"></fb:like>
			</div>

			<?php endif;
		}
	}

	/**
	 * Default theme comments template
	 *
	 * @since 0.1.0
	 */
	public function theme_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case '' :
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
				<?php echo get_avatar( $comment, 40 ); ?>
				<?php printf( __( '%s <span class="says">says:</span>', 'presscoders' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
			</div><!-- .comment-author .vcard -->
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em><?php _e( 'Your comment is awaiting moderation.', 'presscoders' ); ?></em>
				<br />
			<?php endif; ?>

			<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
				<?php
					/* translators: 1: date, 2: time */
					printf( __( '%1$s at %2$s', 'presscoders' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'presscoders' ), ' ' );
				?>

				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div><!-- .reply -->

			</div><!-- .comment-meta .commentmetadata -->

			<div class="comment-body"><?php comment_text(); ?></div>
		</div><!-- #comment-##  -->

		<?php
				break;
			case 'pingback'  :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'presscoders' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'presscoders' ), ' ' ); ?></p>
		<?php
				break;
		endswitch;
	}

	/**
	 * Add CPT icons
	 *
	 * @since 0.1.0
	 */
	public function cpt_icons() {
		?>
		<style type="text/css" media="screen">
			#menu-posts-portfolio .wp-menu-image {
				background: url(<?php echo get_template_directory_uri(); ?>/images/cpt-icons.png) no-repeat -60px -33px !important;
			}
			#menu-posts-portfolio:hover .wp-menu-image, #menu-posts-portfolio.wp-has-current-submenu .wp-menu-image {
				background-position:-60px -1px!important;
			}
			#menu-posts-slide .wp-menu-image {
				background: url(<?php echo get_template_directory_uri(); ?>/images/cpt-icons.png) no-repeat -30px -32px !important;
			}
			#menu-posts-slide:hover .wp-menu-image, #menu-posts-slide.wp-has-current-submenu .wp-menu-image {
				background-position:-30px 0!important;
			}
			#menu-posts-testimonial .wp-menu-image {
				background: url(<?php echo get_template_directory_uri(); ?>/images/cpt-icons.png) no-repeat 0 -32px !important;
			}
			#menu-posts-testimonial:hover .wp-menu-image, #menu-posts-testimonial.wp-has-current-submenu .wp-menu-image {
				background-position:0 0!important;
			}
			#icon-edit.icon32-posts-portfolio {
				background: url(<?php echo get_template_directory_uri(); ?>/images/cpt-icons-32.png) no-repeat -86px 0;
			}
			#icon-edit.icon32-posts-slide {
				background: url(<?php echo get_template_directory_uri(); ?>/images/cpt-icons-32.png) no-repeat -47px 0;
			}
			#icon-edit.icon32-posts-testimonial {
				background: url(<?php echo get_template_directory_uri(); ?>/images/cpt-icons-32.png) no-repeat -2px 0;
			}
		</style>
	<?php }

	/**
	 * Add theme specific content to the header.
	 *
	 * @since 0.1.0
	 */
	public function pc_ts_custom_wp_head() {
?><!--[if IE 8]>
<style type="text/css">
.comment-body, li.pingback, .quote, .avatar, .defaultbtn, .button, .btn, #searchsubmit, #submit, .submit, .post-edit-link, .more-link, input[type="submit"], input[type="text"], textarea, ol.flex-control-nav li a, ol.flex-control-nav li a.active, .flex-direction-nav li a, .post-date, nav.secondary-menu, nav ul ul {
behavior: url(<?php echo PC_Utility::theme_resource_uri( 'includes/js', 'PIE.htc' ); ?>);
}
</style>
<![endif]-->
<?php
	}

}

?>