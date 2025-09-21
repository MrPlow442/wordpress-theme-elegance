<?php 
$testimonials_query = Elegance_Queries::testimonials_query();

if ($testimonials_query->have_posts()) : ?>
    <section class="vertical-slide animated-row" id="testimonials" data-scroll-slide-id="testimonials">
        <div class="section-content padded overflow-hidden">
            <div class="title-block animate" data-animate="fadeInUp">                            
                <h2><?php echo get_theme_mod('nav_testimonials_label', __('Testimonials', 'wordpress-theme-elegance')); ?></h2>                
            </div>
            <div class="swiper horizontal-swiper testimonials-swiper"
                data-swiper-id="testimonials"
                data-swiper-direction="horizontal"
                data-swiper-slides-per-view="2"
                data-swiper-space-between="30"
                data-swiper-pagination='{ "el":".swiper-pagination", "clickable":true }'
                data-swiper-breakpoints='{ "0": { "slidesPerView": 1 }, "1199": { "slidesPerView": 2 }, "3400": { "slidesPerView": 3 } }'
                data-swiper-autoplay='{ "delay":10000, "pauseOnMouseEnter":true }'
                >                
                <div class="swiper-nav-wrapper padded">
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-wrapper">
                    <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post(); ?>
                        <div class="swiper-slide with-background testimonial-slide">
                            <div class="card bg-transparent border-0 animate" data-animate="fadeInDown">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="row g-0">
                                        <div class="col-12 col-mobile-landscape-2">
                                            <?php the_post_thumbnail('thumbnail', array(
                                                'class' => 'rounded-circle', 
                                                'alt' => esc_attr(get_the_title())
                                            )); ?>
                                        </div>
                                        <div class="col-12 col-mobile-landscape-10">
                                            <div class="card-body">
                                                <div class="card-text responsive-mh scrollable">
                                                    <?php the_content(); ?>
                                                </div>                                                
                                            </div>
                                            <div class="card-footer bg-transparent border-top-0">
                                                <?php if (get_the_title()) : ?>
                                                    <h4 class="card-title">
                                                        <?php the_title(); ?>
                                                    </h4>                                                    
                                                <?php endif; ?>
                                                <?php echo elegance_get_testimonial_website_link(get_the_ID()) ?>
                                            </div>
                                        </div>
                                    </div>                                    
                                <?php else : ?>
                                    <div class="card-body">
                                        <div class="card-text responsive-mh scrollable">
                                            <?php the_content(); ?>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0">
                                        <?php if (get_the_title()) : ?>
                                            <h4 class="card-title">
                                                <?php the_title(); ?>
                                            </h4>                                                    
                                        <?php endif; ?>
                                        <?php echo elegance_get_testimonial_website_link(get_the_ID()) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>                                    
                </div>
            </div>               
        </div>
    </section>
<?php 
endif; 
wp_reset_postdata();
?>
