<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Intergalactic
 */

if ( ! function_exists( 'intergalactic_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function intergalactic_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'intergalactic' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'intergalactic' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'intergalactic' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'intergalactic_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function intergalactic_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'intergalactic' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&larr;</span>&nbsp;Previous Post', 'Previous post link', 'intergalactic' ) );
				next_post_link(     '<div class="nav-next">%link</div>',     _x( 'Next Post&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link',     'intergalactic' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'intergalactic_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function intergalactic_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( 'Posted on %s', 'post date', 'intergalactic' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		_x( 'by %s', 'post author', 'intergalactic' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>';

}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function intergalactic_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'intergalactic_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'intergalactic_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so intergalactic_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so intergalactic_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in intergalactic_categorized_blog.
 */
function intergalactic_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'intergalactic_categories' );
}
add_action( 'edit_category', 'intergalactic_category_transient_flusher' );
add_action( 'save_post',     'intergalactic_category_transient_flusher' );


/**
 * Returns the URL from the post.
 *
 * @uses get_the_link() to get the URL in the post meta (if it exists) or
 * the first link found in the post content.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @return string URL
 */
function intergalactic_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}


/*
 * Return the post format, linked to the post format archive
 */
function intergalactic_post_format() {
	$format = get_post_format();
	$formats = get_theme_support( 'post-formats' );

	//If the post has no format, or if it's not a format supported by the theme, return
	if ( ! $format || ! has_post_format( $formats[0] ) )
		return;

	printf( '<a class="entry-format" href="%1$s" title="%2$s">%3$s</a>',
				esc_url( get_post_format_link( $format ) ),
				esc_attr( sprintf( __( 'All %1$s posts', 'intergalactic' ), get_post_format_string( $format ) ) ),
				get_post_format_string( $format )
			);
}