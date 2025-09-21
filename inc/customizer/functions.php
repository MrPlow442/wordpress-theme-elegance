<?php
/**
 * Enhanced Export/Import Functions with Section Selection
 */

function elegance_export_selected_sections($wp_customize) {
    // Check if this is an export request
    if (!isset($_GET['elegance-export']) || !wp_verify_nonce($_GET['elegance-export'], 'customizer_export_nonce')) {
        return;
    }
    
    if (!current_user_can('edit_theme_options')) {
        wp_die(__('Permission denied.', 'wordpress-theme-elegance'));
    }
    
    $selected_sections = isset($_GET['export_sections']) ? $_GET['export_sections'] : array();
    
    if (empty($selected_sections)) {
        wp_die(__('No sections selected for export.', 'wordpress-theme-elegance'));
    }
    
    $export_data = array(
        'theme' => get_stylesheet(),
        'version' => wp_get_theme()->get('Version'),
        'exported' => date('Y-m-d H:i:s'),
        'selected_sections' => $selected_sections,
        'sections_data' => array()
    );
    
    // Get all settings from the ALREADY INITIALIZED customizer
    $settings = $wp_customize->settings();
    
    foreach ($selected_sections as $section_id) {
        $section_data = array();
        
        // Get all controls for this section
        foreach ($wp_customize->controls() as $control) {
            if ($control->section === $section_id) {
                // Get the setting(s) for this control
                if (is_array($control->settings)) {
                    foreach ($control->settings as $setting) {
                        if (isset($settings[$setting->id])) {
                            $section_data[$setting->id] = $settings[$setting->id]->value();
                        }
                    }
                } else {
                    $setting_id = $control->settings->id;
                    if (isset($settings[$setting_id])) {
                        $section_data[$setting_id] = $settings[$setting_id]->value();
                    }
                }
            }
        }
        
        if (!empty($section_data)) {
            $export_data['sections_data'][$section_id] = $section_data;
        }
    }
    
    $filename = get_stylesheet() . '-sections-export-' . date('Y-m-d-H-i-s') . '.json';
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    
    echo json_encode($export_data, JSON_PRETTY_PRINT);
    exit;
}

/**
 * Get all settings that belong to a specific section
 */
function elegance_get_section_settings($section_id) {
    global $wp_customize;
    
    // Ensure customizer is properly initialized
    if (!$wp_customize) {
        require_once get_theme_file_path('/inc/customizer-controls.php');
        require_once ABSPATH . 'wp-includes/class-wp-customize-manager.php';
        $wp_customize = new WP_Customize_Manager();
        // Trigger customize_register to populate settings
        do_action('customize_register', $wp_customize);
    }
    
    $setting_ids = array();
    $controls = $wp_customize->controls();
    
    foreach ($controls as $control_id => $control) {
        if ($control->section === $section_id) {
            // Handle different control types
            if (is_array($control->settings)) {
                // Multi-setting controls
                foreach ($control->settings as $setting) {
                    if (is_object($setting)) {
                        $setting_ids[] = $setting->id;
                    } else {
                        $setting_ids[] = $setting;
                    }
                }
            } else {
                // Single setting controls
                if (is_object($control->settings)) {
                    $setting_ids[] = $control->settings->id;
                } elseif (is_string($control->settings)) {
                    $setting_ids[] = $control->settings;
                } elseif (isset($control->setting) && is_object($control->setting)) {
                    $setting_ids[] = $control->setting->id;
                }
            }
        }
    }
    
    return array_unique(array_filter($setting_ids));
}