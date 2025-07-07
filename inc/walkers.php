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

            if ($elegance_nav_type === 'link') {                                
                $post_id = url_to_postid(trim($item->url, '/'));                
                if (is_null($post_id)) {
                    return;
                }        
            }

            if ($item->type === 'custom') {                
                $href = esc_url($item->url);
                $data_attr = '';
            } else {                
                $anchor_name = sanitize_title($item->title);
                $href = '#' . $anchor_name;
                $data_attr = sprintf('data-menuanchor="%s"', esc_attr($anchor_name));
            }
            
            $output .= sprintf(
                '<li %s><a href="%s">%s</a>',
                $data_attr,
                $href,
                esc_html($item->title)
            );
        }
    }
}