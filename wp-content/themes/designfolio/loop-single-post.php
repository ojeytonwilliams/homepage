<?php
/**
 * The single post loop.
 *
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

	<div class="post-aside">
	<p class="post-date"><?php the_time('M'); ?><br /><?php the_time('j'); ?></p>
	<p class="author">By <?php the_author_posts_link(); ?></p>

	<?php PC_Hooks::pc_pre_post_meta(); /* Framework hook wrapper */ ?>
	</div>
	
	<div class="post-content">

	<?php $post_title = get_the_title(); ?>
	<?php if ( !empty($post_title) ) : ?>
	<h1 class="entry-title"><?php the_title(); ?></h1>
	<?php else : ?>
	<h1 class="entry-title">(no title)</h1>
	<?php endif; ?>

	<div class="post-meta">
		<?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>
		<p>
			<span class="categories">Category: <?php the_category(', '); ?></span>

			<?php the_tags( '<span class="tags">Tags: ', ', ', '</span>'); ?>

			<?php global $post; ?>
			<?php if ( 'open' == $post->comment_status ) : ?>
			<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="<?php the_title_attribute(); ?>"><?php comments_number('Leave a Comment','1 Comment','% Comments'); ?></a></span>
			<?php endif; ?>
		</p>
		<?php PC_Hooks::pc_after_post_meta(); /* Framework hook wrapper */ ?>
	</div><!-- .post-meta -->

		<?php
			the_content('');
			wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
		?>
	</div> <!-- post-content -->
</div> <!-- post-item -->

<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>