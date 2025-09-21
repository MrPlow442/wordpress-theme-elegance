<section class="vertical-slide animated-row" id="home" data-scroll-slide-id="home">
    <div class="section-content">
        <div class="welcome-box">
            <span class="welcome-first animate" data-animate="fadeInUp"><?php echo wp_kses_post(get_theme_mod('home_description_above', '')); ?></span>
            <h1 class="welcome-title animate" data-animate="fadeInUp"><?php bloginfo('name'); ?></h1>
            <p class="animate" data-animate="fadeInUp"><?php echo wp_kses_post(get_theme_mod('home_description_below', '')); ?></p>
            <div class="scroll-down next-section animate" data-animate="fadeInUp">                                                
                <i class="fa-solid fa-angle-down fa-2x" aria-hidden="true"></i>
                <span><?php esc_html_e('Scroll Down', 'wordpress-theme-elegance'); ?></span>
            </div>
        </div>
    </div>
</section>  