<?php
/**
 * Elegance Theme Functions
 * 
 * @package Elegance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies with child theme support and safe inclusion
$theme_includes = [
    '/inc/setup.php',        // Theme setup
    '/inc/assets.php',       // Asset management
    '/inc/walkers.php',      // Custom walkers
    '/inc/template-functions.php', // Template helpers
    '/inc/comments-functions.php', // Comments functions
    '/inc/customizer-functions.php', // Customizer functions
    '/inc/custom-post-types.php', // CPTs    
    '/inc/customizer.php',    // Customizer        
    '/blocks/testimonials-block/testimonials-block.php', // Testimonials block
    '/blocks/work-block/work-block.php', // Work block
];

$admin_includes = [
    '/inc/admin.php', // Admin-only functionality
];

foreach ($theme_includes as $file) {
    require_once get_theme_file_path($file);
}

if (is_admin()) {
  foreach ($admin_includes as $file) {
      require_once get_theme_file_path($file);
  }  
}