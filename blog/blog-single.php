<?php get_header(); ?>

<article class="single-blog-post">
    <?php while (have_posts()) : the_post(); ?>
        <header class="single-post-header">
            <div class="content">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="featured-image">
                        <?php the_post_thumbnail('full'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="post-meta">
                    <span class="post-date"><?php echo get_the_date(); ?></span>
                    <span class="post-author">By <?php the_author(); ?></span>
                    <span class="post-categories"><?php the_category(', '); ?></span>
                </div>
                
                <h1 class="post-title"><?php the_title(); ?></h1>
            </div>
        </header>
        
        <div class="single-post-content">
            <div class="content">
                <?php the_content(); ?>
            </div>
        </div>
        
        <footer class="single-post-footer">
            <div class="content">
                <div class="post-navigation">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    
                    if ($prev_post) : ?>
                        <div class="nav-previous">
                            <a href="<?php echo get_permalink($prev_post); ?>">
                                ← <?php echo get_the_title($prev_post); ?>
                            </a>
                        </div>
                    <?php endif;
                    
                    if ($next_post) : ?>
                        <div class="nav-next">
                            <a href="<?php echo get_permalink($next_post); ?>">
                                <?php echo get_the_title($next_post); ?> →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="back-to-blog">
                    <a href="/blog">← Back to Blog</a>
                </div>
            </div>
        </footer>
    <?php endwhile; ?>
</article>

<?php get_footer(); ?>
