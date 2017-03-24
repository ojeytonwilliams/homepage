<?php

/**
 * Framework meta boxes class.
 *
 * @todo Some of these need moving. Inparticular the sidebar commander ones, which need moving
 * to the sidebar commander class. The jQuery enqueueing function needs moving too. As does
 * theme_seo_settings(), and pc_theme_title().
 *
 * @since 0.1.0
 */
class PC_MetaBoxes {

	/**
	 * PC_MetaBoxes class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		add_action('admin_init', array( &$this, 'theme_meta_box_init' ) );
		add_action( 'load-post.php', array( &$this, 'editor_jquery_ui_tabs' ) );
		add_action( 'load-post-new.php', array( &$this, 'editor_jquery_ui_tabs' ) );
		add_action( 'pc_head_top', array( &$this, 'theme_seo_settings' ) );
	}

	/**
	 * Meta box functions for adding the meta box and saving the data.
	 *
	 * @since 0.1.0
	 */
	public function theme_meta_box_init() {

		$options = get_option( PC_OPTIONS_DB_NAME );

		/* Don't show theme SEO meta box in post/page edit screen if SEO theme option is disabled. */
		if ( isset($options[ PC_SEO_SETTINGS_CHECKBOX ]) && $options[ PC_SEO_SETTINGS_CHECKBOX ] ) {
			add_meta_box( PC_THEME_NAME_SLUG.'-theme-seo-meta', PC_THEME_NAME.': '.__( 'SEO Settings', 'presscoders' ), array( &$this, 'theme_meta_box_seo' ),'post','normal','default');
			add_meta_box( PC_THEME_NAME_SLUG.'-theme-seo-meta', PC_THEME_NAME.': '.__( 'SEO Settings', 'presscoders' ), array( &$this, 'theme_meta_box_seo' ),'page','normal','default');
			// Hook to save our meta box data when the post is saved
			add_action('save_post', array( &$this, 'theme_seo_save_meta_box' ) );
		}

		/* Don't show theme Page Content Templates in page edit screen if theme option is disabled. */
		if( function_exists( 'theme_meta_box_content_templates' ) ) {
			if( defined( 'PC_CONTENT_TEMPLATES_CHECKBOX' ) ) {
				/* If constant defined then check theme options first. */
				if ( isset($options[ PC_CONTENT_TEMPLATES_CHECKBOX ]) && $options[ PC_CONTENT_TEMPLATES_CHECKBOX ] ) {
					$this->add_ct_meta_boxes();
				}
			}
			else {
				/* Rendering of content templates isn't controlled via theme options. */
				$this->add_ct_meta_boxes();
			}
		}

		/* Per post/page layout meta (1-col, 2-col, or 3-col). */
		add_meta_box( PC_THEME_NAME_SLUG.'-display-options-meta', __( 'Post Display Options', 'presscoders' ), array( &$this, 'theme_meta_box_display_options' ),'post','side','default',array('type'=>'post') );
		add_meta_box( PC_THEME_NAME_SLUG.'-display-options-meta', __( 'Page Display Options', 'presscoders' ), array( &$this, 'theme_meta_box_display_options' ),'page','side','default',array('type'=>'page') );

		// @todo add these two in via hooks in each of the CPT classes.
		add_meta_box( PC_THEME_NAME_SLUG.'-display-options-meta', __( 'Portfolio Display Options', 'presscoders' ), array( &$this, 'theme_meta_box_display_options' ),'portfolio','side','default',array('type'=>'portfolio') );
		add_meta_box( PC_THEME_NAME_SLUG.'-display-options-meta', __( 'Slide Display Options', 'presscoders' ), array( &$this, 'theme_meta_box_display_options' ),'slide','side','default',array('type'=>'slide') );

		/* Hook to save our meta box data when the post is saved. */
		add_action('save_post', array( &$this, 'theme_display_options_save_meta_box' ) );

		if( current_theme_supports( 'sidebar-commander' ) ) {
			/* Add Sidebar Commander meta box to post/page editor if theme supports it. */
			add_meta_box( PC_THEME_NAME_SLUG.'-widget-areas-meta', PC_THEME_NAME.': '.__( 'Sidebar Commander', 'presscoders' ), array( &$this, 'theme_meta_box_widget_areas' ),'post','normal','default',array('type'=>'post'));
			add_meta_box( PC_THEME_NAME_SLUG.'-widget-areas-meta', PC_THEME_NAME.': '.__( 'Sidebar Commander', 'presscoders' ), array( &$this, 'theme_meta_box_widget_areas' ),'page','normal','default',array('type'=>'page'));
			add_action('save_post', array( &$this, 'theme_widget_areas_save_meta_box' ) );
		}
	}

	/**
	 * Adds the content template meta box to post/page editor.
	 *
	 * @since 0.1.0
	 */
	public function add_ct_meta_boxes() {
		/* Add content templates to Pages. */
		add_meta_box( PC_THEME_NAME_SLUG.'-content-templates', PC_THEME_NAME.': '.__( 'Content Templates', 'presscoders' ), 'theme_meta_box_content_templates','page','normal','default');

		/* Add content templates to Posts. */
		add_meta_box( PC_THEME_NAME_SLUG.'-content-templates', PC_THEME_NAME.': '.__( 'Content Templates', 'presscoders' ), 'theme_meta_box_content_templates','post','normal','default');

		/* Hook to save our meta box data when the post is saved. */
		add_action('save_post','theme_content_templates_save_meta_box');
	}

	/**
	 * Renders the sidebar commander on post/page editor.
	 *
	 * @since 0.1.0
	 */
	public function theme_meta_box_widget_areas($post,$args) {
		
		// Retrieve our custom meta box values
		$pc_primary_widget_areas = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_primary_sort',true);
		$pc_secondary_widget_areas = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_secondary_sort',true);
		$pc_main_content_widget_areas = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_main_content_sort',true);

		$pc_theme_widget_areas_save = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_widget_areas_save',true);
		?>

		<script type="text/javascript">

			jQuery(document).ready(function($) {

				// Enable jQuery UI tabs
				jQuery(document).ready(function($) {
					$(".tabs").tabs();
				});

				// Set-up sortable and placeholders for sidebar commander
				$( "#primary_widget_areas, #secondary_widget_areas, #main_content_widget_areas" ).sortable({
					placeholder: "ui-state-highlight"
				});
				// Make sure the link text can' be selected whilst dragging
				$( "#primary_widget_areas, #secondary_widget_areas, #main_content_widget_areas" ).disableSelection();
				
				// When page loads set the initial opacity of checked checkboxes to 100%
				$("input:checkbox:checked").parent( 'li' ).each(function (i) {
					if(jQuery.support.opacity) {
						$(this).css("opacity", "1");
					}
					else {
						this.style.filter = "alpha(opacity=100)"; // IE fix
					}
				});

				// When page loads set the initial opacity of unchecked checkboxes to 30%
				$("input:checkbox:not(:checked)").parent( 'li' ).each(function (i) {
					if(jQuery.support.opacity) {
						$(this).css("opacity", "0.30");
					}
					else {
						this.style.filter = "alpha(opacity=30)"; // IE fix
					}
				});

				// When a checkbox is clicked, toggle the opacity
				$("input:checkbox").click(function () {
					if( this.checked ) {
						$(this).parent( 'li' ).css("opacity", "1");
					}
					else {
						$(this).parent( 'li' ).css("opacity", "0.30");
					}
				});
			});

		</script>

		<?php

		// Get column layout for this post/page
		$pc_column_layout = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_column_layout',true);

		// Get the page template used by this page (if a page)
		if( $args['args']['type'] == "page") {
			$pc_page_template = get_post_meta(get_the_ID(), '_wp_page_template',true);
		}

		$options = get_option( PC_OPTIONS_DB_NAME );

		if( empty($pc_column_layout) || $pc_column_layout == 'default' ) {
			$layout = $options[ PC_DEFAULT_LAYOUT_THEME_OPTION ];
		}
		else {
			$layout = $pc_column_layout;
		}

		$layout_text_after = "";
		//$layout_text_after = " is indicated by the icon to the left.<br /><br />";

		if( $layout == "1-col" ) {
			// Define the layout icons to show on each visible tab
			$main_content_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/full.png';

			// Define the layout text
			$main_content_layout_text = "";
			//$main_content_layout_text = "The <span class=\"underline\">main content</span> location for this ".$args['args']['type']." with the current layout (1-col, full width) ";
		}
		elseif( $layout == "2-col-l" ) {
			// Define the layout icons to show on each visible tab
			$primary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/primary-left.png';
			$main_content_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/main-right.png';

			// Define the layout text
			$primary_layout_text = "";
			$main_content_layout_text = "";
			//$primary_layout_text = "The <span class=\"underline\">primary</span> sidebar location for this ".$args['args']['type']." with the current layout (2-col, sidebar left) ";
			//$main_content_layout_text = "The <span class=\"underline\">main content</span> location for this ".$args['args']['type']." with the current layout (2-col, sidebar left) ";
		}
		elseif( $layout == "2-col-r" ) {
			// Define the layout icons to show on each visible tab
			$primary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/primary-right.png';
			$main_content_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/main-left.png';

			// Define the layout text
			$primary_layout_text = "";
			$main_content_layout_text = "";
			//$primary_layout_text = "The <span class=\"underline\">primary</span> sidebar location for this ".$args['args']['type']." with the current layout (2-col, sidebar right) ";
			//$main_content_layout_text = "The <span class=\"underline\">main content</span> location for this ".$args['args']['type']." with the current layout (2-col, sidebar right) ";
		}
		elseif( $layout == "3-col-l" ) {
			// Define the layout icons to show on each visible tab
			$primary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-left.png';
			$secondary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-left1.png';
			$main_content_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-main-right.png';

			// Define the layout text
			$primary_layout_text = "";
			$secondary_layout_text = "";
			$main_content_layout_text = "";
			//$primary_layout_text = "The <span class=\"underline\">primary</span> sidebar location for this ".$args['args']['type']." with the current layout (3-col, sidebars left) ";
			//$secondary_layout_text = "The <span class=\"underline\">secondary</span> sidebar location for this ".$args['args']['type']." with the current layout (3-col, sidebars left) ";
			//$main_content_layout_text = "The <span class=\"underline\">main content</span> location for this ".$args['args']['type']." with the current layout (3-col, sidebars left) ";
		}
		elseif( $layout == "3-col-r" ) {
			// Define the layout icons to show on each visible tab
			$primary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-middle1.png';
			$secondary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-right.png';
			$main_content_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-main-left.png';

			// Define the layout text
			$primary_layout_text = "";
			$secondary_layout_text = "";
			$main_content_layout_text = "";
			//$primary_layout_text = "The <span class=\"underline\">primary</span> sidebar location for this ".$args['args']['type']." with the current layout (3-col, sidebars right) ";
			//$secondary_layout_text = "The <span class=\"underline\">secondary</span> sidebar location for this ".$args['args']['type']." with the current layout (3-col, sidebars right) ";
			//$main_content_layout_text = "The <span class=\"underline\">main content</span> location for this ".$args['args']['type']." with the current layout (3-col, sidebars right) ";
		}
		else {
			// Layout must be 3-col-c
			// Define the layout icons to show on each visible tab
			$primary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-left-2.png';
			$secondary_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-right-2.png';
			$main_content_layout_icon = PC_THEME_ROOT_URI.'/api/images/layout-icons/3col-main-middle.png';

			// Define the layout text
			$primary_layout_text = "";
			$secondary_layout_text = "";
			$main_content_layout_text = "";
			//$primary_layout_text = "The <span class=\"underline\">primary</span> sidebar location for this ".$args['args']['type']." with the current layout (3-col, content center) ";
			//$secondary_layout_text = "The <span class=\"underline\">secondary</span> sidebar location for this ".$args['args']['type']." with the current layout (3-col, content center) ";
			//$main_content_layout_text = "The <span class=\"underline\">main content</span> location for this ".$args['args']['type']." with the current layout (3-col, content center) ";
		}

		// Reference the global widget area variable here so we can access it
		global $wp_registered_sidebars;
		$tmp_primary_wp_registered_sidebars = $wp_registered_sidebars; // store a primary copy
		$tmp_secondary_wp_registered_sidebars = $wp_registered_sidebars; // store a secondary copy
		$tmp_main_content_wp_registered_sidebars = $wp_registered_sidebars; // store a main content copy

		?>

		<div id="page-sidebar-commander">
			<div class="tabs">
				<?php
					// Set this to null if not defined to suppress 'not defined' error notices 
					if( !isset($pc_page_template) ) $pc_page_template = null;
					// Display message if there are no sidebars or main content areas available
					if( $layout == "1-col" && $pc_page_template != "widgetized-page.php" ) {
						echo "This ".$args['args']['type']." has no sidebars, so no unique widget areas can be defined here. Select a two or three column layout to be able to edit sidebar widget areas.";
					}
					else { // Show the tabs ?>
						<!-- the tabs -->
						<ul>
							<?php if($layout != "1-col") { ?> <li><a href="#tabs-primary"><?php _e( 'Primary Sidebar', 'presscoders' ); ?></a></li> <?php } ?>
							<?php
								if($pc_page_template == "widgetized-page.php") { ?>
									<li><a href="#tabs-maincontent"><?php _e( 'Main Content Area', 'presscoders' ); ?></a></li>
								<?php }
							?>
							<?php if($layout == "3-col-l" || $layout == "3-col-r" || $layout == "3-col-c") { ?>
								<li><a href="#tabs-secondary"><?php _e( 'Secondary Sidebar', 'presscoders' ); ?></a></li>
							<?php } ?>
						</ul>
					<?php }
				?>
				
				<?php // ** PRIMARY SIDEBAR WIDGET AREAS ** ?>

				<?php if($layout != "1-col") {
					// %1$s
					$msg = sprintf( __('%1$s%2$sAdd any widget area to the %3$sprimary%4$s sidebar of this %5$s by clicking the checkboxes. Drag and drop to dynamically reorder them. %6$sThe content for each widget area is controlled on the %7$sWidgets%8$s page.', 'presscoders'), $primary_layout_text, $layout_text_after, '<span class="underline">', '</span>', $args['args']['type'], '<br/><br/>', '<a href="'.get_admin_url( null, 'widgets.php' ).'">', '</a>' );
				?>

					<div id="tabs-primary">
						<ul id="primary_widget_areas">
							<?php
							
							// Create array with some entries to exclude from widget areas list
							$exclude_from_primary = array('secondary-post-widget-area', 'secondary-page-widget-area', 'header-widget-area', 'footer-widget-area');

							// To avoid confusion, remove secondary sidebars on the primary sidebar list
							foreach($exclude_from_primary as $exclude) {
								if( isset($tmp_primary_wp_registered_sidebars[$exclude]) ) {
									unset($tmp_primary_wp_registered_sidebars[$exclude]);
								}
							}

							// Now sort array before printing it
							if( !empty($pc_primary_widget_areas) ) {
								$tmp_primary_wp_registered_sidebars = PC_Utility::sortMultiArrayByArray($tmp_primary_wp_registered_sidebars, $pc_primary_widget_areas);
							}

							if( is_array($tmp_primary_wp_registered_sidebars) ) {
								foreach ($tmp_primary_wp_registered_sidebars as $tmp_primary_wp_registered_sidebar) {

									// Only show widget areas with a width of 'normal'
									if($tmp_primary_wp_registered_sidebar['width'] != "normal")
										continue;

									if (isset( $pc_primary_widget_areas[$tmp_primary_wp_registered_sidebar['id']] )) { $chk = checked('1', esc_attr($pc_primary_widget_areas[$tmp_primary_wp_registered_sidebar['id']]), false); }
									else {
										$chk = '';
									}

									echo '<li class="ui-state-default"><input type="checkbox" name="'.PC_THEME_NAME_SLUG.'_primary_sort['.$tmp_primary_wp_registered_sidebar['id'].']" id="'.PC_THEME_NAME_SLUG.'_primary_sort['.$tmp_primary_wp_registered_sidebar['id'].']" value="1" '.$chk.'><label for="'.PC_THEME_NAME_SLUG.'_primary_sort['.$tmp_primary_wp_registered_sidebar['id'].']">'.$tmp_primary_wp_registered_sidebar['name'].'</label></li>';
								}
							}

							?>

						</ul>
						<p class="column_message"><img class="layouticon" src="<?php echo $primary_layout_icon; ?>" /><?php echo $msg; ?></p>
					</div><!-- #tabs-primary -->

				<?php } ?>

				<?php // ** MAIN CONTENT SIDEBAR WIDGET AREAS ** ?>

				<?php
					if($pc_page_template == "widgetized-page.php") {
						$msg = sprintf( __('%1$s%2$sAdd any widget area to the %3$smain content%4$s area of this %5$s by clicking the checkboxes. Drag and drop to dynamically reorder them. %6$sThe content for each widget area is controlled on the %7$sWidgets%8$s page.', 'presscoders'), $main_content_layout_text, $layout_text_after, '<span class="underline">', '</span>', $args['args']['type'], '<br/><br/>', '<a href="'.get_admin_url( null, 'widgets.php' ).'">', '</a>' );
					?>

						<div id="tabs-maincontent">
							<ul id="main_content_widget_areas">
								<?php

								// Create array with some entries to exclude from widget areas list
								//$exclude_from_main_content = array('primary-post-widget-area', 'primary-page-widget-area', 'secondary-post-widget-area', 'secondary-page-widget-area');

								// To avoid confusion, remove secondary sidebars on the primary sidebar list
								/*foreach($exclude_from_main_content as $exclude) {
									if( isset($tmp_main_content_wp_registered_sidebars[$exclude]) ) {
										unset($tmp_main_content_wp_registered_sidebars[$exclude]);
									}
								}*/

								// Now sort array before printing it
								if( !empty($pc_main_content_widget_areas) ) {
									$tmp_main_content_wp_registered_sidebars = PC_Utility::sortMultiArrayByArray($tmp_main_content_wp_registered_sidebars, $pc_main_content_widget_areas);
								}

								if( is_array($tmp_main_content_wp_registered_sidebars) ) {
									foreach ($tmp_main_content_wp_registered_sidebars as $tmp_main_content_wp_registered_sidebar) {

										// Only show widget areas with a width of 'wide'
										if($tmp_main_content_wp_registered_sidebar['width'] != "wide")
											continue;

										if (isset( $pc_main_content_widget_areas[$tmp_main_content_wp_registered_sidebar['id']] )) { $chk = checked('1', esc_attr($pc_main_content_widget_areas[$tmp_main_content_wp_registered_sidebar['id']]), false); }
										else {
											$chk = '';
										}

										echo '<li class="ui-state-default"><input type="checkbox" name="'.PC_THEME_NAME_SLUG.'_main_content_sort['.$tmp_main_content_wp_registered_sidebar['id'].']" id="'.PC_THEME_NAME_SLUG.'_main_content_sort['.$tmp_main_content_wp_registered_sidebar['id'].']" value="1" '.$chk.'><label for="'.PC_THEME_NAME_SLUG.'_main_content_sort['.$tmp_main_content_wp_registered_sidebar['id'].']">'.$tmp_main_content_wp_registered_sidebar['name'].'</label></li>';
									}
								}

								?>

							</ul>
							<p class="column_message"><img class="layouticon" src="<?php echo $main_content_layout_icon; ?>" /><?php echo $msg; ?></p>
						</div><!-- #tabs-maincontent -->

				<?php } // endif ?>

				<?php // ** SECONDARY SIDEBAR WIDGET AREAS ** ?>

				<?php if($layout == "3-col-l" || $layout == "3-col-r" || $layout == "3-col-c") {
					$msg = sprintf( __('%1$s%2$sAdd any widget area to the %3$ssecondary%4$s sidebar of this %5$s by clicking the checkboxes. Drag and drop to dynamically reorder them. %6$sThe content for each widget area is controlled on the %7$sWidgets%8$s page.', 'presscoders'), $secondary_layout_text, $layout_text_after, '<span class="underline">', '</span>', $args['args']['type'], '<br/><br/>', '<a href="'.get_admin_url( null, 'widgets.php' ).'">', '</a>' );
				?>

					<div id="tabs-secondary">
						<ul id="secondary_widget_areas">
							<?php
							
							// Create array with some entries to exclude from widget areas list
							$exclude_from_secondary = array('primary-post-widget-area', 'primary-page-widget-area', 'header-widget-area', 'footer-widget-area');

							// To avoid confusion, remove secondary sidebars on the secondary sidebar list
							foreach($exclude_from_secondary as $exclude) {
								if( isset($tmp_secondary_wp_registered_sidebars[$exclude]) ) {
									unset($tmp_secondary_wp_registered_sidebars[$exclude]);
								}
							}

							// Now sort array before printing it
							if( !empty($pc_secondary_widget_areas) ) {
								$tmp_secondary_wp_registered_sidebars = PC_Utility::sortMultiArrayByArray($tmp_secondary_wp_registered_sidebars, $pc_secondary_widget_areas);
							}

							if( is_array($tmp_secondary_wp_registered_sidebars) ) {
								foreach ($tmp_secondary_wp_registered_sidebars as $tmp_secondary_wp_registered_sidebar) {

									// Only show widget areas with a width of 'normal'
									if($tmp_secondary_wp_registered_sidebar['width'] != "normal")
										continue;

									if (isset( $pc_secondary_widget_areas[$tmp_secondary_wp_registered_sidebar['id']] )) { $chk = checked('1', esc_attr($pc_secondary_widget_areas[$tmp_secondary_wp_registered_sidebar['id']]), false); }
									else {
										$chk = '';
									}

									echo '<li class="ui-state-default"><input type="checkbox" name="'.PC_THEME_NAME_SLUG.'_secondary_sort['.$tmp_secondary_wp_registered_sidebar['id'].']" id="'.PC_THEME_NAME_SLUG.'_secondary_sort['.$tmp_secondary_wp_registered_sidebar['id'].']" value="1" '.$chk.'><label for="'.PC_THEME_NAME_SLUG.'_secondary_sort['.$tmp_secondary_wp_registered_sidebar['id'].']">'.$tmp_secondary_wp_registered_sidebar['name'].'</label></li>';
								}
							}

							?>

						</ul>
						<p class="column_message"><img class="layouticon" src="<?php echo $secondary_layout_icon; ?>" /><?php echo $msg; ?></p>
					</div><!-- #tabs-secondary -->
				<?php } ?>
			</div><!-- .tabs -->

			<input type="hidden" name="<?php echo PC_THEME_NAME_SLUG; ?>_widget_areas_save" id="<?php echo PC_THEME_NAME_SLUG; ?>_widget_areas_save" value="<?php echo esc_attr($pc_theme_widget_areas_save); ?>">

		</div><!-- #page-sidebar-commander -->
		<?php
	}

	/**
	 * Save sidebar commander widget areas meta box settings.
	 *
	 * @since 0.1.0
	 */
	public function theme_widget_areas_save_meta_box($post_id) {

		// process form data if $_POST is set
		if(isset($_POST[ PC_THEME_NAME_SLUG.'_widget_areas_save' ])) {
			// save the meta box data as post meta, using the post ID as a unique prefix
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_primary_sort', $_POST[ PC_THEME_NAME_SLUG.'_primary_sort']);
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_main_content_sort', $_POST[ PC_THEME_NAME_SLUG.'_main_content_sort']);
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_secondary_sort', $_POST[ PC_THEME_NAME_SLUG.'_secondary_sort']);
			//update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_widget_areas', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_widget_areas']));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_widget_areas_save', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_widget_areas_save' ]));
		}
	}

	/**
	 * Add jQuery UI tabs script to post/page editor screen (post.php, post-new.php)
	 *
	 * @since 0.1.0
	 */
	public function editor_jquery_ui_tabs() {

		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_style( 'theme_admin_stylesheet', PC_THEME_ROOT_URI.'/includes/css/theme_admin.css' );
		wp_enqueue_style( 'jquery-tab-styles', PC_THEME_ROOT_URI.'/api/css/jquery-ui-tabs.css' );
		wp_enqueue_style( 'sidebar-commander-styles', PC_THEME_ROOT_URI.'/api/css/sidebar-commander.css' );
	}

	/**
	 * Display the column layout meta box on post/page editor.
	 *
	 * @since 0.1.0
	 */
	public function theme_meta_box_display_options($post,$args) {

		/* Retrieve our custom meta box values. */
		$pc_column_layout = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_column_layout', true);
		$pc_portfolio_group = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_portfolio_group', true);
		$pc_portfolio_columns = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_portfolio_columns', true);
		$pc_theme_column_layout_save = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_column_layout_save', true);
		$hide_title_header_tag = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_hide_title_header_tag', true);

		?>

		<div class="inside">
			<p>
				<?php _e( 'Column layout', 'presscoders' ); ?>:<br />
				<select name='<?php echo PC_THEME_NAME_SLUG; ?>_column_layout' class='widefat'>
					<option value='default' <?php selected('default', $pc_column_layout); ?>><?php _e( '(Default theme setting)', 'presscoders' ); ?></option>
					<option value='1-col' <?php selected('1-col', $pc_column_layout); ?>><?php _e( '1-Column (full width)', 'presscoders' ); ?></option>
					<option value='2-col-l' <?php selected('2-col-l', $pc_column_layout); ?>><?php _e( '2-Column Sidebar Left', 'presscoders' ); ?></option>
					<option value='2-col-r' <?php selected('2-col-r', $pc_column_layout); ?>><?php _e( '2-Column Sidebar Right', 'presscoders' ); ?></option>
					<option value='3-col-l' <?php selected('3-col-l', $pc_column_layout); ?>><?php _e( '3-Column Sidebars Left', 'presscoders' ); ?></option>
					<option value='3-col-r' <?php selected('3-col-r', $pc_column_layout); ?>><?php _e( '3-Column Sidebars Right', 'presscoders' ); ?></option>
					<option value='3-col-c' <?php selected('3-col-c', $pc_column_layout); ?>><?php _e( '3-Column Content Center', 'presscoders' ); ?></option>
				</select>
				<input type="hidden" name="<?php echo PC_THEME_NAME_SLUG; ?>_column_layout_save" id="pc_theme_column_layout_save" value="<?php echo esc_attr($pc_theme_column_layout_save); ?>">
			</p>
		</div>
		<?php
			$page_type = $args['args']['type'];

			/* Only show these fields on pages. */
			if( $page_type == 'page' ) {
				
				?>
				<!-- Hide page title checkbox -->
				<div class="inside"><p><label class="selectit"><input id="hide_title_header_tag" name="<?php echo PC_THEME_NAME_SLUG; ?>_hide_title_header_tag" type="checkbox" value="1" class="alignleft" <?php if (isset($hide_title_header_tag)) { checked('1', $hide_title_header_tag); } ?> />&nbsp;Hide <?php echo $page_type; ?> title</label></p></div>

				<?php

				$pf_group_exists = get_terms( 'portfolio_group', 'orderby=ID&number=1' );

				/* Check for the portfolio-page.php page template. */
				if( post_type_exists( 'portfolio' ) && isset($post->page_template) && $post->page_template == 'portfolio-page.php' && !empty( $pf_group_exists ) ) {

					/* Important! When portfolio group first selected, save a default group (if any exist). */
					if( empty( $pc_portfolio_group ) ) {
						$pf_group = get_terms( 'portfolio_group', 'orderby=ID&number=1' );
						if( !empty($pf_group) ) {
							update_post_meta( $post->ID, '_'.PC_THEME_NAME_SLUG.'_portfolio_group', $pf_group[0]->term_id );
						}
					}

					?>

					<div class="inside" style="margin-bottom:1px;"><label for="<?php echo $pc_portfolio_group; ?>"><?php _e( 'Portfolio group', 'presscoders' ); ?>:</label>

						<?php
							$args = array(
								//'id' =>			$pc_portfolio_group,
								'orderby' =>		'name',
								'hide_empty'=>		1,
								'hierarchical' =>	1,
								'show_count' =>		1,
								'name' =>			PC_THEME_NAME_SLUG.'_portfolio_group',
								'taxonomy' =>		'portfolio_group',
								'class'=>			'widefat',
								'selected' =>		$pc_portfolio_group
							);
							wp_dropdown_categories( $args );
						?>

					</div>

					<div class="inside">
						<p>
							<?php _e( 'Portfolio image size', 'presscoders' ); ?>:<br />
							<select name='<?php echo PC_THEME_NAME_SLUG; ?>_portfolio_columns' class='widefat'>
								<option value='large' <?php selected('large', $pc_portfolio_columns); ?>><?php _e( 'Large', 'presscoders' ); ?></option>
								<option value='medium' <?php selected('medium', $pc_portfolio_columns); ?>><?php _e( 'Medium', 'presscoders' ); ?></option>
								<option value='small' <?php selected('small', $pc_portfolio_columns); ?>><?php _e( 'Small', 'presscoders' ); ?></option>
							</select>
						</p>
					</div>

					<?php
					
					/* Important! Save default column option when portfolio template first selected. */
					$pf_columns_exists = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_portfolio_columns', true);
					if( empty( $pf_columns_exists ) ) {
						update_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_portfolio_columns', 'large' );
					}

				}
				elseif( post_type_exists( 'portfolio' ) && isset($post->page_template) && $post->page_template == 'portfolio-page.php' && empty( $pf_group_exists ) ) {
					echo '<div class="inside submitbox"><p><a href="'.admin_url( 'edit.php?post_type=portfolio' ).'" class="submitdelete" target="_blank">Add Portfolio items to a group!</a></p></div>';
				}

				?>

		<?php }
	}

	/**
	 * Saves the column layout meta box settings.
	 *
	 * @since 0.1.0
	 */
	public function theme_display_options_save_meta_box($post_id) {

		/* Process form data if $_POST is set */
		if(isset($_POST[ PC_THEME_NAME_SLUG.'_column_layout_save' ])) {
			/* Save the meta box data as post meta, using the post ID as a unique prefix */
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_column_layout', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_column_layout' ]));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_hide_title_header_tag', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_hide_title_header_tag' ]));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_column_layout_save', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_column_layout_save' ]));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_portfolio_group', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_portfolio_group' ]));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_portfolio_columns', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_portfolio_columns' ]));
		}
	}

	/**
	 * Displays the SEO meta box on post/page editor.
	 *
	 * @since 0.1.0
	 */
	public function theme_meta_box_seo($post,$box) {

		// retrieve our custom meta box values
		$pc_theme_title = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_title',true);
		$pc_theme_description = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_description',true);
		$pc_theme_keywords = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_keywords',true);
		$pc_theme_seo_save = get_post_meta($post->ID, '_'.PC_THEME_NAME_SLUG.'_seo_save',true);
		
		// custom meta box form elements
		?>
		
		<table class="form-table" id="seosettings">
			<tbody>
				<tr>
					<th style="width: 10%;"><label for="Title"><?php _e( 'Page Title', 'presscoders' ); ?>:</label></th>
					<td><input type="text" name="<?php echo PC_THEME_NAME_SLUG; ?>_title" id="pc_theme_title" value="<?php echo esc_attr($pc_theme_title); ?>" /></td>
				</tr>
				<tr>
					<th style="width: 10%;"><label for="Description"><?php _e( 'Content Description', 'presscoders' ); ?>:</label></th>
					<td><textarea rows="4" name="<?php echo PC_THEME_NAME_SLUG; ?>_description" id="pc_theme_description"><?php echo esc_attr($pc_theme_description); ?></textarea></td>
				</tr>
				<tr>
					<th style="width: 10%;"><label for="Keywords"><?php _e( 'SEO Keywords', 'presscoders' ); ?>:</label></th>
					<td>
						<input type="text" name="<?php echo PC_THEME_NAME_SLUG; ?>_keywords" id="pc_theme_keywords" value="<?php echo esc_attr($pc_theme_keywords); ?>" />
						<input type="hidden" name="<?php echo PC_THEME_NAME_SLUG; ?>_seo_save" id="pc_theme_seo_save" value="<?php echo esc_attr($pc_theme_seo_save); ?>">
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php
	}

	/**
	 * Saves the SEO meta box settings.
	 *
	 * @since 0.1.0
	 */
	public function theme_seo_save_meta_box($post_id) {

		/* Process form data if $_POST is set. */
		if(isset($_POST[ PC_THEME_NAME_SLUG.'_seo_save' ])) {
			/* Save the meta box data as post meta, using the post ID as a unique prefix. */
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_title', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_title']));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_description', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_description' ]));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_keywords', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_keywords' ]));
			update_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_seo_save', esc_attr($_POST[ PC_THEME_NAME_SLUG.'_seo_save' ]));
		}
	}

	/**
	 * Output the site title. If SEO options are turned on then the title specified in the
	 * post/page is displayed (if it exists). Otherwise wp_title is used.
	 *
	 * @since 0.1.0
	 */
	public function pc_theme_title() {

		$options = get_option( PC_OPTIONS_DB_NAME );
		if( isset($options[ PC_SEO_SETTINGS_CHECKBOX ]) && $options[ PC_SEO_SETTINGS_CHECKBOX ] ) {
			global $wp_query;

			/* Wrap this in theme option to turn seo on/off (if off then use wp_title). */
			if ( is_singular() ) {
				/* If we are on a post/page. */
				$id = $wp_query->get_queried_object_id();
				$theme_title = get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_title',true);

				/* If seo title is blank then use wp_title again. */
				if( empty($theme_title) ) $theme_title = wp_title('|',false,'right').get_bloginfo('name');
			}
			else {
				/* Catchall for other types (custom etc.). */
				$theme_title = wp_title('|',false,'right').get_bloginfo('name');
			}
		}
		else {
			/* Theme SEO settings switched off so use default wp title. */
			$theme_title = wp_title('|',false,'right').get_bloginfo('name');
		}
		echo $theme_title;
	}

	/**
	 * Adds SEO settings from each post/page into the header.
	 *
	 * @since 0.1.0
	 */
	public function theme_seo_settings() {

		// If option to show SEO is set output below, otherwise do not show
		// get keyword and description from post/page meta
?>
<title><?php $this->pc_theme_title(); ?></title>
<?php
		$options = get_option( PC_OPTIONS_DB_NAME );
		if ( isset($options[ PC_SEO_SETTINGS_CHECKBOX ]) && $options[ PC_SEO_SETTINGS_CHECKBOX ] ) { // if this constant not set then this will always be false

			/* Get post/page description, and keywords from individual post/page seo settings. */
			global $wp_query;
			$id = $wp_query->get_queried_object_id();
			$pc_theme_description = get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_description',true);
			$pc_theme_keywords = get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_keywords',true);
?>
<meta name="description" content="<?php echo $pc_theme_description; ?>" />
<meta name="keywords" content="<?php echo $pc_theme_keywords; ?>" />
<?php
		} // end if
	}

}

?>