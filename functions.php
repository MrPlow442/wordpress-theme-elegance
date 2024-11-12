<?php
function elegance_theme_scripts() {
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.css');
    wp_enqueue_style('fullpage', get_template_directory_uri() . '/css/fullpage.min.css');
    wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css');
    wp_enqueue_style('animate', get_template_directory_uri() . '/css/animate.css');
    wp_enqueue_style('templatemo-style', get_template_directory_uri() . '/css/templatemo-style.css');
    wp_enqueue_style('wpforms-overrides', get_template_directory_uri() . '/css/wpforms-overrides.css');
    wp_enqueue_style('responsive', get_template_directory_uri() . '/css/responsive.css');
    wp_enqueue_style('elegance-style', get_stylesheet_uri() );

    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '', true);
    wp_enqueue_script('fullpage', get_template_directory_uri() . '/js/fullpage.extensions.min.js', array('jquery'), '', true);
    wp_enqueue_script('scrolloverflow', get_template_directory_uri() . '/js/scrolloverflow.js', array('jquery'), '', true);
    wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-inview', get_template_directory_uri() . '/js/jquery.inview.min.js', array('jquery'), '', true);
    wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '', true);
    wp_enqueue_script('functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), '', true);
}
add_action('wp_enqueue_scripts', 'elegance_theme_scripts');

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

function enqueue_block_editor_assets() {
    wp_enqueue_style(
        'bootstrap-css',
        get_template_directory_uri() . '/css/bootstrap.min.css',
        array(),
        '4.1.3' 
    );
}
add_action('enqueue_block_editor_assets', 'enqueue_block_editor_assets');

require_once get_parent_theme_file_path('/customizer.php');

require_once get_parent_theme_file_path('/blocks/testimonials-block/testimonials-block.php');

require_once get_parent_theme_file_path('/blocks/work-block/work-block.php');


class Single_Page_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {        
        $is_custom_link = ($item->type === 'custom' && !empty($item->url));
        
        $item_id = $item->type == 'custom' ? sanitize_title($item->attr_title) : sanitize_title($item->title);
        
        $href = $is_custom_link ? esc_url($item->url) : '#' . esc_attr($item_id);

        $output .= sprintf(
            '<li data-menuanchor="%s"><a href="%s">%s</a>',
            esc_attr($item_id),
            $href,
            esc_html($item->title)
        );
    }
}
