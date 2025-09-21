<?php
/**
 * Custom Customizer Controls
 */

if (class_exists('WP_Customize_Control')) {

    if (!class_exists('Social_Icons_Repeater_Control')) {
        class Social_Icons_Repeater_Control extends WP_Customize_Control {

            public $type = 'repeater';

            public function render_content() {
                $options = [
                    'Facebook',
                    'Twitter',
                    'LinkedIn',
                    'Instagram',
                    'Behance',
                    'YouTube',
                    'Pinterest',
                    'Snapchat',
                    'GitHub',
                    'Email'
                ];
                ?>
                <style>
                    .social-repeater-list {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    }
            
                    .social-repeater-list li {
                        display: flex;
                        align-items: center;
                        margin-bottom: 10px;
                        padding: 5px;
                        border: 1px solid #ddd;
                        border-radius: 3px;
                        background: #f9f9f9;
                        cursor: move;
                    }
            
                    .social-url,
                    .social-icon {
                        flex: 1;
                        margin-right: 10px;
                    }
            
                    .social-remove {
                        background-color: #d63638;
                        color: white;
                        border: none;
                        padding: 4px 8px;
                        cursor: pointer;
                        border-radius: 3px;
                    }
            
                    .social-remove:hover {
                        background-color: #a82c2d;
                    }
            
                    .ui-sortable-placeholder {
                        border: 1px dashed #ddd;
                        background: #f9f9f9;
                        height: 34px; /* Adjust based on the height of your list items */
                    }
                </style>
                <label>
                    <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                    <ul class="social-repeater-list">
                        <?php                        
                        $social_icons = json_decode($this->value());                        
                        if (!empty($social_icons)) {
                            foreach ($social_icons as $social_icon) {
                                $url = esc_url($social_icon->url);
                                $title = $social_icon->title;
                                ?>
                                <li>
                                    <input type="text" class="social-url" placeholder="URL" value="<?php echo esc_url($url); ?>" />
                                    <select class="social-icon">
                                        <?php foreach ($options as $option): ?>
                                            <option value="<?php echo esc_attr($option); ?>" <?php selected($social_icon->title, $option); ?>>
                                                <?php echo esc_html($option); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" class="social-title" value="<?php echo esc_attr($title); ?>" />
                                    <button type="button" class="social-remove">Remove</button>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                    <button type="button" class="button social-add">Add Social Icon</button>
                    <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr($this->value()); ?>" class="social-repeater-value" />
                </label>
                <script>
                    jQuery(document).ready(function ($) {
                        var iconOptions = '<?php foreach ($options as $option) { echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>'; } ?>';
            
                        function updateRepeaterValues() {
                            var values = [];
                            $('.social-repeater-list li').each(function () {
                                var url = $(this).find('.social-url').val();                                
                                var title = $(this).find('.social-title').val();
                                                                
                                values.push({
                                    'url': url,                                    
                                    'title': title
                                });
                            });
                            
                            $('.social-repeater-value').val(JSON.stringify(values)).trigger('change');
                        }
            
                        $('.social-add').click(function () {
                            $('.social-repeater-list').append('<li><input type="text" class="social-url" placeholder="URL" /><select class="social-icon">' + iconOptions + '</select><input type="hidden" class="social-title" value="" /><button type="button" class="social-remove">Remove</button></li>');
                            updateRepeaterValues();
                        });
            
                        $(document).on('click', '.social-remove', function () {
                            $(this).parent().remove();
                            updateRepeaterValues();
                        });
            
                        $(document).on('input change', '.social-url, .social-icon', function () {
                            updateRepeaterValues();
                        });
                                    
                        $('.social-repeater-list').sortable({
                            placeholder: "ui-sortable-placeholder",
                            update: function () {
                                updateRepeaterValues();
                            }
                        }).disableSelection();
                    });
                </script>
                <?php
            }                                              
        }
    }

    if (!class_exists('Elegance_Export_Control')) {
        class Elegance_Export_Control extends WP_Customize_Control {
            public $type = 'export_sections';
            
            public function render_content() {
                global $wp_customize;
                $sections = $wp_customize->sections();
                                
                unset($sections['customizer_export_import']);
                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                    <div class="export-sections-container">
                        <div class="section-checkboxes">
                            <label class="select-all-label">
                                <input type="checkbox" id="select-all-export" />
                                <strong><?php _e('Select All Sections', 'wordpress-theme-elegance'); ?></strong>
                            </label>
                            <?php foreach ($sections as $section_id => $section) : ?>
                                <label class="section-checkbox-label">
                                    <input type="checkbox" 
                                        class="export-section-checkbox" 
                                        value="<?php echo esc_attr($section_id); ?>"
                                        data-section-title="<?php echo esc_attr($section->title); ?>" />
                                    <?php echo esc_html($section->title); ?>
                                    <small class="section-id">(<?php echo esc_html($section_id); ?>)</small>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="export-actions">
                            <button type="button" class="button button-primary" id="export-selected-sections" disabled>
                                <?php _e('Export Selected Sections', 'wordpress-theme-elegance'); ?>
                            </button>
                            <p class="description">
                                <?php _e('Select which sections to export, then click the export button.', 'wordpress-theme-elegance'); ?>
                            </p>
                        </div>
                    </div>
                </label>
                <?php
            }
        }
    }

    if (!class_exists('Elegance_Import_Control')) {
        class Elegance_Import_Control extends WP_Customize_Control {
            public $type = 'import_sections';
            
            public function render_content() {
                ?>
                <label>
                    <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                    <div class="import-sections-container">
                        <div class="file-upload-section">
                            <input type="file" id="import-file" accept=".json" />
                            <p class="description">
                                <?php _e('Upload JSON file to preview available sections.', 'wordpress-theme-elegance'); ?>
                            </p>
                        </div>
                        
                        <div id="import-sections-preview" style="display: none;">
                            <h4><?php _e('Available Sections in File:', 'wordpress-theme-elegance'); ?></h4>
                            <div class="import-section-checkboxes">
                                <label class="select-all-label">
                                    <input type="checkbox" id="select-all-import" />
                                    <strong><?php _e('Select All Sections', 'wordpress-theme-elegance'); ?></strong>
                                </label>
                                <div id="import-sections-list">
                                    <!-- Dynamically populated via JavaScript -->
                                </div>
                            </div>
                            <div class="import-actions">
                                <button type="button" class="button button-primary" id="import-selected-sections" disabled>
                                    <?php _e('Import Selected Sections', 'wordpress-theme-elegance'); ?>
                                </button>
                                <p class="description">
                                    <?php _e('Select which sections to import and apply to your theme.', 'wordpress-theme-elegance'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </label>
                <?php
            }
        }
    }
}


