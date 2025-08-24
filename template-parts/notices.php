<?php 
$notices_query = Elegance_Queries::notices_query();

if ($notices_query->have_posts()) : ?>
    <section class="vertical-slide animated-row" id="notices" data-scroll-slide-id="notices">
        <div class="section-inner">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="horizontal-scroll-container" 
                        data-scroll-container-id="notices"
                        data-show-nav-buttons="true"
                        data-show-dots="true">
                        <?php while ($notices_query->have_posts()) : $notices_query->the_post(); ?>
                            <div class="horizontal-slide">
                                <div class="notice-container with-background animate" data-animate="fadeInDown">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="card h-100 bg-transparent border-0">
                                            <div class="row no-gutters">
                                                <div class="col-md-4">
                                                    <img src="<?php echo get_the_post_thumbnail_url(); ?>" 
                                                        class="card-img"/>                                                                
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="card-body d-flex flex-column justify-content-center text-left">
                                                        <span class="card-text text-muted small"><?php echo get_the_date(); ?></span>
                                                        <h2 class="card-title h3"><?php the_title(); ?></h2>
                                                        <div class="card-text"><?php the_content(); ?></div>
                                                    </div>                                                        
                                                </div>
                                            </div>                                                                                                        
                                        </div>                                                
                                    <?php else : ?>
                                        <div class="card h-100 bg-transparent border-0">
                                            <div class="card-body d-flex flex-column justify-content-center text-left">
                                                <span class="card-text text-muted small"><?php echo get_the_date(); ?></span>
                                                <h2 class="card-title h3"><?php the_title(); ?></h2>
                                                <div class="card-text"><?php the_content(); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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