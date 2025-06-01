<head>
    <title><?php bloginfo('name'); ?> - <?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body class="blog-page">
<?php get_header("blog"); ?>
<?php while (have_posts()) : the_post(); ?>

    <!-- Featured Image Header -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="container-fluid p-0 mb-4">
            <div class="position-relative" style="height: 400px; overflow: hidden;">
                <?php the_post_thumbnail('full', array('class' => 'w-100 h-100', 'style' => 'object-fit: cover;')); ?>
                <div class="position-absolute w-100 h-100" style="top: 0; left: 0; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.1));"></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Article Content -->
    <article class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <!-- Article Header -->
                <header class="mb-5">
                    <!-- Meta Information -->
                    <div class="d-flex flex-wrap align-items-center mb-3 text-muted">
                        <small class="mr-3">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <?php echo get_the_date(); ?>
                        </small>
                        <small class="mr-3">
                            <i class="far fa-user mr-1"></i>
                            By <?php the_author(); ?>
                        </small>
                        <?php if (get_the_category()) : ?>
                            <div class="badge badge-primary mr-3">
                                <?php the_category(', '); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (get_the_tags()) : ?>
                            <div class="d-flex flex-wrap">
                                <?php foreach (get_the_tags() as $tag) : ?>
                                    <span class="badge badge-outline-secondary mr-1 mb-1">
                                        #<?php echo $tag->name; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Article Title -->
                    <h1 class="display-4 font-weight-bold text-dark mb-4">
                        <?php the_title(); ?>
                    </h1>
                    
                    <!-- Article Excerpt/Summary -->
                    <?php if (has_excerpt()) : ?>
                        <div class="lead text-muted mb-4 p-3 bg-light rounded">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                </header>
                
                <!-- Article Body -->
                <div class="article-content mb-5">
                    <?php
                    // Add Bootstrap classes to content automatically
                    add_filter('the_content', function($content) {
                        // Add Bootstrap classes to images
                        $content = preg_replace('/<img([^>]*)class="([^"]*)"([^>]*)>/i', '<img$1class="$2 img-fluid rounded shadow-sm"$3>', $content);
                        $content = preg_replace('/<img(?![^>]*class)([^>]*)>/i', '<img$1class="img-fluid rounded shadow-sm">', $content);
                        
                        // Add Bootstrap classes to tables
                        $content = preg_replace('/<table([^>]*)>/i', '<div class="table-responsive"><table$1class="table table-striped table-bordered">', $content);
                        $content = str_replace('</table>', '</table></div>', $content);
                        
                        // Add Bootstrap classes to blockquotes
                        $content = preg_replace('/<blockquote([^>]*)>/i', '<blockquote$1class="blockquote bg-light p-3 border-left border-primary">', $content);
                        
                        return $content;
                    });
                    
                    the_content();
                    ?>
                </div>
                
                <!-- Article Footer -->
                <footer class="border-top pt-4 mb-5">
                    <!-- Post Navigation -->
                    <div class="row">
                        <?php
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        
                        if ($prev_post || $next_post) : ?>
                            
                            <?php if ($prev_post) : ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-chevron-left mr-1"></i>Previous Article
                                            </small>
                                            <h6 class="card-title mb-0">
                                                <a href="<?php echo get_permalink($prev_post); ?>" class="text-dark text-decoration-none">
                                                    <?php echo get_the_title($prev_post); ?>
                                                </a>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($next_post) : ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body text-md-right">
                                            <small class="text-muted d-block mb-2">
                                                Next Article <i class="fas fa-chevron-right ml-1"></i>
                                            </small>
                                            <h6 class="card-title mb-0">
                                                <a href="<?php echo get_permalink($next_post); ?>" class="text-dark text-decoration-none">
                                                    <?php echo get_the_title($next_post); ?>
                                                </a>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        <?php endif; ?>
                    </div>
                    
                    <!-- Social Sharing (Optional) -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 pt-3 border-top">
                        <div class="mb-2">
                            <small class="text-muted">Share this article:</small>
                        </div>
                        <div class="d-flex">
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                               target="_blank" class="btn btn-outline-info btn-sm mr-2">
                                <i class="fab fa-twitter"></i> Tweet
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" class="btn btn-outline-primary btn-sm mr-2">
                                <i class="fab fa-facebook-f"></i> Share
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" class="btn btn-outline-dark btn-sm">
                                <i class="fab fa-linkedin-in"></i> LinkedIn
                            </a>
                        </div>
                    </div>
                </footer>
                
                <!-- Comments Section -->
                <?php if (comments_open() || get_comments_number()) : ?>
                    <section class="comments-section mt-5 pt-4 border-top">
                        <h3 class="h4 mb-4">
                            <i class="far fa-comments mr-2"></i>
                            <?php comments_number('Leave a Comment', 'One Comment', '% Comments'); ?>
                        </h3>
                        <?php comments_template(); ?>
                    </section>
                <?php endif; ?>
                
            </div>
        </div>
    </article>

<?php endwhile; ?>

<?php get_footer(); ?>
</body>