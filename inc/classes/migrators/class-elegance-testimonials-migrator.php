<?php
if (!class_exists('Elegance_Testimonials_Migrator')) {
    class Elegance_Testimonials_Migrator implements Elegance_Migrator {
        public function get_id(): string {
            return Elegance_Migrator_Id::TESTIMONIALS;
        }

        public function can_migrate($previous_version): bool {            
            return !get_option('elegance_'. $this->get_id() . '_migrated', false)
                    && version_compare($previous_version, Elegance_Versions::v2_0, '<');
        }
        
        public function migrate(): Elegance_Migration_Result {
            $testimonials_page = $this->find_testimonials_page();
            if (!$testimonials_page) {
                return $this->create_result(Elegance_Migration_Status::FINISHED, $this->create_data(0, ['error' => 'No testimonials page found']));
            }

            $migrated_count = 0;
            $errors = [];
            try {
                $blocks = parse_blocks($testimonials_page->post_content);
                $testimonials_blocks = $this->extract_testimonial_blocks($blocks);

                if (empty($testimonials_blocks)) {                    
                    return $this->create_result(Elegance_Migration_Status::FINISHED, $this->create_data($migrated_count, $errors));
                }

                foreach ($testimonials_blocks as $testimonial_block) {
                    $result = $this->create_testimonial_from_block($testimonial_block);

                    if (is_wp_error($result)) {
                        $errors[] = $result->get_error_message();                        
                    } else {
                        ++$migrated_count;
                    }
                }

                wp_update_post([
                    'ID' => $testimonials_page->ID,
                    'post_status' => 'draft'
                ]);

                $this->remove_legacy_menu_item($testimonials_page->ID);            
                $this->add_theme_testimonials_menu_item();
                
                $status = empty($errors) ? Elegance_Migration_Status::FINISHED : Elegance_Migration_Status::PARTIAL;                
                return $this->create_result($status, $this->create_data($migrated_count, $errors));
            } catch (Exception $e) {
                error_log('Testimonial Migration Error: ' . $e->getMessage());
                return $this->create_result(Elegance_Migration_Status::FAILED, $this->create_data(0, ['error' => $e->getMessage()]));                
            }
        }

        private function find_testimonials_page() {            
            $all_pages = get_posts([
                'post_type' => 'page',
                'post_status' => 'publish',
                'numberposts' => -1
            ]);

            foreach($all_pages as $page) {
                if ($this->page_contains_testimonials($page)) {
                    return $page;
                }
            }
        }        

        private function page_contains_testimonials($page) {
            return strpos($page->post_content, 'wp:elegance-theme/testimonials-block') !== false ||
               strpos($page->post_content, 'wp:elegance-theme/testimonial-item-block') !== false;
        }

        private function extract_testimonial_blocks($blocks) {
            $testimonial_items = [];
        
            foreach ($blocks as $block) {
                if ($block['blockName'] === 'elegance-theme/testimonial-item-block') {
                    $testimonial_items[] = $block;
                }

                if (!empty($block['innerBlocks'])) {
                    $nested_items = $this->extract_testimonial_blocks($block['innerBlocks']);
                    $testimonial_items = array_merge($testimonial_items, $nested_items);
                }
            }                    
            return $testimonial_items;
        }

        private function create_testimonial_from_block($block) {
            $innerHTML = $block['innerHTML'] ?? '';
            $attrs = $block['attrs'] ?? [];
            
            if (empty($innerHTML)) {
                return new WP_Error('empty_content', 'Block content is empty');
            }
            
            $testimonial_data = $this->parse_testimonial_html($innerHTML, $attrs);
            
            if (empty($testimonial_data['title']) && empty($testimonial_data['content'])) {
                return new WP_Error('no_data', 'No testimonial data found in block');
            }
            
            $post_data = array(
                'post_type' => 'testimonial',
                'post_status' => 'publish',
                'post_title' => $testimonial_data['title'] ?: 'Anonymous',
                'post_content' => $testimonial_data['content'] ?: '',
            );

            $post_id = wp_insert_post($post_data);
            
            if (is_wp_error($post_id)) {
                return $post_id;
            }
            
            if (!empty($testimonial_data['image_url'])) {
                $attachment_id = $this->get_attachment_id_from_url($testimonial_data['image_url']);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
            
            if (!empty($testimonial_data['client_url'])) {
                update_post_meta($post_id, 'client_url', sanitize_url($testimonial_data['client_url']));
            }
            
            if (!empty($testimonial_data['link_text'])) {
                update_post_meta($post_id, 'link_text', sanitize_text_field($testimonial_data['link_text']));
            }

            return $post_id;
        }

        private function parse_testimonial_html($html, $attrs = []) {
            $data = [
                'title' => '',
                'content' => '',
                'image_url' => '',
                'client_url' => '',
                'link_text' => ''
            ];
            
            if (!empty($attrs['imageUrl'])) {
                $data['image_url'] = $attrs['imageUrl'];
            }
            
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                        
            $h4_tags = $dom->getElementsByTagName('h4');
            if ($h4_tags->length > 0) {
                $data['title'] = trim($h4_tags->item(0)->textContent);
            }
            
            $p_tags = $dom->getElementsByTagName('p');
            $content_parts = array();
            
            for ($i = 0; $i < $p_tags->length; $i++) {
                $p_element = $p_tags->item($i);                
                $inner_html = '';
                foreach ($p_element->childNodes as $child) {
                    $inner_html .= $dom->saveHTML($child);
                }
                if (trim($inner_html)) {
                    $content_parts[] = '<p>' . trim($inner_html) . '</p>';
                }
            }
            
            $data['content'] = implode("\n\n", $content_parts);
            
            $span_tags = $dom->getElementsByTagName('span');
            for ($i = 0; $i < $span_tags->length; $i++) {
                $span = $span_tags->item($i);
                $a_tags = $span->getElementsByTagName('a');
                if ($a_tags->length > 0) {
                    $link = $a_tags->item(0);
                    $data['client_url'] = $link->getAttribute('href');
                    $data['link_text'] = trim($link->textContent) ?: $data['client_url'];
                    break;
                }
            }
            
            if (empty($data['image_url'])) {
                $img_tags = $dom->getElementsByTagName('img');
                if ($img_tags->length > 0) {
                    $data['image_url'] = $img_tags->item(0)->getAttribute('src');
                }
            }

            return $data;
        }

        private function get_attachment_id_from_url($url) {
            if (empty($url)) {
                return false;
            }
            
            $attachment_id = attachment_url_to_postid($url);
            
            if ($attachment_id) {
                return $attachment_id;
            }
            
            $cleaned_url = preg_replace('/\-\d+x\d+\.(jpe?g|png|gif|webp)$/i', '.$1', $url);
            
            if ($cleaned_url !== $url) {
                $attachment_id = attachment_url_to_postid($cleaned_url);
                if ($attachment_id) {
                    return $attachment_id;
                }
            }
            
            $filename = basename($url);
            $filename_cleaned = preg_replace('/\-\d+x\d+\./', '.', $filename);
            
            global $wpdb;
            $attachment_id = $wpdb->get_var($wpdb->prepare(
                "SELECT post_id FROM $wpdb->postmeta 
                WHERE meta_key = '_wp_attached_file' 
                AND (meta_value LIKE %s OR meta_value LIKE %s)",
                '%' . $filename,
                '%' . $filename_cleaned
            ));

            return $attachment_id ? (int) $attachment_id : false;
        }

        private function remove_legacy_menu_item($page_id) {
            $menu_items = wp_get_nav_menu_items('top');
            foreach($menu_items as $menu_item) {
                if ($menu_item->object_id == $page_id && $menu_item->object == 'page') {
                    wp_delete_post($menu_item->ID, true);
                    return;
                }
            }
        }

        private function add_theme_testimonials_menu_item() {
            $testimonials_nav_item = array(
                'menu-item-title' => get_theme_mod('nav_testimonials_label', __('Testimonials', 'wordpress-theme-elegance')),
                'menu-item-url' => '#testimonials',
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
                'menu-item-attr-title' => EleganceNavId::TESTIMONIALS
            );

            $menu_id = wp_get_nav_menu_object('top');
            if (!$menu_id) {
                return;
            }

            $item_id = wp_update_nav_menu_item($menu_id->term_id, 0, $testimonials_nav_item);
            if (is_wp_error($item_id)) {
                error_log('Error occured ' . print_r($item_id, true));
                return;
            }

            update_post_meta($item_id, ELEGANCE_NAV_META_KEY, EleganceNavType::ANCHOR);
            update_post_meta($item_id, ELEGANCE_NAV_ID_KEY, EleganceNavId::TESTIMONIALS);
        }

        private function create_result(string $status, ?array $data = []): Elegance_Migration_Result {
            return new Elegance_Migration_Result($this->get_id(), $status, $data);
        }

        private function create_data(int $migration_count = 0, array $errors = []): array {
            return [
                Elegance_Testimonials_Migration_Data::KEY_MIGRATION_COUNT => $migration_count,
                Elegance_Testimonials_Migration_Data::KEY_MIGRATION_ERRORS => $errors
            ];
        }
    }

    class Elegance_Testimonials_Migration_Data extends Elegance_Migration_Data {
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
                        __('Testimonials migration completed! %d testimonials were migrated.', 'wordpress-theme-elegance'), 
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