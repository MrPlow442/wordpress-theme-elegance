<header class="blog-header">
    <div class="container-fluid">
        <div class="navbar d-flex justify-content-between align-items-center">
            <!-- Logo/Home Link -->
            <a href="<?php echo home_url(); ?>" id="logo" title="Back to <?php bloginfo('name'); ?>">
                <?php bloginfo('name'); ?>
            </a>
            
            <!-- Centered Blog Title -->
            <div class="blog-header-center text-center">
                <h1 class="blog-title mb-0"><?php echo get_theme_mod('blog_title', 'Blog'); ?></h1>
                <p class="blog-description mb-0">
                    <small><?php echo get_theme_mod('blog_description', 'Latest articles and insights'); ?></small>
                </p>
            </div>
            
            <!-- Back Link (Hidden on Mobile) -->
            <div class="navigation-row">
                <nav id="navigation">
                    <button type="button" class="navbar-toggle d-lg-none"> 
                        <i class="fa fa-bars"></i> 
                    </button>
                    <div class="nav-box navbar-collapse">
                        <ul class="navigation-menu nav navbar-nav navbars">
                            <li><a href="<?php echo home_url(); ?>"><i class="fa fa-chevron-left mr-1"></i> <?php esc_html_e( 'Back to Homepage', 'wordpress-theme-elegance' ); ?></a></li>
                            <li><a href="<?php echo home_url('/blog'); ?>"><i class="fa fa-chevron-left mr-1"></i> <?php esc_html_e( 'Back to Blog Index', 'wordpress-theme-elegance' ); ?></a></li>                        
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
