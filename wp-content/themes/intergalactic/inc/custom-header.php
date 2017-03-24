<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * You can add an optional custom header image to header.php like so ...

	<?php if ( get_header_image() ) : ?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<img src="<?php header_image(); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="">
	</a>
	<?php endif; // End header image check. ?>

 *
 * @package Intergalactic
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses intergalactic_header_style()
 * @uses intergalactic_admin_header_style()
 * @uses intergalactic_admin_header_image()
 */
function intergalactic_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'intergalactic_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '222222',
		'width'                  => 1440,
		'height'                 => 460,
		'flex-height'            => true,
		'wp-head-callback'       => 'intergalactic_header_style',
		'admin-head-callback'    => 'intergalactic_admin_header_style',
		'admin-preview-callback' => 'intergalactic_admin_header_image',
	) ) );
}
add_action( 'after_setup_theme', 'intergalactic_custom_header_setup' );

if ( ! function_exists( 'intergalactic_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see intergalactic_custom_header_setup().
 */
function intergalactic_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == $header_text_color ) {
		return;
	}

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo esc_attr( $header_text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // intergalactic_header_style

if ( ! function_exists( 'intergalactic_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see intergalactic_custom_header_setup().
 */
function intergalactic_admin_header_style() {
?>
	<style type="text/css">
		.appearance_page_custom-header #headimg {
			border: none;
			font-size: 18px;
			position: relative;
		}
		.header-image {
			display: none;
		}
		<?php if ( get_header_image() ) : ?>
			.header-image {
				background-image: url( <?php echo esc_url( get_header_image() ); ?> );
				background-repeat: no-repeat;
				-moz-background-size: cover;
				-webkit-background-size: cover;
				background-size: cover;
				display: block;
				opacity: 0.3;
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				z-index: -1;
			}
		<?php endif; ?>
		#headimg h1,
		#desc {
			margin-top: .75em;
			margin-left: auto;
			margin-right: auto;
			position: relative;
			z-index: 1;
		}
		#headimg h1 {
			font-family: Lato, Helvetica, sans-serif;
			font-size: 5.55em;
			font-weight: bold;
			line-height: 1.25;
			margin: .75em auto .25em;
			padding: 0 .75em;
			text-align: center;
			text-transform: uppercase;
		}
		#headimg h1 a {
			text-decoration: none;
		}
		#desc {
			color: #aaa;
			font-family: Lato, Helvetica, sans-serif;
			font-size: 1.667em;
			font-weight: 300;
			margin: 0 auto 3em;
			padding: 0;
			text-align: center;
		}
		#headimg img {
		}
	</style>
<?php
}
endif; // intergalactic_admin_header_style

if ( ! function_exists( 'intergalactic_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see intergalactic_custom_header_setup().
 */
function intergalactic_admin_header_image() {
	$style = sprintf( ' style="color:#%s;"', get_header_textcolor() );
?>
	<div id="headimg">
		<span class="header-image"></span>
		<h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div class="displaying-header-text" id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	</div>
<?php
}
endif; // intergalactic_admin_header_image
