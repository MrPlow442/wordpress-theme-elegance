<?php
if (!class_exists('Elegance_Navigation')) {
    class Elegance_Navigation {
        public static function get_theme_nav_items() {
            $items = array();
                    
            $home_label = get_theme_mod('nav_home_label', __('Home', 'wordpress-theme-elegance'));
            $items[] = array(
                'id' => EleganceNavId::HOME->value,
                'label' => $home_label,
                'value' => '#home',
                'type' => EleganceNavType::ANCHOR->value
            );
                    
            if (Elegance_Queries::has_notices()) {
                $notices_label = get_theme_mod('nav_notices_label', __('Notices', 'wordpress-theme-elegance'));
                $items[] = array(
                    'id' => EleganceNavId::NOTICES->value,
                    'label' => $notices_label,
                    'value' => '#notices',
                    'type' => EleganceNavType::ANCHOR->value
                );
            }

            if (Elegance_Queries::has_testimonials()) {
                $testimonials_label = get_theme_mod('nav_testimonials_label', __('Testimonials', 'wordpress-theme-elegance'));
                $items[] = array(
                    'id' => EleganceNavId::TESTIMONIALS->value,
                    'label' => $testimonials_label,
                    'value' => '#testimonials',
                    'type' => EleganceNavType::ANCHOR->value
                );
            }
                    
            if (self::has_blog_page()) {
                $blog_label = get_theme_mod('nav_blog_label', __('Blog', 'wordpress-theme-elegance'));
                $blog_url = get_theme_mod('nav_blog_url', '/blog');
                $items[] = array(
                    'id' => EleganceNavId::BLOG->value,
                    'label' => $blog_label,
                    'value' => home_url($blog_url),
                    'type' => EleganceNavType::LINK->value
                );
            }
                        
            return $items;
        }

        public static function url_post_exists($url) {
            $post_id = url_to_postid(trim($url, '/'));
            return !is_null($post_id) && get_post_status($post_id) === 'publish';
        }

        public static function is_blog_page($url) {        
            $blog_url = get_theme_mod('nav_blog_url', '/blog');
            $blog_path = trim($blog_url, '/');                
            return strpos($url, $blog_path) !== false;
        }

        public static function has_blog_page() {
            $blog_url = home_url(get_theme_mod('nav_blog_url', '/blog'));        
            return self::url_post_exists($blog_url);
        }

        public static function is_page_menu_item($item) {
            return $item->type === 'post_type' && $item->object === 'page';
        }

        public static function is_custom_menu_item($item) {
            return $item->type === 'custom';
        }

        public static function is_theme_notices_menu_item($item) {
            return self::is_custom_menu_item($item) && $item->attr_title === 'elegance_nav_notices_nav';
        }

        public static function is_theme_testimonials_menu_item($item) {
            return self::is_custom_menu_item($item) && $item->attr_title === 'elegance_nav_testimonials_nav';
        }
    }
}