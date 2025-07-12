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

if (!function_exists("elegance_get_comment_form_args")) {
    function elegance_get_comment_form_args() {
        $commenter = wp_get_current_commenter();
        $req = get_option('require_name_email');
        $aria_req = ($req ? " aria-required='true'" : '');
        $user = wp_get_current_user();
        if (is_user_logged_in()) {
            $commenter['comment_author'] = $user->display_name;
            $commenter['comment_author_email'] = $user->user_email;
        }

        return array(
            'title_reply_before' => '<div class="card border-0 shadow-sm mt-4 mt-md-5"><div class="card-body p-3 p-md-4"><h4 class="mb-3 mb-md-4">',
            'title_reply_after'  => '</h4>',
            'comment_notes_before' => '<div class="d-flex flex-column"><p class="text-muted mb-3"><small>' . __('Your email address will not be published.') . '</small></p><p class="text-muted mb-3"><small>' . sprintf(__('Required fields are marked %s'), '*') . '</small></p></div>',
            'comment_notes_after'  => '',
            'comment_field'     => '<div class="form-group mb-3"><label for="comment" class="form-label">' . _x( 'Comment', 'noun' ) . ' *</label><textarea class="form-control" name="comment" id="comment" rows="4" required placeholder="' . esc_attr_x( 'Comment', 'noun' ) . '"></textarea></div>',
            'logged_in_as' => sprintf(
                '<p class="logged-in-as mb-3"><small>' . __('Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a>') . '</small></p>',
                esc_html($commenter['comment_author']),
                get_edit_user_link(),
                wp_logout_url(get_permalink())
            ),
            'fields'            => array(
                'author' => '<div class="row"><div class="col-12 col-md-6"><div class="form-group mb-3"><label for="author" class="form-label">' . __('Name') . ' *</label><input type="text" class="form-control" name="author" id="author" value="' . esc_attr($commenter['comment_author']) . '" required' . $aria_req . ' placeholder="' . esc_attr__('Name') . '"></div></div>',
                'email'  => '<div class="col-12 col-md-6"><div class="form-group mb-3"><label for="email" class="form-label">' . __('Email') . ' *</label><input type="email" class="form-control" name="email" id="email" value="' . esc_attr($commenter['comment_author_email']) . '" required' . $aria_req . ' placeholder="' . esc_attr__('Email') . '"></div></div></div>',
                'cookies' => '<div class="form-check mb-3"><input type="checkbox" class="form-check-input" id="comment-cookies-consent" name="comment-cookies-consent" value="yes"><label class="form-check-label" for="comment-cookies-consent">' . __('Save my name, email, and website in this browser for the next time I comment.') . '</label></div>',                    
            ),                
            'submit_button'     => '<div class="mt-3"><button type="submit" name="submit" class="btn btn-dark btn-block"><i class="fa fa-paper-plane mr-2"></i>%4$s</button></div></div></div>',
        );
    }
}