<?php
/**
 * Customizer settings and controls
 * 
 * @package Elegance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once get_theme_file_path('/inc/customizer-controls.php');

if (!function_exists('elegance_customizer_css')) {
    function elegance_customizer_css() {
        $global_text_color = get_theme_mod( 'global_text_color');
        $gradient_color_1 = get_theme_mod( 'gradient_color_1');
        $gradient_color_2 = get_theme_mod( 'gradient_color_2');

        $custom_css = "
            body, a, a:hover, a:focus {
                color: {$global_text_color};
            }

            #background-container:after {
                /* Rest is in templatemo-style.css */
                background: {$gradient_color_1}; /* Old browsers */
                background: -moz-linear-gradient(top, {$gradient_color_1} 0%, {$gradient_color_2} 100%); /* FF3.6-15 */
                background: -webkit-linear-gradient(top, {$gradient_color_1} 0%,{$gradient_color_2} 100%); /* Chrome10-25,Safari5.1-6 */
                background: linear-gradient(to bottom, {$gradient_color_1} 0%,{$gradient_color_2} 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$gradient_color_1}', endColorstr='{$gradient_color_2}',GradientType=0 ); /* IE6-9 */
            }

            .preloader {
                /* Rest is in templatemo-style.css */
                background: {$gradient_color_1}; /* Old browsers */
                background: -moz-linear-gradient(top, {$gradient_color_1} 0%, {$gradient_color_2} 100%); /* FF3.6-15 */
                background: -webkit-linear-gradient(top, {$gradient_color_1} 0%,{$gradient_color_2} 100%); /* Chrome10-25,Safari5.1-6 */
                background: linear-gradient(to bottom, {$gradient_color_1} 0%,{$gradient_color_2} 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$gradient_color_1}', endColorstr='{$gradient_color_2}',GradientType=0 ); /* IE6-9 */
            }

            ::-webkit-input-placeholder {
                /* Rest is in templatemo-style.css */
                color:{$global_text_color} !important;
            }
            ::-moz-placeholder {
                /* Rest is in templatemo-style.css */
                color:{$global_text_color} !important;
            }
            :-ms-input-placeholder {
                /* Rest is in templatemo-style.css */
                color:{$global_text_color} !important;
            }
            :-moz-placeholder {
                /* Rest is in templatemo-style.css */
                color:{$global_text_color} !important;
            }

            .wpforms-field input[type=\"text\"],
            .wpforms-field input[type=\"email\"],
            .wpforms-field textarea {
                /* Rest is in wpforms-overrides.css */
                color:{$global_text_color} !important;
            }

            #blog-background-container:after {
                background: {$gradient_color_1}; /* Old browsers */
                background: -moz-linear-gradient(top, {$gradient_color_1} 0%, {$gradient_color_2} 25%); /* FF3.6-15 */
                background: -webkit-linear-gradient(top, {$gradient_color_1} 0%,{$gradient_color_2} 25%); /* Chrome10-25,Safari5.1-6 */
                background: linear-gradient(to bottom, {$gradient_color_1} 0%,{$gradient_color_2} 25%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$gradient_color_1}', endColorstr='{$gradient_color_2}',GradientType=0 ); /* IE6-9 */
                background-repeat: no-repeat;                
            }
        ";

        wp_add_inline_style( 'elegance-style', $custom_css );
    }
    add_action( 'wp_enqueue_scripts', 'elegance_customizer_css' );
}


if (!function_exists('elegance_theme_customizer_settings')) {
    function elegance_theme_customizer_settings($wp_customize) {

        /****************************************
         *          MAIN PAGE MEDIA SETTINGS    *
        *****************************************/        
        $wp_customize->add_section('main_page_media', array(
            'title' => __('Main Page Media', 'wordpress-theme-elegance'),
            'priority' => 30,
        ));
        
        $wp_customize->add_setting('main_page_background_video', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',  // Sanitize callback to ensure URL is safe
        ));        

        $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize, 'main_page_background_video', array(
            'label' => __('Background Video', 'wordpress-theme-elegance'),
            'section' => 'main_page_media',
        )));

        $wp_customize->add_setting('main_page_background_image', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',  // Sanitize callback to ensure URL is safe
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'main_page_background_image', array(
            'label' => __('Background Image', 'wordpress-theme-elegance'),
            'section' => 'main_page_media',
        )));


        /*************************************
         *          HOME SETTINGS            *
        *************************************/        
        $wp_customize->add_section( 'home_section' , array(
            'title'      => __( 'Home Text Settings', 'wordpress-theme-elegance' ),
            'priority'   => 30,
        ));
        
        $wp_customize->add_setting( 'home_description_above' , array(
            'default'   => __( 'Hello, welcome to', 'wordpress-theme-elegance' ),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'home_description_above_control', array(
            'label'      => __( 'Homepage Description Above Title', 'wordpress-theme-elegance' ),
            'section'    => 'home_section',
            'settings'   => 'home_description_above',
            'type'       => 'textarea',
        )));

        $wp_customize->add_setting( 'home_description_below' , array(
            'default'   => __( 'This is a clean and modern HTML5 template with a video background. You can use this layout for your profile page. Please spread a word about templatemo to your friends. Thank you.', 'wordpress-theme-elegance' ),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'home_description_below_control', array(
            'label'      => __( 'Homepage Description Below Title', 'wordpress-theme-elegance' ),
            'section'    => 'home_section',
            'settings'   => 'home_description_below',
            'type'       => 'textarea',
        )));
        
        /*******************************************
         *           NAVIGATION SETTINGS           *
         ******************************************/
        $wp_customize->add_section('theme_navigation_settings', array(
            'title' => __('Navigation Settings', 'wordpress-theme-elegance'),
            'priority' => 25,
        ));

        // Home Navigation Settings
        $wp_customize->add_setting('nav_home_label', array(
            'default' => __('Home', 'wordpress-theme-elegance'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('nav_home_label', array(
            'label' => __('Home Menu Label', 'wordpress-theme-elegance'),
            'section' => 'theme_navigation_settings',
            'type' => 'text',
        ));

        // Notices Navigation Settings
        $wp_customize->add_setting('nav_notices_label', array(
            'default' => __('Notices', 'wordpress-theme-elegance'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('nav_notices_label', array(
            'label' => __('Notices Menu Label', 'wordpress-theme-elegance'),
            'section' => 'theme_navigation_settings',
            'type' => 'text',
        ));

        // Blog Navigation Settings
        $wp_customize->add_setting('nav_blog_label', array(
            'default' => __('Blog', 'wordpress-theme-elegance'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('nav_blog_label', array(
            'label' => __('Blog Menu Label', 'wordpress-theme-elegance'),
            'section' => 'theme_navigation_settings',
            'type' => 'text',
        ));

        $wp_customize->add_setting('nav_blog_url', array(
            'default' => '/blog',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('nav_blog_url', array(
            'label' => __('Blog URL Path', 'wordpress-theme-elegance'),
            'section' => 'theme_navigation_settings',
            'type' => 'text',
            'description' => __('Relative URL path to blog page (e.g., /blog or /arvakhr/blog)', 'wordpress-theme-elegance'),
        ));

        /*************************************
         *          COLOR SETTINGS           *
        *************************************/
        $wp_customize->add_section( 'color_settings' , array(
            'title'      => __( 'Color Settings', 'wordpress-theme-elegance' ),
            'priority'   => 40,
        ));

        $wp_customize->add_setting( 'global_text_color' , array(
            'default'   => '#ffffff',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'global_text_color_control', array(
            'label'      => __( 'Global Text Color', 'wordpress-theme-elegance' ),
            'section'    => 'color_settings',
            'settings'   => 'global_text_color',
        )));

        $wp_customize->add_setting( 'gradient_color_1' , array(
            'default'   => '#4096ee',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gradient_color_1_control', array(
            'label'      => __( 'Gradient Color 1', 'wordpress-theme-elegance' ),
            'section'    => 'color_settings',
            'settings'   => 'gradient_color_1',
        )));

        $wp_customize->add_setting( 'gradient_color_2' , array(
            'default'   => '#39ced6',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gradient_color_2_control', array(
            'label'      => __( 'Gradient Color 2', 'wordpress-theme-elegance' ),
            'section'    => 'color_settings',
            'settings'   => 'gradient_color_2',
        )));

        /*************************************
         *          SOCIALS SETTINGS         *
        *************************************/
        $wp_customize->add_section('social_icons_section', array(
            'title' => __('Social Icons', 'wordpress-theme-elegance'),
            'priority' => 30,
        ));
        
        $wp_customize->add_setting('social_icons', array(
            'default' => json_encode(array()),
            'sanitize_callback' => 'sanitize_social_icons',
        ));
        
        $wp_customize->add_control(new Social_Icons_Repeater_Control($wp_customize, 'social_icons', array(
            'label' => __('Social Icons', 'wordpress-theme-elegance'),
            'section' => 'social_icons_section',
            'settings' => 'social_icons',
            'priority' => 1,
        )));

        /*************************************
         *          BLOG SETTINGS            *
        *************************************/     
        $wp_customize->add_section('blog_settings', array(
            'title' => 'Blog Settings',
            'priority' => 30,
        ));

        $wp_customize->add_setting('blog_title', array(
            'default' => 'Blog',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('blog_title', array(
            'label' => 'Blog Page Title',
            'section' => 'blog_settings',
            'type' => 'text',
        ));        

        $wp_customize->add_setting('blog_background_image', array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'blog_background_image', array(
            'label' => __('Blog Background Image', 'wordpress-theme-elegance'),
            'section' => 'blog_settings',
            'settings' => 'blog_background_image',
        )));

        /*************************************************
         *          ADDITIONAL GOOGLE FONTS              *
        **************************************************/    
        $wp_customize->add_section('google_fonts_section', array(
            'title'    => __('Google Fonts', 'wordpress-theme-elegance'),
            'priority' => 160,
        ));

        $wp_customize->add_setting('google_fonts_url', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control('google_fonts_url', array(
            'label'       => __('Google Fonts URL', 'wordpress-theme-elegance'),
            'description' => __('Enter the URL from Google Fonts (e.g., https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap)', 'wordpress-theme-elegance'),
            'section'     => 'google_fonts_section',
            'type'        => 'url',
        ));

        /**************************************************
         *          CUSTOMIZER IMPORT/EXPORT              *
        ***************************************************/        
        $wp_customize->add_section('customizer_export_import', array(
            'title'    => __('Export/Import Settings', 'wordpress-theme-elegance'),
            'priority' => 999,
        ));
        
        $wp_customize->add_setting('elegance_export_dummy', array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control(new Elegance_Export_Control($wp_customize, 'elegance_export_dummy', array(
            'label'   => __('Export Customizer Sections', 'wordpress-theme-elegance'),
            'section' => 'customizer_export_import',
        )));
        
        $wp_customize->add_setting('elegance_import_dummy', array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control(new Elegance_Import_Control($wp_customize, 'elegance_import_dummy', array(
            'label'   => __('Import Customizer Sections', 'wordpress-theme-elegance'),
            'section' => 'customizer_export_import',
        )));
        
        elegance_export_selected_sections($wp_customize);
    }
    add_action('customize_register', 'elegance_theme_customizer_settings');
}

if (!function_exists('elegance_sanitize_social_icons')) {
    function elegance_sanitize_social_icons($input) {
        $input_decoded = json_decode($input, true);
        if (!empty($input_decoded)) {
            foreach ($input_decoded as $key => $value) {
                $input_decoded[$key]['url'] = esc_url_raw($value['url']);
                $input_decoded[$key]['icon'] = sanitize_text_field($value['icon']);
            }
            return json_encode($input_decoded);
        }
        return json_encode(array());
    }
}