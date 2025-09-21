<?php
if (!class_exists('Elegance_Templates')) {
    class Elegance_Templates {
        public static function preloader() {
            get_template_part('template-parts/preloader');
        }

        public static function welcome() {
            get_template_part('template-parts/welcome');
        }

        public static function notices() {
            get_template_part('template-parts/notices');
        }

        public static function testimonials() {
            get_template_part('template-parts/testimonials');
        }

        public static function social_icons() {
            get_template_part('template-parts/social_icons');
        }

        public static function blog_post_list() {
            get_template_part('template-parts/blog_post_list');
        }

        public static function blog_post() {
            get_template_part('template-parts/blog_post');
        }

        public static function blog_comments() {
            comments_template('/template-parts/blog_comments.php');
        }

        public static function page($args = array()) {
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

        public static function render_sections_from_menu_items($menu_items) {
            foreach ($menu_items as $item) {                                                
                if (!Elegance_Navigation::is_theme_notices_menu_item($item)
                 && !Elegance_Navigation::is_theme_testimonials_menu_item($item) 
                 && !Elegance_Navigation::is_page_menu_item($item)) {                    
                    continue;
                }                                

                if (Elegance_Navigation::is_theme_notices_menu_item($item)) {                    
                    self::notices();                    
                    continue;
                }

                if (Elegance_Navigation::is_theme_testimonials_menu_item($item)) {                    
                    self::testimonials();
                    continue;
                }

                $menu_item_page = get_post($item->object_id);
                self::page([
                    'slug' => sanitize_title($menu_item_page->post_title),
                    'title' => $menu_item_page->post_title,
                    'description' => get_post_meta($menu_item_page->ID, 'description', true),
                    'content' => apply_filters('the_content', $menu_item_page->post_content),
                    'hide_bg' => get_post_meta($menu_item_page->ID, 'hide_background', true),
                    'do_not_animate' => get_post_meta($menu_item_page->ID, 'do_not_animate', true)
                ]);           
            }
        }
    }
}