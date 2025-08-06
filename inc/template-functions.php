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

if (!function_exists('elegance_notices')) {
    function elegance_notices() {
        get_template_part('template-parts/notices');
    }
}

if (!function_exists('elegance_testimonials')) {
    function elegance_testimonials() {
        get_template_part('template-parts/testimonials');
    }
}

if (!function_exists('elegance_page')) {
    function elegance_page($args = array()) {
        $args = wp_parse_args($args, [
            'slug' => '',
            'title' => '',
            'description' => '',
            'content' => '',
            'hide_bg' => 'no',
            'do_not_animate' => 'no'
        ]);
        
        get_template_part('template-parts/page', null, $args);
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

if (!function_exists('elegance_testimonials_query')) {
    function elegance_testimonials_query() {            
        return new WP_Query(array(
            'post_type'      => 'testimonial',            
            'posts_per_page' => -1,            
            'orderby'        => 'menu_order date',
            'order'          => 'DESC',
            'post_status'    => 'publish'
        ));
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

if (!function_exists('elegance_has_testimonials')) {
    function elegance_has_testimonials() {
        $has_posts = elegance_testimonials_query()->have_posts();
        error_log('Has testimonials: ' . ($has_posts ? 'true' : 'false'));
        return elegance_testimonials_query()->have_posts();
    }
}

if (!function_exists('elegance_url_post_exists')) {
    function elegance_url_post_exists($url) {
        $post_id = url_to_postid(trim($url, '/'));
        return !is_null($post_id) && get_post_status($post_id) === 'publish';
    }
}

if (!function_exists('elegance_is_blog_page')) {
    function elegance_is_blog_page($url) {        
        $blog_url = get_theme_mod('nav_blog_url', '/blog');
        $blog_path = trim($blog_url, '/');                
        return strpos($url, $blog_path) !== false;
    }
}

if (!function_exists('elegance_has_blog_page')) {
    function elegance_has_blog_page() {
        $blog_url = home_url(get_theme_mod('nav_blog_url', '/blog'));        
        return elegance_url_post_exists($blog_url);
    }
}

if (!function_exists('elegance_is_page_menu_item')) {
    function elegance_is_page_menu_item($item) {
        return $item->type === 'post_type' && $item->object === 'page';
    }
}

if (!function_exists('elegance_is_custom_menu_item')) {
    function elegance_is_custom_menu_item($item) {
        return $item->type === 'custom';
    }
}

if (!function_exists('elegance_is_theme_notices_menu_item')) {
    function elegance_is_theme_notices_menu_item($item) {
        return elegance_is_custom_menu_item($item) && $item->attr_title === 'elegance_nav_notices_nav';
    }
}

if (!function_exists('elegance_is_theme_testimonials_menu_item')) {
    function elegance_is_theme_testimonials_menu_item($item) {
        return elegance_is_custom_menu_item($item) && $item->attr_title === 'elegance_nav_testimonials_nav';
    }
}

if (!function_exists('elegance_get_theme_nav_items')) {
    function elegance_get_theme_nav_items() {
        $items = array();
                
        $home_label = get_theme_mod('nav_home_label', __('Home', 'wordpress-theme-elegance'));
        $items[] = array(
            'id' => EleganceNavId::HOME->value,
            'label' => $home_label,
            'value' => '#home',
            'type' => EleganceNavType::ANCHOR->value
        );
                
        if (elegance_has_notices()) {
            $notices_label = get_theme_mod('nav_notices_label', __('Notices', 'wordpress-theme-elegance'));
            $items[] = array(
                'id' => EleganceNavId::NOTICES->value,
                'label' => $notices_label,
                'value' => '#notices',
                'type' => EleganceNavType::ANCHOR->value
            );
        }

        if (elegance_has_testimonials()) {
            $testimonials_label = get_theme_mod('nav_testimonials_label', __('Testimonials', 'wordpress-theme-elegance'));
            $items[] = array(
                'id' => EleganceNavId::TESTIMONIALS->value,
                'label' => $testimonials_label,
                'value' => '#testimonials',
                'type' => EleganceNavType::ANCHOR->value
            );
        }
                
        if (elegance_has_blog_page()) {
            $blog_label = get_theme_mod('nav_blog_label', __('Blog', 'wordpress-theme-elegance'));
            $blog_url = get_theme_mod('nav_blog_url', '/blog');
            $items[] = array(
                'id' => EleganceNavId::BLOG->value,
                'label' => $blog_label,
                'value' => home_url($blog_url),
                'type' => EleganceNavType::LINK->value
            );
        }
        
        error_log('Theme Nav Items: ' . print_r($items, true));
        return $items;
    }
}