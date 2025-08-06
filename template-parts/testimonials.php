<?php 
$testimonials_query = elegance_testimonials_query();

if ($testimonials_query->have_posts()) : ?>
    <section class="snap-section animated-row" id="testimonials">
        <div class="section-inner">
            <div class="row justify-content-center">
                <div class="col-md-8 offset-md-2">
                    <div class="horizontal-scroll-nav-top testimonials-nav">
                        <div class="scroll-dots">
                            <?php 
                            $post_count = $testimonials_query->found_posts;
                            for($i = 0; $i < $post_count; $i++) : ?>
                                <button class="scroll-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></button>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="horizontal-scroll-container testimonials-container" 
                         data-auto-scroll="true" 
                         data-auto-scroll-delay="5000" 
                         data-show-nav-buttons="false" 
                         data-show-dots="true">
                        <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post(); ?>
                            <div class="horizontal-slide testimonial-slide">
                                <div class="testimonial-item with-background animate" data-animate="fadeInUp">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="client-row">
                                            <?php the_post_thumbnail('thumbnail', array(
                                                'class' => 'rounded-circle', 
                                                'alt' => esc_attr(get_the_title())
                                            )); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="testimonial-content">
                                        <div class="testimonial-quote">
                                            <?php the_content(); ?>
                                        </div>
                                        
                                        <?php if (get_the_title()) : ?>
                                            <h4 class="client-name">
                                                <?php the_title(); ?>
                                            </h4>
                                        <?php endif; ?>
                                    </div>
                                </div>    
                            </div>
                        <?php endwhile; ?>                        
                    </div>
                </div>
            </div>    
        </div>
    </section>
<?php 
endif; 
wp_reset_postdata();
?>
