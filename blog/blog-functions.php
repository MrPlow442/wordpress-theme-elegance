<?php

function create_custom_post_types() {
    // Register Notice Post Type
    $notice_labels = array(
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

    register_post_type('notice',
        array(
            'labels' => $notice_labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'supports' => array('title', 'editor', 'thumbnail'),            
            'menu_position' => 5,
            'menu_icon' => 'dashicons-megaphone',
        )
    );

    // Register Blog Post Type
    $blog_labels = array(
        'name'                  => 'Blogs',
        'singular_name'         => 'Blog',
        'menu_name'             => 'Blogs',
        'name_admin_bar'        => 'Blog',
        'archives'              => 'Blog Archives',
        'attributes'            => 'Blog Attributes',
        'parent_item_colon'     => 'Parent Blog:',
        'all_items'             => 'All Blogs',
        'add_new_item'          => 'Add New Blog',
        'add_new'               => 'Add New',
        'new_item'              => 'New Blog',
        'edit_item'             => 'Edit Blog',
        'update_item'           => 'Update Blog',
        'view_item'             => 'View Blog',
        'view_items'            => 'View Blogs',
        'search_items'          => 'Search Blogs',
    );

    register_post_type('blog',
        array(
            'labels' => $blog_labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'comments'),            
            'menu_position' => 6,
            'menu_icon' => 'dashicons-edit',
        )
    );
}
add_action('init', 'create_custom_post_types');