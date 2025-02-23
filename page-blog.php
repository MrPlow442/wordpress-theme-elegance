<?php
/*
Template Name: Blog Page
*/

// get_header(); 
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <?php
                    $args = array(
                        'post_type' => 'blog',
                        'posts_per_page' => 1,
                    );
                    $latest_blog_post = new WP_Query($args);

                    if ($latest_blog_post->have_posts()) :
                        while ($latest_blog_post->have_posts()) : $latest_blog_post->the_post();
                            get_template_part('template-parts/content', 'single-blog');
                        endwhile;
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="col-md-4">
                    <aside id="sidebar" class="sidebar">
                        <h2>Blog Archive</h2>
                        <?php
                        $args = array(
                            'post_type' => 'blog',
                            'posts_per_page' => -1,
                        );
                        $all_blog_posts = new WP_Query($args);

                        if ($all_blog_posts->have_posts()) :
                            $posts_by_year_month = array();
                            while ($all_blog_posts->have_posts()) : $all_blog_posts->the_post();
                                $year = get_the_date('Y');
                                $month = get_the_date('F');
                                $posts_by_year_month[$year][$month][] = get_the_title();
                            endwhile;

                            foreach ($posts_by_year_month as $year => $months) {
                                echo '<h3>' . $year . '</h3>';
                                echo '<ul>';
                                foreach ($months as $month => $posts) {
                                    echo '<li>' . $month . '</li>';
                                    echo '<ul>';
                                    foreach ($posts as $post_title) {
                                        echo '<li><a href="' . get_permalink() . '">' . $post_title . '</a></li>';
                                    }
                                    echo '</ul>';
                                }
                                echo '</ul>';
                            }
                        endif;
                        wp_reset_postdata();
                        ?>
                    </aside>
                </div>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>