<?php
/*
Template Name: Nivo Slider
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
				<?php if ( function_exists('show_nivo_slider') ) { show_nivo_slider(); } ?>
				<div> After nivo slider </div>
				<?php if ( function_exists('nivo_slider') ) {nivo_slider( "slidey-slider" ); } ?>
				<div> After second nivo slider </div>
				<?php PC_Hooks::pc_after_content_open(); /* Framework hook wrapper */ ?>

				<?php get_template_part( 'loop', 'single-page' ); /* Framework standard loop. */ ?>

			</div><!-- .content -->

			<?php PC_Hooks::pc_after_content(); /* Framework hook wrapper */ ?>

		</div><!-- #contentwrap -->

	</div><!-- #container -->

<?php get_footer(); ?>
