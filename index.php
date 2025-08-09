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

    <video id="background-video" class="hidden" autoplay muted loop preload="none">
        <?php if(!empty($main_page_background_video)) : ?>
            <source src="<?php echo esc_url($main_page_background_video); ?>" type="video/mp4">
        <?php endif; ?>
    </video>
    <img id="background-image" class="hidden" src="<?php echo !empty($main_page_background_image) ? esc_url($main_page_background_image) : ''; ?>" alt="Background Image" loading="eager">

    <?php Elegance_Templates::preloader(); ?>

    <?php get_header(); ?>

    <main class="scroll-container">

        <section class="snap-section animated-row" id="home">
            <div class="section-inner">
                <div class="welcome-box">
                    <span class="welcome-first animate" data-animate="fadeInUp"><?php echo wp_kses_post(get_theme_mod('home_description_above', '')); ?></span>
                    <h1 class="welcome-title animate" data-animate="fadeInUp"><?php bloginfo('name'); ?></h1>
                    <p class="animate" data-animate="fadeInUp"><?php echo wp_kses_post(get_theme_mod('home_description_below', '')); ?></p>
                    <div class="scroll-down next-section animate" data-animate="fadeInUp">                        
                        <i class="fa fa-angle-down fa-2x" aria-hidden="true"></i>
                        <span><?php esc_html_e('Scroll Down', 'wordpress-theme-elegance'); ?></span>
                    </div>
                </div>
            </div>
        </section>        

        <?php
            $menu_items = wp_get_nav_menu_items('top'); // Retrieve items in "top" menu

            // Extract page IDs and custom URLs with fragments from menu items
            $page_ids = [];
            $custom_items = [];
            foreach ($menu_items as $item) {                
                if (Elegance_Navigation::is_page_menu_item($item)) {
                    $page_ids[] = $item->object_id; // Collect page IDs for ordering
                } 
                // elseif ($item->type === 'custom') {
                //     error_log('Custom Menu Item: ' . print_r($item, true));
                //     $custom_items[] = [
                //         'title' => $item->title,
                //         'anchor' => sanitize_title($item->attr_title),
                //         'url' => esc_url($item->url)
                //     ];
                // }
            }

            // Fetch pages by IDs, sorted by menu order in "top" menu            
            $menu_item_pages = get_pages([
                'include' => $page_ids,
                'sort_column' => 'post__in'
            ]);                        

            // Append custom items to anchors list for fragment navigation
            $anchors = array_map(function ($item) {
                return sanitize_title($item->post_title);
            }, $menu_item_pages);            
            if (Elegance_Queries::has_notices()) {
                array_unshift($anchors, 'notices');
            }
            foreach ($custom_items as $custom) {                
                $anchors[] = $custom['anchor'];
            }
            array_unshift($anchors, 'home');                    
        
            // Loop through each menu item, rendering custom items separately
            foreach ($menu_items as $item) {                                                
                if (!Elegance_Navigation::is_theme_notices_menu_item($item)
                 && !Elegance_Navigation::is_theme_testimonials_menu_item($item) 
                 && !Elegance_Navigation::is_page_menu_item($item)) {                    
                    continue;
                }                                

                if (Elegance_Navigation::is_theme_notices_menu_item($item)) {                    
                    Elegance_Templates::notices();                    
                    continue;
                }

                if (Elegance_Navigation::is_theme_testimonials_menu_item($item)) {                    
                    Elegance_Templates::testimonials();
                    continue;
                }

                $menu_item_page = get_post($item->object_id);
                Elegance_Templates::page([
                    'slug' => $menu_item_page->post_name,
                    'title' => $menu_item_page->post_title,
                    'description' => get_post_meta($menu_item_page->ID, 'description', true),
                    'content' => apply_filters('the_content', $menu_item_page->post_content),
                    'hide_bg' => get_post_meta($menu_item_page->ID, 'hide_background', true),
                    'do_not_animate' => get_post_meta($menu_item_page->ID, 'do_not_animate', true)
                ]);           
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
        }, $menu_item_pages);
                
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
            // initializeFullpage(config);
            console.log('initializeFullpage exists and is a function:', typeof initializeFullpage === 'function');
            console.log('initializeScrollSnap exists and is a function:', typeof initializeScrollSnap === 'function');
            initializeScrollSnap(EleganceConfig);
        });        
    </script>
    </main>
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