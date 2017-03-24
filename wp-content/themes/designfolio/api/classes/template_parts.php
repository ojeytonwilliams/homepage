<?php

/**
 * Framework template parts class. Contains configurable template parts such as post loops which are all static,
 * so they can be referenced without having to instantiate the class.
 *
 * @since 0.1.0
 */
class PC_Template_Parts {

	/**
	 * PC_Template_Parts class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

	}

	/**
	 * Main posts loop template part.
	 *
	 * Used in theme template files such as index.php, home.php, and category.php etc.
	 *
	 * @since 0.1.0
	 */
	public static function main_post_loop( $args = array() ) {

		$defaults = array(	'read_more' => __( 'Read more', 'presscoders' ),
							'next' => __( '&laquo; Older Entries', 'presscoders' ),
							'prev' => __( 'Newer Entries &raquo;', 'presscoders' ),
							'main_content_loop' => 'default_main_post_loop_content'
						);
		$args = array_merge( $defaults, $args );
		$loop_method = $args['main_content_loop'];
	?>

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			
		<?php self::$loop_method( $args ); ?>

		<?php endwhile; // end of the loop. ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( $args['next'] ) ?></div>
			<div class="alignright"><?php previous_posts_link( $args['prev'] ) ?></div>
		</div>

	<?php
	}

	/**
	 * Blog page loop template part.
	 *
	 * Used for WordPress pages, such as blog-page.php, that don't have access to the main post loop.
	 *
	 * @since 0.1.0
	 */
	public static function blog_page_post_loop( $args = array() ) {

		$defaults = array(	'read_more' => __( 'Read more...', 'presscoders' ),
							'next' => __( '&laquo; Older Entries', 'presscoders' ),
							'prev' => __( 'Newer Entries &raquo;', 'presscoders' ),
							'main_content_loop' => 'default_main_post_loop_content'
						);
		$args = array_merge( $defaults, $args );
		$loop_method = $args['main_content_loop'];
	    ?>

		<?php
			/* Need to specify these two globals here. They were available to reference automatically
			   when this code was in blog-page.php but now we need to place a manual reference to them. */ 
			global $wp_query;
			global $paged;

			$temp = $wp_query;
			$wp_query = null;
			$wp_query = new WP_Query();
            $query_args = array(
                'post_type' => 'post',
                'paged' => $paged
            );
			$wp_query->query( $query_args );
			$cntr = 0;
			while ($wp_query->have_posts()) : $wp_query->the_post();
			$cntr++;
		?>

		<?php self::$loop_method( $args ); ?>
		
		<?php endwhile; ?>

        <?php wp_reset_postdata(); ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link( $args['next'] ) ?></div>
			<div class="alignright"><?php previous_posts_link( $args['prev'] ) ?></div>
		</div>

	<?php
	}

	public static function default_main_post_loop_content( $args = array() ) {
	?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php
				$options = get_option( PC_OPTIONS_DB_NAME );
				if( isset($options['chk_post_thumbails']) ) {
			?>
			  <div class="post-thumb">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail(); /* Show post thumbnail. */
				} else { /* Show default post thumb. */ ?>
					<img src="<?php echo PC_Utility::theme_resource_uri( 'images', 'default-post-thumb.jpg' ); ?>" alt="" />
				<?php
				}
				?>
			  </div> <!-- .post-thumb -->
			<?php } // endif ?>
			
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		   
			<div class="post-content">
			
				<?php
					global $more;
					$more = 0;
					the_content( ' '.$args['read_more'] );
					wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
				?>
			</div> <!-- .post-content -->
			
			<div class="post-meta">

				<?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>

				<p>
					<span class="date"><?php the_time('M j, Y'); ?></span>
					<span class="categories"><?php the_category(', ') ?></span>

					<?php if( comments_open() ) : ?>
					<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="<?php the_title_attribute(); ?>"><?php comments_number('Leave a Comment','1 Comment','% Comments'); ?></a></span>
					<?php endif; ?>
				</p>
			</div><!-- .post-meta -->

		</div> <!-- .post -->
	
	<?php
	}

    public static function custom_main_post_loop_content( $args = array() ) {
    ?>

        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

            <div class="post-thumb">
              <?php
              if ( has_post_thumbnail() ) the_post_thumbnail(); /* Show post thumbnail image, if one exists. */
              ?>
            </div> <!-- .post-thumb -->

            <div class="post-content">

                <?php
                    global $more;
                    $more = 0;
                    the_content( ' '.$args['read_more'] );
					wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
				?>
            </div> <!-- .post-content -->

            <div class="post-meta">

                <?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>

                <p>
					<span class="date"><?php the_time('M j, Y'); ?></span>
					<span class="author">By <?php the_author_posts_link(); ?></span>
					<span class="categories"><?php the_category(', ') ?></span>

					<?php if( comments_open() ) : ?>
					<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="<?php the_title_attribute(); ?>"><?php comments_number('Leave a Comment','1 Comment','% Comments'); ?></a></span>
					<?php endif; ?>
				</p>
            </div><!-- .post-meta -->

        </div> <!-- .post -->

    <?php
    }

	public static function single_post_loop() {
	?>

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

				<h1 class="entry-title"><?php the_title(); ?></h1>
			
		  <div class="post-content">
				<?php
					the_content('');
					wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
				?>
				
			<div class="post-meta">

				<?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>

				<p>
					<span class="date"><?php the_time('M j, Y'); ?></span>
					<span class="categories"><?php the_category(', ') ?></span>

					<?php if( comments_open() ) : ?>
					<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="<?php the_title_attribute(); ?>"><?php comments_number('Leave a Comment','1 Comment','% Comments'); ?></a></span>
					<?php endif; ?>
				</p>
		
			</div><!-- .post-meta -->
			
		  </div> <!-- post-content -->
		</div> <!-- post-item -->

		<?php comments_template( '', true ); ?>

		<?php endwhile; // end of the loop. ?>
	
	<?php
	}

	public static function single_page_loop() {
	?>

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class('singular-page'); ?>>
		
				<?php if ( is_home() ) { ?>
					<?php PC_Utility::hide_title_header_tag( get_the_ID(), "h2", "page-title entry-title" ); ?>
				<?php } else { ?>
					<?php PC_Utility::hide_title_header_tag( get_the_ID(), "h1", "page-title entry-title" ); ?>
				<?php } ?>
				
				<?php
					the_content();
					wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
				?>
				<?php edit_post_link( __( 'Edit', 'presscoders' ), '<span class="edit-link">', '</span>' ); ?>

		</div> <!-- post-item -->

		<?php comments_template( '', true ); ?>

		<?php endwhile; ?>
	
	<?php
	}

	/**
	 * Search page loop template part.
	 *
	 * Used in theme search.php template file.
	 *
	 * @since 0.1.0
	 */
	public static function search_page_loop( $s ) {
	?>

		<?php if ( have_posts() ) : ?>

		<h2 class="entry-title"><?php _e( 'Search results...', 'presscoders' ); ?></h2>
		
		<?php while ( have_posts() ) : the_post(); ?>

			<?php
				// Code to show search terms highlighted
				$keys= explode(" ",$s);
				$title = get_the_title();
				$content = PC_Utility::n_words( wp_strip_all_tags(get_the_content()), 300 );
				
				$title = preg_replace('/('.implode('|', $keys) .')/iu', '<span class="search-results">\0</span>', $title);
				$content = preg_replace('/('.implode('|', $keys) .')/iu', '<span class="search-results">\0</span>', $content);
			?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo $title; ?></a></h2>
				
					<?php echo $content; ?>

				<div class="post-meta">

					<?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>

					<p>
						<span class="date"><?php the_time('M j, Y'); ?></span>
						<span class="categories"><?php the_category(', ') ?></span>

						<?php if( comments_open() ) : ?>
						<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="<?php the_title_attribute(); ?>"><?php comments_number('Leave a Comment','1 Comment','% Comments'); ?></a></span>
						<?php endif; ?>
					</p>
				</div><!-- .post-meta -->

			</div> <!-- post-item -->

		<?php endwhile; // end of the loop. ?>

		<?php else : ?>
		
			<div id="post-0" class="post no-results not-found">

				<h2 class="entry-title"><?php _e( 'No search results found...', 'presscoders' ); ?></h2>
				<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords. Or, choose from the links below to navigate to another page.', 'presscoders' ); ?></p>

				<div style="margin:0 auto;width:300px;"><?php get_search_form(); ?></div>
				
				<div class="widget" style="width:260px;float:left;">
				<h3 class="widget-title"><?php _e( 'Pages', 'presscoders' ); ?></h3>
				<ul>
				<?php wp_list_pages('title_li='); ?>
				</ul>
				</div>
				
				<div class="widget" style="width:260px;float:right;">
				<h3 class="widget-title"><?php _e( 'Post Categories', 'presscoders' ); ?></h3>
				<ul>
				<?php //wp_list_cats();
					  wp_list_categories(); ?>
				</ul>
				</div>
			
			</div><!-- #post-0 -->

		<?php endif; ?>

	<?php
	}

}

?>