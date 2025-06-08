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
    $defaults['title_reply'] = __('Leave a Comment', 'wordpress-theme-elegance');
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

/**
 * Customize comment list arguments
 */
function elegance_comment_list_args($args) {
    $args['avatar_size'] = 60;
    $args['style'] = 'div';
    $args['short_ping'] = true;
    $args['reply_text'] = '<i class="fas fa-reply" aria-hidden="true"></i> ' . __('Reply', 'wordpress-theme-elegance');
    
    return $args;
}
add_filter('wp_list_comments_args', 'elegance_comment_list_args');

/**
 * Custom walker for comment pagination
 */
class Elegance_Comment_Walker extends Walker_Comment {
    
    /**
     * Outputs a comment in the HTML5 format.
     */
    protected function html5_comment($comment, $depth, $args) {
        $tag = ('div' === $args['style']) ? 'div' : 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                <footer class="comment-meta">
                    <div class="comment-author vcard">
                        <?php
                        if (0 != $args['avatar_size']) {
                            echo get_avatar($comment, $args['avatar_size']);
                        }
                        ?>
                        <?php
                        $comment_author = get_comment_author_link($comment);
                        if ('0' == $comment->comment_approved) {
                            echo '<em class="comment-awaiting-moderation">' . __('Your comment is awaiting moderation.', 'wordpress-theme-elegance') . '</em><br />';
                        }
                        printf('<b class="fn">%s</b> <span class="says">%s</span>', $comment_author, __('says:', 'wordpress-theme-elegance'));
                        ?>
                    </div>

                    <div class="comment-metadata">
                        <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                            <time datetime="<?php comment_time('c'); ?>">
                                <?php
                                printf(__('%1$s at %2$s', 'wordpress-theme-elegance'), get_comment_date('', $comment), get_comment_time());
                                ?>
                            </time>
                        </a>
                        <?php edit_comment_link(__('Edit', 'wordpress-theme-elegance'), '<span class="edit-link">', '</span>'); ?>
                    </div>
                </footer>

                <div class="comment-content">
                    <?php comment_text(); ?>
                </div>

                <?php
                $reply_args = array_merge($args, array(
                    'add_below' => 'div-comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '<div class="reply">',
                    'after'     => '</div>',
                ));
                comment_reply_link($reply_args, $comment, get_the_ID());
                ?>
            </article>
        <?php
    }
}
