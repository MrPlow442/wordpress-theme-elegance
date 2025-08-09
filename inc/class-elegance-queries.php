<?php
if (!class_exists('Elegance_Queries')) {
    class Elegance_Queries {
        public static function testimonials_query() {
            return new WP_Query(array(
                'post_type'      => 'testimonial',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order date',
                'order'          => 'DESC',
                'post_status'    => 'publish'
            ));
        }

        public static function notices_query() {
            return new WP_Query(array(
                'post_type'      => 'notice',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_status'    => 'publish'
            ));
        }

        public static function has_notices() {
            return self::notices_query()->have_posts();
        }

        public static function has_testimonials() {            
            return self::testimonials_query()->have_posts();
        }
    }
}