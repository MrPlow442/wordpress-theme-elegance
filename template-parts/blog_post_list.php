<?php
$blog_query = Elegance_Queries::blog_query();

if ($blog_query->have_posts()) :
    while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
        <div class="col-lg-4 col-md-12 mb-4">
            <article class="article-peek card h-100 with-background">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="card-img-top-wrapper" style="height: 200px; overflow: hidden;">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium_large', array('class' => 'card-img-top w-100 h-100', 'style' => 'object-fit: cover;')); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?php echo get_the_date(); ?>
                        </small>
                        <!-- <div class="badge">
                            <?php the_category(', '); ?>
                        </div> -->
                    </div>

                    <h5 class="card-title">
                        <a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none">
                            <?php the_title(); ?>
                        </a>
                    </h5>

                    <div class="card-text text-muted mb-3 flex-grow-1">
                        <?php the_excerpt(); ?>
                    </div>

                    <div class="mt-auto">
                        <a href="<?php the_permalink(); ?>" class="btn btn-dark btn-block">
                            <?php esc_html_e('Read More', 'wordpress-theme-elegance'); ?>
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </article>
        </div>

    <?php endwhile;

    if ($blog_query->max_num_pages > 1) : ?>
        <div class="col-12">
            <nav aria-label="Blog pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php
                    $pagination_links = paginate_links(array(
                        'total' => $blog_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'prev_text' => '<i class="fas fa-chevron-left me-1"></i>' . __(' Previous', 'wordpress-theme-elegance'),
                        'next_text' => __('Next', 'wordpress-theme-elegance') . '<i class="fas fa-chevron-right ms-1"></i>',
                        'type' => 'array'
                    ));

                    if ($pagination_links) {
                        foreach ($pagination_links as $link) {
                            if (strpos($link, 'current') !== false) {
                                echo '<li class="page-item page-item-dark active">' . str_replace('<span', '<span class="page-link"', $link) . '</li>';
                            } elseif (strpos($link, 'dots') !== false) {
                                echo '<li class="page-item page-item-dark disabled">' . str_replace('<span', '<span class="page-link"', $link) . '</li>';
                            } else {
                                echo '<li class="page-item page-item-dark">' . str_replace('<a', '<a class="page-link"', $link) . '</li>';
                            }
                        }
                    }
                    ?>
                </ul>
            </nav>
        </div>
    <?php endif;

else : ?>
    <div class="col-12">
        <div class="text-center py-5">
            <h3>
                <?php esc_html_e('No blog posts found', 'wordpress-theme-elegance'); ?>
            </h3>
            <p>
                <?php esc_html_e('Check back later for new content.', 'wordpress-theme-elegance'); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-dark">
                <?php esc_html_e('Back to Homepage', 'wordpress-theme-elegance'); ?>
            </a>
        </div>
    </div>
<?php 
endif;
wp_reset_postdata(); 
?>