        <?php while (have_posts()) : the_post(); ?>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="container-fluid p-0 mb-4">
                    <div class="position-relative" style="height: 400px; overflow: hidden;">
                        <?php the_post_thumbnail('full', array('class' => 'w-100 h-100', 'style' => 'object-fit: cover;')); ?>
                        <div class="position-absolute w-100 h-100" style="top: 0; left: 0; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.1));"></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <article class="container content with-background">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                                                
                        <header class="mb-5">                            
                            <div class="d-flex flex-wrap align-items-center mb-3 text-muted">
                                <small class="me-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?php echo get_the_date(); ?>
                                </small>
                                <small class="me-3">
                                    <i class="fas fa-user me-1"></i>
                                    <?php printf( esc_html__( 'By %s', 'wordpress-theme-elegance' ), get_the_author() ); ?>
                                </small>
                                <!-- <?php if (get_the_category()) : ?>
                                    <div class="badge me-3">
                                        <?php the_category(', '); ?>
                                    </div>
                                <?php endif; ?> -->
                                <?php if (get_the_tags()) : ?>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach (get_the_tags() as $tag) : ?>
                                            <span class="badge badge-outline-secondary me-1 mb-1">
                                                #<?php echo $tag->name; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                                                        
                            <h1 class="display-4 font-weight-bold text-dark text-center mb-4">
                                <?php the_title(); ?>
                            </h1>
                                                        
                            <?php if (has_excerpt()) : ?>
                                <div class="lead text-muted text-center mb-4 p-3 bg-light rounded">
                                    <?php the_excerpt(); ?>
                                </div>
                            <?php endif; ?>
                        </header>
                                                
                        <div class="article-content mb-5">
                            <?php                            
                            add_filter('the_content', function($content) {
                                $content = preg_replace('/<img([^>]*)class="([^"]*)"([^>]*)>/i', '<img$1class="$2 img-fluid rounded"$3>', $content);
                                $content = preg_replace('/<img(?![^>]*class)([^>]*)>/i', '<img$1class="img-fluid rounded">', $content);
                                                            
                                $content = preg_replace('/<table([^>]*)>/i', '<div class="table-responsive"><table$1class="table table-striped table-bordered">', $content);
                                $content = str_replace('</table>', '</table></div>', $content);
                                
                                $content = preg_replace('/<blockquote([^>]*)>/i', '<blockquote$1class="blockquote bg-light p-3 border-left border-primary">', $content);
                                
                                return $content;
                            });
                            
                            the_content();
                            ?>
                        </div>
                        
                        <footer class="pt-4 mb-5 blog-post-navigation">
                            <div class="row">
                                <?php
                                $prev_post = get_previous_post();
                                $next_post = get_next_post();
                                
                                if ($prev_post || $next_post) : ?>
                                    
                                    <?php if ($prev_post) : ?>
                                        <div class="col-md-6 mb-3">
                                            <a href="<?php echo get_permalink($prev_post); ?>" class="card h-100 with-background text-decoration-none">
                                                <div class="card-body">
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="fas fa-chevron-left me-1"></i><?php esc_html_e( 'Previous Article', 'wordpress-theme-elegance' ); ?>
                                                    </small>
                                                    <h6 class="card-title mb-0 text-dark">
                                                        <?php echo get_the_title($prev_post); ?>
                                                    </h6>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($next_post) : ?>
                                        <div class="col-md-6 mb-3">
                                            <a href="<?php echo get_permalink($next_post); ?>" class="card h-100 with-background text-decoration-none">
                                                <div class="card-body text-md-right">
                                                    <small class="text-muted d-block mb-2">
                                                        <?php esc_html_e( 'Next Article', 'wordpress-theme-elegance' ); ?> <i class="fas fa-chevron-right ms-1"></i>
                                                    </small>
                                                    <h6 class="card-title mb-0 text-dark">
                                                        <?php echo get_the_title($next_post); ?>
                                                    </h6>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-md-12 mb-3">
                                        <a href="<?php echo home_url('/blog'); ?>" class="card h-100 with-background text-decoration-none">
                                            <div class="card-body text-center">                                    
                                                <h6 class="card-title mb-0 text-dark">
                                                    <i class="fas fa-book ms-1"></i> <?php esc_html_e( 'Back to Blog Index', 'wordpress-theme-elegance' ); ?>
                                                </h6>
                                            </div>
                                        </a>
                                    </div>
                                    
                                <?php endif; ?>
                            </div>
                        </footer>
                        
                        <?php if (comments_open() || get_comments_number()) : ?>
                            <section class="comments-section pt-1">                                
                                <?php Elegance_Templates::blog_comments(); ?>
                            </section>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </article>

        <?php endwhile; ?>