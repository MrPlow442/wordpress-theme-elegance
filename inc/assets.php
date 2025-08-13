<?php
/**
 * Asset loading and enqueuing
 * 
 * @package Elegance
 */

if (!defined('ABSPATH')) {
    exit;
}

function elegance_meta_tags() {
    ?>    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php
}
add_action('wp_head', 'elegance_meta_tags', 1);


function elegance_theme_scripts() {
    // Styles
    wp_enqueue_style('elegance-google-fonts', 'https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900', array(), null);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');    
    wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css');
    wp_enqueue_style('animate', get_template_directory_uri() . '/css/animate.css');
    wp_enqueue_style('templatemo-style', get_template_directory_uri() . '/css/templatemo-style.css');
    wp_enqueue_style('blog-style', get_template_directory_uri() . '/css/blog-style.css');
    wp_enqueue_style('wpforms-overrides', get_template_directory_uri() . '/css/wpforms-overrides.css');
    wp_enqueue_style('responsive', get_template_directory_uri() . '/css/responsive.css');
    wp_enqueue_style('elegance-style', get_stylesheet_uri() );
    
    // Scripts
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '', true);    
    wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-inview', get_template_directory_uri() . '/js/jquery.inview.min.js', array('jquery'), '', true);
    // wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '', true);
    // wp_enqueue_script('functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), '', true);

    $theme_modules = [
        'elegance-logger' => '/js/modules/logger.js',
        'elegance-module' => '/js/modules/module.js',
        'elegance-constants' => '/js/modules/constants.js',
        'elegance-theme-core' => '/js/modules/theme-core.js',
        'elegance-scroll-navigator' => '/js/modules/scroll-navigator.js',        
        'elegance-background-manager' => '/js/modules/background-manager.js',
        'elegance-navigation-manager' => '/js/modules/navigation-manager.js',
        'elegance-animation-manager' => '/js/modules/animation-manager.js',
        'elegance-horizontal-scroll-manager' => '/js/modules/horizontal-scroll-manager.js'        
    ];

    foreach ($theme_modules as $handle => $file) {
        wp_enqueue_script($handle, get_template_directory_uri() . $file, [], wp_get_theme()->get('Version'), true);
    }
    wp_enqueue_script('theme-init', get_template_directory_uri() . '/js/theme-init.js', array_keys($theme_modules), wp_get_theme()->get('Version'), true);
        
    wp_localize_script('theme-init', 'EleganceConfig', Elegance_Helpers::get_front_end_config());

}
add_action('wp_enqueue_scripts', 'elegance_theme_scripts');


function elegance_enqueue_block_editor_assets() {
    wp_enqueue_style(
        'bootstrap-css',
        get_template_directory_uri() . '/css/bootstrap.min.css',
        array(),
        '4.1.3' 
    );
}
add_action('enqueue_block_editor_assets', 'elegance_enqueue_block_editor_assets');

function elegance_output_google_fonts() {
    $google_fonts_url = get_theme_mod('google_fonts_url');
    
    if (!empty($google_fonts_url)) {        
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";        
        echo '<link href="' . esc_url($google_fonts_url) . '" rel="stylesheet">' . "\n";
    }
}
add_action('wp_head', 'elegance_output_google_fonts');

function elegance_enqueue_customizer_export_import() {
    if (is_customize_preview() || is_admin()) {
        wp_enqueue_script(
            'elegance-customizer-export-import',
            get_template_directory_uri() . '/js/customizer-export-import.js',
            array('jquery', 'customize-controls'),
            wp_get_theme()->get('Version'),
            true
        );
        
        wp_enqueue_style(
            'elegance-customizer-export-import',
            get_template_directory_uri() . '/css/customizer-export-import.css',
            array(),
            wp_get_theme()->get('Version')
        );
        
        wp_localize_script('elegance-customizer-export-import', 'customizerExportImport', array(
            'customizerURL' => admin_url('customize.php'),
            'currentTheme' => get_stylesheet(),
            'exportNonce' => wp_create_nonce('customizer_export_nonce'),
        ));

    }
}
add_action('customize_controls_enqueue_scripts', 'elegance_enqueue_customizer_export_import');
