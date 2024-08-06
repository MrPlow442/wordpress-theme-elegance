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
        <?php if (!empty($header_background_video)) : ?>
            <video autoplay muted loop id="backgroundVideo">
                <source src="<?php echo esc_url($header_background_video); ?>" type="video/mp4">
            </video>
        <?php endif; ?>

        <?php if (!empty($header_background_image)) : ?>
            <img id="backgroundImage" src="<?php echo esc_url($header_background_image); ?>" alt="Background Image">
        <?php endif; ?>

        <?php get_header(); ?>

        <div id="fullpage" class="fullpage-default">

            <div class="section animated-row" data-section="home">
                <div class="section-inner">
                    <div class="welcome-box">
                        <span class="welcome-first animate" data-animate="fadeInUp"><?php echo get_theme_mod('home_description_above', ''); ?></span>
                        <h1 class="welcome-title animate" data-animate="fadeInUp"><?php bloginfo('name'); ?></h1>
                        <p class="animate" data-animate="fadeInUp"><?php echo get_theme_mod('home_description_below', ''); ?></p>
                        <div class="scroll-down next-section animate data-animate="fadeInUp""><img src="<?php echo get_template_directory_uri() ?>/images/mouse-scroll.png" alt=""><span>Scroll Down</span></div>
                    </div>
                </div>
            </div>

            <?php
                $pages = get_pages(array('sort_column' => 'menu_order'));

                $anchors = array_map(function($page) {
                    return sanitize_title($page->post_title);
                }, $pages);
                array_unshift($anchors, 'home');

                $anchors_json = json_encode($anchors);
                
                foreach ($pages as $page) {
                    $content = apply_filters('the_content', $page->post_content);
                    $title = $page->post_title;
                    $slug = $page->post_name;
                    $thumbnail = get_the_post_thumbnail($page->ID, 'full');

                    if (has_post_thumbnail($page->ID)) {
                        $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($page->ID), 'full')[0];
                        $thumbnail_style = 'style="background-image: url(\'' . esc_url($thumbnail_url) . '\');"';
                        $has_thumbnail_class = 'has-thumbnail';
                    } else {
                        $thumbnail_style = ''; // Empty style if no featured image
                        $has_thumbnail_class = '';
                    }
                    ?>
                    <div class="section animated-row" data-section="<?php echo esc_attr($slug); ?>">
                        <div class="section-inner">
                            <div class="row justify-content-center">
                                <div class="col-md-8 wide-col-laptop">
                                    <div class="page-item <?php echo esc_attr($has_thumbnail_class); ?>" <?php echo $thumbnail_style; ?>>
                                        <div class="title-block animate" data-animate="fadeInUp">
                                            <h2><?php echo esc_html($title); ?></h2>
                                        </div>
                                        <div class="animate" data-animate="fadeInDown">
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

        <div id="social-icons">
            <div class="text-right">
                <ul class="social-icons">
                    <li><a href="#" title="Facebook"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#" title="Linkedin"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="#" title="Instagram"><i class="fa fa-behance"></i></a></li>
                </ul>
            </div>
        </div>

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                if (document.querySelector('.fullpage-default')) {
                    var myFullpage = new fullpage('.fullpage-default', {
                        licenseKey: 'C7F41B00-5E824594-9A5EFB99-B556A3D5',
                        anchors: <?php echo $anchors_json; ?>,
                        menu: '#nav',
                        lazyLoad: true,
                        navigation: true,
                        navigationPosition: 'right',
                        scrollOverflow: true,
                        responsiveWidth: 768,
                        responsiveHeight: 600,
                        responsiveSlides: true
                    });
                }
            });
        </script>

        <?php get_footer(); ?>
    </div>  
</body>