<?php
/**
 * Admin-only functionality for WordPress Theme Elegance
 * 
 * This file contains all functions and classes that should only
 * be loaded in the WordPress admin context.
 */


if (!defined('ABSPATH')) {
    exit;
}


if (!is_admin()) {
    return;
}


add_action('admin_init', 'elegance_load_nav_menu_dependencies');
function elegance_load_nav_menu_dependencies() {            
    require_once ABSPATH . 'wp-admin/includes/nav-menu.php';    
}


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
add_action( 'admin_head-nav-menus.php', 'elegance_register_custom_nav_meta_box' );

function elegance_nav_menu_meta_box() {
    global $_nav_menu_placeholder, $nav_menu_selected_id;
    $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
    
    $menu_id = isset($_GET['menu']) ? absint($_GET['menu']) : 0;
    $existing_items = array();
    
    if ($menu_id) {
        $menu_items = wp_get_nav_menu_items($menu_id);
        if ($menu_items) {
            foreach ($menu_items as $item) {
                $elegance_id = get_post_meta($item->ID, ELEGANCE_NAV_ID_KEY, true);
                if ($elegance_id) {
                    $existing_items[] = $elegance_id;
                }
            }
        }
    }    
    
    $nav_items = array_filter(elegance_get_theme_nav_items(), function($item) use ($existing_items) {
        return !in_array($item['id'], $existing_items, true);
    });
    
    if (empty($nav_items)) {
        echo '<p>' . __('No navigation items available.', 'wordpress-theme-elegance') . '</p>';
        return;
    }
    ?>
    <div class="posttypediv" id="elegance-nav-items">
        <div id="tabs-panel-elegance-nav-items" class="tabs-panel tabs-panel-active">
            <ul id="elegance-nav-items-checklist" class="categorychecklist form-no-clear">
                <?php foreach ($nav_items as $item) : ?>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" 
                                   name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" 
                                   value="0" /> 
                            <?php echo esc_html($item['label']); ?>
                            <span class="elegance-protected-item"><?php _e('(Protected)', 'wordpress-theme-elegance'); ?></span>
                        </label>
                                                
                        <input type="hidden" class="menu-item-type" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" 
                               value="custom" />
                        <input type="hidden" class="menu-item-title" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" 
                               value="<?php echo esc_attr($item['label']); ?>" />
                        <input type="hidden" class="menu-item-url" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" 
                               value="<?php echo esc_url($item['value']); ?>" />
                        <input type="hidden" class="menu-item-object" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object]" 
                               value="custom" />                                            
                        <input type="hidden" class="menu-item-attr-title" 
                               name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-attr-title]" 
                               value="<?php echo esc_attr($item['id']); ?>" />                        
                    </li>
                    <?php $_nav_menu_placeholder--; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <p class="button-controls wp-clearfix">
            <span class="add-to-menu">
                <input type="submit" class="button-secondary submit-add-to-menu right" 
                       value="<?php esc_attr_e('Add to Menu'); ?>" 
                       name="add-elegance-nav-item" id="submit-elegance-nav-items" />
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}

function elegance_save_menu_item_meta($menu_id, $menu_item_db_id, $args) {        
    $id = sanitize_text_field($args['menu-item-attr-title']);

    // check if stats with elegance_nav
    if (strpos($id, 'elegance_nav') !== 0) {        
        return;
    }

    $theme_nav_items = elegance_get_theme_nav_items();        

    $item_data = null;
    foreach ($theme_nav_items as $item) {
        if (isset($item['id']) && $item['id'] === $id) {
            $item_data = $item;
            break;
        }
    }

    if (!$item_data) {        
        return;
    }
    
    if (!$item_data || !is_array($item_data)) {        
        return;
    }


    $elegance_type = $item_data['type'];
    $elegance_id = $id;            
    update_post_meta($menu_item_db_id, ELEGANCE_NAV_META_KEY, $elegance_type);
    update_post_meta($menu_item_db_id, ELEGANCE_NAV_ID_KEY, $elegance_id);    
}
add_action('wp_update_nav_menu_item', 'elegance_save_menu_item_meta', 10, 3);



function elegance_setup_nav_menu_item($menu_item) {            
    $elegance_type = get_post_meta($menu_item->ID, ELEGANCE_NAV_META_KEY, true);
    $elegance_id = get_post_meta($menu_item->ID, ELEGANCE_NAV_ID_KEY, true);
    
    if ($elegance_type && $elegance_id) {        
        $menu_item->elegance_nav_type = $elegance_type;
        $menu_item->elegance_nav_id = $elegance_id;
        $menu_item->type_label = __('Theme Navigation', 'wordpress-theme-elegance');
                
        $nav_items = elegance_get_theme_nav_items();
        foreach ($nav_items as $item) {
            if ($item['id'] === $elegance_id) {
                $menu_item->title = $item['label'];
                $menu_item->url = $item['value'];
                break;
            }
        }
    }
    
    return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'elegance_setup_nav_menu_item');

function elegance_nav_menu_custom_fields($item_id, $item, $depth, $args) {    
    $elegance_type = get_post_meta($item_id, ELEGANCE_NAV_META_KEY, true);    
    if ($elegance_type) {
        ?>
        <div class="elegance-nav-notice">
            <p class="description">
                <strong><?php _e('Theme Navigation Item', 'wordpress-theme-elegance'); ?></strong><br>
                <?php _e('This item is managed by your theme and cannot be edited directly.', 'wordpress-theme-elegance'); ?>
                <a href="<?php echo admin_url('customize.php?autofocus[section]=navigation_settings'); ?>" class="button button-small">
                    <?php _e('Customize'); ?>
                </a>
            </p>
        </div>
        <script>             
        (function () {

            function makeReadOnly() {
                var menuItem = document.getElementById('menu-item-<?php echo $item_id; ?>');                
                if (menuItem) {                    
                    menuItem.classList.add('elegance-protected-item');                    
                                        
                    var inputs = menuItem.querySelectorAll('input[type="text"], textarea');
                    inputs.forEach(function(input) {                        
                        if (!input.classList.contains('menu-item-data-position')) {
                            input.readOnly = true;
                        }
                    });
                                             
                    var title = menuItem.querySelector('.menu-item-title');                    
                    if (title && !title.querySelector('.elegance-protected-badge')) {
                        var badge = document.createElement('span');
                        badge.className = 'elegance-protected-badge';
                        badge.textContent = '<?php _e('Protected', 'wordpress-theme-elegance'); ?>';
                        title.appendChild(badge);                        
                    }
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', makeReadOnly);
            } else {
                makeReadOnly();
            }
        })();    
        </script>
        <?php
    }
}
add_action('wp_nav_menu_item_custom_fields', 'elegance_nav_menu_custom_fields', 10, 4);


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
add_action('admin_enqueue_scripts', 'elegance_admin_menu_styles');

function elegance_sync_menu_items() {
    $menus = wp_get_nav_menus();
    foreach ($menus as $menu) {
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        if (!$menu_items) continue;
        
        foreach ($menu_items as $item) {
            $elegance_id = get_post_meta($item->ID, ELEGANCE_NAV_ID_KEY, true);
            if ($elegance_id) {                
                $nav_items = elegance_get_theme_nav_items();
                foreach ($nav_items as $theme_item) {
                    if ($theme_item['id'] === $elegance_id) {
                        wp_update_post(array(
                            'ID' => $item->ID,
                            'post_title' => $theme_item['label']
                        ));
                        update_post_meta($item->ID, '_menu_item_url', $theme_item['value']);
                        break;
                    }
                }
            }
        }
    }
}
add_action('customize_save_after', 'elegance_sync_menu_items');