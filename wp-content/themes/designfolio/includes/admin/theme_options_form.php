	<div class="wrap pc_settings">

		<div class="icon32" id="icon-themes"><br></div>
		<a href="http://www.presscoders.com/designfolio-offer#buy" target="_blank"><img src="<?php echo PC_THEME_ROOT_URI.'/images/df-free-options-upgrade.png'; ?>" class="alignright" /></a>
		<img src="<?php echo PC_THEME_ROOT_URI.'/images/designfolio-options-logo.png'; ?>" class="optionslogo" />

		<?php
			// Check to see if user clicked on the reset options button
			if(isset($_POST['reset_options'])) {
				// Access theme defaults
				global $pc_default_options;
				
				// Reset theme defaults
				update_option(PC_OPTIONS_DB_NAME, $pc_default_options);

				// Display update notice here
				?>
				<div class="error"><p><?php printf( __( '%s theme options have been reset!', 'presscoders' ), PC_THEME_NAME ); ?></p></div>
				<?php
				PC_Utility::pc_fadeout_element('.error'); // fadeout .updated class
			}

			// Check to see if user clicked on the reset options button
			if( isset($_GET['settings-updated']) && !isset($_POST['reset_options']) ) {
				?>
				<div class="updated"><p><?php printf( __( '%s theme options updated!', 'presscoders' ), PC_THEME_NAME ); ?></p></div>
				<?php
				PC_Utility::pc_fadeout_element(); // fadeout .updated class
			}
		?>

		<!-- Start Main Form -->
		<form name="<?php echo PC_THEME_NAME_SLUG; ?>_options_form" method="post" action="options.php">
			
			<?php settings_fields( PC_THEME_OPTIONS_GROUP ); ?>
			<?php $options = get_option(PC_OPTIONS_DB_NAME); ?>
		<script language="javascript">

		jQuery(document).ready(function($) {
			// Setup ul.tabs to work as jQuery UI tabs for each div directly under div.panes
			$("ul.tabs").tabs("div.panes > div");

			$(".tooltipimg[title]").tooltip({
				effect: 'slide',
				fadeOutSpeed: 100,
				predelay: 350
			});
		});

		// Toggle display of extra options if custom logo option selected
		jQuery(document).ready(function($) {
			// Sync the toggle with the state of the custom logo checkbox		
			if( $('#theme_options_custom_logo_check').attr('checked') )
				$("#theme_options_custom_logo").css("display","table-row");
			else
				$("#theme_options_custom_logo").css("display","none");

			// Toggles the state of the custom logo checkbox and displays the upload text box/buttons
			$("#theme_options_custom_logo_check").click(function () {
				$("#theme_options_custom_logo").toggle("100");
			});

			// Show the 'Update Logo' submit button when the logo image url changes
			//$("#update_image").change( function() {
			$("input[type='text']#upload_image").focus( function() {
				$("#update_logo").css("display","inline");
			});

		});

		<?php
			// need to get a valid post id for the media uploader to work properly
			$posts = new WP_Query();
			$first_post_id = $posts->query('posts_per_page=1');
			$first_post_id = $first_post_id[0]->ID; // only 1 post so get id from first index
		?>

		// This is for the logo upload
		jQuery(document).ready(function() {
			jQuery('#upload_image_button').click(function() {
				formfield = jQuery('#upload_image').attr('name');
				tb_show('', 'media-upload.php?type=image&amp;<?php echo PC_THEME_NAME_SLUG; ?>_replace_text=true&amp;post_id=<?php echo $first_post_id; ?>&amp;TB_iframe=true');
				return false;
			});

			window.send_to_editor = function(html) {
				imgurl = jQuery('img',html).attr('src');
				if(imgurl==undefined) {
					imgurl = "Image undefined, please try again.";
				}
				url_imgurl = jQuery('img',html).attr('src');
				jQuery('#upload_image').val(imgurl);
				jQuery("input[type='text']#upload_image").focus();
				tb_remove();
			}
		});

		</script>

        <?php
            /* Add theme specific JS/jQuery via this hook. */
            PC_Hooks::pc_theme_option_js(); /* Framework hook wrapper */
        ?>

		<!-- jQuery UI Tabs Options Layout -->
		<div class="clear"></div>

		<div id="bluetabs">
		
		<div id="pc-buttons" style="width:410px;">
			<a class="button-secondary pc-lower" href="http://www.presscoders.com/designfolio-setup/" target="_blank">Theme Setup Tutorials</a>
			<a class="button-secondary pc-lower" href="http://www.presscoders.com/designfolio-offer#buy" target="_blank">Upgrade to Pro - Get $20 OFF!!</a>
			<a href="https://twitter.com/#!/presscoders" target="_blank"><img src="<?php echo PC_THEME_ROOT_URI.'/images/twitter.png'; ?>" class="pc-icon" /></a>
			<a href="https://www.facebook.com/PressCoders" target="_blank"><img src="<?php echo PC_THEME_ROOT_URI.'/images/facebook.png'; ?>" class="pc-icon" /></a>
		</div>

		<ul class="tabs">
			<li class="first"><a href="#"><?php _e( 'Theme Appearance', 'presscoders' ); ?></a></li>
			<li class="last"><a href="#"><?php _e( 'General Settings', 'presscoders' ); ?></a></li>
		</ul>
		 
		<!-- tab "panes" --> 
		<div class="panes"> 
			
			<!-- "Theme Appearance" tab --> 
			<div class="tabcontent">
			 <div class="ltinfo">
			 
			  <img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/hdr-icon.png'; ?>" width="32" height="32" class="optionsicon" />
			  <h3><?php _e( 'Header', 'presscoders' ); ?></h3>
			 
			  <p><?php _e( 'Change your site description display settings.', 'presscoders' ); ?></p>
			 
			 </div><!-- .ltinfo -->
			 
			 <div class="rtoptions">
			 
				<div class="box">
				
				<img src="<?php echo PC_THEME_ROOT_URI.'/images/custom-logo-grayedout.png'; ?>" class="tooltipimg" title="Upgrade to Pro to use a custom logo, get more color schemes, custom CSS, Google fonts, SEO options, and more!" /><br />
				
				<label><input name="<?php echo PC_OPTIONS_DB_NAME; ?>[chk_hide_description]" type="checkbox" value="1" class="alignleft" <?php if (isset($options[ 'chk_hide_description' ])) { checked('1', $options[ 'chk_hide_description' ]); } ?> /> <?php _e( 'Hide site description', 'presscoders' ); ?>
				<img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/tooltip.png'; ?>" width="17" height="16" class="tooltipimg" title="<?php _e( 'Check this box to hide the text that is displayed next to your site title or company logo. Modify this text under Settings > General.', 'presscoders' ); ?>" />
				</label>
				</div>

				</div><!-- .rtoptions -->
				
				<div class="line"></div>
				
				<div class="ltinfo">
				
				 <img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/palette.png'; ?>" width="32" height="32" class="optionsicon" />
				 <h3><?php _e( 'Custom Colors and Layout', 'presscoders' ); ?></h3>
			 
				 <p><?php _e( 'Customize your site colors, and column layout for posts/pages.', 'presscoders' ); ?></p>
				
				</div><!-- .ltinfo -->
				
				<div class="rtoptions">

					<div class="box">
						<select name='<?php echo PC_OPTIONS_DB_NAME; ?>[<?php echo PC_DEFAULT_LAYOUT_THEME_OPTION; ?>]'>
							<option value='1-col' <?php selected('1-col', $options[ PC_DEFAULT_LAYOUT_THEME_OPTION ]); ?>><?php _e( '1-Column (full width)', 'presscoders' ); ?></option>
							<option value='2-col-l' <?php selected('2-col-l', $options[ PC_DEFAULT_LAYOUT_THEME_OPTION ]); ?>><?php _e( '2-Column Sidebar Left', 'presscoders' ); ?></option>
							<option value='2-col-r' <?php selected('2-col-r', $options[ PC_DEFAULT_LAYOUT_THEME_OPTION ]); ?>><?php _e( '2-Column Sidebar Right', 'presscoders' ); ?></option>
							<option value='3-col-l' <?php selected('3-col-l', $options[ PC_DEFAULT_LAYOUT_THEME_OPTION ]); ?>><?php _e( '3-Column Sidebars Left', 'presscoders' ); ?></option>
							<option value='3-col-r' <?php selected('3-col-r', $options[ PC_DEFAULT_LAYOUT_THEME_OPTION ]); ?>><?php _e( '3-Column Sidebars Right', 'presscoders' ); ?></option>
							<option value='3-col-c' <?php selected('3-col-c', $options[ PC_DEFAULT_LAYOUT_THEME_OPTION ]); ?>><?php _e( '3-Column Content Center', 'presscoders' ); ?></option>
						</select>&nbsp;&nbsp;<?php _e( 'Default Page Layout', 'presscoders' ); ?>
						<img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/tooltip.png'; ?>" width="17" height="16" class="tooltipimg" title="<?php _e( 'This can be overridden for individual posts/pages.', 'presscoders' ); ?>" />
					</div>

                    <?php
                        /* Add theme specific custom color/layout options via this hook. */
                        PC_Hooks::pc_set_theme_option_fields_custom_colors(); /* Framework hook wrapper */
                    ?>

				</div><!-- .rtoptions -->
				
				<div class="line"></div>

                <?php
                    /* Add theme specific default settings vis this hook. */
                    PC_Hooks::pc_set_theme_option_fields_1(); /* Framework hook wrapper */
                ?>

				<div class="ltinfo">
				
				 <img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/sprocket-32.png'; ?>" width="32" height="32" class="optionsicon" />
				 <h3><?php _e( 'Miscellaneous Options', 'presscoders' ); ?></h3>
			 
				 <p><?php _e( 'Various options that affect the theme appearance.', 'presscoders' ); ?></p>
				
				</div><!-- .ltinfo -->
				
				<div class="rtoptions">

					<div class="box">
 						<label><input name="<?php echo PC_OPTIONS_DB_NAME; ?>[chk_show_social_buttons]" type="checkbox" value="1" class="alignleft" <?php if (isset($options[ 'chk_show_social_buttons' ])) { checked('1', $options[ 'chk_show_social_buttons' ]); } ?> /><?php _e( 'Show Twitter/Facebook Buttons?', 'presscoders' ); ?></label>
					</div>

                    <?php
                        /* Add theme specific misc options via this hook. */
                        PC_Hooks::pc_set_theme_option_fields_misc(); /* Framework hook wrapper */
                    ?>

  			   </div><!-- .rtoptions -->

			</div><!-- .tabcontent -->
			
			<!-- "General Settings" tab --> 
			<div class="tabcontent">
			 <div class="ltinfo">
			 
			 <img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/sprocket-32.png'; ?>" width="32" height="32" class="optionsicon" />
			 <h3><?php _e( 'General Settings', 'presscoders' ); ?></h3>
			 
			 <p><?php _e( 'Change the admin e-mail.', 'presscoders' ); ?></p>
			 
			 </div><!-- .ltinfo -->
			 <div class="rtoptions">

				<div class="box">
					<label><?php _e( 'Admin E-mail:', 'presscoders' ); ?> <input type="text" class="gray" style="width: 220px;" name="<?php echo PC_OPTIONS_DB_NAME; ?>[<?php echo PC_ADMIN_EMAIL_TEXTBOX; ?>]" value="<?php echo $options[ PC_ADMIN_EMAIL_TEXTBOX ]; ?>" />
					</label>
				</div>

				</div><!-- .rtoptions -->
				
				<div class="line"></div>

				<div class="ltinfo">
				
				 <img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/edit.png'; ?>" width="32" height="32" class="optionsicon" style="top:18px;" />
				 <h3><?php _e( 'Header &amp; Footer Inserts', 'presscoders' ); ?></h3>
			 
				 <p><?php _e( 'Easily insert your analytics code, scripts, etc. Not for visible content like text or images!', 'presscoders' ); ?></p>
				
				</div><!-- .ltinfo -->
				
				<div class="rtoptions">

			   <h3><?php _e( 'Header Insert', 'presscoders' ); ?> <img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/tooltip.png'; ?>" width="17" height="16" class="tooltipimg" title="<?php _e( 'Any HTML you add here will be inserted into your site\'s header, right before the closing head tag.', 'presscoders' ); ?>" /></h3>
					<textarea name="<?php echo PC_OPTIONS_DB_NAME; ?>[txtarea_header]" cols="35" rows="3" class="gray" type='textarea'><?php echo $options['txtarea_header']; ?></textarea>
			   
			   <h3><?php _e( 'Footer Insert', 'presscoders' ); ?> <img src="<?php echo PC_THEME_ROOT_URI.'/api/images/icons/tooltip.png'; ?>" width="17" height="16" class="tooltipimg" title="<?php _e( 'Any HTML you add here will be inserted right before the closing body tag.', 'presscoders' ); ?>" /></h3>
					<textarea name="<?php echo PC_OPTIONS_DB_NAME; ?>[txtarea_footer]" cols="35" rows="3" class="gray" type='textarea'><?php echo $options['txtarea_footer']; ?></textarea>

			   <!-- Confirmation dialog before resetting the footer link HTML -->
			   <script type="text/javascript">
				function show_confirm_fl()
				{
					var res=confirm("Reset Footer Links?");
					if (res==true) {
						<?php global $pc_footer_links; ?>
						$("#footer_links_textarea").val('<?php echo $pc_footer_links; ?>');
					}
				}
			   </script>
			   
			   <h3><?php _e( 'Footer Links', 'presscoders' ); ?></h3>
			   
					<textarea id="footer_links_textarea" name="<?php echo PC_OPTIONS_DB_NAME; ?>[txtarea_footer_links]" rows="3" class="gray" type='textarea'><?php echo $options['txtarea_footer_links']; ?></textarea><br />
					<input type="button" style="margin-top:10px;" class="button-secondary" value="<?php _e( 'Reset footer links', 'presscoders' ); ?>" onclick="show_confirm_fl()" />
			 
			 </div><!-- .rtoptions -->
			</div><!-- .tabcontent -->
		</div>

		</div><!-- #bluetabs -->

		<span class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'presscoders' ); ?>" /></span>

		</form> <!-- main form closing tag -->

		<form action="<?php echo PC_Utility::currURL(); // current page url ?>" method="post" id="pc-theme-options-reset" style="display:inline;">
			<span class="submit-theme_options-reset">
				<input type="submit" onclick="return confirm('Are you sure? All theme options will be reset to their default settings!');" class="button submit-button reset-button" value="Reset <?php echo PC_THEME_NAME; ?> Options" name="pc_reset">
				<input type="hidden" name="reset_options" value="true">
			</span>
		</form>

	</div><!-- .wrap pc_settings -->