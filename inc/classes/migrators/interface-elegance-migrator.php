<?php
/**
 * Interface for Elegance theme migrators
 * 
 * @package Elegance
 * @since 2.0.0
 */
if (!interface_exists('Elegance_Migrator')) {    
    interface Elegance_Migrator {
        public function get_id(): string;
        public function can_migrate($previous_version): bool;
        public function migrate(): Elegance_Migration_Result;
    }

    class Elegance_Migrator_Id {
        const TESTIMONIALS = 'testimonials';
        const SOCIAL_ICONS = 'social_icons';

        public static function cases(): array {
            $reflection = new ReflectionClass(static::class);
            return array_values($reflection->getConstants());
        }
    }

    class Elegance_Versions {
        const v1_0 = '1.0';
        const v2_0 = '2.0';

        public static function cases(): array {
            $reflection = new ReflectionClass(static::class);
            return array_values($reflection->getConstants());
        }
    }

    class Elegance_Migration_Status {
        const FINISHED = 'finished';
        const PARTIAL = 'partial';
        const FAILED = 'failed';        
        const PENDING = 'pending';

        public static function cases(): array {
            $reflection = new ReflectionClass(static::class);
            return array_values($reflection->getConstants());
        }
    }

    class Elegance_Migration_Result {
        protected $migrator_id;
        protected $status;
        protected $data;

        public function __construct($migrator_id, $status, $data = []) {
            $this->migrator_id = $migrator_id;
            $this->status = $status;
            $this->data = $data;
        }

        public static function failed($migrator_id, $data): static {
            return new static($migrator_id, Elegance_Migration_Status::FAILED, $data);
        }
                
        public function get_migrator_id() {
            return $this->migrator_id;
        }
        
        public function get_status() {
            return $this->status;
        }
        
        public function get_data() {
            return $this->data;
        }
        
        protected function get_data_value($key, $default = null) {
            return $this->data[$key] ?? $default;
        }
    }

    abstract class Elegance_Migration_Data {
        protected $data;

        public function __construct($data = []) {
            $this->data = $data;
        }

        public function get_data() {
            return $this->data;
        }

        public abstract function print_notice();

        protected function get_data_value($key, $default = null) {
            return $this->data[$key] ?? $default;
        }        
    }
}