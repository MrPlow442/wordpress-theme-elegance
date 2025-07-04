<?php
/**
 * Theme setup functions
 * 
 * @package Elegance
 */

if (!function_exists('elegance_theme_setup')) {
    function elegance_theme_setup() {
        register_nav_menus(array(
            'top' => __('Primary Menu', 'wordpress-theme-elegance'),
        ));

        /*
        * Enable support for Post Formats.
        *
        * See: https://developer.wordpress.org/advanced-administration/wordpress/post-formats/
        */
        add_theme_support(
            'post-formats',
            array(
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'audio',
            )
        );

        add_theme_support('post-thumbnails');

        add_theme_support('html5', array('comment-list', 'comment-form', 'search-form'));
    }
    add_action('after_setup_theme', 'elegance_theme_setup');
}

if (!function_exists('elegance_load_textdomain')) {
    function elegance_load_textdomain() {
        load_theme_textdomain( 'wordpress-theme-elegance', get_template_directory() . '/languages' );
    }
    add_action( 'after_setup_theme', 'elegance_load_textdomain' );
}