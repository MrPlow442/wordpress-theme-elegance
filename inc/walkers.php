<?php
/**
 * Custom Walker Classes
 * 
 * @package Elegance
 */

 if (!class_exists('Single_Page_Walker')) {
    class Single_Page_Walker extends Walker_Nav_Menu {
        function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {                                
            $item_id = $this->is_custom_or_theme_nav($item->type) ? sanitize_title($item->attr_title) : sanitize_title($item->title);
        
            $href = $this->is_custom_link($item) ? esc_url($item->url) : '#' . esc_attr($item_id);

            $output .= sprintf(
                '<li data-menuanchor="%s"><a href="%s">%s</a>',
                esc_attr($item_id),
                $href,
                esc_html($item->title)
            );
        }

        function is_custom_or_theme_nav($item_type) {
            return $item_type === 'custom' || $item_type === 'elegance_nav';
        }

        function is_custom_link($item) {            
            return $this->is_custom_or_theme_nav($item->type) && !empty($item->url);
        }
    }
 }