<?php
/*
Template Name: Sitemap
*/
?>
<?php global $pc_theme_object; /* Reference theme framework class */ ?>
<?php get_header(); ?>

<?php PC_Hooks::pc_after_get_header(); /* Framework hook wrapper */ ?>

	<div id="container" class="singular-page">

		<?php PC_Hooks::pc_after_container(); /* Framework hook wrapper */ ?>

		<div id="contentwrap" <?php echo PC_Utility::contentwrap_layout_classes(); ?>>

			<?php PC_Hooks::pc_before_content(); /* Framework hook wrapper */ ?>

			<div class="<?php echo PC_Utility::content_layout_classes_primary(); ?>">

				<?php PC_Hooks::pc_after_content_open(); /* Framework hook wrapper */ ?>

				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class('singular-page'); ?>>
				
						<?php if ( is_front_page() ) { ?>
							<h2 class="page-title entry-title"><?php the_title(); ?></h2>
						<?php } else { ?>
							<h1 class="page-title entry-title"><?php the_title(); ?></h1>
						<?php } ?>
						
						<?php
							$page_args = array('title_li' => '');
							the_content();
							wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );

							echo '<div style="margin-left: 5px;"><ul class="page_item_list">';
							wp_list_pages($page_args); /* Show the sorted pages. */
						echo '</ul></div>';
						?>

				</div> <!-- post-item -->

				<?php endwhile; ?>

			</div><!-- .content -->

			<?php PC_Hooks::pc_after_content(); /* Framework hook wrapper */ ?>
		
		</div><!-- #contentwrap -->
	
	</div><!-- #container -->

<?php get_footer(); ?>