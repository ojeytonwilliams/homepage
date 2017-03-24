<?php
function ig_child_enqueue_scripts() {
    wp_enqueue_script( 'bootstrap', '/vendor/twbs/bootstrap/dist/js/bootstrap.min.js', array ( 'jquery' ), '3.3.7', true);
}
function ig_child_enqueue_styles() {

    $parent_style = 'intergalactic-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style('bootstrap', '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css
',false,'3.3.7','all');
}
add_action('wp_enqueue_scripts', 'ig_child_enqueue_styles' );
add_action('wp_enqueue_scripts', 'ig_child_enqueue_scripts' );
add_action('wp_head', 'show_template');
function show_template() {
	global $template;
	print_r($template);
}
?>
