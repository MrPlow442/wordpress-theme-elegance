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
        if (!empty($blog_title)) {            
            return esc_html($blog_title);
        }        
        return get_bloginfo('name');
    }
}

if (!function_exists('elegance_notices_query')) {
    function elegance_notices_query() {
        return new WP_Query(array(
            'post_type' => 'notice',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish'
        ));        
    }
}

if (!function_exists('elegance_has_notices')) {
    function elegance_has_notices() {        
        return elegance_notices_query()->have_posts();
    }
}

if (!function_exists('elegance_has_blog_page')) {
    function elegance_has_blog_page() {
        $blog_url = get_theme_mod('nav_blog_url', '/blog');        
        $blog_path = trim($blog_url, '/');        
        $page = get_page_by_path($blog_path);        
        return !is_null($page);
    }
}


if (!function_exists('elegance_get_theme_nav_items')) {
    function elegance_get_theme_nav_items() {
        $items = array();
        
        // Home item (always available)
        $home_label = get_theme_mod('nav_home_label', __('Home', 'wordpress-theme-elegance'));
        $items[] = array(
            'id' => _elegance_create_theme_nav_item_id('home_nav'),
            'label' => $home_label,
            'value' => '#home',
            'type' => 'anchor'
        );
        
        // Notices item (conditional)
        if (elegance_has_notices()) {
            $notices_label = get_theme_mod('nav_notices_label', __('Notices', 'wordpress-theme-elegance'));
            $items[] = array(
                'id' => _elegance_create_theme_nav_item_id('notices_nav'),
                'label' => $notices_label,
                'value' => '#notices',
                'type' => 'anchor'
            );
        }
        
        // Blog item (conditional)
        if (elegance_has_blog_page()) {
            $blog_label = get_theme_mod('nav_blog_label', __('Blog', 'wordpress-theme-elegance'));
            $blog_url = get_theme_mod('nav_blog_url', '/blog');
            $items[] = array(
                'id' => _elegance_create_theme_nav_item_id('blog_nav'),
                'label' => $blog_label,
                'value' => home_url($blog_url),
                'type' => 'link'
            );
        }
        
        return $items;
    }
}

if (!function_exists('_elegance_create_theme_nav_item_id')) {
    function _elegance_create_theme_nav_item_id($name) {
        return 'elegance_nav_' . sanitize_title($name);
    }
}