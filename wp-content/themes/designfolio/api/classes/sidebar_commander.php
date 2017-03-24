<?php

/**
 * Framework sidebar commander class.
 *
 * @since 0.1.0
 */
class PC_SidebarCommander {

	/**
	 * PC_SidebarCommander class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		define( "PC_WIDGET_OPTIONS_DB_NAME", 'presscoders'."_widget_theme_options" );
		define( "PC_THEME_WIDGET_OPTIONS_GROUP", 'presscoders'."_widget_theme_options_group" );

		add_action( 'widgets_admin_page', array( &$this, 'widgets_page_form' ) );
		add_action( 'admin_init', array( &$this, 'register_widget_area_settings_api' ) );
		add_action( 'load-widgets.php', array( &$this, 'enqueue_widget_Page_scripts' ) );
		add_action( 'widgets_init', array( &$this, 'register_extra_widget_areas' ), 12 );
	}

	/**
	 * Render the Sidebar Commander form on widgets.php.
	 *
	 * @since 0.1.0
	 */
	public function widgets_page_form() {
		?>

		<!-- Start main sidebar commander form -->
		<div id="custom_widget_form_container" style="display:none;">
			<form id="add_new_widget_area" name="<?php echo PC_THEME_NAME_SLUG; ?>_widget_options_form" method="post" action="options.php">
				<?php settings_fields(PC_THEME_WIDGET_OPTIONS_GROUP); ?>
				<?php $options = get_option(PC_WIDGET_OPTIONS_DB_NAME); ?>

				<!-- Sidebar Commander -->
				 <div class="sc_container">
				 <h3><?php _e( 'Add Custom Widget Area', 'presscoders' ); ?></h3>

				 <div class="scroll_checkboxes">
						<?php

						$tmp_options = $options; // make temporary copy to hold just the custom widget areas

						if( is_array($options) ) {
							$options_keys = array_keys($options);
							$find = "txt_custom_widget_area";

							// remove all the non-custom widget area text boxes
							foreach($options_keys as $option_key) {
								$res = strpos( $option_key, $find );
								if($res === false) {
									unset($tmp_options[$option_key]);
								}
							}
							
							// Show custom widget areas if any defined
							if ( !empty($tmp_options) ) {
								$i = 1; // set index to 0
								foreach($tmp_options as $tmp_option => $value) {
									// Setting textbox default values here in a loop as there may be quite a few
									$tmp_val = empty($value) ? __( 'Custom Widget Area', 'presscoders' ).' '.$i : $value; ?>

									<input class="widget_area_hidden" name="<?php echo PC_WIDGET_OPTIONS_DB_NAME."[".$tmp_option."]"; ?>" type='hidden' value='<?php echo $tmp_val; ?>' />

								<?php
									$i++; // increment the index
								}
							}
						}
						?>
					 </div><!-- .scroll_checkboxes -->

					<div>
						<!-- Label for new widget area -->
						<label><?php _e( 'Name', 'presscoders' ); ?>: <input id="widget_area_txt_input" name="new_widget_area_label" type='text' /></label>
						<div id="custom-widgets-width-trigger"><a>[+] <em><?php _e( 'Options', 'presscoders' ); ?></em></a></div>
						<div id="custom-widgets-width-toggle">
							<p>
								<?php _e( 'Widget Area Type', 'presscoders' ); ?>:
								<select name='widget_area_width_drp' id='drp_widget_area_type'>
									<option value='normal'><?php _e( 'Sidebar', 'presscoders' ); ?>&nbsp;</option>
									<option value='wide'><?php _e( 'Main Content', 'presscoders' ); ?>&nbsp;</option>
									<!-- Before content widget area <option value='full'></option> -->
								</select>
							</p>

							<?php if( is_array($options) ) : // Any widget areas to delete? ?>
								<p>
									<?php
									$tmp_options = $options; // make temporary copy to hold just the custom widget areas

									// If custom widget areas created, show the delete widget area button and drop down

									$options_keys = array_keys($options);
									$find = "txt_custom_widget_area";

									// Remove all the non-custom widget area text boxes
									foreach($options_keys as $option_key) {
										$res = strpos( $option_key, $find );
										if($res === false) {
											unset($tmp_options[$option_key]);
										}
									}
									
									// Show custom widget areas if any defined
									if ( !empty($tmp_options) ) { ?>
										<select name='delete_widget_area_drp' id='drp_delete_widget_area'>
											<?php
											foreach($tmp_options as $tmp_option => $value) {
													$tmp_val = empty($value) ? 'Custom Widget Area '.$i : $value; ?>
													<option value='<?php echo PC_WIDGET_OPTIONS_DB_NAME."[".$tmp_option."]"; ?>'><?php echo $tmp_val; ?>&nbsp;</option>
											<?php } ?>
										</select>
										<?php
									}
									?>
									<input id="delete_widget_area" type="button" class="button-secondary" value="Delete" />
								</p>
							<?php endif; // if( is_array($options) ) ?>
						</div><!-- #custom-widgets-width-toggle -->
						<input id="add_widget_area" type="button" class="button-primary" value="<?php _e( 'Add New', 'presscoders' ) ?>" />
					</div>

				 </div><!-- .sc_container -->

			</form><!-- custom widget form closing tag -->
		</div><!-- #custom_widget_form_container -->

		<?php
	}

	/**
	 * Register widgets.php sidebar commander options with the Settings API.
	 *
	 * @since 0.1.0
	 */
	public function register_widget_area_settings_api(){
		register_setting( PC_THEME_WIDGET_OPTIONS_GROUP, PC_WIDGET_OPTIONS_DB_NAME );
	}

	/**
	 * Enqueue script and styles for the sidebar commander form on widgets.php.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_widget_Page_scripts() {
		wp_enqueue_script( 'custom_widget_area_script', PC_THEME_ROOT_URI.'/api/js/presscoders/theme_widget_options_form.js' ); // enqueue script on widgets.php page
		wp_enqueue_style( 'custom_widget_area_script', PC_THEME_ROOT_URI.'/api/css/theme_widget_options_form.css' );

		// PHP value(s) to pass into the enqueued JavaScript above
		$params = array(
			'widget_options_db_name' => PC_WIDGET_OPTIONS_DB_NAME
		);

		wp_localize_script( 'custom_widget_area_script', 'custom_widget_options', $params );
	}

	/**
	 * Register optional extra sidebars (widget areas).
	 * 
	 * Register any custom widget areas created with sidebar commander. The priority '11' for this
	 * callback displays the custom widgets AFTER the default ones on widget.php. To make them display
	 * BEFORE the default ones, make this number less than '10'.
	 *
	 * @since 0.1.0
	 */
	public function register_extra_widget_areas() {
		/* Handle the custom widget areas created on the widgets.php page */
		$options = get_option(PC_WIDGET_OPTIONS_DB_NAME);
		if( !is_array($options) ) return; // Just return if theme options array not initialized yet
		
		$tmp_options = $options; // Make temporary copy to hold just the custom widget areas
		$options_keys = array_keys($options);
		$find = "txt_custom_widget_area";

		// Remove all the non-custom widget area text boxes
		foreach($options_keys as $option_key) {
			$res = strpos( $option_key, $find );
			if($res === false) {
				unset($tmp_options[$option_key]);
			}
		}

		// Show custom widget areas if any defined
		if ( !empty($tmp_options) ) {
			foreach($tmp_options as $tmp_option => $value) {
				$split_str = explode("_", $tmp_option);
				$width  = end($split_str); // get last element

				// Format widget area width label
				if( $width == "full" ) {
					$width_label = __( 'Before Content', 'presscoders' );
				}
				elseif ( $width == "wide" ) {
					$width_label = __( 'Main Content', 'presscoders' );
				}
				else {
					// assume widget area is normal width
					$width_label = __( 'Sidebar', 'presscoders' );
				}

				// Test for legacy custom widget areas that may still exist in theme options db
				// If any found, just skip, don't create a widget from them
				if($tmp_option == "txt_custom_widget_area")
					continue;

				// Create the extra widget areas
				register_sidebar( array(
					'name' => $value,
					'id' => $tmp_option,
					'description' => '\''.ucfirst($width_label).'\' '.__( 'custom widget area type.', 'presscoders' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<h3 class="widget-title">',
					'after_title' => '</h3>',
					'width' => $width
				) );
			}
		}
	}

}

?>