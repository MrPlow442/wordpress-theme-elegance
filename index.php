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

    <main class="scroll-container" 
        data-scroll-container-id="main">

        <?php Elegance_Templates::welcome(); ?>   

        <?php
            $menu_items = wp_get_nav_menu_items('top');            
            $menu_item_pages = Elegance_Queries::pages_from_menu_items($menu_items);
            $anchors = Elegance_Navigation::get_nav_anchors($menu_item_pages);
            Elegance_Templates::render_sections_from_menu_items($menu_items);          
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
            initializeEleganceTheme(EleganceConfig);
        });        
    </script>
    </main>
    <?php Elegance_Templates::social_icons(); ?>
    <?php get_footer(); ?>    
</body>