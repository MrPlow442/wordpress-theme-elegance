<header id="blog-header" class="blog-header">
    <div class="container-fluid">
        <div class="navbar">                
            <a href="<?php echo home_url(); ?>" id="logo" title="Back to <?php bloginfo('name'); ?>">
                <?php echo Elegance_Helpers::get_blog_title(); ?>
            </a>
                        
            <div class="navigation-row">
                <nav id="navigation">
                    <button type="button" class="navbar-toggle"> <i class="fas fa-bars pe-none"></i> </button>
                    <div class="nav-box navbar-collapse">
                        <ul class="navigation-menu nav navbar-nav navbars">                            
                            <li><a href="<?php echo home_url(); ?>"><i class="fas fa-chevron-left me-1"></i> <?php esc_html_e( 'Back to Homepage', 'wordpress-theme-elegance' ); ?></a></li>
                            <?php if (!is_page('blog') && !is_home()) : ?>
                                <li><a href="<?php echo home_url('/blog'); ?>"><i class="fas fa-chevron-left me-1"></i> <?php esc_html_e( 'Back to Blog Index', 'wordpress-theme-elegance' ); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>    
</header>
