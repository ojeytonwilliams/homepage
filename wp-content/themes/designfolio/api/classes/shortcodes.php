<?php

/**
 * Framework shortcodes class.
 *
 * @since 0.1.0
 */
class PC_Shortcodes {

	/**
	 * Registers the framework shortcodes, and allows them to be used in widgets.
	 *
	 * @since 0.1.0
	 */
	public function __construct($selected_shortcodes = null, $mode = 'add' ) {

		/* List of available framework shortcode names and callback functions. */
		$framework_shortcodes = array(	'year'					=> 'year_shortcode',
										'week-dates'			=> 'week_dates_shortcode',
										'site-url'				=> 'site_url_shortcode',
										'testimonial'			=> 'testimonial_shortcode',
										'tml'					=> 'tml_shortcode',
										'box'					=> 'box_shortcode',
										'button'				=> 'button_shortcode',
										'one_half'				=> 'one_half_shortcode',
										'one_third'				=> 'one_third_shortcode',
										'two_thirds'			=> 'two_thirds_shortcode',
										'one_fourth'			=> 'one_fourth_shortcode',
										'three_fourths'			=> 'three_fourths_shortcode',
										'one_half_last'			=> 'one_half_last_shortcode',
										'one_third_last'		=> 'one_third_last_shortcode',
										'two_thirds_last'		=> 'two_thirds_last_shortcode',
										'one_fourth_last'		=> 'one_fourth_last_shortcode',
										'three_fourths_last'	=> 'three_fourths_last_shortcode' );

		/* Optionally load selective shortcodes only. */
		if( $selected_shortcodes ) {

			if( $mode == 'diff' ){
				/* Remove selected shortcodes from the full list. */
				$framework_shortcodes = array_diff_key( $framework_shortcodes, array_flip($selected_shortcodes) );
			}
			else if( $mode == 'add' ) {
				/* Add only the specified shortcodes. */
				$framework_shortcodes = array_intersect_key( $framework_shortcodes, array_flip($selected_shortcodes) );
			}
			/* If $mode doesn't match 'diff' or 'add' then just load all shortcodes. */
		}

		/* Use a custom [gallery] shortcode to always use the link to the image if fancybox enabled. Otherwise, use the default WP [gallery] shortcode. */
		global $_wp_theme_features; /* Access the global theme features array. */
		if( current_theme_supports( 'fancybox' ) ) {
			$options = get_option( PC_OPTIONS_DB_NAME );
			if( isset($options[ 'chk_enable_fancybox' ]) && $options[ 'chk_enable_fancybox' ])
				add_shortcode( 'gallery', array( &$this, 'custom_gallery_shortcode' ) );
		}

		/* Load specified shortcodes. If $selected_shortcodes not array then load all. */
		foreach($framework_shortcodes as $shortcode_name => $callback) {
			add_shortcode( $shortcode_name, array( &$this, $callback ) );
		}

		/* Allow shortcodes to be used in widgets. These callbacks are WordPress functions. */
		add_filter( 'widget_text', 'shortcode_unautop' );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * [year] shortcode function.
	 *
	 * Example usage: [year]
	 *
	 * @since 0.1.0
	 */
	public function year_shortcode() {

		return date('Y');
	}

	/**
	 * [week-dates] shortcode function.
	 *
	 * Example usage: [week-dates]
	 *
	 * @since 0.1.0
	 */
	public function week_dates_shortcode(){

		$month_start = date ('F', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . 0 ) );
		$month_end = date ('F', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . 6 ) );
		if ($month_start==$month_end) { $month_end=""; }
		$start = date ('j\<\s\u\p\>S\<\/\s\u\p\>', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . 0 ) );
		$end = date ('j\<\s\u\p\>S\<\/\s\u\p\>', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . 6 ) );
		$year = date ('Y', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . 6 ) );

		return $month_start." ".$start." &ndash; ".$month_end." ".$end;
	}

	/**
	 * [site-url] shortcode function.
	 *
	 * Example usage: [site-url]
	 *
	 * @since 0.1.0
	 */
	public function site_url_shortcode(){

		return '<a href="'.home_url( '/' ).'" title="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'" rel="home">'.get_bloginfo( 'name' ).'</a>';
	}

	/**
	 * [testimonial] shortcode function.
	 *
	 * Example usage: [testimonial]Add testimonial here.[/testimonial]
	 *
	 * @todo This shortcode can probably be moved to the deprecated section.
	 *
	 * @since 0.1.0
	 */
	public function testimonial_shortcode($atts, $content = NULL ){

		// Support self-closing, and enclosing shortcodes (but does not like mixed on the same page/post)
		extract(shortcode_atts(array(
			'image' => '',
			'name' => '',
			'company' => ''
		), $atts));

		if(!empty($image)){
			$image = PC_Utility::validate_image_str($image);
		}

		if(empty($content)){
			$content = __( 'Please add your testimonial in-between opening and closing tags: e.g. &#91;testimonial&#93;Your testimonial here..&#91;/testimonial&#93;', 'presscoders' );
		}

		if(!empty($name)){
			$name = "<p class=\"testimonial-name\">{$name}</p>";
		}

		if(!empty($company)){
			$company = "<p class=\"testimonial-company\">{$company}</p>";
		}

		return "<div class=\"testimonial\"><div class=\"quote\"><p>{$content}</p></div><div class=\"testimonial-meta\">{$image}{$name}{$company}</div></div>";
	}

	/**
	 * [tml] Testimonial shortcode function.
	 *
	 * Example usage: [tml id="123"] where id is the id of the testimonial custom type post.
	 *
	 * @since 0.1.0
	 */
	public function tml_shortcode($atts){

		/* Check to see if CPT exists. */
		if( !post_type_exists( 'testimonial' ) ) {
			return '<p>Testimonial custom post type not found. It is needed for the [tml] shortcode to work properly.</p>';
		} 

		/* Get the testimonial id from the shortcode attributes. */
		extract(shortcode_atts(array(
			'id' => ''
		), $atts));

		/* Check to see if CPT has been specified. */
		if( empty($id) ) {
			return '<p>Testimonial not found. Please make sure you enter a valid ID.</p>';
		} 

		/* Get the custom post type object from the post id. */
		$post = get_post( $id );

		if( isset($post) ) {
			/* Get the post content. */
			$content = $post->post_content;

			/* Get the rest of the shortcode attributes from the custom post type meta data. */

			/* If no featured image set, use gravatar if specified. */
			if( !($image = get_the_post_thumbnail( $id, array(32,32), array('class' => 'avatar', 'title' => '') ) ) ) {
				$image = get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_testimonial_cpt_image',true);
				if( !trim($image) == '' ) {
					$image = get_avatar( $image, $size = '50' );
				}
			}

			$name = $post->post_title;
			if(!empty($name)){
				$name = "<p class=\"testimonial-name\">{$name}</p>";
			}

			$company_url = trim(get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_testimonial_cpt_company_url',true)); 
			$company = get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_testimonial_cpt_company',true);
			if(!empty($company)){
				if( empty($company_url) ) {
					$company = "<p class=\"testimonial-company\">{$company}</p>";
				}
				else {
					/* Add in support for making the company name a link. */
					$company = "<p class=\"testimonial-company\"><a href=\"{$company_url}\" target=\"_blank\">{$company}</a></p>";
				}
			}

			return "<div class=\"testimonial\"><div class=\"quote\"><p>{$content}</p></div><div class=\"testimonial-meta\">{$image}{$name}{$company}</div></div>";
		}
		else {
			return "<p>Testimonial with id='".$id."' not found. Please make sure you enter a valid ID.</p>";
		}
	}

	/**
	 * [box] shortcode function.
	 *
	 * Example usage: [box color="green"]Here is some content in a coloured box![/box]
	 *
	 * @since 0.1.0
	 */
	public function box_shortcode($atts, $content = NULL ){

		// Support self-closing, and enclosing shortcodes (but does not like mixed on the same page/post)
		extract(shortcode_atts(array(
			'color' => 'default'
		), $atts));

		if(empty($content)){
			$content = __( 'Please add your content in-between opening and closing box tags: e.g. &#91;box&#93;Your content here..&#91;/box&#93;', 'presscoders' );
		}

		if($color == "green" || $color == "blue" || $color == "yellow" || $color == "red") { return do_shortcode( "<div class=\"box {$color}box\">{$content}</div>" ); }
		else { return do_shortcode( "<div class=\"box defaultbox\">{$content}</div>" ); }
	}

	/**
	 * [button] shortcode function.
	 *
	 * Example usage: [button href="http://www.google.com" target="_blank"]Click Me![/button]
	 *
	 * @since 0.1.0
	 */
	public function button_shortcode($atts, $content=null ){

		// Support self-closing, and enclosing shortcodes (but does not like mixed on the same page/post)!
		extract(shortcode_atts(array(
			'size' => '',
			'target' => '',
			'link' => '#',
			'color' => 'defaultbtn'
		), $atts));

		if($target != "") {
			if($target == "_blank" || $target == "_parent" || $target == "_self" || $target == "_top") { $target = " target=\"$target\""; } // i.e. _blank => target="_blank"
			else { $target = ""; } // not valid target so reset to default
		}

		if($link != "#") { /* If default link then don't bother to sanitize. */
            $link = esc_url( $link );
		}

		// make sure the size attribute is 'normal', or 'big'
		if($size == "big") {
			$size = " ".$size;
		}
		else {
			$size = "";
		}

		if(empty($content)){
			$content = __( 'Button Text', 'presscoders' );
		}

		if($color == "gold" || $color == "lightblue" || $color == "gray" || $color == "black" || $color == "white" || $color == "red" || $color == "blue" || $color == "yellow" || $color == "green") { return do_shortcode( "<a href=\"$link\"$target class=\"button{$size} {$color}\">{$content}</a>" ); }
		else { return do_shortcode( "<a href=\"$link\"$target class=\"button{$size} defaultbtn\">{$content}</a>" ); }
	}

	/**
	 * [one_half] shortcode function.
	 *
	 * Example usage: [one_half]Add content here.[/one_half]
	 *
	 * @since 0.1.0
	 */
	public function one_half_shortcode($atts, $content = NULL ){

		if(empty($content))
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );
	
		return "<div class=\"one-half\">".wpautop(do_shortcode($content))."</div>";
	}

	/**
	 * [one_half_last] shortcode function.
	 *
	 * Example usage: [one_half_last]Add content here.[/one_half_last]
	 *
	 * @since 0.1.0
	 */
	public function one_half_last_shortcode($atts, $content = NULL ){

		if(empty($content))
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );

		return "<div class=\"one-half last-col\">".wpautop(do_shortcode($content))."</div><br class=\"clear\" />";
	}

	/**
	 * [one_third] shortcode function.
	 *
	 * Example usage: [one_third]Add content here.[/one_third]
	 *
	 * @since 0.1.0
	 */
	public function one_third_shortcode($atts, $content = NULL ){

		if(empty($content))
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );

		return "<div class=\"one-third\">".wpautop(do_shortcode($content))."</div>";
	}

	/**
	 * [one_third_last] shortcode function.
	 *
	 * Example usage: [one_third_last]Add content here.[/one_third_last]
	 *
	 * @since 0.1.0
	 */
	public function one_third_last_shortcode($atts, $content = NULL ){

		if(empty($content))
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );

		return "<div class=\"one-third last-col\">".wpautop(do_shortcode($content))."</div><br class=\"clear\" />";
	}

	/**
	 * [two_thirds] shortcode function.
	 *
	 * Example usage: [two_thirds]Add content here.[/two_thirds]
	 *
	 * @since 0.1.0
	 */
	public function two_thirds_shortcode($atts, $content = NULL ){

		if(empty($content))
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );

		return "<div class=\"two-thirds\">".wpautop(do_shortcode($content))."</div>";
	}

	/**
	 * [two_thirds_last] shortcode function.
	 *
	 * Example usage: [two_thirds_last]Add content here.[/two_thirds_last]
	 *
	 * @since 0.1.0
	 */
	public function two_thirds_last_shortcode($atts, $content = NULL ){

		if(empty($content))
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );

		return "<div class=\"two-thirds last-col\">".wpautop(do_shortcode($content))."</div><br class=\"clear\" />";
	}

	/**
	 * [one_fourth] shortcode function.
	 *
	 * Example usage: [one_fourth]Add content here.[/one_fourth]
	 *
	 * @since 0.1.0
	 */
	public function one_fourth_shortcode($atts, $content = NULL ){

		if(empty($content)){
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );
		}

		return "<div class=\"one-fourth\">".wpautop(do_shortcode($content))."</div>";
	}

	/**
	 * [one_fourth_last] shortcode function.
	 *
	 * Example usage: [one_fourth_last]Add content here.[/one_fourth_last]
	 *
	 * @since 0.1.0
	 */
	public function one_fourth_last_shortcode($atts, $content = NULL ){

		if(empty($content)){
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );
		}

		return "<div class=\"one-fourth last-col\">".wpautop(do_shortcode($content))."</div><br class=\"clear\" />";
	}

	/**
	 * [three_fourths] shortcode function.
	 *
	 * Example usage: [three_fourths]Add content here.[/three_fourths]
	 *
	 * @since 0.1.0
	 */
	public function three_fourths_shortcode($atts, $content = NULL ){

		if(empty($content)){
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );
		}

		return "<div class=\"three-fourths\">".wpautop(do_shortcode($content))."</div>";
	}

	/**
	 * [three_fourths_last] shortcode function.
	 *
	 * Example usage: [three_fourths_last]Add content here.[/three_fourths_last]
	 *
	 * @since 0.1.0
	 */
	public function three_fourths_last_shortcode($atts, $content = NULL ){

		if(empty($content)){
			$content = __( 'No content specified. Add content in-between the opening and closing shortcode tags.', 'presscoders' );
		}

		return "<div class=\"three-fourths last-col\">".wpautop(do_shortcode($content))."</div><br class=\"clear\" />";
	}

	public function custom_gallery_shortcode($attr) {
		global $post;

		static $instance = 0;
		$instance++;

		// Allow plugins/themes to override the default gallery template.
		$output = apply_filters('post_gallery', '', $attr);
		if ( $output != '' )
			return $output;

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}

		extract(shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => ''
		), $attr));

		$id = intval($id);
		if ( 'RAND' == $order )
			$orderby = 'none';

		if ( !empty($include) ) {
			$include = preg_replace( '/[^0-9,]+/', '', $include );
			$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($exclude) ) {
			$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
			$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		} else {
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		}

		if ( empty($attachments) )
			return '';

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
			return $output;
		}

		$itemtag = tag_escape($itemtag);
		$captiontag = tag_escape($captiontag);
		$columns = intval($columns);
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		$float = is_rtl() ? 'right' : 'left';

		$selector = "gallery-{$instance}";

		$gallery_style = $gallery_div = '';
		if ( apply_filters( 'use_default_gallery_style', true ) )
			$gallery_style = "
			<style type='text/css'>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;
				}
				#{$selector} img {
					border: 2px solid #cfcfcf;
				}
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
			</style>
			<!-- see gallery_shortcode() in wp-includes/media.php -->";
		$size_class = sanitize_html_class( $size );
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
		$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

		$i = 0;
		foreach ( $attachments as $id => $attachment ) {
			$link = wp_get_attachment_link($id, $size, false, false);

			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
				<{$icontag} class='gallery-icon'>
					$link
				</{$icontag}>";
			if ( $captiontag && trim($attachment->post_excerpt) ) {
				$output .= "
					<{$captiontag} class='wp-caption-text gallery-caption'>
					" . wptexturize($attachment->post_excerpt) . "
					</{$captiontag}>";
			}
			$output .= "</{$itemtag}>";
			if ( $columns > 0 && ++$i % $columns == 0 )
				$output .= '<br style="clear: both" />';
		}

		$output .= "
				<br style='clear: both;' />
			</div>\n";

		return $output;
	}

}

?>