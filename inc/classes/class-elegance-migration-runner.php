<?php
if (!class_exists('Elegance_Migration_Runner')) {
    class Elegance_Migration_Runner {
        private static $migrators = [];
        private static $initialized = false;

        private static function init() {
            if (self::$initialized) {
                return;
            }

            self::$migrators = [
                new Elegance_Testimonials_Migrator(),
                new Elegance_Social_Icons_Migrator()
            ];

            self::$initialized = true;
        }

        public static function get_migrators(): array {
            self::init();
            return self::$migrators;
        }

        public static function run_migrations(): array {            
            $current_version = Elegance_Migrator_Helpers::get_current_version();
            $previous_version = Elegance_Migrator_Helpers::get_stored_version();

            if (version_compare($current_version, $previous_version, '==')) {
                return [];
            }

            error_log('Running migrations ' . $previous_version . ' -> ' . $current_version);
            $results = [];
            foreach(self::get_migrators() as $migrator) {
                if (!$migrator->can_migrate($previous_version)) {
                    error_log('Skipping migrator ' . $migrator->get_id());
                    continue;
                }
                error_log('Running migration: ' . $migrator->get_id());
                $results[] = self::run_migration($migrator);            
            }
            Elegance_Migrator_Helpers::set_stored_version($current_version);
            Elegance_Migrator_Helpers::clear_notice_flags();
            return array_filter($results);
        }

        public static function set_migration_status_option(string $migrator_id, bool $status) {
            update_option('elegance_'. $migrator_id . '_migrated', $status);            
        }

        public static function is_migrated(string $migrator_id) {            
            return get_option('elegance_' . $migrator_id . '_migrated');
        }

        public static function set_migration_data_option(string $migrator_id, array $data) {
            update_option('elegance_'. $migrator_id . '_migration_data', $data);
        }

        public static function get_migration_option_data_by_id(string $migrator_id): ?Elegance_Migration_Data {
            if (!self::is_migrated($migrator_id)) {
                return null;
            }

            $result = get_option('elegance_'. $migrator_id . '_migration_data');

            if (!$result) {
                return null;
            }

            error_log('Retrieved migration data for ' . $migrator_id . ': ' . print_r($result, true));

            switch($migrator_id) {
                case Elegance_Migrator_Id::TESTIMONIALS:
                    return Elegance_Testimonials_Migration_Data::from_data($result);
                case Elegance_Migrator_Id::SOCIAL_ICONS:
                    return Elegance_Social_Icons_Migration_Data::from_data($result);                    
                default:
                    return null;
            }
        }

        public static function delete_migration_options(string $migrator_id) {
            error_log('Deleting migration options for ' . $migrator_id);
            delete_option('elegance_'. $migrator_id . '_migrated');
            delete_option('elegance_'. $migrator_id . '_migration_data');
        }

        public static function run_migration_by_id(string $migrator_id): ?Elegance_Migration_Result {            
            $migrators = array_filter(self::get_migrators(), function($m) use ($migrator_id) {
                return $m->get_id() === $migrator_id;
            });

            if (empty($migrators)) {
                return null;
            }

            $migrator = array_values($migrators)[0];
            return self::run_migration($migrator);
        }

        public static function clear_migration_options() {
            foreach(self::get_migrators() as $migrator) {
                self::delete_migration_options($migrator->get_id());
            }
        }

        private static function run_migration($migrator): ?Elegance_Migration_Result {
            if(!$migrator) {
                return null;
            }
            try {
                $result = $migrator->migrate();
                self::set_migration_status_option($migrator->get_id(), true);
                self::set_migration_data_option($migrator->get_id(), $result->get_data());
                return $result;
            } catch(Exception $e) {
                error_log('Migration Error for migrator ' . $migrator->get_id() . ': ' . $e->getMessage());                    
                return Elegance_Migration_Result::failed($migrator->get_id(), ['error' => $e->getMessage()]);
            }
        }
    }
}