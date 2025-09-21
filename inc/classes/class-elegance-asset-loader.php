<?php
if (!class_exists('Elegance_Asset_Loader')) {
    class Elegance_Asset_Loader {            
        public static function get_assets_version() {
            $version = wp_get_theme()->get('Version');
            if (Elegance_Helpers::is_dev()) {
                $version .= '.' . time();
            }
            return $version;
        }

        public static function get_asset_uri($type, $file) {
            $theme_dir = get_template_directory();
            $theme_uri = get_template_directory_uri();
            $assets_base = "/assets/{$type}/";

            $original_path = $theme_dir . $assets_base . $file;            
            if (file_exists($original_path)) {         
                return $theme_uri . $assets_base . $file;
            }

            if (strpos($file, '.min.') !== false) {
                $alt_file = str_replace('.min.', '.', $file);
            } else {
                $pathinfo = pathinfo($file);
                $alt_file = $pathinfo['filename'] . '.min.' . $pathinfo['extension'];
            }
                        
            $alt_path = $theme_dir . $assets_base . $alt_file;
            if (file_exists($alt_path)) {
                return $theme_uri . $assets_base . $alt_file;
            }
                        
            return $theme_uri . $assets_base . $file;
        }


    }
}