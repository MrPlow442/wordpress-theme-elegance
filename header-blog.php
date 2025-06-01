<header class="blog-header">
    <div class="container-fluid">
        <div class="navbar">
            <a href="<?php echo home_url(); ?>" id="logo" title="<?php bloginfo('name'); ?>">
                <?php bloginfo('name'); ?>
            </a>
            <p class="font-weight-bold lead d-flex flex-column">
                    <?php echo get_theme_mod('blog_title', 'Blog'); ?>
                </p>
                <p class="d-flex flex-column">
                    <small><?php echo get_theme_mod('blog_description', 'Latest articles and insights'); ?></small>
                </p>
        </div>
        <!-- <div class="text-center">
            <h1 class="display-4 font-weight-bold mb-3">
                <?php echo get_theme_mod('blog_title', 'Blog'); ?>
            </h1>
            <p class="lead">
                <?php echo get_theme_mod('blog_description', 'Latest articles and insights'); ?>
            </p>
        </div> -->
    </div>
</header>