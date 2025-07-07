<?php
/*
Plugin Name: Testimonials Block
Description: A custom block for displaying testimonials.
Version: 1.0
Author: Matija Lovrekovic
*/

function register_testimonials_block() {
    // Register block editor script
    wp_register_script(
        'testimonials-block-editor-script',
        get_template_directory_uri() . '/blocks/testimonials-block/testimonials-block.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components')
    );    

    // Register frontend script
    wp_register_script(
        'testimonials-block-frontend-script',
        get_template_directory_uri() . '/blocks/testimonials-block/testimonials-frontend.js',
        array('jquery'),
        null,
        true
    );

    // Register editor style
    wp_register_style(
        'testimonials-block-editor-style',
        get_template_directory_uri() . '/blocks/testimonials-block/testimonials-block.css'
    );

    // Register frontend style
    wp_register_style(
        'testimonials-block-style',
        get_template_directory_uri() . '/blocks/testimonials-block/testimonials-block.css'
    );

    // Register the block
    register_block_type('wordpress-theme-elegance/testimonials-block', array(
        'editor_script' => 'testimonials-block-editor-script',
        'script' => 'testimonials-block-frontend-script',
        'editor_style' => 'testimonials-block-editor-style',
        'style' => 'testimonials-block-style',
    ));
}

add_action('init', 'register_testimonials_block');
