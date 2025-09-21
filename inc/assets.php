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
    wp_enqueue_style('elegance-main-style',
                    Elegance_Asset_Loader::get_asset_uri('css', 'style.css'),
                    array(),
                    Elegance_Asset_Loader::get_assets_version());

    // Scripts                    
    wp_enqueue_script('elegance-main-script',
                    Elegance_Asset_Loader::get_asset_uri('js', 'main.js'),
                    array('jquery'),
                    Elegance_Asset_Loader::get_assets_version());
            
    wp_add_inline_script('elegance-main-script', 'const EleganceConfig = ' . json_encode(Elegance_Helpers::get_front_end_config()) . ';', 'before');
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
        wp_enqueue_script('elegance-customizer-export-import',
                    Elegance_Asset_Loader::get_asset_uri('js', 'customizer-export-import.js'),
                    array('jquery', 'customize-controls'),
                    Elegance_Asset_Loader::get_assets_version(),
                    true);
        
        wp_enqueue_style('elegance-admin',
                    Elegance_Asset_Loader::get_asset_uri('css', 'admin-style.css'),
                    array(),
                    Elegance_Asset_Loader::get_assets_version());    
        
        wp_localize_script('elegance-customizer-export-import', 'customizerExportImport', array(
            'customizerURL' => admin_url('customize.php'),
            'currentTheme' => get_stylesheet(),
            'exportNonce' => wp_create_nonce('customizer_export_nonce'),
        ));

    }
}
add_action('customize_controls_enqueue_scripts', 'elegance_enqueue_customizer_export_import');

function elegance_enqueue_testimonial_meta_script() {
    $screen = get_current_screen();
    if ( $screen && $screen->post_type === 'testimonial' ) {
        wp_enqueue_script('testimonial-meta-js',
            Elegance_Asset_Loader::get_asset_uri('js', 'testimonial-meta.js'),
            array( 'wp-edit-post' ),
            Elegance_Asset_Loader::get_assets_version(),
            true);
    }
}
add_action( 'enqueue_block_editor_assets', 'elegance_enqueue_testimonial_meta_script' );

function elegance_admin_menu_styles($hook) {    
    if ($hook === 'nav-menus.php') {
        wp_enqueue_style('elegance-admin',
                    Elegance_Asset_Loader::get_asset_uri('css', 'admin-style.css'),
                    array(),
                    Elegance_Asset_Loader::get_assets_version());           
    }
}
add_action('admin_enqueue_scripts', 'elegance_admin_menu_styles');