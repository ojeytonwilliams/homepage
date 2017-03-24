<?php
/**
 * The single attachment post loop.
 *
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

	<div class="post-aside">
	<p class="post-date"><?php the_time('M'); ?><br /><?php the_time('j'); ?></p>

	<p class="author">By <?php the_author(); ?></p>

	<?php PC_Hooks::pc_pre_post_meta(); /* Framework hook wrapper */ ?>
	</div>
	
	<div class="post-content">

		<h1 class="entry-title"><?php the_title(); ?></h1>

		<div class="post-meta">
			<?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>
			<p>
				<?php
					$att_cat = get_the_category();
					if(!empty($att_cat)) :
				?>
				<span class="categories"><?php echo $att_cat; ?></span>
				<?php endif; ?>

				<?php global $post; ?>
				<?php if ( 'open' == $post->comment_status ) : ?>
				<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="<?php the_title_attribute(); ?>"><?php comments_number('Leave a Comment','1 Comment','% Comments'); ?></a></span>
				<?php endif; ?>
			</p>
			<?php PC_Hooks::pc_after_post_meta(); /* Framework hook wrapper */ ?>
		</div><!-- .post-meta -->

		<?php if ( wp_attachment_is_image( get_the_ID() ) ) :
			$att_image = wp_get_attachment_image_src( get_the_ID(), "medium"); ?>
			<p class="attachment"><a href="<?php echo wp_get_attachment_url(get_the_ID()); ?>" title="<?php the_title(); ?>" rel="attachment"><img src="<?php echo $att_image[0];?>" /></a></p>
		<?php endif; ?>

		<?php
			the_content('');
			wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
		?>
	</div> <!-- post-content -->
</div> <!-- post-item -->

<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>