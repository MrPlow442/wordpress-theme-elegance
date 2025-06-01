<?php
/*
Template Name: Blog Page
*/
?>

<head>
    <title><?php bloginfo('name'); ?> - <?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body>
<div class="back-to-main">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link">
        Back to Main Site
    </a>
</div>
<div class="blog-page-wrapper">
    <header class="blog-header">
        <div class="content">
            <h1 class="blog-title"><?php echo get_theme_mod('blog_title', 'Blog'); ?></h1>
            <p class="blog-description"><?php echo get_theme_mod('blog_description', 'Latest articles and insights'); ?></p>
        </div>
    </header>

    <main class="blog-content">
        <div class="content">
            <div class="blog-grid">
                <?php
                $blog_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 9,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                ));

                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                        <article class="blog-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="blog-card-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <span class="blog-date"><?php echo get_the_date(); ?></span>
                                    <span class="blog-category"><?php the_category(', '); ?></span>
                                </div>
                                
                                <h2 class="blog-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="blog-card-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <a href="<?php the_permalink(); ?>" class="blog-read-more">Read More</a>
                            </div>
                        </article>
                    <?php endwhile;
                    
                    // Pagination
                    echo '<div class="blog-pagination">';
                    echo paginate_links(array(
                        'total' => $blog_query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'prev_text' => '← Previous',
                        'next_text' => 'Next →'
                    ));
                    echo '</div>';
                    
                else : ?>
                    <p>No blog posts found.</p>
                <?php endif;
                wp_reset_postdata(); ?>
            </div>
        </div>
    </main>
</div>
<?php get_footer(); ?>
</body>
