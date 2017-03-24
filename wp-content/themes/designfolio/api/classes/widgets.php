<?php

/**
 * Registers theme sidebars and widgets used by the theme framework.
 *
 * @since 0.1.0
 */
class PC_Widgets {

	/**
	 * Widgets class properties.
	 *
	 * @since 0.1.0
	 */
	protected $_widgets;

	/**
	 * PC_Widgets class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct( $widgets ) {

		/* Set supported widgets. */
		$this->_widgets = $widgets;

		/* Add icon to theme widgets on widgets.php page. */
		add_action( 'sidebar_admin_page', array( &$this, 'add_widget_icons' ) );

		add_action( 'widgets_init', array( &$this, 'theme_register_sidebars' ) );
		add_action( 'widgets_init', array( &$this, 'theme_register_widgets' ) );
	}

	/**
	 * Add small theme icon to admin widget title in widgets.php.
	 *
	 *
	 * @since 0.1.0
	 */
	public function add_widget_icons() {

		/* @todo Test for existence of a widget-icon.png image. If none found then exit function. */

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Add icons to theme widgets on the widgets.php page 'Available Widgets' section
				$('div#widget-list div[id*="<?php echo PC_THEME_NAME_SLUG; ?>"] .widget-title h4').css("padding-left", "21px");
				$('div#widget-list div[id*="<?php echo PC_THEME_NAME_SLUG; ?>"] .widget-title').css("background", "transparent url(<?php echo PC_THEME_ROOT_URI.'/images/widget-icon.png'; ?>) no-repeat 8px 5px");

				// Add icons to theme widgets on the widgets.php page "Widget Areas" (RHS of page)
				$('div#widgets-right div[id*="<?php echo PC_THEME_NAME_SLUG; ?>"] .widget-title h4').css("padding-left", "21px");
				$('div#widgets-right div[id*="<?php echo PC_THEME_NAME_SLUG; ?>"] .widget-title').css("background", "transparent url(<?php echo PC_THEME_ROOT_URI.'/images/widget-icon.png'; ?>) no-repeat 8px 5px");

				// Add icons to theme widgets on the widgets.php page "Inactive Widgets" (bottom of page)
				$('div#wp_inactive_widgets div[id*="<?php echo PC_THEME_NAME_SLUG; ?>"] .widget-title h4').css("padding-left", "21px");
				$('div#wp_inactive_widgets div[id*="<?php echo PC_THEME_NAME_SLUG; ?>"] .widget-title').css("background", "transparent url(<?php echo PC_THEME_ROOT_URI.'/images/widget-icon.png'; ?>) no-repeat 8px 5px");
			});
		</script>
		<?php
	}

	/**
	 * Register framework widget areas.
	 *
	 * @since 0.1.0
	 */
	public function theme_register_sidebars() {

		// GLOBAL WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Global', 'presscoders' ),
			'id' => 'global-widget-area',
			'description' => __( 'These widgets appear at the top of the primary sidebar on ALL posts/pages', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'normal'
		) );

		// PRIMARY POST WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Post: Primary', 'presscoders' ),
			'id' => 'primary-post-widget-area',
			'description' => __( 'The primary single post, and main blog page, widget area', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'normal'
		) );

		// SECONDARY POST WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Post: Secondary', 'presscoders' ),
			'id' => 'secondary-post-widget-area',
			'description' => __( 'The secondary single post widget area. Only displayed on three-column post layouts.', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'normal'
		) );

		// PRIMARY PAGE WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Page: Primary', 'presscoders' ),
			'id' => 'primary-page-widget-area',
			'description' => __( 'The primary single page widget area', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'normal'
		) );

		// SECONDARY PAGE WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Page: Secondary', 'presscoders' ),
			'id' => 'secondary-page-widget-area',
			'description' => __( 'The secondary single page widget area. Only displayed on three-column page layouts.', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'normal'
		) );

		// HEADER BLOG WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Header', 'presscoders' ),
			'id' => 'header-widget-area',
			'description' => __( 'The header widget area', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'normal'
		) );

		// FOOTER WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Footer', 'presscoders' ),
			'id' => 'footer-widget-area',
			'description' => __( 'The footer widget area', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'normal'
		) );

		// FRONT PAGE CONTENT WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Front Page: Main Content', 'presscoders' ),
			'id' => 'front-page-content-widget-area',
			'description' => __( 'The front page main content widget area', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'wide'
		) );

		// FRONT PAGE BEFORE CONTENT WIDTH WIDGET AREA
		register_sidebar( array(
			'name' => __( 'Front Page: Before Content', 'presscoders' ),
			'id' => 'front-page-before-content-widget-area',
			'description' => __( 'Front page full width widgets, displayed before main content or sidebars. The slider widget works great here!', 'presscoders' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
			'width' => 'full'
		) );

	}

	/**
	 * Register framework widgets.
	 *
	 * @since 0.1.0
	 */
	function theme_register_widgets() {

		/* Get the array of widgets. */
		$widgets = $this->_widgets;

		/* Process array. */
		if( is_array($widgets) )
		    array_walk( $widgets, array( &$this, 'add_widget_callback' ) );
	}

	/**
	 * Register widgets callback.
	 *
	 * @since 0.1.0
	 */
	public function add_widget_callback($value,$key)
	{
		switch ($value) {
		case 'twitter-feed':
			require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-twitter-feed.php' );
			register_widget( 'pc_twitter_feed_widget' );
			break;
		case 'testimonial':
			require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-testimonial.php' );
			register_widget( 'pc_testimonial_widget' );
			break;
		case 'tml':
			/* Only register testimonial widget if theme supports the testimonial CPT. */
			if( !post_type_exists( 'testimonial' ) ) {
				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-tml.php' );
				register_widget( 'pc_tml_widget' );
            }
			break;
		case 'theme-recent-posts':
			require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-theme-recent-posts.php' );
			register_widget( 'pc_recent_posts_widget' );
			break;
		case 'blog-style-recent-posts':
			require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-blog-style-recent-posts.php' );
			register_widget( 'pc_blog_style_recent_posts_widget' );
			break;
		case 'color-scheme-switcher':
            /* Only register color switcher widget if theme supports color schemes. */
            if( current_theme_supports( 'color-schemes' ) ) {
                require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-color-scheme-switcher.php' );
                register_widget( 'pc_color_scheme_switcher_widget' );
            }
			break;
		case 'nivo-slider':
			if( file_exists( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-nivo-slider.php' ) ) {
				/* Use default slider sizes. */
				if ( !defined( 'PC_SLIDER_IMG_THIRD_WIDTH' ) )		define( 'PC_SLIDER_IMG_THIRD_WIDTH', 300 );
				if ( !defined( 'PC_SLIDER_IMG_TWO_THIRDS_WIDTH' ) )	define( 'PC_SLIDER_IMG_TWO_THIRDS_WIDTH', 640 );
				if ( !defined( 'PC_SLIDER_IMG_FULL_WIDTH' ) )		define( 'PC_SLIDER_IMG_FULL_WIDTH', 940 );
				if ( !defined( 'PC_SLIDER_IMG_HEIGHT' ) )			define( 'PC_SLIDER_IMG_HEIGHT', 300 );
				if ( !defined( 'PC_SLIDER_NAV_ARROWS' ) )			define( 'PC_SLIDER_NAV_ARROWS', false );

				/* Scripts needed for Nivo slider. */
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_nivo_slider' ) );

				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-nivo-slider.php' );
				register_widget( 'pc_nivo_slider_widget' );
			}
			break;
		case 'content-slider':
			/* Only register slider widget if the widget class exists. */
			if( file_exists( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-content-slider.php' ) ) {

				/* Use default content slider sizes, with a fixed height. */
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_THIRD_WIDTH' ) )		define( 'PC_SLIDER_CONTENT_IMG_THIRD_WIDTH', 300 );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH' ) )	define( 'PC_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH', 640 );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_FULL_WIDTH' ) )		define( 'PC_SLIDER_CONTENT_IMG_FULL_WIDTH', 940 );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_HEIGHT' ) )			define( 'PC_SLIDER_CONTENT_IMG_HEIGHT', 300 );
				if ( !defined( 'PC_SLIDER_CONTENT_NAV_ARROWS' ) )			define( 'PC_SLIDER_CONTENT_NAV_ARROWS', false );

				/* Enqueue FlexSlider scripts. */
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_content_slider' ) );

				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-content-slider.php' );
				register_widget( 'pc_content_slider_widget' );
			}
			break;
		case 'portfolio-slider':
			/* Only register portfolio slider widget if the widget class exists. */
			if( file_exists( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-portfolio-cpt-slider.php' ) ) {

				/* Use default content slider sizes, with a fixed height. */
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_THIRD_WIDTH' ) )		define( 'PC_PF_SLIDER_CONTENT_IMG_THIRD_WIDTH', 300 );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH' ) )	define( 'PC_PF_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH', 640 );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_FULL_WIDTH' ) )		define( 'PC_PF_SLIDER_CONTENT_IMG_FULL_WIDTH', 940 );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_HEIGHT' ) )			define( 'PC_PF_SLIDER_CONTENT_IMG_HEIGHT', 300 );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_NAV_ARROWS' ) )			define( 'PC_PF_SLIDER_CONTENT_NAV_ARROWS', false );

				/* Enqueue FlexSlider scripts. */
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_content_slider' ) );

				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-portfolio-cpt-slider.php' );
				register_widget( 'pc_portfolio_cpt_slider_widget' );
			}
			break;
		case 'carousel-slider':
			/* Only register carousel widget if the widget class existss. */
			if( file_exists( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-carousel-slider.php' ) ) {

				/* Use default slider sizes. */
				if ( !defined( 'PC_CAROUSEL_CONTENT_WIDTH' ) )		define( 'PC_CAROUSEL_CONTENT_WIDTH', 100 );
				if ( !defined( 'PC_CAROUSEL_CONTENT_HEIGHT' ) )		define( 'PC_CAROUSEL_CONTENT_HEIGHT', 100 );
				if ( !defined( 'PC_CAROUSEL_CONTENT_NAV_ARROWS' ) )	define( 'PC_CAROUSEL_CONTENT_NAV_ARROWS', true );

				/* Enqueue FlexSlider scripts. */
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_content_slider' ) );

				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-carousel-slider.php' );
				register_widget( 'pc_carousel_slider_widget' );
			}
			break;
		case 'info-box':
			require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-info-box.php' );
			register_widget( 'pc_info_box_widget' );
			break;
        case 'news-ticker':
            require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-news-ticker.php' );
            register_widget( 'pc_news_ticker_widget' );
            break;
		}

		if( is_array( $value ) ) {
			/* Check for custom Nivo slider image widths. */
			if( array_key_exists( 'nivo-slider', $value ) && file_exists( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-nivo-slider.php' ) ) {

                $params = $value[ 'nivo-slider' ];
                $one_third_width = ( array_key_exists( 'one-third-width', $params ) ) ? (int)$params['one-third-width'] : 300;
                $two_thirds_width = ( array_key_exists( 'two-thirds-width', $params ) ) ? (int)$params['two-thirds-width'] : 640;
                $full_width = ( array_key_exists( 'full-width', $params ) ) ? (int)$params['full-width'] : 940;
                $height = ( array_key_exists( 'height', $params ) ) ? (int)$params['height'] : 300;
                $nav_arrows = ( array_key_exists( 'nav-arrows', $params ) ) ? $params['nav-arrows'] : false;

				if ( !defined( 'PC_SLIDER_IMG_THIRD_WIDTH' ) )	    define( 'PC_SLIDER_IMG_THIRD_WIDTH', $one_third_width );
				if ( !defined( 'PC_SLIDER_IMG_TWO_THIRDS_WIDTH' ) )	define( 'PC_SLIDER_IMG_TWO_THIRDS_WIDTH', $two_thirds_width );
				if ( !defined( 'PC_SLIDER_IMG_FULL_WIDTH' ) )	    define( 'PC_SLIDER_IMG_FULL_WIDTH', $full_width );
				if ( !defined( 'PC_SLIDER_IMG_HEIGHT' ) )		    define( 'PC_SLIDER_IMG_HEIGHT', $height );
                if ( !defined( 'PC_SLIDER_NAV_ARROWS' ) )		    define( 'PC_SLIDER_NAV_ARROWS', $nav_arrows );

				/* Scripts needed for Nivo slider. */
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_nivo_slider' ) );

				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-nivo-slider.php' );
				register_widget( 'pc_nivo_slider_widget' );
			}

			/* Check for custom content slider image widths. */
			if( array_key_exists( 'content-slider', $value ) && file_exists( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-content-slider.php' ) ) {

                $params = $value[ 'content-slider' ];
                $one_third_width = ( array_key_exists( 'one-third-width', $params ) ) ? (int)$params['one-third-width'] : 300;
                $two_thirds_width = ( array_key_exists( 'two-thirds-width', $params ) ) ? (int)$params['two-thirds-width'] : 640;
                $full_width = ( array_key_exists( 'full-width', $params ) ) ? (int)$params['full-width'] : 940;
				$nav_arrows = ( array_key_exists( 'nav-arrows', $params ) ) ? $params['nav-arrows'] : false;

				/* Check if individual content slider heights have been specified. Any that aren't, set to null. */
				$one_third_height = ( array_key_exists( 'one-third-height', $params ) ) ? (int)$params['one-third-height'] : null;
				$two_thirds_height = ( array_key_exists( 'two-thirds-height', $params ) ) ? (int)$params['two-thirds-height'] : null;
				$full_third_height = ( array_key_exists( 'full-height', $params ) ) ? (int)$params['full-height'] : null;

				/* Fixed height used for all content slider sizes. This will be used if individual slider size heights are not ALL specified. */
				$height = ( array_key_exists( 'height', $params ) ) ? (int)$params['height'] : 300;

				if ( !defined( 'PC_SLIDER_CONTENT_IMG_THIRD_WIDTH' ) )		define( 'PC_SLIDER_CONTENT_IMG_THIRD_WIDTH', $one_third_width );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH' ) )	define( 'PC_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH', $two_thirds_width );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_FULL_WIDTH' ) )		define( 'PC_SLIDER_CONTENT_IMG_FULL_WIDTH', $full_width );
                if ( !defined( 'PC_SLIDER_CONTENT_NAV_ARROWS' ) )			define( 'PC_SLIDER_CONTENT_NAV_ARROWS', $nav_arrows );

				if ( !defined( 'PC_SLIDER_CONTENT_IMG_ONE_THIRD_HEIGHT' ) )		define( 'PC_SLIDER_CONTENT_IMG_ONE_THIRD_HEIGHT', $one_third_height );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_TWO_THIRDS_HEIGHT' ) )	define( 'PC_SLIDER_CONTENT_IMG_TWO_THIRDS_HEIGHT', $two_thirds_height );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_FULL_HEIGHT' ) )			define( 'PC_SLIDER_CONTENT_IMG_FULL_HEIGHT', $full_third_height );
				if ( !defined( 'PC_SLIDER_CONTENT_IMG_HEIGHT' ) )				define( 'PC_SLIDER_CONTENT_IMG_HEIGHT', $height );

				/* Enqueue FlexSlider scripts. */
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_content_slider' ) );

				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-content-slider.php' );
				register_widget( 'pc_content_slider_widget' );
			}

			/* Check for custom portfolio slider image widths. */
			if( array_key_exists( 'portfolio-slider', $value ) && file_exists( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-portfolio-cpt-slider.php' ) ) {

                $params = $value[ 'portfolio-slider' ];
                $one_third_width = ( array_key_exists( 'one-third-width', $params ) ) ? (int)$params['one-third-width'] : 300;
                $two_thirds_width = ( array_key_exists( 'two-thirds-width', $params ) ) ? (int)$params['two-thirds-width'] : 640;
                $full_width = ( array_key_exists( 'full-width', $params ) ) ? (int)$params['full-width'] : 940;
				$nav_arrows = ( array_key_exists( 'nav-arrows', $params ) ) ? $params['nav-arrows'] : false;

				/* Check if individual content slider heights have been specified. Any that aren't, set to null. */
				$one_third_height = ( array_key_exists( 'one-third-height', $params ) ) ? (int)$params['one-third-height'] : null;
				$two_thirds_height = ( array_key_exists( 'two-thirds-height', $params ) ) ? (int)$params['two-thirds-height'] : null;
				$full_third_height = ( array_key_exists( 'full-height', $params ) ) ? (int)$params['full-height'] : null;

				/* Fixed height used for all content slider sizes. This will be used if individual slider size heights are not ALL specified. */
				$height = ( array_key_exists( 'height', $params ) ) ? (int)$params['height'] : 300;

				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_THIRD_WIDTH' ) )		define( 'PC_PF_SLIDER_CONTENT_IMG_THIRD_WIDTH', $one_third_width );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH' ) )	define( 'PC_PF_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH', $two_thirds_width );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_FULL_WIDTH' ) )		define( 'PC_PF_SLIDER_CONTENT_IMG_FULL_WIDTH', $full_width );
                if ( !defined( 'PC_PF_SLIDER_CONTENT_NAV_ARROWS' ) )			define( 'PC_PF_SLIDER_CONTENT_NAV_ARROWS', $nav_arrows );

				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_ONE_THIRD_HEIGHT' ) )	define( 'PC_PF_SLIDER_CONTENT_IMG_ONE_THIRD_HEIGHT', $one_third_height );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_TWO_THIRDS_HEIGHT' ) )	define( 'PC_PF_SLIDER_CONTENT_IMG_TWO_THIRDS_HEIGHT', $two_thirds_height );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_FULL_HEIGHT' ) )		define( 'PC_PF_SLIDER_CONTENT_IMG_FULL_HEIGHT', $full_third_height );
				if ( !defined( 'PC_PF_SLIDER_CONTENT_IMG_HEIGHT' ) )			define( 'PC_PF_SLIDER_CONTENT_IMG_HEIGHT', $height );

				/* Enqueue FlexSlider scripts. */
				add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_content_slider' ) );

				require_once( PC_THEME_ROOT_DIR.'/api/classes/widgets/widget-portfolio-cpt-slider.php' );
				register_widget( 'pc_portfolio_cpt_slider_widget' );
			}
		}
	}

	/**
	 * Register and enqueue front end Nivo slider scripts and styles.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_nivo_slider() {

		/* Don't enqueue these scripts on admin pages. */
		if(!is_admin()){
			/* @todo This needs moving to the Nivo slider class and including ONLY on pages that use the slider. */
			wp_register_script( 'nivo-slider', PC_THEME_ROOT_URI.'/api/js/sliders/nivo-slider/jquery.nivo.slider.js', array('jquery') );
			wp_register_style( 'nivo-slider-stylesheet', PC_THEME_ROOT_URI.'/api/js/sliders/nivo-slider/nivo-slider.css' );
			wp_register_style( 'custom-nivo-slider-stylesheet', PC_THEME_ROOT_URI.'/includes/css/custom-nivo-slider.css' );

			wp_enqueue_style( 'nivo-slider-stylesheet' );
			wp_enqueue_style( 'custom-nivo-slider-stylesheet' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'nivo-slider' );
		}
	}

	/**
	 * Register and enqueue front end Content slider scripts and styles.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_content_slider() {

		/* Don't enqueue these scripts on admin pages. */
		if(!is_admin()){
			/* @todo This needs moving to the Content slider class and including ONLY on pages that use the slider. */
			//wp_register_script( 'content-slider', PC_THEME_ROOT_URI.'/api/js/sliders/slides/slides.min.jquery.js', array('jquery') );

			//wp_register_script( 'flexslider-js', PC_THEME_ROOT_URI.'/api/js/sliders/flexslider/jquery.flexslider.js', array('jquery') );
			//wp_register_script( 'flexslider-js', PC_THEME_ROOT_URI.'/api/js/sliders/flexslider/jquery.flexslider2-beta.js', array('jquery') );
			wp_register_script( 'flexslider-js', PC_THEME_ROOT_URI.'/api/js/sliders/flexslider/jquery.flexslider2.1beta.js', array('jquery') );

			wp_enqueue_script( 'jquery' );
			//wp_enqueue_script( 'content-slider' );
			wp_enqueue_script( 'flexslider-js' );
		}
	}
}

?>