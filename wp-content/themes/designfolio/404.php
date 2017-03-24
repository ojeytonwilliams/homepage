<?php global $pc_theme_object; /* Reference theme framework class */ ?>
<?php get_header(); ?>

<?php PC_Hooks::pc_after_get_header(); /* Framework hook wrapper */ ?>

	<div id="container">

		<?php PC_Hooks::pc_after_container(); /* Framework hook wrapper */ ?>

		<div id="contentwrap" <?php echo PC_Utility::contentwrap_layout_classes(); ?>>

			<?php PC_Hooks::pc_before_content(); /* Framework hook wrapper */ ?>

			<div class="<?php echo PC_Utility::content_layout_classes_primary(); ?>">

				<?php PC_Hooks::pc_after_content_open(); /* Framework hook wrapper */ ?>

				<div id="main">                                                         
					<div>
						<h2 class="page-title"><?php _e('Error 404 - Page not found!', 'presscoders' ) ?></h2>
						
						<div>
							<p>
							<?php _e('Apologies, but the page you trying to reach does not exist, or has been moved. Why not try going back to the ', 'presscoders' ) ?><a href="<?php echo home_url(); ?>"><?php _e('home page', 'presscoders' ) ?></a><?php _e(', using the menus, or searching for something more specific?', 'presscoders' ) ?>
							</p>
							<div class="search404"><?php get_search_form(); ?></div>
							
						</div><!-- .entry -->
					</div>
				</div><!-- #main -->

			</div><!-- .content -->
			
			<?php PC_Hooks::pc_after_content(); /* Framework hook wrapper */ ?>

		</div><!-- #contentwrap -->
			
	</div><!-- #container -->

<?php get_footer(); ?>