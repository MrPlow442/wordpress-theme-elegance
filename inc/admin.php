<?php
/**
 * Admin-only functionality for WordPress Theme Elegance
 * 
 * This file contains all functions and classes that should only
 * be loaded in the WordPress admin context.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Ensure we're in admin context
if (!is_admin()) {
    return;
}

/**
 * Load WordPress admin dependencies when needed
 */
add_action('admin_init', 'elegance_load_nav_menu_dependencies');
function elegance_load_nav_menu_dependencies() {            
    require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
    require_once ABSPATH . 'wp-admin/includes/class-walker-nav-menu-edit.php';
}

/**
 * Register admin meta boxes for custom navigation
 * Use wp_nav_menu_setup hook for proper timing
 */
//add_action('wp_nav_menu_setup', 'elegance_register_custom_nav_meta_box');
add_action( 'admin_head-nav-menus.php', 'elegance_register_custom_nav_meta_box' );
function elegance_register_custom_nav_meta_box() {
    add_meta_box(
        'elegance-nav-items',
        __('Theme Navigation', 'wordpress-theme-elegance'),
        'elegance_nav_menu_meta_box',
        'nav-menus',
        'side',
        'default'
    );
}

/**
 * Display custom navigation meta box
 */
function elegance_nav_menu_meta_box() {
    global $_nav_menu_placeholder, $nav_menu_selected_id;
    $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
    
    $nav_items = elegance_get_theme_nav_items();
    
    if (empty($nav_items)) {
        echo '<p>' . __('No navigation items available.', 'wordpress-theme-elegance') . '</p>';
        return;
    }
    ?>
    <div class="posttypediv" id="elegance-nav-items">
        <div id="tabs-panel-elegance-nav-items" class="tabs-panel tabs-panel-active">
            <ul id="elegance-nav-items-checklist" class="categorychecklist form-no-clear">
                <?php
                foreach ($nav_items as $item) :                    
                    ?>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" 
                                   name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" 
                                   value="<?php echo esc_attr($item['id']); ?>" /> 
                            <?php echo esc_html($item['label']); ?>
                            <span class="elegance-protected-item"><?php _e('(Protected)', 'wordpress-theme-elegance'); ?></span>
                        </label>                        
                        <input type="hidden" class="menu-item-type" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" 
                               value="elegance_nav" />
                        <input type="hidden" class="menu-item-title" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" 
                               value="<?php echo esc_attr($item['label']); ?>" />                                                                                   
                        <input type="hidden" class="menu-item-url" 
                               name="menu-item[<?= $_nav_menu_placeholder ?>][menu-item-url]"
                               value="<?= ($item['type']==='link' ? esc_attr($item['value']) : '') ?>" />
                        <input type="hidden" class="menu-item-object" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object]" 
                               value="elegance_nav" />                        
                        <input type="hidden" class="menu-item-object-id" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" 
                               value="<?php echo esc_attr($item['id']); ?>" />
                        <input type="hidden" class="menu-item-attr-title"
                               name="menu-item[<?= $_nav_menu_placeholder ?>][menu-item-attr-title]"
                               value="<?= ($item['type']==='anchor' ? esc_attr($item['value']) : '') ?>" />
                    </li>
                    <?php
                    $_nav_menu_placeholder--;
                endforeach;
                ?>
            </ul>
        </div>
        <p class="button-controls wp-clearfix">
            <span class="add-to-menu">
                <input type="submit" class="button-secondary submit-add-to-menu right" 
                       value="<?php esc_attr_e('Add to Menu', 'wordpress-theme-elegance'); ?>" 
                       name="add-elegance-nav-item" id="submit-elegance-nav-items" />
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}

/**
 * Handle custom menu item type processing
 */
add_filter('wp_setup_nav_menu_item', 'elegance_setup_nav_menu_item');
function elegance_setup_nav_menu_item($menu_item) {    
    if (empty($menu_item) || $menu_item->post_type !== 'nav_menu_item' || $menu_item->type !== 'elegance_nav') {
        return $menu_item;
    }
        
    $menu_item->type_label = __('Theme Navigation', 'wordpress-theme-elegance');        
    $nav_items = elegance_get_theme_nav_items();            
    foreach ($nav_items as $item) {        
        if ($item['id'] === $menu_item->object_id) {            
            $menu_item->title = $item['label'];                                

            if ($item['type'] === 'anchor') {                
                $menu_item->attr_title = sanitize_title($item['value']);
            } 
            
            if ($item['type'] === 'link') {                
                $menu_item->url = esc_url($item['value']);
            }             
            break;
        }
    }    
    return $menu_item;
}

/**
 * Custom walker for menu editor (only load if class exists)
 */
add_action('admin_init', 'elegance_register_custom_walker');
function elegance_register_custom_walker() {    
    if (class_exists('Walker_Nav_Menu_Edit')) {
        add_filter('wp_edit_nav_menu_walker', 'elegance_edit_nav_menu_walker', 10, 2);
    }
}

function elegance_edit_nav_menu_walker($walker, $menu_id) {
    return 'Elegance_Walker_Nav_Menu_Edit';
}

/**
 * Custom walker class (only define if parent class exists)
 */
add_action('admin_init', 'elegance_define_custom_walker');
function elegance_define_custom_walker() {        
    if (class_exists('Walker_Nav_Menu_Edit')) {
        if (!class_exists('Elegance_Walker_Nav_Menu_Edit')) {
            class Elegance_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {
                
                public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0) {
                    global $_wp_nav_menu_max_depth;
                    $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

                    // Restores the more descriptive, specific name for use within this method.
                    $menu_item = $data_object;                    

                    $indent = ($depth) ? str_repeat("\t", $depth) : '';

                    ob_start();
                    $item_id = esc_attr($menu_item->ID);
                    $removed_args = array(
                        'action',
                        'customlink-tab',
                        'edit-menu-item',
                        'menu-item',
                        'page-tab',
                        '_wpnonce',
                    );

                    $original_title = false;
                    if ('taxonomy' === $menu_item->type) {
                        $original_title = get_term_field('name', $menu_item->object_id, $menu_item->object, 'raw');
                        if (is_wp_error($original_title)) {
                            $original_title = false;
                        }
                    } elseif ('post_type' === $menu_item->type) {
                        $original_object = get_post($menu_item->object_id);
                        if ($original_object) {
                            $original_title = get_the_title($original_object->ID);
                        }
                    } elseif ('post_type_archive' === $menu_item->type) {
                        $original_title = get_post_type_object($menu_item->object)->labels->archives;
                    }

                    $classes = array(
                        'menu-item menu-item-depth-' . $depth,
                        'menu-item-' . esc_attr($menu_item->object),
                        'menu-item-edit-' . ((isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item']) ? 'active' : 'inactive'),
                    );
                    
                    if ($menu_item->type === 'elegance_nav') {
                        $classes[] = 'elegance-protected-item';
                    }

                    $title = $menu_item->title;

                    if (!empty($menu_item->_invalid)) {
                        $classes[] = 'menu-item-invalid';
                        $title = sprintf(__('%s (Invalid)', 'wordpress-theme-elegance'), $menu_item->title);
                    } elseif (isset($menu_item->post_status) && 'draft' === $menu_item->post_status) {
                        $classes[] = 'pending';
                        $title = sprintf(__('%s (Pending)', 'wordpress-theme-elegance'), $menu_item->title);
                    }

                    $title = (!isset($menu_item->label) || '' === $menu_item->label) ? $title : $menu_item->label;

                    $submenu_text = '';
                    if (0 == $depth) {
                        $submenu_text = 'style="display: none;"';
                    }

                    ?>
                    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes); ?>">
                        <div class="menu-item-bar">
                            <div class="menu-item-handle">
                                <label class="item-title" for="edit-menu-item-title-<?php echo $item_id; ?>">
                                    <span class="menu-item-title"><?php echo esc_html($title); ?></span>
                                    <?php if ($menu_item->type === 'elegance_nav') : ?>
                                        <span class="elegance-protected-badge"><?php _e('Protected', 'wordpress-theme-elegance'); ?></span>
                                    <?php endif; ?>
                                    <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e('sub item', 'wordpress-theme-elegance'); ?></span>
                                </label>
                                <span class="item-controls">
                                    <span class="item-type"><?php echo esc_html($menu_item->type_label); ?></span>
                                    <span class="item-order hide-if-js">
                                        <a href="<?php
                                        echo wp_nonce_url(
                                            add_query_arg(
                                                array(
                                                    'action' => 'move-up-menu-item',
                                                    'menu-item' => $item_id,
                                                ),
                                                remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                            ),
                                            'move-menu_item'
                                        );
                                        ?>" class="item-move-up" aria-label="<?php esc_attr_e('Move up', 'wordpress-theme-elegance'); ?>">&#8593;</a>
                                        |
                                        <a href="<?php
                                        echo wp_nonce_url(
                                            add_query_arg(
                                                array(
                                                    'action' => 'move-down-menu-item',
                                                    'menu-item' => $item_id,
                                                ),
                                                remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                            ),
                                            'move-menu_item'
                                        );
                                        ?>" class="item-move-down" aria-label="<?php esc_attr_e('Move down', 'wordpress-theme-elegance'); ?>">&#8595;</a>
                                    </span>
                                    <a class="item-edit" id="edit-<?php echo $item_id; ?>" href="<?php
                                    echo (isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item']) ? admin_url('nav-menus.php') : add_query_arg('edit-menu-item', $item_id, remove_query_arg($removed_args, admin_url('nav-menus.php#menu-item-settings-' . $item_id)));
                                    ?>" aria-label="<?php esc_attr_e('Edit menu item', 'wordpress-theme-elegance'); ?>">
                                        <span class="screen-reader-text"><?php _e('Edit', 'wordpress-theme-elegance'); ?></span>
                                    </a>
                                </span>
                            </div>
                        </div>

                        <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo $item_id; ?>">
                            <?php if ($menu_item->type === 'elegance_nav') : ?>                                
                                <fieldset class="field-move hide-if-no-js description description-wide">
                                    <span class="field-move-visual-label" aria-hidden="true"><?php _e('Move', 'wordpress-theme-elegance'); ?></span>
                                    <button type="button" class="button-link menus-move menus-move-up" data-dir="up"><?php _e('Up one', 'wordpress-theme-elegance'); ?></button>
                                    <button type="button" class="button-link menus-move menus-move-down" data-dir="down"><?php _e('Down one', 'wordpress-theme-elegance'); ?></button>
                                    <button type="button" class="button-link menus-move menus-move-left" data-dir="left"></button>
                                    <button type="button" class="button-link menus-move menus-move-right" data-dir="right"></button>
                                    <button type="button" class="button-link menus-move menus-move-top" data-dir="top"><?php _e('To the top', 'wordpress-theme-elegance'); ?></button>
                                </fieldset>

                                <p class="field-title description description-wide">
                                    <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                                        <?php _e('Navigation Label'); ?><br />
                                        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item->title); ?>" readonly />
                                        <span class="description"><?php _e('This label is controlled by the theme. Use the Customizer to change it.', 'wordpress-theme-elegance'); ?></span>
                                    </label>
                                </p>

                                <p class="field-title-attribute description description-wide">
                                    <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                                        <?php _e('Title Attribute'); ?><br />
                                        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat code edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item->attr_title); ?>" readonly />
                                        <span class="description"><?php _e('This Title Attribute is controlled by the theme.', 'wordpress-theme-elegance'); ?></span>
                                    </label>
                                </p>

                                <p class="field-url description description-wide">
                                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                                        <?php _e('URL'); ?><br />
                                        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item->url); ?>" readonly />
                                        <span class="description"><?php _e('This URL is controlled by the theme.', 'wordpress-theme-elegance'); ?></span>
                                    </label>
                                </p>

                                <p class="field-description description description-wide">
                                    <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                                        <?php _e('Description'); ?><br />
                                        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]" readonly><?php echo esc_html($menu_item->description); ?></textarea>
                                        <span class="description"><?php _e('This is a protected theme navigation item.', 'wordpress-theme-elegance'); ?></span>
                                    </label>
                                </p>

                                <div class="menu-item-actions description-wide submitbox">
                                    <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="#">
                                        <?php _e('Remove'); ?>
                                    </a>
                                    <span class="meta-sep hide-if-no-js"> | </span>
                                    <a class="item-cancel submitcancel hide-if-no-js button-secondary" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url(add_query_arg(array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg($removed_args, admin_url('nav-menus.php')))); ?>#menu-item-settings-<?php echo $item_id; ?>">
                                        <?php _e('Cancel', 'wordpress-theme-elegance'); ?>
                                    </a>
                                    <span class="meta-sep hide-if-no-js"> | </span>
                                    <a href="<?php echo admin_url('customize.php?autofocus[section]=navigation_settings'); ?>" class="button button-primary">
                                        <?php _e('Customize Navigation', 'wordpress-theme-elegance'); ?>
                                    </a>
                                </div>

                            <?php else : ?>                                
                                <?php 
                                // Call parent method for regular menu items
                                parent::start_el($temp_output, $data_object, $depth, $args, $current_object_id);
                                // Extract just the settings part from parent output
                                $settings_start = strpos($temp_output, '<div class="menu-item-settings');
                                if ($settings_start !== false) {
                                    $settings_end = strrpos($temp_output, '</div>');
                                    if ($settings_end !== false) {
                                        $settings_content = substr($temp_output, $settings_start, $settings_end - $settings_start + 6);
                                        // Remove the outer div since we already have it
                                        $settings_content = preg_replace('/^<div[^>]*>/', '', $settings_content);
                                        $settings_content = preg_replace('/<\/div>$/', '', $settings_content);
                                        echo $settings_content;
                                    }
                                }
                                ?>
                            <?php endif; ?>                                                                            
                            <?php if ($menu_item->type === 'elegance_nav') : ?>
                                <input type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
                                <input type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item->object_id); ?>" />
                                <input type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item->object); ?>" />
                                <input type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item->menu_item_parent); ?>" />
                                <input type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item->menu_order); ?>" />
                                <input type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="elegance_nav" />
                            <?php endif; ?>
                        </div><!-- .menu-item-settings -->
                        <ul class="menu-item-transport"></ul>
                    </li>
                    <?php
                    $output .= ob_get_clean();
                }
            }
        }
    }
}

/**
 * Prevent deletion of protected menu items
 */
add_action('wp_ajax_delete-menu-item', 'elegance_prevent_protected_deletion', 5);
function elegance_prevent_protected_deletion() {
    if (isset($_POST['menu-item']) && is_array($_POST['menu-item'])) {
        $menu_item_id = absint($_POST['menu-item']);
        $menu_item = get_post($menu_item_id);
        
        if ($menu_item && get_post_meta($menu_item_id, '_menu_item_type', true) === 'elegance_nav') {
            wp_die(__('This menu item is protected and cannot be deleted.', 'wordpress-theme-elegance'));
        }
    }
}

/**
 * Enqueue admin styles
 */
add_action('admin_enqueue_scripts', 'elegance_admin_menu_styles');
function elegance_admin_menu_styles($hook) {
    if ($hook === 'nav-menus.php') {
        wp_enqueue_style(
            'elegance-admin-menu',
            get_template_directory_uri() . '/css/admin-style.css',
            array(),
            '1.0.0'
        );
    }
}

/**
 * Debug function to check if nav items exist
 */
add_action('admin_notices', 'elegance_debug_nav_items');
function elegance_debug_nav_items() {
    global $pagenow;
    
    if ($pagenow === 'nav-menus.php' && current_user_can('manage_options')) {
        $nav_items = elegance_get_theme_nav_items();
        if (empty($nav_items)) {
            echo '<div class="notice notice-warning"><p>';
            echo __('Elegance Theme: No navigation items available. Check if notices exist and blog page is created.', 'wordpress-theme-elegance');
            echo '</p></div>';
        }
    }
}
