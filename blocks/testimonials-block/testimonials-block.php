<?php
/*
Plugin Name: Testimonials Block
Description: A custom block for displaying testimonials.
Version: 1.0
Author: Your Name
*/

function testimonials_block_register() {
    // Register the block editor script
    wp_register_script(
        'testimonials-block-editor-script',
        plugins_url('build/index.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components')
    );

    // Register the frontend script
    wp_register_script(
        'testimonials-block-frontend-script',
        plugins_url('build/frontend.js', __FILE__),
        array('jquery'),
        null,
        true
    );

    // Register the editor style
    wp_register_style(
        'testimonials-block-editor-style',
        plugins_url('src/style.css', __FILE__)
    );

    // Register the frontend style
    wp_register_style(
        'testimonials-block-style',
        plugins_url('build/style.css', __FILE__)
    );

    // Register the block type
    register_block_type('testimonials/testimonials-block', array(
        'editor_script' => 'testimonials-block-editor-script',
        'script' => 'testimonials-block-frontend-script',
        'editor_style' => 'testimonials-block-editor-style',
        'style' => 'testimonials-block-style',
    ));
}

add_action('init', 'testimonials_block_register');
