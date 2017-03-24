<?php

/**
 * Theme specific framework utility class.
 *
 * Contains theme specific general helper functions which are all static, so they can
 * be referenced without having to instantiate the class.
 *
 * @since 0.1.0
 */
class PC_TS_Utility {

	/**
	 * PC_TS_Utility class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
	}

	/**
	 * Renders demo content upon successful theme activation.
	 *
	 * @since 0.1.0
	 */
	public static function theme_demo_default_content() {

		/* Add default widgets. */
		self::add_default_demo_widgets();
	}

	/**
	 * Add default widgets upon successful theme activation.
	 *
	 * @since 0.1.0
	 */
	public static function add_default_demo_widgets() {

	}

    public static function blog_style_recent_posts_widget_loop( $args = array() ) {
	?>

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

					<?php if( comments_open() ) : ?>
					<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="<?php the_title_attribute(); ?>"><?php comments_number('Leave a Comment','1 Comment','% Comments'); ?></a></span>
					<?php endif; ?>
				</p>
            </div><!-- .post-meta -->

			<?php if( $args['spt'] ) { ?>

			<?php if ( has_post_thumbnail() ) : ?>
            <div class="post-thumb">
              <?php
			  $post_id = get_the_ID();
              echo PC_Utility::get_responsive_slider_image( $post_id, 'post-thumbnail' ); /* Show post thumbnail image, if one exists. */
              ?>
            </div> <!-- .post-thumb -->
			<?php endif; ?>

			<?php } ?>

                <?php
                    global $more;
                    $more = 0;
                    the_content( ' '.$args['read_more'] );
					wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
                ?>
            </div> <!-- .post-content -->

        </div> <!-- .post -->

    <?php
    }

	/**
	 * Content slider custom jQuery 'start' callback code.
	 *
	 * This can potentially change from theme to theme so the slider 'start' callback is placed here in the theme specific utility class.
	 *
	 * @since 0.1.0
	 */
	public static function content_slider_jquery_start_cb() {

		$start_cb = "jQuery(document).ready(function($) {\n\t\t\t\t\t\t\t";
		$start_cb .= "  jQuery(\"#before-content .flex-container\").attr('style', 'padding-top:10px;margin-top:50px;');\n\t\t\t\t\t\t\t";
		$start_cb .= "  jQuery(\"#before-content .flexslider\").css({ 'padding-top': '20px' });\n\t\t\t\t\t\t\t";
		$start_cb .= "  jQuery(\"#before-content\").css({ 'margin': '30px 0 20px 0' });\n\t\t\t\t\t\t\t";
		$start_cb .= "});\n";

		return $start_cb;
	}
}

?>