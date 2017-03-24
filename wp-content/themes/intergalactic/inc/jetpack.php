<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Intergalactic
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function intergalactic_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'page',
		'render'    => 'intergalatic_infinite_scroll_render'
	) );

	add_theme_support( 'jetpack-responsive-videos' );
}
add_action( 'after_setup_theme', 'intergalactic_jetpack_setup' );


if ( ! function_exists( 'intergalatic_infinite_scroll_render' ) ) {

	function intergalatic_infinite_scroll_render() {
		while( have_posts() ) {
		    the_post();
		    get_template_part( 'content', 'home' );
		}
	}

}