<?php
function elegance_theme_scripts() {
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');
    wp_enqueue_style('fullpage', get_template_directory_uri() . '/css/fullpage.min.css');
    wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css');
    wp_enqueue_style('animate', get_template_directory_uri() . '/css/animate.css');
    wp_enqueue_style('templatemo-style', get_template_directory_uri() . '/css/templatemo-style.css');
    wp_enqueue_style('responsive', get_template_directory_uri() . '/css/responsive.css');
    wp_enqueue_style('elegance-style', get_stylesheet_uri() );

    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('fullpage', get_template_directory_uri() . '/js/fullpage.extensions.min.js', array('jquery'), '', true);
    wp_enqueue_script('scrolloverflow', get_template_directory_uri() . '/js/scrolloverflow.js', array('jquery'), '', true);
    wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-inview', get_template_directory_uri() . '/js/jquery.inview.min.js', array('jquery'), '', true);
    wp_enqueue_script('form', get_template_directory_uri() . '/js/form.js', array('jquery'), '', true);
    wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '', true);
    wp_enqueue_script('functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'elegance_theme_scripts');

function elegance_customizer_css() {
    $global_text_color = get_theme_mod( 'global_text_color');
    $gradient_color_1 = get_theme_mod( 'gradient_color_1');
    $gradient_color_2 = get_theme_mod( 'gradient_color_2');

    $custom_css = "
        body, a, a:hover, a:focus {
            color: {$global_text_color};
        }

        #backgroundContainer:after {
            content: '';
            opacity: 0.75;
            position:fixed;
            left:0;
            top:0;
            right:0;
            bottom: 0;
            background: {$gradient_color_1}; /* Old browsers */
            background: -moz-linear-gradient(top, {$gradient_color_1} 0%, {$gradient_color_2} 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(top, {$gradient_color_1} 0%,{$gradient_color_2} 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to bottom, {$gradient_color_1} 0%,{$gradient_color_2} 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$gradient_color_1}', endColorstr='{$gradient_color_2}',GradientType=0 ); /* IE6-9 */
        }
    ";

    wp_add_inline_style( 'elegance-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'elegance_customizer_css' );


function elegance_theme_setup() {
    register_nav_menus(array(
        'top' => __('Primary Menu', 'elegance-theme'),
    ));

    /*
	 * Enable support for Post Formats.
	 *
	 * See: https://developer.wordpress.org/advanced-administration/wordpress/post-formats/
	 */
	add_theme_support(
		'post-formats',
		array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
			'gallery',
			'audio',
		)
	);

    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'elegance_theme_setup');


// Register Customizer settings
function theme_customizer_settings($wp_customize) {

    /*************************************
     *          HEADER MEDIA SETTINGS    *
    *************************************/
    // Section for Header Media
    $wp_customize->add_section('header_media', array(
        'title' => __('Header Media', 'elegance-theme'),
        'priority' => 30,
    ));

    // Setting for Background Video
    $wp_customize->add_setting('header_background_video', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',  // Sanitize callback to ensure URL is safe
    ));

    // $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'header_background_video', array(
    //     'label' => __('Background Video', 'elegance-theme'),
    //     'section' => 'header_media',
    //     'mime_type' => 'video',
    // )));

    $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize, 'header_background_video', array(
        'label' => __('Background Video', 'elegance-theme'),
        'section' => 'header_media',
    )));

    // Setting for Background Image
    $wp_customize->add_setting('header_background_image', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',  // Sanitize callback to ensure URL is safe
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'header_background_image', array(
        'label' => __('Background Image', 'elegance-theme'),
        'section' => 'header_media',
    )));


    /*************************************
     *          HOME SETTINGS            *
    *************************************/
    // Add a new section for the homepage settings
    $wp_customize->add_section( 'home_section' , array(
        'title'      => __( 'Home Text Settings', 'elegance-theme' ),
        'priority'   => 30,
    ) );

    // Add the setting for the homepage description above the title
    $wp_customize->add_setting( 'home_description_above' , array(
        'default'   => __( 'Hello, welcome to', 'elegance-theme' ),
        'transport' => 'refresh',
    ) );

    // Add the control for the homepage description above the title
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'home_description_above_control', array(
        'label'      => __( 'Homepage Description Above Title', 'elegance-theme' ),
        'section'    => 'home_section',
        'settings'   => 'home_description_above',
        'type'       => 'textarea',
    ) ) );

    // Add the setting for the homepage description below the title
    $wp_customize->add_setting( 'home_description_below' , array(
        'default'   => __( 'This is a clean and modern HTML5 template with a video background. You can use this layout for your profile page. Please spread a word about templatemo to your friends. Thank you.', 'elegance-theme' ),
        'transport' => 'refresh',
    ) );

    // Add the control for the homepage description below the title
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'home_description_below_control', array(
        'label'      => __( 'Homepage Description Below Title', 'elegance-theme' ),
        'section'    => 'home_section',
        'settings'   => 'home_description_below',
        'type'       => 'textarea',
    ) ) );

    /*************************************
     *          COLOR SETTINGS           *
    *************************************/
     // Add a new section for color settings
     $wp_customize->add_section( 'color_settings' , array(
        'title'      => __( 'Color Settings', 'elegance-theme' ),
        'priority'   => 40,
    ) );

    // Add setting for global text color
    $wp_customize->add_setting( 'global_text_color' , array(
        'default'   => '#ffffff',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    // Add control for global text color
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'global_text_color_control', array(
        'label'      => __( 'Global Text Color', 'elegance-theme' ),
        'section'    => 'color_settings',
        'settings'   => 'global_text_color',
    ) ) );

    // Add setting for gradient color 1
    $wp_customize->add_setting( 'gradient_color_1' , array(
        'default'   => '#4096ee',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    // Add control for gradient color 1
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gradient_color_1_control', array(
        'label'      => __( 'Gradient Color 1', 'elegance-theme' ),
        'section'    => 'color_settings',
        'settings'   => 'gradient_color_1',
    ) ) );

    // Add setting for gradient color 2
    $wp_customize->add_setting( 'gradient_color_2' , array(
        'default'   => '#39ced6',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    // Add control for gradient color 2
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gradient_color_2_control', array(
        'label'      => __( 'Gradient Color 2', 'elegance-theme' ),
        'section'    => 'color_settings',
        'settings'   => 'gradient_color_2',
    ) ) );
}
add_action('customize_register', 'theme_customizer_settings');

function register_testimonials_block() {
    // Register block editor script
    wp_register_script(
        'testimonials-block-editor-script',
        get_template_directory_uri() . '/blocks/testimonials-block/index.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components')
    );

    wp_enqueue_script(
        'testimonial-item-block',
        get_template_directory_uri() . '/blocks/testimonials-block/testimonial-item-block.js',
        array('wp-blocks', 'wp-editor', 'wp-element')
    );

    // Register frontend script
    wp_register_script(
        'testimonials-block-frontend-script',
        get_template_directory_uri() . '/blocks/testimonials-block/frontend.js',
        array('jquery'),
        null,
        true
    );

    // Register editor style
    wp_register_style(
        'testimonials-block-editor-style',
        get_template_directory_uri() . '/blocks/testimonials-block/style.css'
    );

    // Register frontend style
    wp_register_style(
        'testimonials-block-style',
        get_template_directory_uri() . '/blocks/testimonials-block/style.css'
    );

    // Register the block
    register_block_type('elegance-theme/testimonials-block', array(
        'editor_script' => 'testimonials-block-editor-script',
        'script' => 'testimonials-block-frontend-script',
        'editor_style' => 'testimonials-block-editor-style',
        'style' => 'testimonials-block-style',
    ));
}

add_action('init', 'register_testimonials_block');



class Single_Page_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {        
        $item_id = $item->type == 'custom' ? sanitize_title($item->attr_title) : sanitize_title($item->title);
        $output .= sprintf( '<li data-menuanchor="%s"><a href="#%s">%s</a>', $item_id, $item_id, $item->title );
    }
}