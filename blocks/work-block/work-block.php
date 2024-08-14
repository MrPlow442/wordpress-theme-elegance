<?php
/*
Plugin Name: Work Block
Description: A custom block for displaying work.
Version: 1.0
Author: Matija Lovrekovic
*/

function work_block_register() {
    // Register the block editor script
    wp_register_script(
        'work-block-editor-script',
        plugins_url('build/index.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components')
    );

    // Register the frontend script
    wp_register_script(
        'work-block-frontend-script',
        plugins_url('build/frontend.js', __FILE__),
        array('jquery'),
        null,
        true
    );

    // Register the editor style
    wp_register_style(
        'work-block-editor-style',
        plugins_url('src/style.css', __FILE__)
    );

    // Register the frontend style
    wp_register_style(
        'work-block-style',
        plugins_url('build/style.css', __FILE__)
    );

    // Register the block type
    register_block_type('work/work-block', array(
        'editor_script' => 'work-block-editor-script',
        'script' => 'work-block-frontend-script',
        'editor_style' => 'work-block-editor-style',
        'style' => 'work-block-style',
    ));
}

add_action('init', 'work_block_register');
