<?php
if (class_exists('WP_Customize_Control')) {

    class Social_Icons_Repeater_Control extends WP_Customize_Control {

        public $type = 'repeater';

        public function render_content() {
            $icons = [
                'fa-facebook' => 'Facebook',
                'fa-twitter' => 'Twitter',
                'fa-linkedin' => 'LinkedIn',
                'fa-instagram' => 'Instagram',
                'fa-behance' => 'Behance',
                'fa-youtube' => 'YouTube',
                'fa-pinterest' => 'Pinterest',
                'fa-snapchat' => 'Snapchat',
                'fa-github' => 'GitHub',
                'fa-envelope' => 'Email'
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
                            $url = $social_icon->icon === 'fa-envelope' ? 'mailto:' . esc_url($social_icon->url) : esc_url($social_icon->url);
                            $title = isset($icons[$social_icon->icon]) ? $icons[$social_icon->icon] : '';
                            ?>
                            <li>
                                <input type="text" class="social-url" placeholder="URL" value="<?php echo esc_url($url); ?>" />
                                <select class="social-icon">
                                    <?php foreach ($icons as $icon => $label): ?>
                                        <option value="<?php echo esc_attr($icon); ?>" <?php selected($social_icon->icon, $icon); ?>>
                                            <?php echo esc_html($label); ?>
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
                    var iconOptions = '<?php foreach ($icons as $icon => $label) { echo '<option value="' . esc_attr($icon) . '">' . esc_html($label) . '</option>'; } ?>';
        
                    function updateRepeaterValues() {
                        var values = [];
                        $('.social-repeater-list li').each(function () {
                            var url = $(this).find('.social-url').val();
                            var icon = $(this).find('.social-icon').val();
                            var title = $(this).find('.social-title').val();
                            // Set URL to mailto: if the icon is fa-envelope
                            if (icon === 'fa-envelope' && !url.startsWith('mailto:')) {
                                url = 'mailto:' + url;
                            }
                            values.push({
                                'url': url,
                                'icon': icon,
                                'title': title
                            });
                        });
                        console.log('UPDATING REPEATER VALUES: ', JSON.stringify(values));
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
        
                    // Initialize jQuery UI Sortable
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
