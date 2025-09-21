<?php
/**
 * Comments Template
 * 
 * The template for displaying comments and comment form
 * 
 * @package Elegance
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!comments_open() && get_comments_number() && post_password_required()) {
    return;
}

?>

<div id="comments">
    <div class="container">        
        <?php if (have_comments()) : ?>
            <div class="title-block">                
                <?php
                printf(
                    '<span>' . _nx(
                        'One comment to %2$s',
                        '%1$s comments to %2$s',
                        get_comments_number(),
                        'comments title',
                        'wordpress-theme-elegance'
                    ) . '</span>',
                    number_format_i18n( get_comments_number() ),
                    '<h2>' . get_the_title() . '</h2>'
                );                    
                ?>                
            </div>

            <div class="comments-list">
                <?php                
                wp_list_comments( [
                    'style'       => 'div',
                    'avatar_size' => EleganceBlogAvatarSize::SMALL,
                    'callback'    => 'elegance_bootstrap_comment',
                    'depth'      => 0,
                ] );
                ?>
            </div>

            <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
                <nav class="comments-pagination text-center" role="navigation">
                    <div class="nav-previous d-inline-block me-3"><?php previous_comments_link(__('Older Comments')); ?></div>
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