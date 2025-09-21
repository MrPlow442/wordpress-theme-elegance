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

$migrator_includes = [
    '/inc/classes/migrators/class-elegance-migrator-helpers.php', // Migrator Helpers
    '/inc/classes/migrators/interface-elegance-migrator.php', // Theme version migrator interface, classes and constants    
    '/inc/classes/migrators/class-elegance-testimonials-migrator.php', // Testimonials migrator
    '/inc/classes/migrators/class-elegance-social-icons-migrator.php' // Social icons migrator
];

// Load dependencies with child theme support and safe inclusion
$theme_includes = [
    '/inc/constants.php',    // Theme constants
    '/inc/setup/setup.php',        // Theme setup
    '/inc/setup/migration.php',  // Theme migration setup
    '/inc/assets.php',       // Asset management
    '/inc/walkers.php',      // Custom walkers
    '/inc/classes/class-elegance-helpers.php', // Helper functions
    '/inc/classes/class-elegance-asset-loader.php', // Asset loader
    '/inc/classes/class-elegance-templates.php', // Templates and helpers
    '/inc/classes/class-elegance-navigation.php', // Navigation class
    '/inc/classes/class-elegance-queries.php', // Theme specific queries   
    '/inc/classes/class-elegance-migration-runner.php', // Theme version migrations    
    '/inc/testimonials/functions.php', // Testimonials functions
    '/inc/comments/functions.php', // Comments functions    
    '/inc/custom-post-types.php', // CPTs
    '/inc/customizer/functions.php', // Customizer functions
    '/inc/customizer/controls.php', // Customizer controls
    '/inc/customizer/inline-styles.php', // Customizer inline styles
    '/inc/customizer/customizer.php',    // Customizer     
];

$admin_includes = [
    '/inc/admin.php', // Admin-only functionality
];

foreach ($migrator_includes as $file) {
    require_once get_theme_file_path($file);
}

foreach ($theme_includes as $file) {
    require_once get_theme_file_path($file);
}

if (is_admin()) {
    foreach ($admin_includes as $file) {
        require_once get_theme_file_path($file);
    }
}
