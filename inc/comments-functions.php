<?php
/**
 * Comment functionality for Elegance theme
 *
 * @package WordPress-Theme-Elegance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customize comment form defaults
 */
function elegance_comment_form_defaults($defaults) {
    $defaults['title_reply'] = __('Leave a Comment');
    $defaults['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title">';
    $defaults['title_reply_after'] = '</h3>';
    
    return $defaults;
}
add_filter('comment_form_defaults', 'elegance_comment_form_defaults');

/**
 * Add custom comment meta for verified users
 */
function elegance_add_comment_meta_verified($comment_id) {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (user_can($user, 'edit_posts')) {
            add_comment_meta($comment_id, 'comment_author_verified', true);
        }
    }
}
add_action('comment_post', 'elegance_add_comment_meta_verified');