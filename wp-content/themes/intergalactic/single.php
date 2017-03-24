<?php
/**
 * The template for displaying all single posts.
 *
 * @package Intergalactic
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>


			<div class="entry-footer-wrapper">
				<?php if ( '' != get_the_author_meta( 'description' ) ) : ?>
					<div class="entry-author">
						<div class="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), '140' ); ?>
						</div>
						<div class="author-bio">
							<?php echo get_the_author_meta( 'description' ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php intergalactic_post_nav(); ?>
			</div><!-- .entry-footer-wrapper -->

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>