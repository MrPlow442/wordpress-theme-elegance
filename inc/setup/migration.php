<?php
if (!function_exists('elegance_run_migrations_after_init')) {
    function elegance_run_migrations_after_init() {
        Elegance_Migration_Runner::run_migrations();
    }
    add_action('after_setup_theme', 'elegance_run_migrations_after_init');
}

if (!function_exists('elegance_migration_notice')) {
    function elegance_migration_notice() {
        Elegance_Migrator_Helpers::print_migration_notices();
    }
    add_action('admin_notices', 'elegance_migration_notice');
}

if (!function_exists('elegance_add_migration_admin_page')) {
    function elegance_add_migration_admin_page(): void {
        add_management_page(
            __('Elegance Migration Status', 'wordpress-theme-elegance'),
            __('Elegance Migration Status', 'wordpress-theme-elegance'),
            'manage_options',
            'elegance-migration-status',
            'elegance_migration_status_page'
        );
    }
    add_action('admin_menu', 'elegance_add_migration_admin_page');
}

if (!function_exists('elegance_migration_status_page')) {
    function elegance_migration_status_page(): void {
        $is_dev = Elegance_Helpers::is_dev();
        if ($is_dev && isset($_POST['action']) && check_admin_referer('elegance_migration_status')) {
            elegance_handle_migration_actions();
        }

        $migrators = Elegance_Migration_Runner::get_migrators();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Migration Status', 'wordpress-theme-elegance'); ?></h1>

            <?php if ($is_dev): ?>
                <div class="notice notice-warning">
                    <p><strong><?php echo esc_html__('Development Mode:', 'wordpress-theme-elegance'); ?></strong> 
                       <?php echo esc_html__('Testing controls are available. Use with caution.', 'wordpress-theme-elegance'); ?>
                    </p>
                </div>

                <?php elegance_render_migration_dev_controls(); ?>

            <?php else: ?>
                <div class="notice notice-info">
                    <p><?php echo esc_html__('This page shows the status of theme migrations that have been performed on your site.', 'wordpress-theme-elegance'); ?></p>
                </div>
            <?php endif; ?>

            <?php elegance_render_migration_status_table($migrators, $is_dev); ?>
        </div>
        <?php
    }
}

if (!function_exists('elegance_render_migration_dev_controls')) {    
    function elegance_render_migration_dev_controls(): void {
        ?>
        <div class="card">
            <h2><?php echo esc_html__('Development Controls', 'wordpress-theme-elegance'); ?></h2>
            
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">                
                <div>
                    <form method="post">
                        <?php wp_nonce_field('elegance_migration_status'); ?>
                        <input type="hidden" name="action" value="run_all">
                        <p><?php echo esc_html__('Run all available migrations.', 'wordpress-theme-elegance'); ?></p>
                        <p class="submit">
                            <input type="submit" class="button button-primary" 
                                   value="<?php echo esc_attr__('Run All Migrations', 'wordpress-theme-elegance'); ?>"
                                   onclick="return confirm('<?php echo esc_js(__('Are you sure you want to run all migrations?', 'wordpress-theme-elegance')); ?>')">
                        </p>
                    </form>
                </div>
                
                <div>
                    <form method="post">
                        <?php wp_nonce_field('elegance_migration_status'); ?>
                        <input type="hidden" name="action" value="reset_all">
                        <p><?php echo esc_html__('Reset all migration statuses.', 'wordpress-theme-elegance'); ?></p>
                        <p class="submit">
                            <input type="submit" class="button button-secondary" 
                                   value="<?php echo esc_attr__('Reset All Statuses', 'wordpress-theme-elegance'); ?>"
                                   onclick="return confirm('<?php echo esc_js(__('This will reset all migration flags. Are you sure?', 'wordpress-theme-elegance')); ?>')">
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}

if (!function_exists('elegance_render_migration_status_table')) {    
    function elegance_render_migration_status_table(array $migrators, bool $is_dev): void {
        ?>
        <div class="card">
            <h2><?php echo esc_html__('Migration Status', 'wordpress-theme-elegance'); ?></h2>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Migration Type', 'wordpress-theme-elegance'); ?></th>
                        <th><?php echo esc_html__('ID', 'wordpress-theme-elegance'); ?></th>
                        <th><?php echo esc_html__('Status', 'wordpress-theme-elegance'); ?></th>                        
                        <th><?php echo esc_html__('Details', 'wordpress-theme-elegance'); ?></th>
                        <?php if ($is_dev): ?>
                            <th><?php echo esc_html__('Dev Actions', 'wordpress-theme-elegance'); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($migrators as $migrator): 
                        $is_migrated = Elegance_Migration_Runner::is_migrated($migrator->get_id());                        
                        $migration_data = Elegance_Migration_Runner::get_migration_option_data_by_id($migrator->get_id());
                        error_log('Migration Data for ' . $migrator->get_id() . ': ' . print_r($migration_data, true));
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html(get_class($migrator)); ?></strong></td>
                            <td><code><?php echo esc_html($migrator->get_id()); ?></code></td>
                            <td>
                                <?php if ($is_migrated): ?>
                                    <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                    <span style="color: #46b450;"><?php echo esc_html__('Completed', 'wordpress-theme-elegance'); ?></span>
                                <?php else: ?>
                                    <span class="dashicons dashicons-clock" style="color: #999;"></span>
                                    <span style="color: #999;"><?php echo esc_html__('Pending', 'wordpress-theme-elegance'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($migration_data): ?>
                                    <?php elegance_render_migration_summary($migration_data); ?>
                                <?php else: ?>
                                    <em><?php echo esc_html__('No data available', 'wordpress-theme-elegance'); ?></em>
                                <?php endif; ?>
                            </td>
                            <?php if ($is_dev): ?>
                                <td>
                                    <?php elegance_render_migration_dev_actions($migrator, $is_migrated); ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

if (!function_exists('elegance_render_migration_summary')) {
    function elegance_render_migration_summary(Elegance_Migration_Data $migration_data): void {
        if($migration_data instanceof Elegance_Testimonials_Migration_Data)  {
            $count = $migration_data->get_migration_count();
            $errors = $migration_data->get_migration_errors();
            
            if ($count > 0) {
                echo '<span class="dashicons dashicons-migrate" style="color: #46b450;"></span> ';
                printf(                        
                    __('%d testimonials migrated', 'wordpress-theme-elegance'),
                    $count
                );
            }
            
            if (!empty($errors)) {
                echo '<br><span class="dashicons dashicons-warning" style="color: #d63638;"></span> ';
                printf(
                    __('%d errors occurred', 'wordpress-theme-elegance'),
                    count($errors)
                );
                                    
                if (isset($errors[0])) {
                    echo '<br><small><em>' . esc_html(wp_trim_words($errors[0], 10, '...')) . '</em></small>';
                }
            }
        } else {
            echo esc_html__('Migration completed', 'wordpress-theme-elegance');
        }
    }
}

if (!function_exists('elegance_render_migration_dev_actions')) {
    function elegance_render_migration_dev_actions(Elegance_Migrator $migrator, bool $is_migrated): void {
        ?>
        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
            <form method="post" style="display: inline;">
                <?php wp_nonce_field('elegance_migration_status'); ?>
                <input type="hidden" name="action" value="run_single">
                <input type="hidden" name="migrator_id" value="<?php echo esc_attr($migrator->get_id()); ?>">
                <input type="submit" class="button button-small button-primary" 
                       value="<?php echo esc_attr__('Run', 'wordpress-theme-elegance'); ?>"
                       onclick="return confirm('<?php echo esc_js(__('Run this migration?', 'wordpress-theme-elegance')); ?>')">
            </form>
            
            <?php if ($is_migrated): ?>                
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('elegance_migration_status'); ?>
                    <input type="hidden" name="action" value="reset_single">
                    <input type="hidden" name="migrator_id" value="<?php echo esc_attr($migrator->get_id()); ?>">
                    <input type="submit" class="button button-small button-secondary" 
                           value="<?php echo esc_attr__('Reset', 'wordpress-theme-elegance'); ?>"
                           onclick="return confirm('<?php echo esc_js(__('Reset migration status?', 'wordpress-theme-elegance')); ?>')">
                </form>
                                
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('elegance_migration_status'); ?>
                    <input type="hidden" name="action" value="view_data">
                    <input type="hidden" name="migrator_id" value="<?php echo esc_attr($migrator->get_id()); ?>">
                    <input type="submit" class="button button-small button-secondary" 
                           value="<?php echo esc_attr__('Details', 'wordpress-theme-elegance'); ?>">
                </form>
            <?php endif; ?>
        </div>
        <?php
    }
}

if (!function_exists('elegance_handle_migration_actions')) {
    function elegance_handle_migration_actions() {
       $action = sanitize_text_field($_POST['action']);
        $migrator_id = isset($_POST['migrator_id']) ? 
            sanitize_text_field($_POST['migrator_id']) : 
            null;

        switch ($action) {
            case 'run_all':
                $results = Elegance_Migration_Runner::run_migrations();
                elegance_display_migration_results($results);
                break;

            case 'run_single':
                if ($migrator_id) {
                    $result = Elegance_Migration_Runner::run_migration_by_id($migrator_id);
                    if ($result) {
                        elegance_display_migration_results([$result]);
                    } else {
                        echo '<div class="notice notice-error"><p>' . 
                             esc_html__('Migration not found or failed to run.', 'wordpress-theme-elegance') . 
                             '</p></div>';
                    }
                }
                break;

            case 'reset_single':
                if ($migrator_id) {
                    Elegance_Migration_Runner::delete_migration_options($migrator_id);
                    echo '<div class="notice notice-success"><p>' . 
                         sprintf(
                             __('Migration status reset for: %s', 'wordpress-theme-elegance'), 
                             esc_html($migrator_id)
                         ) . 
                         '</p></div>';
                }
                break;

            case 'reset_all':
                Elegance_Migration_Runner::clear_migration_options();
                echo '<div class="notice notice-success"><p>' . 
                     esc_html__('All migration statuses have been reset.', 'wordpress-theme-elegance') . 
                     '</p></div>';
                break;

            case 'view_data':
                if ($migrator_id) {
                    elegance_display_migration_data($migrator_id);
                }
                break;
        }
    }
}

if (!function_exists('elegance_display_migration_data')) {
    function elegance_display_migration_data(string $migrator_id): void {
        $migration_data = Elegance_Migration_Runner::get_migration_option_data_by_id($migrator_id);

        if (!$migration_data) {
            echo '<div class="notice notice-info"><p>' .
                esc_html__('No migration data found for this migrator.', 'wordpress-theme-elegance') .
                '</p></div>';
            return;
        }

        echo '<div class="card">';
        echo '<h3>' . sprintf(
            __('Migration Data for: %s', 'wordpress-theme-elegance'),
            esc_html($migrator_id)
        ) . '</h3>';

        echo '<pre>' . esc_html(print_r($migration_data->get_data(), true)) . '</pre>';

        echo '</div>';
    }
}

if (!function_exists('elegance_display_migration_results')) {
    function elegance_display_migration_results(array $results): void {
        if (empty($results)) {
            echo '<div class="notice notice-info"><p>' .
                esc_html__('No migration results to display.', 'wordpress-theme-elegance') .
                '</p></div>';
            return;
        }

        foreach ($results as $result) {
            $status_class = '';
            switch ($result->get_status()) {
                case Elegance_Migration_Status::FINISHED:
                    $status_class = 'notice-success';
                    break;
                case Elegance_Migration_Status::PARTIAL:
                    $status_class = 'notice-warning';
                    break;
                case Elegance_Migration_Status::FAILED:
                    $status_class = 'notice-error';
                    break;
                default:
                    $status_class = 'notice-info';
                    break;
            }

            echo '<div class="notice ' . esc_attr($status_class) . '"><p>';
            echo '<strong>' . esc_html($result->get_migrator_id()) . ':</strong> ';
            echo esc_html(ucfirst($result->get_status()));

            $data = $result->get_data();
            if (!empty($data['message'])) {
                echo ' - ' . esc_html($data['message']);
            }
            if (!empty($data['error'])) {
                echo ' - <strong>Error:</strong> ' . esc_html($data['error']);
            }

            if (
                $result->get_migrator_id() === Elegance_Migrator_Id::TESTIMONIALS &&
                isset($data[Elegance_Testimonials_Migration_Data::KEY_MIGRATION_COUNT])
            ) {
                $count = $data[Elegance_Testimonials_Migration_Data::KEY_MIGRATION_COUNT];
                if ($count > 0) {
                    echo ' - ' . sprintf(
                        __('%d items migrated', 'wordpress-theme-elegance'),
                        $count
                    );
                }
            }

            //TODO: Dedupe
            if (
                $result->get_migrator_id() === Elegance_Migrator_Id::SOCIAL_ICONS &&
                isset($data[Elegance_Social_Icons_Migration_Data::KEY_MIGRATION_COUNT])
            ) {
                $count = $data[Elegance_Social_Icons_Migration_Data::KEY_MIGRATION_COUNT];
                if ($count > 0) {
                    echo ' - ' . sprintf(
                        __('%d items migrated', 'wordpress-theme-elegance'),
                        $count
                    );
                }
            }

            echo '</p></div>';
        }
    }
}
