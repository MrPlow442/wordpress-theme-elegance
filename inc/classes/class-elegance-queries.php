<?php
if (!class_exists('Elegance_Queries')) {
    class Elegance_Queries {
        
        public static function notices_query() {
            return new WP_Query([
                'post_type'      => 'notice',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_status'    => 'publish'
            ]);
        }

        public static function testimonials_query() {
            return new WP_Query([
                'post_type'      => 'testimonial',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order date',
                'order'          => 'DESC',
                'post_status'    => 'publish'
            ]);
        }        

        public static function blog_query() {
            return new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 9,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'paged'          => get_query_var('paged') ? get_query_var('paged') : 1
            ]);
        }

        public static function has_notices() {
            return self::notices_query()->have_posts();
        }

        public static function has_testimonials() {            
            return self::testimonials_query()->have_posts();
        }

        public static function has_blog_posts() {
            return self::blog_query()->have_posts();
        }

        public static function pages_from_menu_items($menu_items) {
            $page_ids = [];            
            foreach ($menu_items as $item) {                
                if (Elegance_Navigation::is_page_menu_item($item)) {
                    $page_ids[] = $item->object_id;
                } 
            }
            return get_pages([
                'include' => $page_ids,
                'sort_column' => 'post__in' // sort in the same order of the given id's
            ]); 
        }
    }
}