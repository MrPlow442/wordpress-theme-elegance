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
                        
            if (Elegance_Helpers::is_dev()) {
                $dev_file = str_replace('.min.', '.', $file);
                error_log('Checking if file exists ' . "{$theme_dir}/assets/{$type}/{$dev_file}");
                if (file_exists("{$theme_dir}/assets/{$type}/{$dev_file}")) {
                    error_log('Returning file ' . "{$theme_uri}/assets/{$type}/{$dev_file}");
                    return "{$theme_uri}/assets/{$type}/{$dev_file}";
                }
            }
                    
            error_log('Checking if file exists ' . "{$theme_dir}/assets/{$type}/{$file}");
            if (file_exists("{$theme_dir}/assets/{$type}/{$file}")) {
                error_log('Returning file ' . "{$theme_uri}/assets/{$type}/{$file}");
                return "{$theme_uri}/assets/{$type}/{$file}";
            }
                        
            $base_file = str_replace('.min.', '.', $file);
            error_log('Checking if file exists ' . "{$theme_dir}/assets/{$type}/{$base_file}");
            if (file_exists("{$theme_dir}/assets/{$type}/{$base_file}")) {
                error_log('Returning file ' . "{$theme_uri}/assets/{$type}/{$base_file}");
                return "{$theme_uri}/assets/{$type}/{$base_file}";
            }
                        
            error_log('None of the files found, returning ' . "{$theme_uri}/{$type}/{$file}");
            return "{$theme_uri}/{$type}/{$file}";
        }


    }
}