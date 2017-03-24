<?php

/**
 * Framework utility class. Contains general helper functions which are all static, so they can
 * be referenced without having to instantiate the class.
 *
 * @since 0.1.0
 */
class PC_Utility {

	/**
	 * PC_Utility class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
	}

	/**
	 * Layout Classes used in page templates and the primary sidebar.
	 *
	 * @since 0.1.0
	 */
	public static function content_layout_classes_primary( $sidebar=false ) {

		global $pc_global_column_layout;

		$cols = $pc_global_column_layout;

		if( $sidebar == true ) { /* Add classes to sidebar. */
			if($cols == "1-col") { return ""; }
			elseif($cols == "2-col-l") { return "left"; }
			elseif($cols == "2-col-r") { return "right"; }
			elseif($cols == "3-col-l") { return "left sidebar-extra-margin-right"; }
			elseif($cols == "3-col-r") { return "right sidebar-extra-margin-right"; }
			elseif($cols == "3-col-c") { return "left sidebar-extra-margin-right"; }
		}
		else { /* Add classes to the content. */
			if($cols == "1-col") { return "content"; }
			elseif($cols == "2-col-l") { return "content right"; }
			elseif($cols == "2-col-r") { return "content left"; }
			elseif($cols == "3-col-l") { return "content right"; }
			elseif($cols == "3-col-r") { return "content left"; }
			elseif($cols == "3-col-c") { return "content left"; }
		}

		return ""; /* Else return nothing. */
	}

	/**
	 * Layout Classes used in the secondary sidebar.
	 *
	 * @since 0.1.0
	 */
	public static function content_layout_classes_secondary() {

		global $pc_global_column_layout;

		$cols = $pc_global_column_layout;

		if($cols == "3-col-l") { return "left"; }
		elseif($cols == "3-col-r") { return "right"; }
		elseif($cols == "3-col-c") { return "right"; }
		else { return ""; }
	}

	/**
	 * Layout Classes used in the #contentwrap div.
	 *
	 * @since 0.1.0
	 */
	public static function contentwrap_layout_classes() {

		global $pc_global_column_layout;

		$cols = $pc_global_column_layout;

        /* Replace numbers with text. e.g. '3-col-r' => 'three-col-r' as CSS classes can't start with numbers. */
        $cols = str_replace( array( '1', '2', '3' ), array( 'one', 'two', 'three' ), $cols);
        return 'class="'.$cols.'"';
	}

	/**
	 * Sidebar rendering logic before content.
	 *
	 * @since 0.1.0
	 */
	public static function render_sidebar_before() {

		global $pc_global_column_layout;

		$layout = $pc_global_column_layout;

		if ( $layout == "3-col-l" ) {
			get_sidebar( 'primary' );
			get_sidebar( 'secondary' );
		}

		if ( $layout == "3-col-c" ) {
			get_sidebar( 'primary' );
		}
	}

	/**
	 * Sidebar rendering logic after content.
	 *
	 * @since 0.1.0
	 */
	public static function render_sidebar_after() {

		global $pc_global_column_layout;

		$layout = $pc_global_column_layout;

		if ( $layout == "2-col-l" || $layout == "2-col-r" ) {
			get_sidebar( 'primary' );
		}

		if ( $layout == "3-col-r" ) {
			get_sidebar( 'secondary' );
			get_sidebar( 'primary' );
		}

		if ( $layout == "3-col-c" ) {
			get_sidebar( 'secondary' );
		}
	}

	/**
	 * Get featured image, if it exists, else get default.
	 *
	 * @since 0.1.0
	 */
	public static function theme_get_slider_image( $obj, $show_title=true, $post_thumb_size ) {

		if($show_title) { $title = "#".$obj->ID; }
		else { $title = ""; }

		$attr = array(
			'class'	=> "",
			'alt'	=> "",
			'title'	=> $title
		);

		if ( has_post_thumbnail( $obj->ID ) ) {
			return get_the_post_thumbnail( $obj->ID, $post_thumb_size, $attr );
		}
	}

	/**
	 * Get responsive slider featured image, if it exists.
	 *
	 * Returns a post featured image inside an image tag, or false if one doesn't exist.
	 * No width or height attributes are returned, so the image can be included in a responsive theme design.
	 *
	 * @since 0.1.0
	 */
	public static function get_responsive_slider_image( $post_id, $slider_thumb_size = 'slider_content_third' ) {

		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		$featured_image_src_arr = wp_get_attachment_image_src( $post_thumbnail_id, $slider_thumb_size );
		$featured_image_src = $featured_image_src_arr[0];

		if( $slider_thumb_size == 'post-thumbnail' ) {
			if($featured_image_src_arr) {
				$featured_image_class = 'class="attachment-post-thumbnail wp-post-image"';
				return '<img src="'.$featured_image_src.'" '.$featured_image_class.' />';
			} else {
				return false;
			}
		} else {
			if($featured_image_src_arr) {
				$featured_image_class = 'class="slide-featured-image"';
				return '<img src="'.$featured_image_src.'" '.$featured_image_class.' />';
			} else {
				return false;
			}
		}

	}

	/**
	 * Get responsive featured image, if it exists.
	 *
	 * Returns a post featured image inside an image tag, or false if one doesn't exist.
	 * No width or height attributes are returned, so the image can be included in a responsive theme design.
	 * This is a generic version of the get_responsive_slider_image() function.
	 *
	 * @since 0.1.0
	 */
	public static function get_responsive_featured_image( $post_id, $thumb_size = 'thumbnail', $args = null ) {

		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		$featured_image_src_arr = wp_get_attachment_image_src( $post_thumbnail_id, $thumb_size );
		$featured_image_src = $featured_image_src_arr[0];

		if($featured_image_src_arr) {

			if( is_array($args) ) {
				foreach( $args as $attr => $val ) {
					$attributes = ' '.$attr.'="'.$val.'"';
				}
			}
			else {
				$attributes = '';
			}

			return '<img src="'.$featured_image_src.'"'.$attributes.' />';
		} else {
			return false;
		}
	}

	/**
	 * Return N-number of words from a string.
	 *
	 * @since 0.1.0
	 */
	public static function n_words($text,$maxchar,$end='...'){
	 if(mb_strlen($text)>$maxchar){
	  $words=explode(" ",$text);
	  $output = '';
	  $i=0;
	  while(1){
	   $length = (mb_strlen($output)+mb_strlen($words[$i]));
	   if($length>$maxchar){
		break;
	   }else{
		$output = $output." ".$words[$i];
		++$i;
	   };
	  };
	 }else{
	  $output = $text;
	 }
	 return $output.$end;
	}

	/**
	 * Check link contains 'http://'.
	 *
	 * @since 0.1.0
	 */
	public static function checkLink($link) {
		if(preg_match("/http:\/\//", $link)) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * Check favicon url is valid.
     *
     * @since 0.1.0
     */
    public static function get_custom_favicon() {
        $options = get_option( PC_OPTIONS_DB_NAME );
        $favicon = $options['txt_favicon'];

		if( isset($favicon) && !empty($favicon) ) { /* If variable set, and non-empty. */
            if( self::checkLink($favicon) ) { /* If favicon url contains 'http://'. */
                $allowed = array( 'ico', 'png', 'jpg', 'gif' );
                $ext = substr($favicon, -3);
                if(in_array( $ext, $allowed) ) { /* If the extension is valid. */
                    return $favicon;
                }
            }
        }

        /* Favicon url set in theme options doesn't exist or seem to be valid. So, use theme default or child theme favicon. */
        $favicon = PC_Utility::theme_resource_uri( 'images', array( 'favicon.ico', 'favicon.png', 'favicon.jpg', 'favicon.gif' ) );
        return $favicon;
    }

	/**
	 * Get URL of current page.
	 *
	 * @since 0.1.0
	 */
	public static function currURL() {
		$pageURL = 'http';
		if( isset($_SERVER["HTTPS"]) ) {
			if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	/**
	 * Sort an array with the order of another. Use this function if the array to be sorted
	 * is an array of arrays.
	 *
	 * @since 0.1.0
	 */
	public static function sortMultiArrayByArray($array,$orderArray) {
		$ordered = array();
		foreach($orderArray as $key => $value) {
			if(array_key_exists($key,$array)) {
					$ordered[$key] = $array[$key];
					unset($array[$key]);
			}
		}
		return $ordered + $array;
	}

	/**
	 * Sort an array with the order of another. Use this function if the array to be sorted
	 * is NOT an array of arrays (i.e. just a normal array).
	 *
	 * @since 0.1.0
	 */
	public static function sortArrayByArray($array,$orderArray) {
		$ordered = array();
		foreach($orderArray as $key) {
			if(array_key_exists($key,$array)) {
					$ordered[$key] = $array[$key];
					unset($array[$key]);
			}
		}
		return $ordered + $array;
	}

	/**
	 * Fade out theme update notices to make them go away without having to reload the page.
	 *
	 * @since 0.1.0
	 */
	public static function pc_fadeout_element($element=".updated", $delay=3000, $fadeout=1500) {
	?>
		<script language="javascript">
			jQuery(document).ready(function($) {
				$("<?php echo $element; ?>").delay(<?php echo $delay; ?>).fadeOut(<?php echo $fadeout; ?>);
			});
		</script>
	<?php
	}

	/**
	 * Show logo in site header.
	 *
	 * @todo This is dependent on theme options so should be moved to a more relevant location.
	 *
	 * @since 0.1.0
	 */
	public static function pc_display_theme_logo($options) {

		// Show image logo if one defined
		if( isset($options[ PC_LOGO_CHK_OPTION_NAME ]) ) {
			if ( $options[ PC_LOGO_URL_OPTION_NAME ] != "" ) {
			?>
			<div id="site-logo"><a href="<?php echo get_home_url(); ?>" /><img src="<?php echo $options[PC_LOGO_URL_OPTION_NAME]; ?>" /></a></div>
			<?php
			}
			else {
				$theme_options_url = admin_url( 'themes.php?page='.PC_THEME_MENU_SLUG );
				echo "<p>No image specified. Please add a valid logo in <a href=\"{$theme_options_url}\" target=\"_blank\">".PC_THEME_NAME." theme options.</a></p>";
			}
		}
		// Otherwise show title text instead of logo
		else {
			if ( is_front_page() ) {
				echo "<h1 id=\"site-title\"><span><a href=\"".get_home_url()."\" />".get_bloginfo( 'name' )."</a></span></h1>";
			}
			else {
				echo "<h2 id=\"site-title\"><span><a href=\"".get_home_url()."\" />".get_bloginfo( 'name' )."</a></span></h2>";
			}
		}

	}

	/**
	 * Enqueue a script on a specific CTP editor page.
	 *
	 * @since 0.1.0
	 */
	public static function wp_enqueue_admin_cpt_script( $cpt, $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {

		/* Check the admin page we are on. */
		global $pagenow;

		/* Default to null to prevent enqueuing. */
		$enqueue = null;

		/* Enqueue if we are on an 'Add New' type page. */
		if ( isset($_GET['post_type']) && $_GET['post_type'] == $cpt && $pagenow == "post-new.php" ) {
			$enqueue = true;
		}

		/* Enqueue if we are on an 'Edit' type page. */
		if ( isset($_GET['post']) && $pagenow == "post.php" ) {
			/* Check post is a testimonial CPT. */
			$post_id = $_GET['post'];
			$post_obj = get_post( $post_id );
			if( $post_obj->post_type == $cpt )
				$enqueue = true;
		}

		/* Only enqueue if editor page is a specific CPT. */
		if( $enqueue )
			wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer);
	}

	/**
	 * Enqueue a style on a specific CTP editor page.
	 *
	 * @since 0.1.0
	 */
	public static function wp_enqueue_admin_cpt_style( $cpt, $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {

		/* Check the admin page we are on. */
		global $pagenow;

		/* Default to null to prevent enqueuing. */
		$enqueue = null;

		/* Enqueue if we are on an 'Add New' type page. */
		if ( isset($_GET['post_type']) && $_GET['post_type'] == $cpt && $pagenow == "post-new.php" ) {
			$enqueue = true;
		}

		/* Enqueue if we are on an 'Edit' type page. */
		if ( isset($_GET['post']) && $pagenow == "post.php" ) {
			/* Check post is a testimonial CPT. */
			$post_id = $_GET['post'];
			$post_obj = get_post( $post_id );
			if( $post_obj->post_type == $cpt )
				$enqueue = true;
		}

		/* Only enqueue if editor page is a specific CPT. */
		if( $enqueue )
			wp_enqueue_style( $handle, $src, $deps, $ver, $media );
	}

	/**
	 * Reference a child theme resource (if it exists) that you want to use in preference to the parent resource.
	 *
	 * This function assumes that the parent resource exists, but the associated child resource should be used instead if it has been defined.
	 * If it has NOT been defined in the child theme then use the parent resource. This prevents enforcement of resources expected in child themes,
	 * and makes them optional by providing the parent resource as a fallback.
	 *
	 * Example usage: Define a default image for post thumbnails (parent theme), and add default child thumbnail image to override this image.
	 *
	 * @since 0.1.0
	 *
	 * @parameter $parent_rel is the relative path to the resource from parent root.
	 * @parameter $child_rel is the relative path to the resource from child root.
	 * @parameter $file is the file name of the resource. Can be an array of files, which can be useful if you don't know the exact filename or extension.
	 *
	 * @return Path to child resource, if it exists, else path to parent resource.
	 */
	public static function theme_resource_uri( $parent_rel = '', $file, $child_rel = '' ) {

        if( empty($file) || empty($parent_rel) ) return false; /* If no file name, or parent directory, specified then just return. */

        if( empty($child_rel) ) $child_rel = $parent_rel; /* If no specific child theme directory then just use parent directory. */

		/* If more than one parent dir specified. */
		if( is_array($parent_rel) ) {
			/* Find the 'first' dir that contains the file and use that as the $parent_rel. */
			foreach( $parent_rel as $pr ) {
				$parent_resource_dir = trailingslashit( PC_THEME_ROOT_DIR ).trailingslashit( $pr ).$file;
				$parent_resource_uri = trailingslashit( PC_THEME_ROOT_URI ).trailingslashit( $pr ).$file;
				if ( file_exists( $parent_resource_dir ) ) {
					$parent_rel = $pr; /* This will change $parent_rel from an array back to a string value. */
					break;
				}
				$parent_rel = ''; /* If file doesn't exist in dir then cast this back to a string. */
			}
		}

        if( !is_array($file) ) { /* If a single file. */

			$child_resource_uri = trailingslashit( PC_CHILD_ROOT_URI ).trailingslashit( $child_rel ).$file;
			$child_resource_dir = trailingslashit( PC_CHILD_ROOT_DIR ).trailingslashit( $child_rel ).$file;
			$parent_resource_dir = trailingslashit( PC_THEME_ROOT_DIR ).trailingslashit( $parent_rel ).$file;
			$parent_resource_uri = trailingslashit( PC_THEME_ROOT_URI ).trailingslashit( $parent_rel ).$file;

            /* Check if child/parent resource exists, otherwise return false. */
            if( file_exists( $child_resource_dir ) ) {
                return $child_resource_uri;
            }
            else if ( file_exists( $parent_resource_dir ) )  {
                return $parent_resource_uri;
            }
            else {
                return false; /* No match found. */
            }
        }
        else {
			/* If an array of files is specified then cylcle through them and return the 'first' one that exists. */
            foreach( $file as $fl ) {
                $child_resource_uri = trailingslashit( PC_CHILD_ROOT_URI ).trailingslashit( $child_rel ).$fl;
                $child_resource_dir = trailingslashit( PC_CHILD_ROOT_DIR ).trailingslashit( $child_rel ).$fl;
                $parent_resource_dir = trailingslashit( PC_THEME_ROOT_DIR ).trailingslashit( $parent_rel ).$fl;
                $parent_resource_uri = trailingslashit( PC_THEME_ROOT_URI ).trailingslashit( $parent_rel ).$fl;

                if( file_exists( $child_resource_dir ) ) {
                    return $child_resource_uri;
                }
                else if ( file_exists( $parent_resource_dir ) )  {
                    return $parent_resource_uri;
                }
            }
            return false; /* No matches have been found in the array. */
        }
	}

	/**
	 * Install default content when theme is activated.
	 *
	 * Set the 'PC_INSTALL_DEFAULT_CONTENT' and 'PC_INSTALL_CONTENT_PROMPT' constants
	 * to control how this appears to the user.
	 *
	 * @since 0.1.0
	 */
	public static function install_default_content($theme_options_url) {

		/* Create default content, and configure menus, widgets etc. */

		/* Add some default pages. */
		$pages = array(	array(	'title' => 'Blog',
								'content' => '',
								'template' => 'blog-page.php'
						),
						array(	'title' => 'About Us',
								'content' => 'Some information all about us.',
								'template' => ''
						),
						array(	'title' => 'Sitemap',
								'content' => '',
								'template' => 'sitemap-page.php'
						),
						array(	'title' => 'Contact Us',
								'content' => 'Please use the contact form below for all enquiries. We will respond to your message as soon as possible.',
								'template' => 'contact-page.php'
						)
				);

		/* Create some new theme pages. */
		self::create_theme_pages($pages);

		/* Create nav menu if it doesn't already exist and add some pages. */
		self::create_new_theme_nav_menu($pages);

		/* Define the multi number for each widget type here, then increment for each additional widget added of the same type. */
		global $pc_info_box_multi_number;
		$pc_info_box_multi_number = next_widget_id_number('pc_info_widget_'.PC_THEME_NAME_SLUG);

		/* Add an Info Box widget to the header widget area. */
		$info_box_widget = array(
			'widget_area' => 'header-widget-area',
			'widget_name' => 'Info Box',
			'default_settings' => array(
				'title' => 'Header Info Box',
				'info_description' => '',
				'phone_number' => '(949) 867-5307',
				'facebook_id' => 'PressCoders',
				'twitter_id' => 'presscoders',
				'youtube_id' => 'PressCoders',
				'flickr_id' => '',
				'googleplus_id' => 'http://plus.google.com',
				'linkedin_id' => 'http://www.linkedin.com',
				'rss_id' => 'http://www.presscoders.com',
				'custom_id_1' => '',
				'custom_img_1' => '',
				'show_search' => ''
			),
		);
		self::add_default_widget($info_box_widget, true);

		/* Install extra content if demo site active. */
		if( PC_INSTALL_DEMO_CONTENT && method_exists( 'PC_TS_Utility', 'theme_demo_default_content' ) ) {
			/* Install theme specific demo content if class method has been declared. */
			PC_TS_Utility::theme_demo_default_content();
		}

		/* Render theme activation message. */
		self::theme_activation_message($theme_options_url);
	}

	/**
	 * Renders the successful theme activation message.
	 *
	 * @since 0.1.0
	 */
	public static function theme_activation_message($theme_options_url) {

		if ( PC_INSTALL_DEFAULT_CONTENT ) {
			$header = ( PC_INSTALL_CONTENT_PROMPT ) ? 'Default Content Installed!' : 'Congratulations, '.PC_THEME_NAME.' successfully activated!';
			$message = 'Why not visit the home page to see what your new site looks like, or go to the '.PC_THEME_NAME.' options page to begin configuring your theme!';
			$buttons = '<span><a class="button" href="'.home_url().'">Home Page</a>&nbsp;<a class="button" href="'.admin_url( $theme_options_url ).'">'.PC_THEME_NAME.' Theme Options</a></span>';
		}
		else {
			$header = 'Congratulations, '.PC_THEME_NAME.' successfully activated!';
			$message = 'Start customizing your theme on the '.PC_THEME_NAME.' options page.';
			$buttons = '<span><a class="button" href="'.admin_url( $theme_options_url ).'">'.PC_THEME_NAME.' Theme Options</a></span>';
		}

		/* Display admin notice that default content has been installed. */
		?>
		<div class="updated" style="margin-top: 10px;padding-bottom:10px;">
			<?php echo '<h3 style="margin: 0.7em 0;padding-top: 5px;">'.$header.'</h3>'.$message.'<br /><br />'.$buttons; ?>
		</div>
		<?php
	}

	/**
	 * Create theme pages.
	 *
	 * General function to create theme pages. Just pass in an array of arrays containing
	 * the new page 'title', 'content', and 'template'.
	 *
	 * @since 0.1.0
	 */
	public static function create_theme_pages($pages) {

		/* Create new theme pages, from the array passed in. */
		foreach($pages as $page) {
			$page_check = get_page_by_title($page['title']);

			$new_page = array(
				'post_type' => 'page',
				'post_title' => $page['title'],
				'post_content' => $page['content'],
				'post_status' => 'publish',
				'post_author' => 1
			);

			/* Create new page if one doesn't exist with the new title, and published post status. */
			if( !isset($page_check) || (isset($page_check) && $page_check->post_status != "publish") ){
				$new_page_id = wp_insert_post($new_page);
				if(!empty($page['template'])){
					update_post_meta($new_page_id, '_wp_page_template', $page['template']);
				}
			}
		}
	}

	/**
	 * Create new theme navigation menu and add pages to it.
	 *
	 * General function to create theme nav menu.
	 *
	 * @since 0.1.0
	 */
	public static function create_new_theme_nav_menu( $pages, $menu_name = 'Main Menu', $home_link = true, $assign_theme_location = true ) {

		/* Check if menu exists, and create it if not. */
		if ( !is_nav_menu( $menu_name )) {

			/* Create nav menu. */
			$menu_id = wp_create_nav_menu( $menu_name );

			/* Get menu ID. */
			$menu = wp_get_nav_menu_object($menu_name);
			$menuID = (int) $menu->term_id;

			/* Optionally add a 'Home' menu item. */
			if( $home_link ) {
				global $blog_id; /* Needed as get_home_url() returns main network url if multisite activated. */
				$menu1 = array(	'menu-item-status'	=> 'publish',
								'menu-item-type'	=> 'custom',
								'menu-item-url'		=> get_home_url($blog_id),
								'menu-item-title'	=> 'Home'
				);
				wp_update_nav_menu_item( $menuID, 0, $menu1 );
			}

			/* Add a menu item for each page created earlier. */
			foreach($pages as $page) {
				$new_page = get_page_by_title( $page['title'] );
				$menu2 = array(	'menu-item-object-id' => $new_page->ID,
								'menu-item-parent-id' => 0,
								//'menu-item-position'  => 0,
								'menu-item-object' => 'page',
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
								'menu-item-title' => $new_page->post_title
				);
				wp_update_nav_menu_item( $menuID, 0, $menu2 );
			}

			/* Check for page with 'Members Area' title, and add it to menu if found. */
			$page_check = get_page_by_title('Members Area');
			if( isset($page_check) && $page_check->post_status == "publish" ){
				$menu3 = array(	'menu-item-object-id' => $page_check->ID,
								'menu-item-parent-id' => 0,
								'menu-item-position'  => 3,
								'menu-item-object' => 'page',
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
								'menu-item-title' => $page_check->post_title
				);
				wp_update_nav_menu_item( $menuID, 0, $menu3 );
			}
		}
		else {
			/* Menu exists, so just get menu ID to assign theme location. */
			$menu = wp_get_nav_menu_object($menu_name);
			$menuID = (int) $menu->term_id;
		}

		/* Optionally assign menu location. */
		if( $assign_theme_location ) {
			$locations = get_theme_mod('nav_menu_locations');
			$locations[ PC_CUSTOM_NAV_MENU_1 ] = $menuID;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	/**
	 * Add pages to navigation menu.
	 *
	 * Add pages only to an existing nav menu. No new menu is created or any theme locations set.
	 *
	 * @since 0.1.0
	 */
	public static function add_pages_to_nav_menu( $pages, $menu_name = 'Main Menu', $insert_from = 0 ) {

		/* Check if menu exists. */
		if ( is_nav_menu( $menu_name )) {

			/* Get menu ID. */
			$menu = wp_get_nav_menu_object($menu_name);
			$menuID = (int) $menu->term_id;

			/* Get existing menu items. */
			$menu_items = wp_get_nav_menu_items( $menu_name );

			/* Add a menu item for each page created earlier. */
			foreach($pages as $page) {
				
				/* An 'add page' flag. */
				$add_page = true;

				/*  Check that a page with the same title doesn't already exist in the menu. */
				foreach($menu_items as $menu_item) {
					if ( $menu_item->title == $page['title'] ) {
						$add_page = false;
						break; /* No point in checking the other menu items for this title. */
					}
				}

				if( !$add_page ) continue; /* Skip to the next page to be added. */

				$new_page = get_page_by_title( $page['title'] );
				$menu1 = array(	'menu-item-object-id' => $new_page->ID,
								'menu-item-parent-id' => 0,
								'menu-item-position'  => $insert_from,
								'menu-item-object' => 'page',
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
								'menu-item-title' => $new_page->post_title
				);
				wp_update_nav_menu_item( $menuID, 0, $menu1 );
			}
		}
	}

	/**
	 * Hide the title header tag via CSS.
	 *
	 * Only hide if checkbox selected on the editor screen, but still render the header tag for SEO purposes.
	 *
	 * @since 0.1.0
	 */
	public static function hide_title_header_tag($id, $hd_tag = "h1", $classes = "") {
		$hide_title_header_tag = get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_hide_title_header_tag', true);
		$title = get_the_title();

		/* Add classes attribute if not empty. */
		if( !empty($classes) ) $classes = ' class="'.$classes.'"';

		if( $hide_title_header_tag ) {
			echo '<'.$hd_tag.' style="display:none;"'.$classes.'>'.$title.'</'.$hd_tag.'>';
		}
		else {
			echo '<'.$hd_tag.$classes.'>'.$title.'</'.$hd_tag.'>';
		}
	}

	/**
	 * Get widget-number, and multi-number for the next widget to be added.
	 *
	 * Gets the widget-number and multi-number for a particular widget (Info Box,
	 * Color Switcher etc.).
	 *
	 * @since 0.1.0
	 */
	public static function get_widget_args( $widget_name ) {

		global $wp_registered_widgets, $wp_registered_widget_controls;

		$sort = $wp_registered_widgets;
		usort( $sort, '_sort_name_callback' );
		$done = array();

		foreach ( $sort as $widget ) {

			if ( in_array( $widget['callback'], $done, true ) ) // We already showed this multi-widget
				continue;

			$done[] = $widget['callback'];

			if ( ! isset( $widget['params'][0] ) )
				$widget['params'][0] = array();

			$args = array( 'widget_id' => $widget['id'], 'widget_name' => $widget['name'], '_display' => 'template' );

			$id_base = $wp_registered_widget_controls[$widget['id']]['id_base'];
			$args['_temp_id'] = "$id_base-__i__";
			$args['_id_base'] = $id_base;
			$args['_add'] = 'multi';

			$widget_id = $widget['id'];
			$control = isset($wp_registered_widget_controls[$widget_id]) ? $wp_registered_widget_controls[$widget_id] : array();
			$args['_widget_num'] = isset($control['params'][0]['number']) ? $control['params'][0]['number'] : '';

			if( $widget['name'] == $widget_name )
				break;
		}

		return $args;
	}

	/**
	 * Add default widgets upon successful theme activation.
	 *
	 * @since 0.1.0
	 */
	public static function add_default_widget($widget, $overwrite_widgets = false ) {

		global $pc_info_box_multi_number;
		$multi_number = $pc_info_box_multi_number;
		$pc_info_box_multi_number++;

		$args = PC_UTILITY::get_widget_args( $widget['widget_name'] ); /* Returns an array. */
		$id_base = $args['_id_base'];
		$widget_number = $args['_widget_num'];
		$widget_id = $id_base.'-'.$multi_number;
		$widget_area = $widget['widget_area'];

		/*echo "<br />overwrite_widgets: ".$overwrite_widgets."<br />";
		echo "id_base: ".$id_base."<br />";
		echo "widget_number: ".$widget_number."<br />";
		echo "multi_number: ".$multi_number."<br />";
		echo "widget_id: ".$widget_id."<br />";
		echo "widget_area: ".$widget_area."<br />";*/

		/* Create widget. */
		$add_new_widget = get_option( 'widget_'.$id_base );
		$add_new_widget[$multi_number] = $widget['default_settings'];
		$add_new_widget['_multiwidget'] = 1;
		update_option( 'widget_'.$id_base, $add_new_widget );

		/* Add to widget area. */
		$add_to_sidebar = get_option( 'sidebars_widgets' );
		/* Overwrite (or just add to) existing widgets in the specified widget area. */
		if($overwrite_widgets)
			$add_to_sidebar[$widget_area] = array();
		$add_to_sidebar[$widget_area][] = $widget_id;
		wp_set_sidebars_widgets($add_to_sidebar);
	}

	/**
	 * Tests a string for valid Gravatar e-mail or image URL.
	 *
	 * An <img> tag is returned if a valid image or Gravatar.
	 *
	 * @since 0.1.0
	 */
	public static function validate_image_str($image, $class = "avatar", $size = "50") {

		$end = substr( $image, -4 );
		if($class != "") $class = "class=\"{$class}\"";

		/* Looks like a direct image URL let's check it is valid. */
		if( $end == ".jpg" || $end == ".png" || $end == ".gif" ) {
			try {
				if( $image == "" || !($img_size = @getimagesize($image)) ) {
					throw new Exception('Not a valid image.');
				}
				// Image URL OK so show image icon
				$image = "<img {$class} src=\"{$image}\" width=\"{$size}\" height=\"{$size}\" />";
			}
			catch (Exception $e)
			{
				$image = ""; // Image URL no good so make sure it's blank and outputs nothing
			}
		}
		/* Try to get as a gravatar image. */
		else {
			$image = get_avatar( $image, $size );
		}
		return $image;
	}

	/**
	 * Renders the widgets areas for sidebar-xx.php files.
	 *
	 * @since 0.1.0
	 */
	public static function render_widget_area( $widget_area_name = '', $show_default = false, $default_file = '', $check_global = false ) {

		/* Default to post widget area if nothing else specified. */
		if( $show_default && empty($default_file) ) $default_file = 'primary_post_generic_default_widgets.php';
		if( empty($widget_area_name) ) $widget_area_name = 'primary-post-widget-area';

		if ( is_active_sidebar( $widget_area_name ) ) :
			echo '<div id="'.$widget_area_name.'" class="widget-area">';
			dynamic_sidebar( $widget_area_name );
			echo '</div>';
		else:
			if($show_default) {
				$default_path = get_template_directory().'/includes/sidebars/'.$default_file;

				if ( $check_global ) {
					/* Don't render widgets if any exist in global widget area. */
					if( !is_active_sidebar( 'global-widget-area' ) ) include($default_path);
				}
				else {
					/* Render widgets regardless of any widgets in global widget area. */
					include($default_path);
				}
			}
		endif;
	}

	/**
	 * Renders the custom widgets areas for sidebar-xx.php files.
	 *
	 * @since 0.1.0
	 */
	public static function render_custom_widget_areas( $custom_widget_areas, $default_file = '' ) {

		global $wp_registered_sidebars;

		$custom_widget_areas = array_keys($custom_widget_areas);

		foreach($custom_widget_areas as $custom_widget_area) {

			/* Custom widget areas. */
			if ( is_active_sidebar( $custom_widget_area ) ) : ?>
				<div id="<?php echo $custom_widget_area; ?>" class="widget-area">
					<?php dynamic_sidebar( $custom_widget_area ); ?>
				</div> <?php
			else:
				if( empty($default_file) ) :
				?>
				<div class="widget widget_text">
					<h3 class="widget-title"><?php echo $wp_registered_sidebars[$custom_widget_area]['name']; ?></h3>
					<div class="textwidget"><?php _e( 'This widget area is currently empty. You can add widgets to this area via: Appearance => Widgets.', 'presscoders' ); ?></div>
				</div>
				<?php
				else:
					$default_path = get_template_directory().'/includes/sidebars/'.$default_file;
					require($default_path);
				endif;
			endif; /* End custom widget areas. */
		}
	}

	/**
	 * Loop through the specified custom theme template files.
	 *
	 * @since 0.1.0
	 */
	public static function custom_widget_area_loop( $sidebar_hook = '', $fallback_default_widget_area_name = '', $fallback_default_file = '', $show_default = false, $default_file = '', $check_global = false ) {

		global $pc_template;

		/* Default to post widget area if nothing else specified. */
		if( empty($fallback_default_file) ) $fallback_default_file = 'primary_post_generic_default_widgets.php';
		if( empty($fallback_default_widget_area_name) ) $fallback_default_widget_area_name = 'primary-post-widget-area';

		$custom_pages = array(); /* Initialize to empty array. */

		/* At the moment this feature only supports (i.e. was only needed for) primary sidebars, but it can be easily extended for secondary sidebars. */
		switch ($sidebar_hook) {
		case 'primary-archive':
			$custom_pages = PC_Hooks::pc_custom_primary_sidebar_archive($custom_pages); /* Framework hook wrapper */
			break;
		case 'primary-pages':
			$custom_pages = PC_Hooks::pc_custom_primary_sidebar_pages($custom_pages); /* Framework hook wrapper */
			break;
		case 'primary-posts':
			$custom_pages = PC_Hooks::pc_custom_primary_sidebar_posts($custom_pages); /* Framework hook wrapper */
			break;
		default:
			$custom_pages = array();
		}

		$custom_pages_flag = 0;
		foreach($custom_pages as $custom_page => $widget_area) {
			if( $pc_template == $custom_page ) {
				self::render_widget_area( $widget_area, $show_default, $default_file, $check_global );
				$custom_pages_flag = 1;
				break;
			}
		}
		/* If no custom pages set then show a default widget area. */
		if( $custom_pages_flag == 0 ) self::render_widget_area( $fallback_default_widget_area_name, true, $fallback_default_file, true );
	}

	/**
	 * Set the global WordPress $content_width variable.
	 *
	 * @since 0.1.0
	 */
	public static function set_content_width( $pc_global_column_layout ) {

		global $content_width;

		if( $pc_global_column_layout == "1-col" )
			$content_width = 960;
		elseif( $pc_global_column_layout == "2-col-l" || $pc_global_column_layout == "2-col-r" )
			$content_width = 650;
		else /* Assume 3-column layout. */
			$content_width = 374;
	}

	/**
	 * Render formatted object/array values.
	 *
	 * @since 0.1.0
	 */
	public static function pc_printr( $obj ) {

		echo "<pre>";
		print_r($obj);
		echo "</pre>";
	}

	/**
	 * Get the taxonomy terms (and permalinks) for a CPT.
	 *
	 * @since 0.1.0
	 */
	public static function get_cpt_terms( $taxonomy = null, $post_id ) {

		/* Check for taxonomy. */
		if( empty($taxonomy) ) return null;

		$post_type = get_post_type($post_id);
		$terms = get_the_terms( $post_id, $taxonomy );

		/* get_the_terms() only returns an array on success so need check for valid array. */
		if( is_array($terms) ) {
			$str = "";
			foreach( $terms as $term) {
				$term_link = get_term_link( $term, $taxonomy );
				//$str .= esc_html(sanitize_term_field('name', $term->name, $term->term_id, 'group', 'edit')).', ';
				$str .= "<a href='".$term_link."'> " . esc_html(sanitize_term_field('name', $term->name, $term->term_id, 'group', 'edit')) . "</a>, ";
			}
			return rtrim( $str, ", ");
		}
		else {
			return null;
		}
	}

	/**
	 * Check for empty titles and output placeholder text for empty titles.
	 *
	 * @since 0.1.0
	 */
	public static function check_empty_post_title( $post_id = null, $tag_wrapper = 'h2', $no_title = '(no title)', $tag_wrapper_class = "entry-title", $rel = "bookmark" ) {

		/* Check for post id. */
		if( empty($post_id) ) return;

		$post_title = get_the_title($post_id);
		
		if ( !empty($post_title) ) :
			echo '<'.$tag_wrapper.' class="'.$tag_wrapper_class.'"><a href="'.get_permalink($post_id).'" rel="'.$rel.'">'.$post_title.'</a></'.$tag_wrapper.'>';
		else :
			echo '<'.$tag_wrapper.' class="'.$tag_wrapper_class.'"><a href="'.get_permalink($post_id).'" rel="'.$rel.'">'.$no_title.'</a></'.$tag_wrapper.'>';
		endif;
	}

	/**
	 * Flush permalinks.
	 *
	 * Useful to call this function after registering a new taxonomy to prevent 404 errors.
	 *
	 * @since 0.1.0
	 */
	public static function flush_permalink_rules() {

		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
}

?>