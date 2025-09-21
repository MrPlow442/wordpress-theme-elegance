<?php
/**
 * Inject CSS variables for Customizer-controlled colors.
 *
 * Loaded on both front-end and Customizer preview.
 *
 * @package Elegance
 */

if ( ! function_exists( 'elegance_output_root_variables' ) ) {    
    function elegance_output_root_variables() {                
        $vars = array(
            '--gradient-color-1' => sanitize_hex_color(
                get_theme_mod( 'gradient_color_1', '#4096ee' )
            ),
            '--gradient-color-2' => sanitize_hex_color(
                get_theme_mod( 'gradient_color_2', '#39ced6' )
            ),
            '--global-text-color'       => sanitize_hex_color(
                get_theme_mod( 'global_text_color', '#ffffff' )
            ),
        );
        
        $root_css = ':root {';
        foreach ( $vars as $name => $value ) {
            if ( ! empty( $value ) ) {                
                $root_css .= sprintf( '%s: %s;', $name, $value);
            }
        }
        $root_css .= '}';
        
        $handle = 'elegance-style';        
        if ( ! wp_style_is( $handle, 'registered' ) ) {
            wp_register_style( $handle, get_stylesheet_uri(), array(), '1.0.0' );
        }
        
        if ( ! wp_style_is( $handle, 'enqueued' ) ) {
            wp_enqueue_style( $handle );
        }
        
        wp_add_inline_style( $handle, $root_css );
    }
}
add_action( 'wp_enqueue_scripts', 'elegance_output_root_variables', 20 );
