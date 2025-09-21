<?php
if (!class_exists('Elegance_Migrator_Helpers')) {
    class Elegance_Migrator_Helpers {        
        private const VERSION_OPTION = 'elegance_theme_version';
        private const MIGRATION_NOTICES_FLAG = 'elegance_displayed_migration_notices';

        public static function get_current_version() {
            return wp_get_theme()->get('Version');
        }

        public static function get_stored_version() {
            return get_option(self::VERSION_OPTION, Elegance_Versions::v1_0);
        }

        public static function set_stored_version($previous_version) {
            return update_option(self::VERSION_OPTION, $previous_version);
        }

        public static function flag_notices_as_displayed($notices) {
            set_transient(self::MIGRATION_NOTICES_FLAG, $notices);
        }

        public static function get_notices_flagged_as_displayed() {
            return get_transient(self::MIGRATION_NOTICES_FLAG);
        }

        public static function clear_notice_flags() {
            delete_transient(self::MIGRATION_NOTICES_FLAG);
        }

        public static function print_migration_notices() {
            $displayed_notices = self::get_notices_flagged_as_displayed();
            if (!is_array($displayed_notices)) {
                $displayed_notices = [];
            }
            foreach (Elegance_Migration_Runner::get_migrators() as $migrator) {
                $migrator_id = $migrator->get_id();
                if (in_array($migrator_id, $displayed_notices)) {
                    continue;
                }

                if (!Elegance_Migration_Runner::is_migrated($migrator_id)) {
                    continue;
                }                

                $result = Elegance_Migration_Runner::get_migration_option_data_by_id($migrator_id);
                if (!$result) {
                    continue;
                }

                $result->print_notice();
                $displayed_notices[] = $migrator_id;
            }
            self::flag_notices_as_displayed($displayed_notices);
        }
    }
}