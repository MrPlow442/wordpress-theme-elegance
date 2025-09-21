<div id="social-icons">
    <div class="text-right">
        <ul class="social-icons">
            <?php
            $icons = [
                'Facebook' => 'fa-brands fa-facebook',
                'Twitter' => 'fa-brands fa-x-twitter',
                'LinkedIn' => 'fa-brands fa-linkedin',
                'Instagram' => 'fa-brands fa-instagram',
                'Behance' => 'fa-brands fa-behance',
                'YouTube' => 'fa-brands fa-youtube',
                'Pinterest' => 'fa-brands fa-pinterest',
                'Snapchat' => 'fa-brands fa-snapchat',
                'GitHub' => 'fa-brands fa-github',
                'Email' => 'fas fa-envelope'
            ];
            $social_icons = json_decode(get_theme_mod('social_icons'));
            if (!empty($social_icons)) {
                foreach ($social_icons as $social_icon) {
                    $title = !empty($social_icon->title) ? esc_attr($social_icon->title) : '';
                    $url = $social_icon->title === 'Email' ? 'mailto:' . esc_attr($social_icon->url) : esc_url($social_icon->url);
                    echo '<li><a href="' . $url . '" title="' . $title . '"><i class="' . esc_attr($icons[$social_icon->title]) . '"></i></a></li>';
                }
            }
            ?>
        </ul>
    </div>
</div>