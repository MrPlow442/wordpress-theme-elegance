<?php
/**
 * Customizer settings and controls
 * 
 * @package Elegance
 */

 if (!function_exists('elegance_customizer_css')) {
    function elegance_customizer_css() {
        $global_text_color = get_theme_mod( 'global_text_color');
        $gradient_color_1 = get_theme_mod( 'gradient_color_1');
        $gradient_color_2 = get_theme_mod( 'gradient_color_2');

        $custom_css = "
            body, a, a:hover, a:focus {
                color: {$global_text_color};
            }

            #backgroundContainer:after {
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

            .blog-page {
                background: {$gradient_color_1}; /* Old browsers */
                background: -moz-linear-gradient(top, {$gradient_color_1} 0%, {$gradient_color_2} 25%); /* FF3.6-15 */
                background: -webkit-linear-gradient(top, {$gradient_color_1} 0%,{$gradient_color_2} 25%); /* Chrome10-25,Safari5.1-6 */
                background: linear-gradient(to bottom, {$gradient_color_1} 0%,{$gradient_color_2} 25%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$gradient_color_1}', endColorstr='{$gradient_color_2}',GradientType=0 ); /* IE6-9 */
                background-repeat: no-repeat;
                color: {$global_text_color};
            }
        ";

        wp_add_inline_style( 'elegance-style', $custom_css );
    }
    add_action( 'wp_enqueue_scripts', 'elegance_customizer_css' );
}


if (!function_exists('elegance_theme_customizer_settings')) {
    function elegance_theme_customizer_settings($wp_customize) {

        /*************************************
         *          HEADER MEDIA SETTINGS    *
        *************************************/
        // Section for Header Media
        $wp_customize->add_section('header_media', array(
            'title' => __('Header Media', 'wordpress-theme-elegance'),
            'priority' => 30,
        ));

        // Setting for Background Video
        $wp_customize->add_setting('header_background_video', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',  // Sanitize callback to ensure URL is safe
        ));

        // $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'header_background_video', array(
        //     'label' => __('Background Video', 'wordpress-theme-elegance'),
        //     'section' => 'header_media',
        //     'mime_type' => 'video',
        // )));

        $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize, 'header_background_video', array(
            'label' => __('Background Video', 'wordpress-theme-elegance'),
            'section' => 'header_media',
        )));

        // Setting for Background Image
        $wp_customize->add_setting('header_background_image', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',  // Sanitize callback to ensure URL is safe
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'header_background_image', array(
            'label' => __('Background Image', 'wordpress-theme-elegance'),
            'section' => 'header_media',
        )));


        /*************************************
         *          HOME SETTINGS            *
        *************************************/
        // Add a new section for the homepage settings
        $wp_customize->add_section( 'home_section' , array(
            'title'      => __( 'Home Text Settings', 'wordpress-theme-elegance' ),
            'priority'   => 30,
        ));

        // Add the setting for the homepage description above the title
        $wp_customize->add_setting( 'home_description_above' , array(
            'default'   => __( 'Hello, welcome to', 'wordpress-theme-elegance' ),
            'transport' => 'refresh',
        ));

        // Add the control for the homepage description above the title
        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'home_description_above_control', array(
            'label'      => __( 'Homepage Description Above Title', 'wordpress-theme-elegance' ),
            'section'    => 'home_section',
            'settings'   => 'home_description_above',
            'type'       => 'textarea',
        )));

        // Add the setting for the homepage description below the title
        $wp_customize->add_setting( 'home_description_below' , array(
            'default'   => __( 'This is a clean and modern HTML5 template with a video background. You can use this layout for your profile page. Please spread a word about templatemo to your friends. Thank you.', 'wordpress-theme-elegance' ),
            'transport' => 'refresh',
        ));

        // Add the control for the homepage description below the title
        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'home_description_below_control', array(
            'label'      => __( 'Homepage Description Below Title', 'wordpress-theme-elegance' ),
            'section'    => 'home_section',
            'settings'   => 'home_description_below',
            'type'       => 'textarea',
        )));

        /*************************************
         *          COLOR SETTINGS           *
        *************************************/
        // Add a new section for color settings
        $wp_customize->add_section( 'color_settings' , array(
            'title'      => __( 'Color Settings', 'wordpress-theme-elegance' ),
            'priority'   => 40,
        ));

        // Add setting for global text color
        $wp_customize->add_setting( 'global_text_color' , array(
            'default'   => '#ffffff',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        // Add control for global text color
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'global_text_color_control', array(
            'label'      => __( 'Global Text Color', 'wordpress-theme-elegance' ),
            'section'    => 'color_settings',
            'settings'   => 'global_text_color',
        )));

        // Add setting for gradient color 1
        $wp_customize->add_setting( 'gradient_color_1' , array(
            'default'   => '#4096ee',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        // Add control for gradient color 1
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gradient_color_1_control', array(
            'label'      => __( 'Gradient Color 1', 'wordpress-theme-elegance' ),
            'section'    => 'color_settings',
            'settings'   => 'gradient_color_1',
        )));

        // Add setting for gradient color 2
        $wp_customize->add_setting( 'gradient_color_2' , array(
            'default'   => '#39ced6',
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        // Add control for gradient color 2
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gradient_color_2_control', array(
            'label'      => __( 'Gradient Color 2', 'wordpress-theme-elegance' ),
            'section'    => 'color_settings',
            'settings'   => 'gradient_color_2',
        )));

        /*************************************
         *          SOCIALS SETTINGS         *
        *************************************/
        // Add a section for the social icons
        $wp_customize->add_section('social_icons_section', array(
            'title' => __('Social Icons', 'wordpress-theme-elegance'),
            'priority' => 30,
        ));

        // Add a setting to manage social icons (this will be a repeater)
        $wp_customize->add_setting('social_icons', array(
            'default' => json_encode(array()),
            'sanitize_callback' => 'sanitize_social_icons',
        ));

        // Add a control to manage social icons
        $wp_customize->add_control(new Social_Icons_Repeater_Control($wp_customize, 'social_icons', array(
            'label' => __('Social Icons', 'wordpress-theme-elegance'),
            'section' => 'social_icons_section',
            'settings' => 'social_icons',
            'priority' => 1,
        )));

        /*************************************
         *          BLOG SETTINGS            *
        *************************************/    
        // Blog Section
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

        $wp_customize->add_setting('blog_description', array(
            'default' => 'Latest articles and insights',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('blog_description', array(
            'label' => 'Blog Page Description',
            'section' => 'blog_settings',
            'type' => 'textarea',
        ));

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
    
    require_once get_theme_file_path('/template-parts/social-icons-repeater-control.php');
}