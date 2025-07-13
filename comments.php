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

define('ELEGANCE_BLOG_AVATAR_SIZE_LG', 48);
define('ELEGANCE_BLOG_AVATAR_SIZE_SM', 32);

if ( ! function_exists( 'elegance_bootstrap_comment' ) ) {
    function elegance_bootstrap_comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        $indent_classes     = [ '', 'ml-md-4', 'ml-md-5', 'ml-md-5 pl-md-3' ]; // Only indent on medium+ screens
        $indent_class       = $indent_classes[ min( $depth, 3 ) ];
        $tag                = 'div' === $args['style'] ? 'div' : 'li';
        $reply_args         = array_merge(
            $args,
            [
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
                'reply_text'=> __( 'Reply' ),
                'add_below' => 'comment-actions',
                'before'    => '<div class="px-2"><strong>',
                'after'     => '</strong></div>',
            ]
        );
        $edit_before = '<div class="px-2"><strong>';
        $edit_after  = '</strong></div>';
        ?>
        <<?php echo $tag; ?> <?php comment_class( "comment-item $indent_class", $comment ); ?> id="comment-<?php comment_ID(); ?>">
            <div class="card border-0 shadow-sm mb-3 mb-md-4">
                <div class="card-body p-3 p-md-4">
                    <div class="media">
                        <?php echo get_avatar( $comment, ELEGANCE_BLOG_AVATAR_SIZE_LG, '', '', [ 'class' => 'rounded-circle mr-2 mr-md-3 d-none d-sm-block' ] ); ?>
                        <?php echo get_avatar( $comment, ELEGANCE_BLOG_AVATAR_SIZE_SM, '', '', [ 'class' => 'rounded-circle mr-2 d-sm-none' ] ); ?>
                        
                        <div id="comment-body-<?php comment_ID(); ?>" class="media-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-weight-bold text-dark">
                                        <?php comment_author_link(); ?>
                                        <?php
                                        if ( get_comment_meta( get_comment_ID(), 'comment_author_verified', true ) ) {
                                            printf(
                                                '<span class="badge badge-primary badge-sm ml-1 ml-md-2"><i class="fa fa-check"></i></span>'                                                
                                            );
                                        }
                                        ?>
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
                                            printf( __( '%s ago'), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) );
                                            ?>
                                        </time>
                                    </small>                                    
                                </div>                                                                                                                            
                            </div>

                            <?php if ( '0' === $comment->comment_approved ) : ?>
                                <div class="alert alert-warning py-2 mb-2 mb-md-3" role="alert">
                                    <small><i class="fa fa-clock mr-1"></i><?php esc_html_e( 'Your comment is awaiting moderation.' ); ?></small>
                                </div>
                            <?php endif; ?>

                            <div class="comment-content">
                                <?php comment_text(); ?>
                            </div>

                            <div class="comment-actions text-left">
                                <div class="d-flex justify-content-start">                                    
                                    <?php 
                                            comment_reply_link( $reply_args ); 
                                        ?>
                                    <?php 
                                        edit_comment_link( 
                                            __( 'Edit' ), 
                                            $edit_before, 
                                            $edit_after 
                                        );
                                    ?>                       
                                </div>                                                                
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
                    'style'       => 'div',
                    'avatar_size' => ELEGANCE_BLOG_AVATAR_SIZE_SM,
                    'callback'    => 'elegance_bootstrap_comment',
                    'depth'      => 0,
                ] );
                ?>
            </div>

            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
                <nav class="comments-pagination text-center" role="navigation">
                    <div class="nav-previous d-inline-block mr-3"><?php previous_comments_link(__('Older Comments')); ?></div>
                    <div class="nav-next d-inline-block"><?php next_comments_link(__('Newer Comments')); ?></div>
                </nav>
            <?php endif; ?>

        <?php endif; ?>

        <div class="comment-form">        
        <?php                                
            comment_form(elegance_get_comment_form_args());
            ?>
        </div>
    </div>
</div>