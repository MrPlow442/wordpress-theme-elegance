<?php
/*
Template Name: Blog Page
*/
?>
<!DOCTYPE html> 
<html>
<head>
    <title><?php bloginfo('name'); ?> - <?php wp_title(); ?></title>    
    <?php wp_head(); ?>
</head>
<body id="background-container" <?php body_class("blog-page"); ?>>
    <?php        
        $blog_background_image = get_theme_mod('blog_background_image');
    ?>
    
    <img id="background-image" class="hidden" src="<?php echo !empty($blog_background_image) ? esc_url($blog_background_image) : ''; ?>" alt="Background Image" loading="eager">

    <?php Elegance_Templates::preloader(); ?>

    <?php get_header("blog"); ?>

    <main id="blog-content" class="container my-5">        
        <div class="row">
            <?php Elegance_Templates::blog_post_list(); ?>
        </div>

        <script type="text/javascript">
            <?php                            
            $js_config = [                
                'imageElementId' => 'background-image',                
                'defaultImageUrl' => $blog_background_image ?? '',
            ];
            ?>

            const config = <?php echo wp_json_encode($js_config); ?>;                          
            document.addEventListener('DOMContentLoaded', function() {                
                initializeBlogPage(config);
            });
        </script>
    </main>

    <?php get_footer(); ?>
</body>
</html>