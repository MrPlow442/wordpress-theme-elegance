<?php 
$testimonials_query = Elegance_Queries::testimonials_query();

if ($testimonials_query->have_posts()) : ?>
    <section class="vertical-slide animated-row" id="testimonials" data-scroll-slide-id="testimonials">
        <div class="section-inner">
            <div class="row justify-content-center">
                <div class="col-12">                    
                    <div class="testimonials-block-item with-background">
                        <div class="title-block animate" data-animate="fadeInUp">            
                            <h2><?php echo esc_html_e( 'Testimonials', 'wordpress-theme-elegance' ); ?></h2>
                            <span><?php echo esc_html_e( 'What do the clients say?', 'wordpress-theme-elegance' ); ?></span>                        
                        </div>
                        <div class="horizontal-scroll-container testimonials-scroll-container"
                            data-scroll-container-id="testimonials"
                            data-scroll-behavior="smooth"
                            data-scroll-animation-duration="600"
                            data-auto-scroll="true"
                            data-auto-scroll-delay="10000" 
                            data-show-nav-buttons="false" 
                            data-show-dots="true">                                                 
                            <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post(); ?>
                                <div class="horizontal-slide testimonial-slide">
                                    <div class="testimonial-container animate" data-animate="fadeInDown">
                                        <div class="testimonial-item">
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
                                </div>
                            <?php endwhile; ?>                        
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </section>
<?php 
endif; 
wp_reset_postdata();
?>
