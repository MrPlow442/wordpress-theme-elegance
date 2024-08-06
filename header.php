<header id="header">
    <div class="container-fluid">
        <div class="navbar">
            <a href="<?php echo home_url(); ?>" id="logo" title="<?php bloginfo('name'); ?>">
                <?php bloginfo('name'); ?>
            </a>
            <div class="navigation-row">
                <nav id="navigation">
                    <button type="button" class="navbar-toggle"> <i class="fa fa-bars"></i> </button>
                    <div class="nav-box navbar-collapse">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'top',
                            'menu_class' => 'navigation-menu nav navbar-nav navbars',
                            'container' => 'ul',
                            'walker'         => new Single_Page_Walker()
                        ));
                        ?>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>