<?php
extract($args);
?>

<section class="vertical-slide animated-row" id="<?php echo esc_attr($slug); ?>" data-scroll-slide-id="<?php echo esc_attr($slug); ?>">
    <div class="section-content padded page-item <?php echo $hide_bg == 'yes' ? '' : 'with-background'; ?> overflow-y-auto invisible-scrollbar">
        <div class="title-block animate" data-animate="fadeInUp">
            <h2><?php echo esc_html($title); ?></h2>
            <?php if ($description) : ?>
                <span><?php echo esc_html($description); ?></span>
            <?php endif; ?>
        </div>
        <div class="page-item-content <?php echo $do_not_animate == 'yes' ? '' : 'animate'; ?>" 
            <?php echo $do_not_animate == 'yes' ? '' : 'data-animate="fadeInDown"'; ?>>
            <?php echo $content; ?>
        </div>
    </div>
</section>