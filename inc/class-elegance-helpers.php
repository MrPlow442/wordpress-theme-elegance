<?php
if (!class_exists('Elegance_Helpers')) {
    class Elegance_Helpers {
        public static function is_dev() {            
            return (
                (defined('WP_DEBUG') && WP_DEBUG) &&
                (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)
            );
        }        

        public static function get_front_end_config() {
            
            $page_info = array();
            $menu_items = wp_get_nav_menu_items('top');
            
            if ($menu_items) {
                foreach ($menu_items as $menu_item) {
                    $section_data = null;
                                    
                    if (Elegance_Navigation::is_page_menu_item($menu_item)) {
                        $section = get_post($menu_item->object_id);
                        if ($section) {
                            $section_data = array(
                                'name' => sanitize_title($section->post_title),
                                'title' => $section->post_title,
                                'hasThumbnail' => has_post_thumbnail($section->ID),
                                'thumbnail' => has_post_thumbnail($section->ID) ? get_the_post_thumbnail_url($section->ID, 'full') : null
                            );
                        }
                    } elseif (Elegance_Navigation::is_theme_notices_menu_item($menu_item)) {
                        $section_data = array(
                            'name' => 'notices',
                            'title' => $menu_item->title,
                            'hasThumbnail' => false,
                            'thumbnail' => null
                        );
                    } elseif (Elegance_Navigation::is_theme_testimonials_menu_item($menu_item)) {
                        $section_data = array(
                            'name' => 'testimonials', 
                            'title' => $menu_item->title,
                            'hasThumbnail' => false,
                            'thumbnail' => null
                        );
                    }
                    
                    if ($section_data) {
                        $page_info[] = $section_data;
                    }
                }
            }
                        
            array_unshift($page_info, array(
                'name' => 'home',
                'title' => 'Home',
                'hasThumbnail' => false,
                'thumbnail' => null
            ));
                        
            return array(
                'debug' => true,
                'videoElementId' => 'background-video',
                'imageElementId' => 'background-image',            
                'defaultVideoUrl' => get_theme_mod('main_page_background_video'),
                'defaultImageUrl' => get_theme_mod('main_page_background_image'),
                'pageInfo' => $page_info
            );
        }
    }
}