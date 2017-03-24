<?php

// ----------------------------------
//  Portfolio CPT Slider Widget Class
// ----------------------------------

class pc_portfolio_cpt_slider_widget extends WP_Widget {

	function pc_portfolio_cpt_slider_widget() {

		/* Add additional slider image sizes used only by the slider */
		$this->set_slider_image_sizes();

		$widget_ops = array(	'classname' => 'pc_portfolio_cpt_slider_widget',
								'description' => __( 'Slider to display groups of Portfolio items.', 'presscoders' )
		);
		$this->WP_Widget('pc_portfolio_cpt_slider_widget_'.PC_THEME_NAME_SLUG, __( 'Portfolio Slider', 'presscoders' ), $widget_ops);
	}

	function form( $instance ) {
		$defaults = array(	'transition_effect' => 'fade',
							'delay_speed' => '7000',
							'transition_speed' => '600',
							'slider_groups' => '1',
							'number_items' => 4,
							'randomize' => false,
							'fi_post_link' => true,
							'autoplay' => false,
							'animation_loop' => true,
							'smooth_height' => false
		);
        $instance = wp_parse_args( (array) $instance, $defaults );

		$delay_speed = strip_tags($instance['delay_speed']);
		$transition_speed = strip_tags($instance['transition_speed']);
		$randomize = strip_tags($instance['randomize']);
		$fi_post_link = strip_tags($instance['fi_post_link']);
		$autoplay = strip_tags($instance['autoplay']);
        $animation_loop = strip_tags($instance['animation_loop']);
        $smooth_height = strip_tags($instance['smooth_height']);
		$transition_effect = $instance['transition_effect'];

		if ( !isset($instance['delay_speed']) || !$delay_speed = (int) $instance['delay_speed'] )
			$delay_speed = 3000;

		if ( !isset($instance['transition_speed']) || !$transition_speed = (int) $instance['transition_speed'] )
			$transition_speed = 350;

		if ( !isset($instance['number_items']) || !$number_items = (int) $instance['number_items'] )
			$number_items = 4;

		/* Check the taxonmy contains any terms. If none found then exit the function. */
		$args = array( 'taxonomy' => 'portfolio_group', 'title_li' => '', 'show_option_none' => 'zero', 'style' => 'none', 'echo' => 0 );
		if( wp_list_categories( $args ) == 'zero' ) {
			_e( 'No slider groups found. New groups can be added via Slides -> Portfolio Groups.', 'presscoders' );
			return;
		}
		?>

		<div style="margin-bottom:2px;"><label for="<?php echo $this->get_field_id('slider_groups'); ?>"><?php _e( 'Choose portfolio group:', 'presscoders' ); ?></label></div>
		<?php
			$args = array(
				'id' =>				$this->get_field_id( 'slider_groups' ),
				'hide_empty'=>		0,
				'hierarchical' =>	1,
				'show_count' =>		1,
				'name' =>			$this->get_field_name( 'slider_groups' ),
				'taxonomy' =>		'portfolio_group',
				'class'=>			'widefat',
				'selected' =>		$instance[ 'slider_groups' ]
			);
			wp_dropdown_categories( $args );
		?>
		<p style="margin:10px 0px;">
			<label for="<?php echo $this->get_field_id('number_items'); ?>"><?php _e( 'Maximum No. of slider items', 'presscoders' ); ?>: </label>
			<input id="<?php echo $this->get_field_id('number_items'); ?>" name="<?php echo $this->get_field_name('number_items'); ?>" type="text" value="<?php echo $number_items; ?>" size="3" />
		</p>

		<label><input type="checkbox" value="1" <?php checked( $randomize, '1' ); ?> name="<?php echo $this->get_field_name( 'randomize' ); ?>" />&nbsp;<?php _e('Random slide order', 'presscoders' ) ?></label><br />
		<label><input type="checkbox" value="1" <?php checked( $autoplay, '1' ); ?> name="<?php echo $this->get_field_name( 'autoplay' ); ?>" />&nbsp;<?php _e('Autoplay slides', 'presscoders' ) ?></label><br />
		<label><input type="checkbox" value="1" <?php checked( $smooth_height, '1' ); ?> name="<?php echo $this->get_field_name( 'smooth_height' ); ?>" />&nbsp;<?php _e('Slide Smooth Height', 'presscoders' ) ?></label><br />
		<label><input type="checkbox" value="1" <?php checked( $fi_post_link, '1' ); ?> name="<?php echo $this->get_field_name( 'fi_post_link' ); ?>" />&nbsp;<?php _e('Link featured image to post url', 'presscoders' ) ?></label><br />

		<?php // @todo This was hidden 23/03/2012 as it's probably not that useful an option. If it's not missed then remove it completely rather than just hide it. ?>
		<label style="display:none;"><input type="checkbox" value="1" <?php checked( $animation_loop, '1' ); ?> name="<?php echo $this->get_field_name( 'animation_loop' ); ?>" />&nbsp;<?php _e('Loop Slides', 'presscoders' ) ?></label>

		<p>
			<div style="margin-bottom:2px;"><?php _e( 'Transition settings', 'presscoders' ); ?>:</div>
			<?php _e( 'Effect', 'presscoders' ); ?>: <select id="<?php echo $this->get_field_id('transition_effect'); ?>" name="<?php echo $this->get_field_name('transition_effect'); ?>">
				<option value="slide" <?php selected('slide', $instance['transition_effect']); ?>><?php _e( 'Slide', 'presscoders' ); ?>&nbsp;</option>
				<option value="fade" <?php selected('fade', $instance['transition_effect']); ?>><?php _e( 'Fade', 'presscoders' ); ?>&nbsp;</option>
			</select>
			<label for="<?php echo $this->get_field_id('transition_speed'); ?>">&nbsp;&nbsp;<?php _e( 'Speed', 'presscoders' ); ?>: </label>
			<input id="<?php echo $this->get_field_id('transition_speed'); ?>" name="<?php echo $this->get_field_name('transition_speed'); ?>" type="text" value="<?php echo $transition_speed; ?>" size="2" /> ms
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('delay_speed'); ?>"><?php _e( 'Delay between slides', 'presscoders' ); ?>: </label>
			<input id="<?php echo $this->get_field_id('delay_speed'); ?>" name="<?php echo $this->get_field_name('delay_speed'); ?>" type="text" value="<?php echo $delay_speed; ?>" size="2" /> ms
		</p>

	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'delay_speed' ] = strip_tags($new_instance[ 'delay_speed' ]);
		$instance[ 'transition_speed' ] = strip_tags($new_instance[ 'transition_speed' ]);
		$instance[ 'slider_groups' ] = $new_instance[ 'slider_groups' ];
		$instance[ 'number_items' ] = (int) $new_instance[ 'number_items' ];
        $instance[ 'randomize' ] = strip_tags($new_instance['randomize']);
        $instance[ 'fi_post_link' ] = strip_tags($new_instance['fi_post_link']);
        $instance[ 'autoplay' ] = strip_tags($new_instance['autoplay']);
        $instance[ 'smooth_height' ] = strip_tags($new_instance['smooth_height']);
		$instance[ 'animation_loop' ] = strip_tags($new_instance['animation_loop']);
		$instance[ 'transition_effect' ] = $new_instance['transition_effect'];

		return $instance;
	}

	function widget($args, $instance) {

		extract($args);

		$delay_speed = $instance[ 'delay_speed' ];
		$transition_speed = $instance[ 'transition_speed' ];
        $number_items = $instance[ 'number_items' ];
		$slider_groups = $instance[ 'slider_groups' ];
		$randomize = $instance['randomize'];
		$fi_post_link = $instance['fi_post_link'];
		$autoplay = $instance['autoplay'];
		$smooth_height = $instance['smooth_height'];
		$animation_loop = $instance['animation_loop'];
		$transition_effect = $instance['transition_effect'];

		/* */
		if( empty($slider_groups) ) $slider_groups = '';

		$order = ( $randomize ) ? 'rand' : 'date';
		$autoplay_slides = ($autoplay) ? 'true' : 'false';
		$smooth_height = ($smooth_height) ? 'true' : 'false';
		$auto_animation_loop = ($animation_loop) ? 'true' : 'false';

		$r = new WP_Query( array(	'post_type' => 'portfolio',
									'orderby' => $order,
									'showposts' => $number_items,
									'nopaging' => 0,
									'post_status' => 'publish',
									'ignore_sticky_posts' => 1,
									'tax_query' => array(
										array(
											'taxonomy' => 'portfolio_group',
											'field' => 'id',
											'terms' => $slider_groups
										)
									)							
		));

		if ($r->have_posts()) :

			// There is at least one post to show
			global $pc_is_front_page;
			global $pc_post_id; // need the post id to check for the sidebars per post/page feature
			global $wp_registered_sidebars; // global widget areas array
			global $pc_global_column_layout;

			// ***********************************************************
			// **     DETERMINE WIDGET AREA LOCATION AND WIDTH TYPE     **
			// ***********************************************************

			$widget_area = null;
			$sidebars_widgets = wp_get_sidebars_widgets();
			// Try to get the widget area the widget belongs to
			foreach ( $sidebars_widgets as $sidebar => $widgets ) {
				if ( 'wp_inactive_widgets' == $sidebar )
					continue; // Ignore inactive widgets
				if ( is_array($widgets) ) {
					if ( in_array( $this->id, $widgets ) ) {
						$widget_area = $sidebar;
					}
				}
			}

			// If widget instance exists in a widget area
			if( $widget_area ) {
				// find the widget area width
				if( isset($wp_registered_sidebars[$widget_area]['width']) ) {
					$widget_area_width = $wp_registered_sidebars[$widget_area]['width'];
					$width_default = 0; // for debugging
				}
				// if not found, default to normal (i.e. standard widget width)
				else {
					$widget_area_width = 'normal';
					$width_default = 1; // for debugging
				}
			}

			// *******************************************************
			// **     SET THE THUMBNAIL SIZE USED BY THE WIDGET     **
			// *******************************************************

			// add code to enter the correct classname, and slider thumbnail size, depending on the column layout and the widget area width
			if($widget_area_width == 'full') {
				$slider_thumb_size = 'slider_content_full';
			}
			elseif ($widget_area_width == 'wide') {
				// check page layout to see if the page layout is twothirds, or full width
				$layout_num = (int)substr($pc_global_column_layout, 0, 1);
				if ($layout_num == 1) {
					// full width layout
					$slider_thumb_size = 'slider_content_full';
				}
				elseif ($layout_num == 2) {
					// 2 column layout
					$slider_thumb_size = 'slider_content_twothirds';
				}
				else {
					// else assume 3 column layout
					$slider_thumb_size = 'slider_content_third';
				}
			}
			else {
				// assume widget area width is 'normal'
				$slider_thumb_size = 'slider_content_third';
			}

		?>

		<?php echo $before_widget; ?>

			<script type="text/javascript">
				
				jQuery(window).load(function() {
					jQuery('.flexslider_<?php echo $this->id; ?>').flexslider({
						  animation: '<?php echo $transition_effect; ?>',
						  animationLoop: <?php echo $auto_animation_loop; ?>,
						  pauseOnAction: true,
						  slideshow: <?php echo $autoplay_slides; ?>,
						  pauseOnHover: true,
						  slideshowSpeed: <?php echo $delay_speed; ?>,
						  animationSpeed: <?php echo $transition_speed; ?>,
						  controlsContainer: ".flex-container_<?php echo $this->id; ?>",
						  smoothHeight: <?php echo $smooth_height; ?>,
						  multipleKeyboard: true,
						  start: function(slider) {
							<?php
								// Add any 'start' callback code via a theme specific utility function cb
								if( method_exists( 'PC_TS_Utility', 'portfolio_slider_jquery_start_cb' ) )
									echo PC_TS_Utility::portfolio_slider_jquery_start_cb();
							?>
						  },
						  before: function(slider) {
							// Add code here (via theme specific callback function)
						  },
						  after: function(slider) {
							// Add code here (via theme specific callback function)
						  }
					});
				});

			</script>

			<div class="flex-container flex-container_<?php echo $this->id; ?>">

				<div class="flexslider flexslider_<?php echo $this->id; ?>">
					<ul class="slides slides_<?php echo $this->id; ?>">

						<?php while ($r->have_posts()) : $r->the_post(); ?>

							<?php

								/* Image */
								$post_id = get_the_ID();
								$featured_image = PC_Utility::get_responsive_slider_image( $post_id, $slider_thumb_size );

								//$show_title = get_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_slide_cpt_show_title',true);
								$title = get_the_title(); //($show_title == 0) ? get_the_title() : null;

								$portfolio_fi_url = trim(get_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_portfolio_cpt_fi_link',true));
								$portfolio_title_url = trim(get_post_meta($post_id, '_'.PC_THEME_NAME_SLUG.'_portfolio_cpt_title_link',true));

								$post_thumbnail_id = get_post_thumbnail_id( $post_id );
								$featured_image_src_arr = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );

								/* Portfolio featured image link. */
								if( $fi_post_link ) {
									/* Override default PF CPT behavior and link FI to post url by default. */
									$featured_image_src = get_permalink($post_id);
									$featured_image = '<a href="'.$featured_image_src.'">'.$featured_image.'</a>';
								}
								else {
									if( empty($portfolio_fi_url) ) {
										if( $featured_image && is_array($featured_image_src_arr) ) {
											$featured_image_src = $featured_image_src_arr[0];
											$featured_image = '<a href="'.$featured_image_src.'">'.$featured_image.'</a>';
										}
									}
									else {
										if( $portfolio_fi_url != 'http://none' ) {
											$featured_image = '<a href="'.$portfolio_fi_url.'">'.$featured_image.'</a>';
										}
									}
								}

								/* Portfolio title link. */
								if( empty($portfolio_title_url) ) {
									$pl = get_permalink($post_id);
									$title = '<a href="'.$pl.'">'.$title.'</a>';
								}
								else {
									if( $portfolio_title_url != 'http://none' ) {
										$title = '<a href="'.$portfolio_title_url.'">'.$title.'</a>';
									}
								}

								if( !empty($title) ) $title = '<h3 class="portfolio-title">'.$title.'</h3>';

								$content = wpautop( do_shortcode( get_the_content() ) );

							?>

							<?php
								/* Allow optional theme specific slider structure to be defined. */
								if( method_exists( 'PC_TS_Utility', 'custom_portfolio_slider_li_structure' ) ) :
									
									$args = array(	//'show_title'		=>  $show_title,
													'title'				=>	$title,
													'featured_image'	=>	$featured_image,
													'content'			=>	$content,
													'post_id'			=>	$post_id
									);

									PC_TS_Utility::custom_portfolio_slider_li_structure($args);

								/* Otherwise use the default structure. */
								else :
							?>

									<li>
										<?php
											echo '<div class="portfolio-slide-content">';
											echo $title; //if($show_title == 0) echo $title;
											if(!empty($featured_image)) echo $featured_image;
											if(!empty($content)) echo $content;
											echo '</div>';
										?>
									</li>

							<?php endif; ?>

						<?php endwhile; ?>

					</ul>
				</div>

			</div>

		<?php echo $after_widget; ?>

		<?php

		/* Reset the global $the_post as this query will have stomped on it. */
		wp_reset_postdata();

		else :

			echo "<div>No slider items to show. Please select a group that contains some items!</div>";

		endif;
	}

	/**
	 * Set content slider image sizes.
	 *
	 * @since 0.1.0
	 */
	public function set_slider_image_sizes() {

		global $_wp_additional_image_sizes;

		/* Check if slider image sizes need specifying, as they may have been set in another slider widget. */
		if ( !isset( $_wp_additional_image_sizes['slider_content_third'] ) ) {

			/* Only define individual content slider heights if ALL three values supplied in functions.php. */
			if( PC_SLIDER_CONTENT_IMG_ONE_THIRD_HEIGHT && PC_SLIDER_CONTENT_IMG_TWO_THIRDS_HEIGHT && PC_SLIDER_CONTENT_IMG_FULL_HEIGHT ) {
				add_image_size( 'slider_content_third', PC_SLIDER_CONTENT_IMG_THIRD_WIDTH, PC_SLIDER_CONTENT_IMG_ONE_THIRD_HEIGHT, true );
				add_image_size( 'slider_content_twothirds', PC_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH, PC_SLIDER_CONTENT_IMG_TWO_THIRDS_HEIGHT, true );
				add_image_size( 'slider_content_full', PC_SLIDER_CONTENT_IMG_FULL_WIDTH, PC_SLIDER_CONTENT_IMG_FULL_HEIGHT, true );
			}
			else {
				add_image_size( 'slider_content_third', PC_SLIDER_CONTENT_IMG_THIRD_WIDTH, PC_SLIDER_CONTENT_IMG_HEIGHT, true );
				add_image_size( 'slider_content_twothirds', PC_SLIDER_CONTENT_IMG_TWO_THIRDS_WIDTH, PC_SLIDER_CONTENT_IMG_HEIGHT, true );
				add_image_size( 'slider_content_full', PC_SLIDER_CONTENT_IMG_FULL_WIDTH, PC_SLIDER_CONTENT_IMG_HEIGHT, true );
			}

		}
	}

	/**
	 * PHP callback function to filter an array, returning only numbers greater than zero.
	 *
	 * @since 0.1.0
	 */
	public function delete_zero($var)
	{
		return ($var > 0);
	}
}

?>