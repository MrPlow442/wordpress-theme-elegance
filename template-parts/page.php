<?php
extract($args);
?>

<section class="snap-section animated-row" id="<?php echo esc_attr($slug); ?>">
    <div class="section-inner">
        <div class="row justify-content-center">
            <div class="col-lg-8 wide-col-laptop">
                <div class="page-block-item <?php echo $hide_bg == 'yes' ? '' : 'with-background'; ?>">
                    <div class="title-block animate" data-animate="fadeInUp">
                        <h2><?php echo esc_html($title); ?></h2>
                        <?php if ($description) : ?>
                            <span><?php echo esc_html($description); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="page-block-item-content <?php echo $do_not_animate == 'yes' ? '' : 'animate'; ?>" 
                        <?php echo $do_not_animate == 'yes' ? '' : 'data-animate="fadeInDown"'; ?>>
                        <?php echo $content; ?>
                    </div>
                </div>                                
            </div>
        </div>    
    </div>
</section>