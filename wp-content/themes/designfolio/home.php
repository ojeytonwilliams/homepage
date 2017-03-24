<?php global $pc_theme_object; /* Reference theme framework class */ ?>
<?php get_header(); ?>

<?php PC_Hooks::pc_after_get_header(); /* Framework hook wrapper */ ?>

	<div id="container">

		<?php PC_Hooks::pc_after_container(); /* Framework hook wrapper */ ?>

		<div id="contentwrap" <?php echo PC_Utility::contentwrap_layout_classes(); ?>>
		
			<?php PC_Hooks::pc_before_content(); /* Framework hook wrapper */ ?>

			<div class="<?php echo PC_Utility::content_layout_classes_primary(); ?>">

				<?php PC_Hooks::pc_after_content_open(); /* Framework hook wrapper */ ?>

				<?php get_template_part( 'loop', 'blog-page' ); /* Framework standard loop. */ ?>

			</div><!-- .content -->
			  
	  		<?php PC_Hooks::pc_after_content(); /* Framework hook wrapper */ ?>
		  
		</div><!-- #contentwrap -->
			
	</div><!-- #container -->

<?php get_footer(); ?>