<?php
/**
 * @package Intergalactic
 */

$formats = get_theme_support( 'post-formats' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail() ) {
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'intergalactic-large' ); ?>
		<div class="entry-background" style="background-image:url(<?php echo esc_url( $thumbnail[0] ); ?>)"></div>
	<?php } ?>
	<header class="entry-header">
		<?php if ( 'link' == get_post_format() ) : ?>
			<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( intergalactic_get_link_url() ) ), '</a></h1>' ); ?>
		<?php else : ?>
			<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( has_post_format( $formats[0] ) ) : ?>
		<div class="entry-content">
			<?php the_content( __( 'Read More', 'intergalactic' ) ); ?>
			<?php if( ! strpos( $post->post_content, '<!--more-->' ) ) : ?>
				<a class="more-link" href="<?php the_permalink(); ?>" rel="bookmark">
					<?php
					/* translators: %s: Name of current post */
					printf(
						__( 'Read more %s', 'intergalactic' ),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					); ?>
				</a>
			<?php endif; ?>
		</div><!-- .entry-content -->
	<?php else : ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
			<a class="more-link" href="<?php the_permalink(); ?>" rel="bookmark">
				<?php
				/* translators: %s: Name of current post */
				printf(
					__( 'Read more %s', 'intergalactic' ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				); ?>
			</a>
		</div><!-- .entry-summary -->
	<?php endif; ?>

	<footer class="entry-footer">
		<div class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'intergalactic' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</footer>
</article><!-- #post-## -->
