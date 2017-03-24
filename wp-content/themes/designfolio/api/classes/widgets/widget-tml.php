<?php

// ---------------------------------
//  Theme *New* Testimonials Class
// ---------------------------------

class pc_tml_widget extends WP_Widget {

	function pc_tml_widget() {
		$widget_ops = array('classname' => 'pc_tml_widget', 'description' => __( 'Display a list of testimonials by group.', 'presscoders' ) );
		$this->WP_Widget('pc_tml_widget_'.PC_THEME_NAME_SLUG, __( 'Testimonials', 'presscoders' ), $widget_ops);
	}

	function form( $instance ) {
		$defaults = array( 'title' => '', 'tml_groups' => '1', 'number_tml' => 4, 'description' => '', 'randomize' => false );
        $instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags($instance['title']);
		$description = strip_tags($instance['description']);
        $randomize = strip_tags($instance['randomize']);

		if ( !isset($instance['number_tml']) || !$number_tml = (int) $instance['number_tml'] )
			$number_tml = 4;

		/* Check the taxonmy contains any terms. If none found then exit the function. */
		$args = array( 'taxonomy' => 'testimonial_group', 'title_li' => '', 'show_option_none' => 'zero', 'style' => 'none', 'echo' => 0 );
		if( wp_list_categories( $args ) == 'zero' ) {
			_e( 'No testimonial groups found. New groups can be added via Testimonials -> Testimonial Groups.', 'presscoders' );
			return;
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'presscoders' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e( 'Description:', 'presscoders' ) ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" rows="2"><?php echo esc_attr($description); ?></textarea>
		</p>
		<p>
			<div style="margin-bottom:1px;"><label for="<?php echo $this->get_field_id('tml_groups'); ?>"><?php _e( 'Display testimonial group:', 'presscoders' ); ?></label></div>
			<?php
				$args = array(
					'id' =>				$this->get_field_id( 'tml_groups' ),
					'hide_empty'=>		0,
					'hierarchical' =>	1,
					'show_count' =>		1,
					'name' =>			$this->get_field_name( 'tml_groups' ),
					'taxonomy' =>		'testimonial_group',
					'class'=>			'widefat',
					'selected' =>		$instance[ 'tml_groups' ]
				);
				wp_dropdown_categories( $args );
			?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number_tml'); ?>"><?php _e( 'Max number of testimonials', 'presscoders' ); ?></label>
			<input id="<?php echo $this->get_field_id('number_tml'); ?>" name="<?php echo $this->get_field_name('number_tml'); ?>" type="text" value="<?php echo $number_tml; ?>" size="3" />
		</p>
		<p>
			<label><?php _e('Show random testimonials?', 'presscoders' ) ?>&nbsp;<input type="checkbox" value="1" <?php checked( $randomize, '1' ); ?> name="<?php echo $this->get_field_name( 'randomize' ); ?>" /></label>
		</p>

	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ] = strip_tags($new_instance[ 'title' ]);
		$instance[ 'tml_groups' ] = $new_instance[ 'tml_groups' ];
		$instance[ 'number_tml' ] = (int) $new_instance[ 'number_tml' ];
		$instance[ 'description' ] = $new_instance[ 'description' ];
        $instance[ 'randomize' ] = strip_tags($new_instance['randomize']);

		return $instance;
	}

	function widget($args, $instance) {

		extract($args);

		$title = $instance[ 'title' ];
        $number_tml = $instance[ 'number_tml' ];
		$tml_groups = $instance[ 'tml_groups' ];
		$description = $instance[ 'description' ];
		$randomize = $instance['randomize'];

		$order = ( $randomize ) ? 'rand' : 'date';

		$r = new WP_Query( array(	'post_type' => 'testimonial',
									'orderby' => $order,
									'showposts' => $number_tml,
									'nopaging' => 0,
									'post_status' => 'publish',
									'ignore_sticky_posts' => 1,
									'tax_query' => array(
										array(
											'taxonomy' => 'testimonial_group',
											'field' => 'id',
											'terms' => $tml_groups
										)
									)							
			));

		if ($r->have_posts()) :
		?>

		<?php echo $before_widget; ?>
			<?php if ( $title ) echo $before_title . $title . $after_title; ?>

			<p><?php echo $description; ?></p>

			<?php while ($r->have_posts()) : $r->the_post(); ?>

				<?php
					/* Image */
					$id = get_the_ID();
					/* If no featured image set, use gravatar if specified. */
					if( !($image = get_the_post_thumbnail( $id, array(32,32), array('class' => 'avatar', 'title' => '') ) ) ) {
						$image = get_post_meta($id, '_'.PC_THEME_NAME_SLUG.'_testimonial_cpt_image',true);
						if( !trim($image) == '' ) {
							$image = get_avatar( $image, $size = '50' );
						}
					}

					/* Name */
					$name = get_the_title();
					if(!empty($name)){
						$name = "<p class=\"testimonial-name\">{$name}</p>";
					}
					
					/* Company */
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
				?>

				<div class="testimonial">
					<div class="quote">
						<p><?php the_content(); ?></p>
					</div>
					
					<?php echo "<div class=\"testimonial-meta\">{$image}{$name}{$company}</div>"; ?>
				</div>	
			<?php endwhile; ?>

		<?php echo $after_widget; ?>

		<?php

		/* Reset the global $the_post as this query will have stomped on it. */
		wp_reset_postdata();

		endif;
	}
}

?>