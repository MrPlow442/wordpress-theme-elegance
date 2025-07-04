<?php
/**
 * Template helper functions
 * 
 * @package Elegance
 */

 if (!function_exists('elegance_preloader')) {
    function elegance_preloader() {
        get_template_part('template-parts/preloader');
    }
}

if (!function_exists('elegance_get_blog_title')) {
    function elegance_get_blog_title() {
        $blog_title = get_theme_mod('blog_title');
        error_log("Blog title: " . print_r($blog_title, true));
        if (!empty($blog_title)) {
            error_log("Blog title is not empty");
            return esc_html($blog_title);
        }
        error_log("Blog title is empty, using default " . get_bloginfo('name'));
        return get_bloginfo('name');
    }
}