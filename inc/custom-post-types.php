<?php
/**
 * Custom Post Types
 * 
 * @package Elegance
 */

 if (!function_exists('elegance_register_notices_post_type')) {
    function elegance_register_notices_post_type() {
        $labels = array(
            'name'                  => __('Notices', 'wordpress-theme-elegance'),
            'singular_name'         => __('Notice', 'wordpress-theme-elegance'),
            'menu_name'             => __('Notices', 'wordpress-theme-elegance'),
            'name_admin_bar'        => __('Notice', 'wordpress-theme-elegance'),
            'archives'              => __('Notice Archives', 'wordpress-theme-elegance'),
            'attributes'            => __('Notice Attributes', 'wordpress-theme-elegance'),
            'parent_item_colon'     => __('Parent Notice:', 'wordpress-theme-elegance'),
            'all_items'             => __('All Notices', 'wordpress-theme-elegance'),
            'add_new_item'          => __('Add New Notice', 'wordpress-theme-elegance'),
            'add_new'               => __('Add New', 'wordpress-theme-elegance'),
            'new_item'              => __('New Notice', 'wordpress-theme-elegance'),
            'edit_item'             => __('Edit Notice', 'wordpress-theme-elegance'),
            'update_item'           => __('Update Notice', 'wordpress-theme-elegance'),
            'view_item'             => __('View Notice', 'wordpress-theme-elegance'),
            'view_items'            => __('View Notices', 'wordpress-theme-elegance'),
            'search_items'          => __('Search Notices', 'wordpress-theme-elegance'),
        );

        $args = array(
            'label'                 => __('Notice', 'wordpress-theme-elegance'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-megaphone',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
        );

        register_post_type('notice', $args);
    }
    add_action('init', 'elegance_register_notices_post_type', 0);
}