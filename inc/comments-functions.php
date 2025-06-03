<?php
/**
 * Comments Support Functions
 * Add these functions to your inc/template-functions.php file
 * 
 * @package Elegance
 */

/**
 * Enqueue comments reply script when needed
 */
function elegance_enqueue_comment_reply_script() {
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'elegance_enqueue_comment_reply_script');

/**
 * Enqueue comments styles
 */
function elegance_enqueue_comments_styles() {
    if (is_singular() && (have_comments() || comments_open())) {
        wp_enqueue_style(
            'elegance-comments-style',
            get_template_directory_uri() . '/css/comments-styles.css',
            array('elegance-main-style'),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'elegance_enqueue_comments_styles');

/**
 * Modify comment form default fields to match theme styling
 */
function elegance_comment_form_defaults($defaults) {
    $defaults['class_form'] = 'comment-form';
    $defaults['class_submit'] = 'btn';
    $defaults['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title">';
    $defaults['title_reply_after'] = '</h3>';
    
    return $defaults;
}
add_filter('comment_form_defaults', 'elegance_comment_form_defaults');

/**
 * Add custom comment meta for verified users (optional feature)
 */
function elegance_add_comment_meta_verified($comment_id) {
    // Example: Mark comments from registered users as verified
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
    $args['reply_text'] = '<i class="fa fa-reply"></i> ' . __('Reply', 'wordpress-theme-elegance');
    
    return $args;
}
add_filter('wp_list_comments_args', 'elegance_comment_list_args');

/**
 * Add microdata to comments for better SEO
 */
function elegance_comment_microdata($comment_text, $comment, $args) {
    if (is_admin()) {
        return $comment_text;
    }
    
    return '<div itemscope itemtype="http://schema.org/Comment">' .
           '<meta itemprop="datePublished" content="' . get_comment_time('c') . '">' .
           '<div itemprop="text">' . $comment_text . '</div>' .
           '</div>';
}
add_filter('comment_text', 'elegance_comment_microdata', 10, 3);

/**
 * Improve comment form accessibility
 */
function elegance_comment_form_field_comment($field) {
    $field = str_replace(
        '<textarea',
        '<textarea aria-describedby="comment-notes"',
        $field
    );
    
    return $field;
}
add_filter('comment_form_field_comment', 'elegance_comment_form_field_comment');

/**
 * Add AJAX support for comment submission (optional enhancement)
 */
function elegance_ajax_comment_scripts() {
    if (is_singular() && comments_open()) {
        wp_localize_script('elegance-main-script', 'eleganceComments', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('elegance_comment_nonce'),
            'processing' => __('Processing...', 'wordpress-theme-elegance'),
            'error' => __('There was an error submitting your comment.', 'wordpress-theme-elegance'),
            'success' => __('Comment submitted successfully!', 'wordpress-theme-elegance'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'elegance_ajax_comment_scripts');

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
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body" itemscope itemtype="http://schema.org/Comment">
                <footer class="comment-meta">
                    <div class="comment-author vcard" itemprop="author" itemscope itemtype="http://schema.org/Person">
                        <?php
                        $comment_author_url = get_comment_author_url($comment);
                        $comment_author = get_comment_author($comment);
                        $avatar = get_avatar($comment, $args['avatar_size']);
                        
                        if (0 != $args['avatar_size']) {
                            if (empty($comment_author_url)) {
                                echo $avatar;
                            } else {
                                printf('<a href="%s" rel="external nofollow" class="url">%s</a>', $comment_author_url, $avatar);
                            }
                        }
                        ?>
                        
                        <div class="comment-author-info">
                            <?php
                            printf(
                                '<b class="fn" itemprop="name">%1$s</b><span class="says screen-reader-text"> %2$s</span>',
                                esc_html($comment_author),
                                __('says:', 'wordpress-theme-elegance')
                            );
                            ?>
                            
                            <div class="comment-metadata">
                                <time datetime="<?php comment_time('c'); ?>" itemprop="datePublished">
                                    <?php
                                    printf(
                                        '<i class="fa fa-clock-o"></i> %1$s %2$s %3$s',
                                        esc_html(get_comment_date()),
                                        esc_html(_x('at', 'used between date and time', 'wordpress-theme-elegance')),
                                        esc_html(get_comment_time())
                                    );
                                    ?>
                                </time>
                                
                                <?php
                                edit_comment_link(
                                    sprintf(
                                        wp_kses(
                                            __('Edit <span class="screen-reader-text">comment by %s</span>', 'wordpress-theme-elegance'),
                                            array('span' => array('class' => array()))
                                        ),
                                        esc_html($comment_author)
                                    ),
                                    ' <span class="edit-link">',
                                    '</span>'
                                );
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php if ('0' == $comment->comment_approved) : ?>
                        <p class="comment-awaiting-moderation">
                            <i class="fa fa-clock-o"></i>
                            <?php esc_html_e('Your comment is awaiting moderation.', 'wordpress-theme-elegance'); ?>
                        </p>
                    <?php endif; ?>
                </footer>

                <div class="comment-content" itemprop="text">
                    <?php comment_text(); ?>
                </div>

                <?php
                $reply_args = array_merge($args, array(
                    'add_below' => 'div-comment',
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                    'before' => '<div class="reply">',
                    'after' => '</div>',
                ));
                
                comment_reply_link($reply_args, $comment, get_the_ID());
                ?>
            </article>
        <?php
    }
}

/**
 * Custom pagination for comments
 */
function elegance_comment_navigation() {
    if (get_comment_pages_count() > 1 && get_option('page_comments')) {
        ?>
        <nav class="navigation comment-navigation" role="navigation">
            <h2 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'wordpress-theme-elegance'); ?></h2>
            <div class="nav-links">
                <?php
                if ($prev_link = get_previous_comments_link(__('Older Comments', 'wordpress-theme-elegance'))) {
                    printf('<div class="nav-previous">%s</div>', $prev_link);
                }
                
                if ($next_link = get_next_comments_link(__('Newer Comments', 'wordpress-theme-elegance'))) {
                    printf('<div class="nav-next">%s</div>', $next_link);
                }
                ?>
            </div>
        </nav>
        <?php
    }
}

/**
 * Check if comment form should show required field indicators
 */
function elegance_comment_form_required_indicator() {
    $req = get_option('require_name_email');
    if ($req) {
        return '<span class="required">*</span>';
    }
    return '';
}
?>