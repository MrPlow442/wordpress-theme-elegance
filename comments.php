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

if ( ! function_exists( 'elegance_bootstrap_comment' ) ) {
    function elegance_bootstrap_comment( $comment, $args, $depth ) {
        $indent_classes = [ '', 'ml-4', 'ml-5', 'ml-5 pl-3' ]; // depth 0-3+
        $indent_class   = $indent_classes[ min( $depth, 3 ) ];

        $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
        $reply = array_merge(
            $args,
            [
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
                'reply_text'=> '<i class="fa fa-reply"></i> ' . __( 'Reply'),
                'add_below' => 'comment-body',                    
                'before'    => '<div class="mt-3">',
                'after'     => '</div>',
            ]
        );
        ?>
        <<?php echo $tag; ?> <?php comment_class( "comment-item $indent_class", $comment ); ?> id="comment-<?php comment_ID(); ?>">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="media">
                        <?php echo get_avatar( $comment, 56, '', '', [ 'class' => 'rounded-circle mr-3' ] ); ?>
                        
                        <div id="comment-body-<?php comment_ID(); ?>" class="media-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1 font-weight-bold text-dark">
                                        <?php comment_author_link(); ?>
                                        <?php if (get_comment_meta(get_comment_ID(), 'comment_author_verified', true)) : ?>
                                            <span class="badge badge-success badge-sm ml-2">
                                                <i class="fa fa-check"></i> Verified
                                            </span>
                                        <?php endif; ?>
                                        <?php 
                                        if ($comment->comment_parent) {
                                            printf(
                                                '<small class="text-muted"><i class="fa fa-caret-right"></i> %s</small>',
                                                esc_html(get_comment_author($comment->comment_parent))
                                            );
                                        }
                                        ?>                                        
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fa fa-clock mr-1"></i>
                                        <time datetime="<?php comment_time( 'c' ); ?>">
                                            <?php
                                            printf(
                                                __( '%s ago'),
                                                human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) )
                                            );
                                            ?>
                                        </time>
                                    </small>
                                </div>
                                <div class="comment-actions">
                                    <?php edit_comment_link( '<i class="fa fa-edit"></i> ' . __( 'Edit'), '<span class="btn btn-sm btn-outline-secondary mr-2">', '</span>' ); ?>
                                    <?php comment_reply_link( $reply ); ?>
                                </div>
                            </div>

                            <?php if ( '0' === $comment->comment_approved ) : ?>
                                <div class="alert alert-warning alert-sm mb-3" role="alert">
                                    <i class="fa fa-clock mr-2"></i>
                                    <?php _e( 'Your comment is awaiting moderation.'); ?>
                                </div>
                            <?php endif; ?>

                            <div class="comment-content">
                                <?php comment_text(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
}
?>

<div class="comments-section section-inner" id="comments">
    <div class="container">
        
        <?php if (have_comments()) : ?>
            <div class="title-block">
                <h3>
                    <?php
                    $comments_number = get_comments_number();
                    if ($comments_number == 1) {
                        printf(esc_html__('One comment on "%s"'), get_the_title());
                    } else {
                        printf(
                            esc_html(_nx('%1$s comment on "%2$s"', '%1$s comments on "%2$s"', $comments_number, 'comments title')),
                            number_format_i18n($comments_number),
                            get_the_title()
                        );
                    }
                    ?>
                </h3>
            </div>

            <div class="comments-list">
                <?php                
                wp_list_comments( [
                    'style'       => 'div',              // Bootstrap likes <div> wrapper
                    'avatar_size' => 56,
                    'callback'    => 'elegance_bootstrap_comment',
                    'depth'      => 0,
                ] );
                ?>
            </div>

            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
                <nav class="comments-pagination text-center" role="navigation">
                    <div class="nav-previous d-inline-block mr-3"><?php previous_comments_link(__('← Older Comments', 'wordpress-theme-elegance')); ?></div>
                    <div class="nav-next d-inline-block"><?php next_comments_link(__('Newer Comments →', 'wordpress-theme-elegance')); ?></div>
                </nav>
            <?php endif; ?>

        <?php endif; ?>

        <div class="comment-form">        
        <?php
            // Comment form
            $commenter = wp_get_current_commenter();
            $req = get_option('require_name_email');
            $aria_req = ($req ? " aria-required='true'" : '');

            $comment_form_args = array(
                'title_reply'       => __('Leave a Comment'),
                'title_reply_to'    => __('Leave a Reply to %s'),
                'cancel_reply_link' => __('Cancel Reply'),
                'label_submit'      => __('Post Comment'),
                'title_reply_before' => '<div class="card border-0 shadow-sm mt-5"><div class="card-body p-4"><h4 class="mb-4">',
                'title_reply_after'  => '</h4>',
                'comment_notes_before' => '<p class="text-muted mb-3"><small>' . __('Your email address will not be published. Required fields are marked *') . '</small></p>',
                'comment_notes_after'  => '</div></div>',
                'comment_field'     => '<div class="form-group mb-3"><label for="comment" class="form-label">' . __('Comment *') . '</label><textarea class="form-control" name="comment" id="comment" rows="5" required placeholder="' . esc_attr__('Write your comment here...') . '"></textarea></div>',
                'fields'            => array(
                    'author' => '<div class="row"><div class="col-md-6"><div class="form-group mb-3"><label for="author" class="form-label">' . __('Name *') . '</label><input type="text" class="form-control" name="author" id="author" value="' . esc_attr($commenter['comment_author']) . '" required' . $aria_req . ' placeholder="' . esc_attr__('Your Name') . '"></div></div>',
                    'email'  => '<div class="col-md-6"><div class="form-group mb-3"><label for="email" class="form-label">' . __('Email *') . '</label><input type="email" class="form-control" name="email" id="email" value="' . esc_attr($commenter['comment_author_email']) . '" required' . $aria_req . ' placeholder="' . esc_attr__('Your Email') . '"></div></div></div>'                    
                ),
                'class_submit'      => 'btn btn-primary btn-lg px-4',
                'submit_button'     => '<button type="submit" name="submit" class="btn btn-primary btn-lg px-4"><i class="fa fa-paper-plane mr-2"></i>%4$s</button>',
            );

            
            comment_form($comment_form_args);
            ?>
        </div>
    </div>
</div>