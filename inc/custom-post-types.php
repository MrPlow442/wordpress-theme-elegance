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

    function elegance_register_testimonials_post_type() {
        $labels = array(
            'name'                  => __('Testimonials', 'Post Type General Name', 'wordpress-theme-elegance'),
            'singular_name'         => __('Testimonial', 'Post Type Singular Name', 'wordpress-theme-elegance'),
            'menu_name'             => __('Testimonials', 'wordpress-theme-elegance'),
            'name_admin_bar'        => __('Testimonial', 'wordpress-theme-elegance'),
            'archives'              => __('Testimonial Archives', 'wordpress-theme-elegance'),
            'attributes'            => __('Testimonial Attributes', 'wordpress-theme-elegance'),
            'parent_item_colon'     => __('Parent Testimonial:', 'wordpress-theme-elegance'),
            'all_items'             => __('All Testimonials', 'wordpress-theme-elegance'),
            'add_new_item'          => __('Add New Testimonial', 'wordpress-theme-elegance'),
            'add_new'               => __('Add New', 'wordpress-theme-elegance'),
            'new_item'              => __('New Testimonial', 'wordpress-theme-elegance'),
            'edit_item'             => __('Edit Testimonial', 'wordpress-theme-elegance'),
            'update_item'           => __('Update Testimonial', 'wordpress-theme-elegance'),
            'view_item'             => __('View Testimonial', 'wordpress-theme-elegance'),
            'view_items'            => __('View Testimonials', 'wordpress-theme-elegance'),
            'search_items'          => __('Search Testimonial', 'wordpress-theme-elegance'),
            'not_found'             => __('Not found', 'wordpress-theme-elegance'),
            'not_found_in_trash'    => __('Not found in Trash', 'wordpress-theme-elegance'),
            'featured_image'        => __('Client Photo', 'wordpress-theme-elegance'),
            'set_featured_image'    => __('Set client photo', 'wordpress-theme-elegance'),
            'remove_featured_image' => __('Remove client photo', 'wordpress-theme-elegance'),
            'use_featured_image'    => __('Use as client photo', 'wordpress-theme-elegance'),
            'insert_into_item'      => __('Insert into testimonial', 'wordpress-theme-elegance'),
            'uploaded_to_this_item' => __('Uploaded to this testimonial', 'wordpress-theme-elegance'),
            'items_list'            => __('Testimonials list', 'wordpress-theme-elegance'),
            'items_list_navigation' => __('Testimonials list navigation', 'wordpress-theme-elegance'),
            'filter_items_list'     => __('Filter testimonials list', 'wordpress-theme-elegance'),
        );

        $args = array(
            'label'                 => __('Testimonial', 'wordpress-theme-elegance'),
            'description'           => __('Customer testimonials and reviews', 'wordpress-theme-elegance'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'page-attributes'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-format-quote',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
        );

        register_post_type('testimonial', $args);
    }
    add_action('init', 'elegance_register_testimonials_post_type', 0);
}