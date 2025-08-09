<?php
if (!class_exists('Elegance_Templates')) {
    class Elegance_Templates {
        public static function preloader() {
            get_template_part('template-parts/preloader');
        }

        public static function notices() {
            get_template_part('template-parts/notices');
        }

        public static function testimonials() {
            get_template_part('template-parts/testimonials');
        }

        public static function page($args = array()) {
            $args = wp_parse_args($args, [
                'slug' => '',
                'title' => '',
                'description' => '',
                'content' => '',
                'hide_bg' => 'no',
                'do_not_animate' => 'no'
            ]);
            
            get_template_part('template-parts/page', null, $args);
        }
    }
}