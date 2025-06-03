<?php
/**
 * Asset loading and enqueuing
 * 
 * @package Elegance
 */


function elegance_theme_scripts() {
    wp_enqueue_style('elegance-google-fonts', 'https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900', array(), null);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');
    wp_enqueue_style('fullpage', get_template_directory_uri() . '/css/fullpage.min.css');
    wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css');
    wp_enqueue_style('animate', get_template_directory_uri() . '/css/animate.css');
    wp_enqueue_style('templatemo-style', get_template_directory_uri() . '/css/templatemo-style.css');
    wp_enqueue_style('blog-style', get_template_directory_uri() . '/css/blog-style.css');
    wp_enqueue_style('wpforms-overrides', get_template_directory_uri() . '/css/wpforms-overrides.css');
    wp_enqueue_style('responsive', get_template_directory_uri() . '/css/responsive.css');
    wp_enqueue_style('elegance-style', get_stylesheet_uri() );

    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('fullpage', get_template_directory_uri() . '/js/fullpage.extensions.min.js', array('jquery'), '', true);
    wp_enqueue_script('scrolloverflow', get_template_directory_uri() . '/js/scrolloverflow.js', array('jquery'), '', true);
    wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-inview', get_template_directory_uri() . '/js/jquery.inview.min.js', array('jquery'), '', true);
    wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '', true);
    wp_enqueue_script('functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'elegance_theme_scripts');


function elegance_enqueue_block_editor_assets() {
    wp_enqueue_style(
        'bootstrap-css',
        get_template_directory_uri() . '/css/bootstrap.min.css',
        array(),
        '4.1.3' 
    );
}
add_action('enqueue_block_editor_assets', 'elegance_enqueue_block_editor_assets');