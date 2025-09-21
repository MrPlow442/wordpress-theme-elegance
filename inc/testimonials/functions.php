<?php
/**
 * Testimonials Functions
 * 
 * @package Elegance
 * @version 2.0.0
 */

function elegance_testimonials_help_text() {
    $screen = get_current_screen();
    
    if ($screen->post_type === 'testimonial') {
        echo '<div class="notice notice-info" style="margin-top: 20px;">';
        echo '<h3>' . __('How to create testimonials:', 'wordpress-theme-elegance') . '</h3>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li><strong>' . __('Title:', 'wordpress-theme-elegance') . '</strong> ' . __('Enter the client\'s name (e.g., "John Smith")', 'wordpress-theme-elegance') . '</li>';
        echo '<li><strong>' . __('Content:', 'wordpress-theme-elegance') . '</strong> ' . __('Write the testimonial quote. You can use any blocks including paragraphs, quotes, links, etc.', 'wordpress-theme-elegance') . '</li>';
        echo '<li><strong>' . __('Featured Image:', 'wordpress-theme-elegance') . '</strong> ' . __('Upload the client\'s photo (optional)', 'wordpress-theme-elegance') . '</li>';
        echo '<li><strong>' . __('Order:', 'wordpress-theme-elegance') . '</strong> ' . __('Use the Order field in Page Attributes to control the display sequence', 'wordpress-theme-elegance') . '</li>';
        echo '</ul>';
        echo '<p><em>' . __('Note: You can add links, formatting, and any other content blocks in the testimonial quote.', 'wordpress-theme-elegance') . '</em></p>';
        echo '</div>';
    }
}
add_action('edit_form_after_title', 'elegance_testimonials_help_text');

function elegance_testimonials_title_placeholder($title, $post) {
    if ($post->post_type === 'testimonial') {
        return __('Enter the client\'s name (e.g., "John Smith")', 'wordpress-theme-elegance');
    }
    return $title;
}
add_filter('enter_title_here', 'elegance_testimonials_title_placeholder', 10, 2);

function elegance_testimonials_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Client Name', 'wordpress-theme-elegance');
    $new_columns['testimonial_content'] = __('Testimonial Preview', 'wordpress-theme-elegance');
    $new_columns['featured_image'] = __('Photo', 'wordpress-theme-elegance');
    $new_columns['menu_order'] = __('Order', 'wordpress-theme-elegance');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_testimonial_posts_columns', 'elegance_testimonials_admin_columns');

function elegance_testimonials_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'testimonial_content':
            $content = get_post_field('post_content', $post_id);
            $preview = wp_strip_all_tags($content);
            $preview = wp_trim_words($preview, 15, '...');
            echo '<em>' . esc_html($preview) . '</em>';
            break;
            
        case 'featured_image':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50), array('style' => 'border-radius: 50%;'));
            } else {
                echo '<span style="color: #666;">—</span>';
            }
            break;
            
        case 'menu_order':
            $post = get_post($post_id);
            echo '<strong>' . $post->menu_order . '</strong>';
            break;
    }
}
add_action('manage_testimonial_posts_custom_column', 'elegance_testimonials_admin_column_content', 10, 2);

function elegance_testimonials_sortable_columns($columns) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}
add_filter('manage_edit-testimonial_sortable_columns', 'elegance_testimonials_sortable_columns');

function elegance_testimonials_editor_settings($settings, $editor_id) {
    global $post;
    
    if (isset($post) && $post->post_type === 'testimonial') {        
        $settings['quicktags'] = array(
            'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close'
        );
                
        $settings['textarea_name'] = 'content';
        $settings['textarea_rows'] = 8;
    }
    
    return $settings;
}
add_filter('wp_editor_settings', 'elegance_testimonials_editor_settings', 10, 2);

function elegance_register_testimonial_meta() {
    register_post_meta( 'testimonial', 'client_url', array(
        'type'              => 'string',
        'description'       => 'URL of the client’s website',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback'     => function() {
            return current_user_can( 'edit_posts' );
        },
    ) );
    register_post_meta( 'testimonial', 'link_text', array(
        'type'              => 'string',
        'description'       => 'Text for the client link',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() {
            return current_user_can( 'edit_posts' );
        },
    ) );
}
add_action( 'init', 'elegance_register_testimonial_meta' );

function elegance_get_testimonial_website_link($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $website_url = get_post_meta($post_id, 'client_url', true);
    $website_text = get_post_meta($post_id, 'link_text', true);
    
    if (empty($website_url)) {
        return '';
    }
        
    if (empty($website_text)) {
        $website_text = $website_url;
    }
    
    return sprintf(
        '
        <div class="client-website">
            <i class="fa-solid fa-link"></i> <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>
        </div>
        ',
        esc_url($website_url),
        esc_html($website_text)
    );
}
?>