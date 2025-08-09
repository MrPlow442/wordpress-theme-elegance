<?php
/**
 * Template helper functions
 * 
 * @package Elegance
 */


if (!function_exists('elegance_get_blog_title')) {
    function elegance_get_blog_title() {
        $blog_title = get_theme_mod('blog_title');        
        if (!empty($blog_title)) {            
            return esc_html($blog_title);
        }        
        return get_bloginfo('name');
    }
}