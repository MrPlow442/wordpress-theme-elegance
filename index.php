<!DOCTYPE html>
<html>
<head>     
    <title><?php bloginfo('name'); ?> - <?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body id="background-container" <?php body_class("elegance-body"); ?>>
    <?php
        $main_page_background_video = get_theme_mod('main_page_background_video');
        $main_page_background_image = get_theme_mod('main_page_background_image');
    ?>

    <video id="background-video" class="hidden" autoplay muted loop>
        <source src="<?php echo !empty($main_page_background_video) ? esc_url($main_page_background_video) : ''; ?>" type="video/mp4">
    </video>
    <img  id="background-image" class="hidden" src="<?php echo !empty($main_page_background_image) ? esc_url($main_page_background_image) : ''; ?>" alt="Background Image" loading="eager">

    <?php elegance_preloader(); ?>

    <?php get_header(); ?>

    <div id="fullpage" class="fullpage-default">

        <div class="section animated-row" data-section="home">
            <div class="section-inner">
                <div class="welcome-box">
                    <span class="welcome-first animate" data-animate="fadeInUp"><?php echo wp_kses_post(get_theme_mod('home_description_above', '')); ?></span>
                    <h1 class="welcome-title animate" data-animate="fadeInUp"><?php bloginfo('name'); ?></h1>
                    <p class="animate" data-animate="fadeInUp"><?php echo wp_kses_post(get_theme_mod('home_description_below', '')); ?></p>
                    <div class="scroll-down next-section animate" data-animate="fadeInUp"><img src="<?php echo get_template_directory_uri() ?>/images/mouse-scroll.png" alt=""><span>Scroll Down</span></div>
                </div>
            </div>
        </div>

        <?php 
        $notices_query = elegance_notices_query();

        if ($notices_query->have_posts()) : ?>
            <div class="section animated-row" data-section="notices">
                <div class="section-inner">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 wide-col-laptop">
                            <?php while ($notices_query->have_posts()) : $notices_query->the_post(); ?>
                                <div class="slide">
                                    <div class="row post-container animate" data-animate="fadeInDown">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="col-md-6">
                                                <figure class="post-img animate" data-animate="fadeInUp">
                                                    <img src="<?php echo get_the_post_thumbnail_url(); ?>"/>
                                                </figure>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-<?php echo has_post_thumbnail() ? '6' : '12' ?>">
                                            <div class="post-contentbox">
                                                <div class="animate" data-animate="fadeInUp">
                                                    <span><?php echo get_the_date(); ?></span>
                                                    <h2><?php the_title(); ?></h2>                                                        
                                                    <p><?php the_content(); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            <?php endwhile; ?>                        
                        </div>
                    </div>    
                </div>
            </div>
        <?php 
        endif; 
        wp_reset_postdata();
        ?>

        <?php
            $menu_items = wp_get_nav_menu_items('top'); // Retrieve items in "top" menu

            // Extract page IDs and custom URLs with fragments from menu items
            $page_ids = [];
            $custom_items = [];
            foreach ($menu_items as $item) {
                if ($item->type === 'post_type' && $item->object === 'page') {
                    $page_ids[] = $item->object_id; // Collect page IDs for ordering
                } elseif ($item->type === 'custom') {
                    $custom_items[] = [
                        'title' => $item->title,
                        'anchor' => sanitize_title($item->attr_title),
                        'url' => esc_url($item->url)
                    ];
                }
            }

            // Fetch pages by IDs, sorted by menu order in "top" menu
            $pages = get_pages([
                'include' => $page_ids,
                'sort_column' => 'post__in'
            ]);

            // Append custom items to anchors list for fragment navigation
            $anchors = array_map(function ($item) {
                return sanitize_title($item->post_title);
            }, $pages);
            if ($notices_query->have_posts()) {
                array_unshift($anchors, 'notices');
            }
            foreach ($custom_items as $custom) {
                $anchors[] = $custom['anchor'];
            }
            array_unshift($anchors, 'home');            

            // Loop through each menu item, rendering custom items separately
            foreach ($menu_items as $item) {
                if ($item->type === 'post_type' && $item->object === 'page') {
                    $page = get_post($item->object_id);
                    $content = apply_filters('the_content', $page->post_content);
                    $title = $page->post_title;
                    $slug = $page->post_name;
                    $description = get_post_meta($page->ID, 'description', true);
                    $hide_bg = get_post_meta($page->ID, 'hide_background', true);
                    $do_not_animate = get_post_meta($page->ID, 'do_not_animate', true);
                    ?>
                    <div class="section animated-row" data-section="<?php echo esc_attr($slug); ?>">
                        <div class="section-inner">
                            <div class="row justify-content-center">
                                <div class="col-lg-8 wide-col-laptop">
                                    <div class="page-block-item <?php echo $hide_bg == 'yes' ? '' : 'with-background' ; ?>">
                                        <div class="title-block animate" data-animate="fadeInUp">
                                            <h2><?php echo esc_html($title); ?></h2>
                                            <?php if ($description) : ?>
                                                <span><?php echo esc_html($description); ?></span>
                                            <?php endif ; ?>
                                        </div>
                                        <div class="page-block-item-content <?php echo $do_not_animate == 'yes' ? '' : 'animate'; ?>" <?php echo $do_not_animate == 'yes' ? '' : 'data-animate="fadeInDown"'; ?>>
                                            <?php echo $content; ?>
                                        </div>
                                    </div>                                
                                </div>
                            </div>    
                        </div>
                    </div>
                    <?php
                }
            }
        ?>


    <script type="text/javascript">        
        <?php        
        $page_info = array_map(function($page) {
            return [
                'name' => $page->post_name,
                'hasThumbnail' => has_post_thumbnail($page->ID),
                'thumbnail' => has_post_thumbnail($page->ID) ? get_the_post_thumbnail_url($page->ID) : ''
            ];
        }, $pages);
                
        $js_config = [
            'videoElementId' => 'background-video',
            'imageElementId' => 'background-image',
            'defaultVideoUrl' => $main_page_background_video ?? '',
            'defaultImageUrl' => $main_page_background_image ?? '',
            'anchorsJson' => $anchors,
            'pageInfo' => $page_info
        ];
        ?>
        
        const config = <?php echo wp_json_encode($js_config); ?>;            
        document.addEventListener('DOMContentLoaded', function() {            
            initializeFullpage(config);
        });        
    </script>
    </div>
    <div id="social-icons">
        <div class="text-right">
            <ul class="social-icons">
                <?php
                $social_icons = json_decode(get_theme_mod('social_icons'));
                if (!empty($social_icons)) {
                    foreach ($social_icons as $social_icon) {
                        $title = !empty($social_icon->title) ? esc_attr($social_icon->title) : ''; // Safely access the title
                        $url = $social_icon->icon === 'fa-envelope' ? 'mailto:' . esc_attr($social_icon->url) : esc_url($social_icon->url);
                        echo '<li><a href="' . $url . '" title="' . $title . '"><i class="fa ' . esc_attr($social_icon->icon) . '"></i></a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <?php get_footer(); ?>    
</body>