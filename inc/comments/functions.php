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
            'title_reply_before' => '<div class="card border-0 mt-4 mt-md-5"><div class="card-body p-3 p-md-4"><h4 class="mb-3 mb-md-4">',
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
            'submit_button'     => '<div class="mt-3"><button type="submit" name="submit" class="btn btn-dark btn-block"><i class="fas fa-paper-plane me-2"></i>%4$s</button></div></div></div>',
        );
    }
}

if ( ! function_exists( 'elegance_bootstrap_comment' ) ) {
    function elegance_bootstrap_comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        $indent_classes     = [ '', 'ms-md-4', 'ms-md-5', 'ms-md-5 ps-md-3' ];
        $indent_class       = $indent_classes[ min( $depth, 3 ) ];
        $tag                = 'div' === $args['style'] ? 'div' : 'li';
        $reply_args         = array_merge(
            $args,
            [
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
                'reply_text'=> __( 'Reply' ),
                'add_below' => 'comment-actions',
                'before'    => '<div class="pe-2"><strong>',
                'after'     => '</strong></div>',
            ]
        );
        $edit_before = '<div class="pe-2"><strong>';
        $edit_after  = '</strong></div>';
        ?>
        <<?php echo $tag; ?> <?php comment_class( "comment-item $indent_class", $comment ); ?> id="comment-<?php comment_ID(); ?>">
            <div class="card border-0 mb-3 mb-md-4">
                <div class="card-body p-3 p-md-4">
                    <div class="media">                                                
                        <div id="comment-body-<?php comment_ID(); ?>" class="media-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-shrink-1">
                                    <?php echo get_avatar( $comment, EleganceBlogAvatarSize::LARGE, '', '', [ 'class' => 'rounded-circle me-2 me-md-3 d-none d-sm-block' ] ); ?>
                                    <?php echo get_avatar( $comment, EleganceBlogAvatarSize::SMALL, '', '', [ 'class' => 'rounded-circle me-2 d-sm-none' ] ); ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-weight-bold text-dark">
                                        <?php comment_author_link(); ?>
                                        <?php
                                        if ( get_comment_meta( get_comment_ID(), 'comment_author_verified', true ) ) {
                                            printf(
                                                '<span class="badge rounded-pill bg-secondary badge-sm ms-1 ms-md-2"><i class="fas fa-check"></i></span>'                                                
                                            );
                                        }
                                        ?>
                                        <?php
                                        if ($comment->comment_parent) {
                                            printf(
                                                '<small class="text-muted"><i class="fas fa-caret-right"></i> %s</small>',
                                                esc_html(get_comment_author($comment->comment_parent))
                                            );
                                        }
                                        ?>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
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
                                    <small><i class="fas fa-clock me-1"></i><?php esc_html_e( 'Your comment is awaiting moderation.' ); ?></small>
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