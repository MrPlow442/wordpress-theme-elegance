<?php
if (!class_exists('Elegance_Social_Icons_Migrator')) {
    class Elegance_Social_Icons_Migrator implements Elegance_Migrator {
        const EMAIL_PATTERN = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

        private const TITLE_BY_DOMAIN = [
            'facebook'  => 'Facebook',
            'x'         => 'Twitter',
            'linkedin'  => 'LinkedIn',
            'instagram' => 'Instagram',
            'behance'   => 'Behance',
            'youtube'   => 'YouTube',
            'pinterest' => 'Pinterest',
            'snapchat'  => 'Snapchat',
            'github'    => 'GitHub'
        ];

        public function get_id(): string {
            return Elegance_Migrator_Id::SOCIAL_ICONS;
        }

        public function can_migrate($previous_version): bool {            
            return !get_option('elegance_'. $this->get_id() . '_migrated', false)
                    && version_compare($previous_version, Elegance_Versions::v2_0, '<');
        }

        public function migrate(): Elegance_Migration_Result {
            $social_icons = json_decode(get_theme_mod('social_icons'));            

            if ($social_icons === null || empty($social_icons)) {
                return $this->create_result(Elegance_Migration_Status::FINISHED, $this->create_data(0, ['error' => 'No social icons found']));
            }

            $migrated_count = 0;
            $errors = [];
            foreach ($social_icons as $social_icon) {                
                $url = $social_icon->url;
                if ($this->is_email($url)) {
                    $social_icon->url = $this->fix_email($url);
                    $social_icon->title = 'Email';
                    unset($social_icon->icon);
                    ++$migrated_count;
                    continue;
                }

                $title = $this->get_title_by_domain($url);
                if ($title === null || empty($title)) {
                    $errors[] = 'No title found for url ' . $social_icon->url;
                    continue;
                }

                $social_icon->title = $title;
                unset($social_icon->icon);
            }            
            set_theme_mod('social_icons', json_encode($social_icons));
            $status = empty($errors) ? Elegance_Migration_Status::FINISHED : Elegance_Migration_Status::PARTIAL;                        
            return $this->create_result($status, $this->create_data($migrated_count, $errors));
        }

        function get_title_by_domain($url): ?string {
            foreach (self::TITLE_BY_DOMAIN as $domain => $title) {
                if (strpos($url, $domain) === false) {
                    continue;
                }
                return $title;
            }
            return null;
        }
        
        function is_email($url): bool {            
            return preg_match(self::EMAIL_PATTERN, $url) === 1;
        }

        function fix_email($url) {
            if (preg_match(self::EMAIL_PATTERN, $url, $matches)) {
                $email = $matches[0];
                $cleaned_email = preg_replace('/^(mailto:|https?:\/\/|ftp:\/\/)+/i', '', $email);                
                return $cleaned_email;
            }        
            return $url;
        }

        private function create_result(string $status, ?array $data = []): Elegance_Migration_Result {
            return new Elegance_Migration_Result($this->get_id(), $status, $data);
        }

        private function create_data(int $migration_count = 0, array $errors = []): array {
            return [
                Elegance_Social_Icons_Migration_Data::KEY_MIGRATION_COUNT => $migration_count,
                Elegance_Social_Icons_Migration_Data::KEY_MIGRATION_ERRORS => $errors
            ];
        }
    }

    class Elegance_Social_Icons_Migration_Data extends Elegance_Migration_Data {
        public const KEY_MIGRATION_COUNT = 'migration_count';
        public const KEY_MIGRATION_ERRORS = 'migration_errors';

        public static function from_data(array $data): static {
            return new static($data);
        }

        public function get_migration_count(): int {
            return (int) $this->get_data_value(self::KEY_MIGRATION_COUNT, 0);
        }
        
        public function get_migration_errors(): array {
            return (array) $this->get_data_value(self::KEY_MIGRATION_ERRORS, []);
        }

        public function print_notice() {
            $count = $this->get_migration_count();
            $errors = $this->get_migration_errors();
            if ($count > 0 || !empty($errors)) {
                $class = empty($errors) ? 'notice-success' : 'notice-warning';
                echo '<div class="notice ' . esc_attr($class) . ' is-dismissible"><p>';
                
                if ($count > 0) {
                    printf(                        
                        __('Social icons migration completed! %d social icons were migrated.', 'wordpress-theme-elegance'), 
                        $count
                    );
                }
                
                if (!empty($errors)) {
                    echo '<br><strong>' . __('Errors encountered:', 'wordpress-theme-elegance') . '</strong><br>';
                    foreach ($errors as $error) {
                        echo '• ' . esc_html($error) . '<br>';
                    }
                }
                echo '</p></div>';                            
            }
        }
    }
}