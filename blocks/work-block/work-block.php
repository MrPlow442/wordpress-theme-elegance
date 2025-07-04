<?php
/*
Plugin Name: Work Block
Description: A custom block for displaying work.
Version: 1.0
Author: Matija Lovrekovic
*/

function register_work_block() {
    // Register block editor script
    wp_register_script(
        'work-block-editor-script',
        get_template_directory_uri() . '/blocks/work-block/work-block.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components')
    );

    wp_enqueue_script(
        'work-item-block',
        get_template_directory_uri() . '/blocks/work-block/work-item-block.js',
        array('wp-blocks', 'wp-editor', 'wp-element')
    );

    // Register frontend script
    wp_register_script(
        'work-block-frontend-script',
        get_template_directory_uri() . '/blocks/work-block/work-frontend.js',
        array('jquery'),
        null,
        true
    );

    // Register editor style
    wp_register_style(
        'work-block-editor-style',
        get_template_directory_uri() . '/blocks/work-block/work-block.css'
    );

    // Register frontend style
    wp_register_style(
        'work-block-style',
        get_template_directory_uri() . '/blocks/work-block/work-block.css'
    );

    // Register the block
    register_block_type('wordpress-theme-elegance/work-block', array(
        'editor_script' => 'work-block-editor-script',
        'script' => 'work-block-frontend-script',
        'editor_style' => 'work-block-editor-style',
        'style' => 'work-block-style',
    ));
}

add_action('init', 'register_work_block');
