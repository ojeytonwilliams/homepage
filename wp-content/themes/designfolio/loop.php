<?php
/**
 * The main posts loop.
 *
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<div class="post-aside">
		<p class="post-date"><?php the_time('M'); ?><br /><?php the_time('j'); ?></p>

		<p class="author">By <?php the_author_posts_link(); ?></p>
		</div>
		
		<div class="post-content">

		<?php $post_title = get_the_title(); ?>
		<?php if ( !empty($post_title) ) : ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		<?php else : ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark">(no title)</a></h2>
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
		</div><!-- .post-meta -->

		<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumb">
		  <?php
		  $post_id = get_the_ID();
		  echo PC_Utility::get_responsive_slider_image( $post_id, 'post-thumbnail' ); /* Show post thumbnail image, if one exists. */
		  ?>
		</div> <!-- .post-thumb -->
		<?php endif; ?>

			<?php
				global $more;
				$more = 0;
				the_content( ' '.__( 'Read More', 'presscoders' ) );
				wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
			?>
		</div> <!-- .post-content -->

	</div> <!-- .post -->

<?php endwhile; // end of the loop. ?>

<?php if( get_next_posts_link() || get_previous_posts_link() ) : ?>
<div class="navigation">
	<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Posts', 'presscoders' ) ) ?></div>
	<div class="alignright"><?php previous_posts_link( __( 'Newer Posts &raquo;', 'presscoders' ) ) ?></div>
</div>
<?php endif; ?>
