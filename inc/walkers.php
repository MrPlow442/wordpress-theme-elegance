<?php
/**
 * Custom Walker Classes
 * 
 * @package Elegance
 */

 if (!class_exists('Single_Page_Walker')) {
    class Single_Page_Walker extends Walker_Nav_Menu {
        function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {        
            $is_custom_link = ($item->type === 'custom' && !empty($item->url));
            
            $item_id = $item->type == 'custom' ? sanitize_title($item->attr_title) : sanitize_title($item->title);
            
            $href = $is_custom_link ? esc_url($item->url) : '#' . esc_attr($item_id);

            $output .= sprintf(
                '<li data-menuanchor="%s"><a href="%s">%s</a>',
                esc_attr($item_id),
                $href,
                esc_html($item->title)
            );
        }
    }
 }