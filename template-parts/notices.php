<?php 
$notices_query = Elegance_Queries::notices_query();

if ($notices_query->have_posts()) : ?>
    <section class="vertical-slide animated-row" id="notices" data-scroll-slide-id="notices">
        <div class="section-content overflow-hidden">                            
            <div class="swiper horizontal-swiper notices-swiper" 
                data-swiper-id="notices"
                data-swiper-direction="horizontal"
                data-swiper-slides-per-view="1"
                data-swiper-space-between="30"
                data-swiper-navigation='{ "nextEl":".swiper-button-next", "prevEl":".swiper-button-prev" }'
                data-swiper-pagination='{ "el":".swiper-pagination", "clickable":true }'>                                
                <div class="swiper-nav-wrapper">
                    <div class="swiper-button-prev"><i class="fas fa-chevron-left"></i></div>
                    <div class="swiper-pagination"></div>                            
                    <div class="swiper-button-next"><i class="fas fa-chevron-right"></i></div>
                </div>                
                <div class="swiper-wrapper">
                    <?php while ($notices_query->have_posts()) : $notices_query->the_post(); ?>
                        <div class="swiper-slide with-background notice-slide padded">              
                            <div class="card bg-transparent border-0">
                                <?php if (has_post_thumbnail()) : ?>                                                     
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" 
                                                class="img-fluid notice-img"/>                                                                
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                            <span class="card-text text-muted small"><?php echo get_the_date(); ?></span>
                                                <h2 class="card-title"><?php the_title(); ?></h2>
                                                <div class="card-text mvh-70 scrollable"><?php the_content(); ?></div>
                                            </div>                                                        
                                        </div>
                                    </div>  
                                <?php else : ?>
                                    <div class="card-body">
                                        <span class="card-text text-muted small"><?php echo get_the_date(); ?></span>
                                        <h2 class="card-title"><?php the_title(); ?></h2>
                                        <div class="card-text mvh-70 scrollable"><?php the_content(); ?></div>
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