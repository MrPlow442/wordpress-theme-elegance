<?php
/**
 * Custom Post Types
 * 
 * @package Elegance
 */

 if (!function_exists('elegance_register_notices_post_type')) {
    function elegance_register_notices_post_type() {
        $labels = array(
            'name'                  => 'Notices',
            'singular_name'         => 'Notice',
            'menu_name'             => 'Notices',
            'name_admin_bar'        => 'Notice',
            'archives'              => 'Notice Archives',
            'attributes'            => 'Notice Attributes',
            'parent_item_colon'     => 'Parent Notice:',
            'all_items'             => 'All Notices',
            'add_new_item'          => 'Add New Notice',
            'add_new'               => 'Add New',
            'new_item'              => 'New Notice',
            'edit_item'             => 'Edit Notice',
            'update_item'           => 'Update Notice',
            'view_item'             => 'View Notice',
            'view_items'            => 'View Notices',
            'search_items'          => 'Search Notices',
        );

        $args = array(
            'label'                 => 'Notice',
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