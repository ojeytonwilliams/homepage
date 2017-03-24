<?php
/*
Template Name: Blog Template
*/
?>
<?php global $pc_theme_object; /* Reference theme framework class */ ?>
<?php get_header(); ?>

<?php PC_Hooks::pc_after_get_header(); /* Framework hook wrapper */ ?>

	<div id="container">

		<?php PC_Hooks::pc_after_container(); /* Framework hook wrapper */ ?>

		<div id="contentwrap" <?php echo PC_Utility::contentwrap_layout_classes(); ?>>

			<?php PC_Hooks::pc_before_content(); /* Framework hook wrapper */ ?>

			<div class="<?php echo PC_Utility::content_layout_classes_primary(); ?> blog">

				<?php PC_Hooks::pc_after_content_open(); /* Framework hook wrapper */ ?>

				<?php
					/* Store the page query and show the blog post loop instead. */
					$temp = $wp_query;
					$wp_query = null;
					$wp_query = new WP_Query();
					$query_args = array(
						'post_type' => 'post',
						'paged' => $paged
					);
					$wp_query->query( $query_args );
				?>

				<?php get_template_part( 'loop', 'blog-page' ); /* Framework blog page posts loop. */ ?>

				<?php
				/* Reset the page query. */
				$wp_query = null;
				$wp_query = $temp;
				wp_reset_query();
				?>

			</div><!-- .content -->
			  
	  		<?php PC_Hooks::pc_after_content(); /* Framework hook wrapper */ ?>
				
		</div><!-- #contentwrap -->
	
	</div><!-- #container -->

<?php get_footer(); ?>