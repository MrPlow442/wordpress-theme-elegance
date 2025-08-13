<?php
/**
 * Custom Walker Classes
 * 
 * @package Elegance
 */

 if (!class_exists('Single_Page_Walker')) {
    class Single_Page_Walker extends Walker_Nav_Menu {
        
        function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {                              
            $elegance_nav_type = get_post_meta($item->ID, ELEGANCE_NAV_META_KEY, true);            

            switch ($elegance_nav_type) {
                case EleganceNavType::ANCHOR->value:                    
                    if ($item->url === '#notices' && !Elegance_Queries::has_notices()) {      
                        // If no notices, don't render the nav item              
                        return;
                    } else if ($item->url === '#testimonials' && !Elegance_Queries::has_testimonials()) {
                        // If no testimonials, don't render the nav item
                        return;
                    }                   
                    break;
                case EleganceNavType::LINK->value:
                    if (Elegance_Navigation::is_blog_page($item->url) && !Elegance_Navigation::has_blog_page()) {                         
                        // If no blog page, don't render the nav item
                        return;
                    }
                    break;
                default:
                    break;
            }            

            if ($item->type === 'custom') {
                if ($elegance_nav_type && $elegance_nav_type === EleganceNavType::ANCHOR->value) {
                    $scroll_attributes = self::create_scroll_attributes(ltrim($item->url, '#'));
                }
                $href = esc_url($item->url);                
            } else {                
                $anchor_name = sanitize_title($item->title);
                $scroll_attributes = self::create_scroll_attributes($anchor_name);
                $href = '#' . $anchor_name;                
            }
            
            $output .= sprintf(
                '<li><a href="%s" %s>%s</a>',                
                $href,
                $scroll_attributes,
                esc_html($item->title)
            );
        }

        function create_scroll_attributes($name) {
            return 'data-scroll-to="' . $name . '" data-scroll-to-container="main"';
        }
    }
}