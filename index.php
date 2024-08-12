<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900" rel="stylesheet">
    <title><?php bloginfo('name'); ?> - <?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class("eleganceBody"); ?>>
    <?php
        $header_background_video = get_theme_mod('header_background_video');
        $header_background_image = get_theme_mod('header_background_image');
    ?>

    <div id="backgroundContainer">
        <video id="backgroundVideo" class="hidden" autoplay muted loop>
            <source src="<?php echo !empty($header_background_video) ? esc_url($header_background_video) : ''; ?>" type="video/mp4">
        </video>
        <img  id="backgroundImage" class="hidden" src="<?php echo !empty($header_background_image) ? esc_url($header_background_image) : ''; ?>" alt="Background Image">

        <?php get_header(); ?>

        <div id="fullpage" class="fullpage-default">

            <div class="section animated-row" data-section="home">
                <div class="section-inner">
                    <div class="welcome-box">
                        <span class="welcome-first animate" data-animate="fadeInUp"><?php echo get_theme_mod('home_description_above', ''); ?></span>
                        <h1 class="welcome-title animate" data-animate="fadeInUp"><?php bloginfo('name'); ?></h1>
                        <p class="animate" data-animate="fadeInUp"><?php echo get_theme_mod('home_description_below', ''); ?></p>
                        <div class="scroll-down next-section animate" data-animate="fadeInUp"><img src="<?php echo get_template_directory_uri() ?>/images/mouse-scroll.png" alt=""><span>Scroll Down</span></div>
                    </div>
                </div>
            </div>

            <?php if (have_posts()) : ?>
                <div class="section animated-row" data-section="posts">
                    <div class="section-inner">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 wide-col-laptop">
                                <?php while (have_posts()) : the_post(); ?>
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
            <?php endif; ?>

            <?php
                $pages = get_pages(array('sort_column' => 'menu_order'));

                $anchors = array_map(function($page) {
                    return sanitize_title($page->post_title);
                }, $pages);
                if (have_posts()) {                    
                    array_unshift($anchors, 'posts');
                }

                array_unshift($anchors, 'home');                

                $anchors_json = json_encode($anchors);

                foreach ($pages as $page) {
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
                                    <div class="page-item <?php echo $hide_bg == 'yes' ? '' : 'with-background' ; ?>">
                                        <div class="title-block animate" data-animate="fadeInUp">
                                                <h2><?php echo esc_html($title); ?></h2>
                                                <?php if ($description) : ?>
                                                    <span><?php echo esc_html($description); ?></span>
                                                <?php endif ; ?>
                                        </div>
                                        <div <?php echo $do_not_animate == 'yes' ? '' : 'class="animate" data-animate="fadeInDown"' ; ?>>
                                            <?php echo $content; ?>
                                        </div>
                                    </div>                                
                                </div>
                            </div>    
                        </div>
                    </div>
                    <?php
                }
            ?>

        <!-- <div id="social-icons">
            <div class="text-right">
                <ul class="social-icons">
                    <li><a href="#" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="#" title="Instagram"><i class="fa fa-behance"></i></a></li>
                </ul>
            </div>
        </div> -->

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var backgroundVideo = document.getElementById('backgroundVideo');
                var backgroundImage = document.getElementById('backgroundImage');

                var defaultVideoUrl = '<?php echo $header_background_video; ?>';
                var defaultImageUrl = '<?php echo $header_background_image; ?>';

                function showDefault() {
                    if (!defaultVideoUrl && !defaultImageUrl) {
                        return;
                    }

                    if (defaultVideoUrl) {                        
                        swapElementDisplay(backgroundImage, backgroundVideo, defaultVideoUrl);                    
                    } else {                        
                        swapElementDisplay(backgroundVideo, backgroundImage, defaultImageUrl);
                    }
                }

                function showImage(imageUrl) {
                    if (!imageUrl) {
                        return;
                    }

                    swapElementDisplay(backgroundVideo, backgroundImage, imageUrl);
                }

                showDefault();

                if (document.querySelector('.fullpage-default')) {
                    var myFullpage = new fullpage('.fullpage-default', {
                        licenseKey: 'C7F41B00-5E824594-9A5EFB99-B556A3D5',
                        anchors: <?php echo $anchors_json; ?>,
                        menu: '#nav',
                        lazyLoad: true,
                        navigation: true,
                        slidesNavigation: true,
                        navigationPosition: 'right',
                        scrollOverflow: true,
                        scrollOverflowReset: true,
                        responsiveWidth: 768,
                        responsiveHeight: 600,
                        responsiveSlides: true,
                        onLeave: function(origin, destination, direction) {
                            var section = destination.item;
                            var sectionName = section.getAttribute('data-section');
                            
                            switch(sectionName) {
                                <?php foreach ($pages as $page) : ?>
                                case '<?php echo $page->post_name; ?>':
                                    <?php if (has_post_thumbnail($page->ID)) : ?>
                                        showImage('<?php echo get_the_post_thumbnail_url($page->ID); ?>');
                                    <?php else : ?>
                                        showDefault();
                                    <?php endif; ?>
                                     break;   
                                <?php endforeach; ?>
                                default:
                                    showDefault();
                            }
                        }
                    });
                }
            });
        </script>

        <?php get_footer(); ?>
    </div>  
</body>