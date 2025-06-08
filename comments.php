<?php
/**
 * Comments Template
 * 
 * The template for displaying comments and comment form
 * 
 * @package Elegance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// If comments are closed and there are no comments, return early
if (!comments_open() && get_comments_number() && post_password_required()) {
    return;
}
?>

<div class="comments-section section-inner" id="comments">
    <div class="container border-0">
        
        <?php if (have_comments()) : ?>
            <div class="title-block">
                <h3>
                    <?php
                    $comments_number = get_comments_number();
                    if ($comments_number == 1) {
                        printf(esc_html__('One comment on "%s"', 'wordpress-theme-elegance'), get_the_title());
                    } else {
                        printf(
                            esc_html(_nx('%1$s comment on "%2$s"', '%1$s comments on "%2$s"', $comments_number, 'comments title', 'wordpress-theme-elegance')),
                            number_format_i18n($comments_number),
                            get_the_title()
                        );
                    }
                    ?>
                </h3>
            </div>

            <div class="comments-list">
                <?php
                wp_list_comments(array(
                    'style'       => 'div',
                    'short_ping'  => true,
                    'avatar_size' => 60,
                    'callback'    => 'elegance_comment_callback',
                ));
                ?>
            </div>

            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
                <nav class="comments-pagination" role="navigation">
                    <div class="nav-previous"><?php previous_comments_link(__('Older Comments', 'wordpress-theme-elegance')); ?></div>
                    <div class="nav-next"><?php next_comments_link(__('Newer Comments', 'wordpress-theme-elegance')); ?></div>
                </nav>
            <?php endif; ?>

        <?php endif; // Check for have_comments() ?>

        <?php
        // Comment form
        $commenter = wp_get_current_commenter();
        $req = get_option('require_name_email');
        $aria_req = ($req ? " aria-required='true'" : '');

        $comment_form_args = array(
            'title_reply'       => __('Leave a Comment', 'wordpress-theme-elegance'),
            'title_reply_to'    => __('Leave a Reply to %s', 'wordpress-theme-elegance'),
            'cancel_reply_link' => __('Cancel Reply', 'wordpress-theme-elegance'),
            'label_submit'      => __('Post Comment', 'wordpress-theme-elegance'),
            'comment_field'     => '<div class="input-field"><textarea class="form-control" name="comment" id="comment" rows="6" required placeholder="' . esc_attr__('Your Comment', 'wordpress-theme-elegance') . '"></textarea></div>',
            'fields'            => array(
                'author' => '<div class="row"><div class="col-md-6"><div class="input-field"><input type="text" class="form-control" name="author" id="author" value="' . esc_attr($commenter['comment_author']) . '" required' . $aria_req . ' placeholder="' . esc_attr__('Your Name', 'wordpress-theme-elegance') . '"></div></div>',
                'email'  => '<div class="col-md-6"><div class="input-field"><input type="email" class="form-control" name="email" id="email" value="' . esc_attr($commenter['comment_author_email']) . '" required' . $aria_req . ' placeholder="' . esc_attr__('Your Email', 'wordpress-theme-elegance') . '"></div></div></div>',
                'url'    => '<div class="input-field"><input type="url" class="form-control" name="url" id="url" value="' . esc_attr($commenter['comment_author_url']) . '" placeholder="' . esc_attr__('Your Website (Optional)', 'wordpress-theme-elegance') . '"></div>',
            ),
            'class_submit'      => 'btn btn-dark',
            'submit_button'     => '<button type="submit" name="submit" class="btn btn-dark">%4$s</button>',
            'comment_notes_before' => '<p class="comment-notes">' . __('Your email address will not be published.', 'wordpress-theme-elegance') . '</p>',
            'comment_notes_after'  => '',
        );

        comment_form($comment_form_args);
        ?>

    </div>
</div>

<?php
/**
 * Custom comment callback function
 */
if (!function_exists('elegance_comment_callback')) {
    function elegance_comment_callback($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        ?>
        <div <?php comment_class('comment-item'); ?> id="comment-<?php comment_ID(); ?>">
            <div class="comment-body">
                <div class="comment-meta">
                    <div class="comment-author">
                        <?php echo get_avatar($comment, 60, '', '', array('class' => 'comment-avatar')); ?>
                        <div class="comment-author-info">
                            <h5 class="comment-author-name">
                                <?php comment_author_link(); ?>
                                <?php if (get_comment_meta(get_comment_ID(), 'comment_author_verified', true)) : ?>
                                    <span class="verified-badge"><i class="fa fa-check-circle"></i></span>
                                <?php endif; ?>
                            </h5>
                            <div class="comment-date">
                                <i class="fa fa-clock-o"></i>
                                <time datetime="<?php comment_time('c'); ?>">
                                    <?php
                                    printf(
                                        esc_html__('%1$s at %2$s', 'wordpress-theme-elegance'),
                                        get_comment_date(),
                                        get_comment_time()
                                    );
                                    ?>
                                </time>
                            </div>
                        </div>
                    </div>
                    
                    <div class="comment-reply">
                        <?php
                        comment_reply_link(array_merge($args, array(
                            'depth'      => $depth,
                            'max_depth'  => $args['max_depth'],
                            'reply_text' => '<i class="fa fa-reply"></i> ' . __('Reply', 'wordpress-theme-elegance'),
                        )));
                        ?>
                    </div>
                </div>

                <div class="comment-content">
                    <?php if ($comment->comment_approved == '0') : ?>
                        <p class="comment-awaiting-moderation">
                            <i class="fa fa-clock-o"></i>
                            <?php esc_html_e('Your comment is awaiting moderation.', 'wordpress-theme-elegance'); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php comment_text(); ?>
                </div>
            </div>
        </div>
        <?php
    }
}
?>