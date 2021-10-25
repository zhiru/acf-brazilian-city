<?php

    class acf_historia_real_ficha_tecnica extends acf_field
    {
        var $settings, $defaults;

        public function __construct()
        {
            $this->name = 'historia_real_ficha_tecnica';
            $this->label = __('Ficha Técnica - História Real');
            $this->category = __("basic", 'acf');

            $this->defaults = [
                "supplier_name" => '',
                "real_stories_datasheet_name" => '',
                "supplier_id" => 0,
                "real_stories_datasheet_id" => 0,
            ];

            parent::__construct();

            $this->settings = [
                'version' => '1.0.3',
                'url' => plugin_dir_url(__FILE__),
                'path' => plugin_dir_path(__FILE__)
            ];
        }

        function render_field($field)
        {
            // global $wpdb;

            $wpdb_mariee_original_otherdb = new wpdb('thaisa_dalmut_mariee2020', 'Tx6wf@90@#$', 'thaisa_dalmut_mariee2020', 'localhost');
            $wpdb_mariee_original_otherdb->show_errors();

            $field['value'] = isset($field['value']) ? $field['value'] : '';

            $fieldName = $field['name'];
            $real_stories_datasheet_id = (isset($field['value']['real_stories_datasheet_id'])) ? $field['value']['real_stories_datasheet_id'] : 0;
            $supplier_id = (isset($field['value']['supplier_id'])) ? $field['value']['supplier_id'] : 0;

            // Load Real Stories Datasheet Types
            $real_stories_datasheet = $this->list_real_stories_datasheet($wpdb_mariee_original_otherdb);

            // Load Suppliers
            $suppliers = $this->list_suppliers($wpdb_mariee_original_otherdb);

            ?>
                <div class="acf-fields">
                    <?php $real_stories_datasheet_field = $field['name'] . '[real_stories_datasheet_id]'; ?>
                    <div id="field-<?php echo $real_stories_datasheet_field; ?>" class="acf-field acf-field-select" data-name="field-<?php echo $real_stories_datasheet_field; ?>" data-type="select" data-key="<?php echo $field['key'];?>_state" style="width: 50%;" data-width="50">
                        <div class="acf-label">
                            <label for="acf-<?php echo $field['key'];?>_real_stories_datasheet_id"><?php _e("Selecione o tipo", 'acf'); ?></label>
                        </div>
                        <div class="acf-input">
                            <?php
                                acf_render_field([
                                    'id' => $field['key'].'_real_stories_datasheet_id',
                                    'type' => 'select',
                                    'name' => $real_stories_datasheet_field,
                                    'value' => $real_stories_datasheet_id,
                                    'ui' => 1,
                                    'choices' => $real_stories_datasheet,
                                ]);
                            ?>
                        </div>
                    </div>

                    <?php $supplier_field = $field['name'] . '[supplier_id]'; ?>
                    <div id="field-<?php echo $supplier_field; ?>" class="acf-field acf-field-select" data-name="field-<?php echo $supplier_field; ?>" data-type="select" data-key="<?php echo $field['key'];?>_supplier" style="width: 50%;" data-width="50">
                        <div class="acf-label">
                            <label for="acf-<?php echo $field['key'];?>_sulier_id"><?php _e("Selecione o fornecedor", 'acf'); ?></label>
                        </div>
                        <div class="acf-input">
                            <?php
                                acf_render_field([
                                    'id' => $field['key'].'_supplier',
                                    'type' => 'select',
                                    'name' => $supplier_field,
                                    'value' => $supplier_id,
                                    'ui' => 1,
                                    'choices' => $suppliers,
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
            <?php
        }

        function update_value($value, $post_id, $field)
        {
            $value['supplier_name'] = $this->supplier_name($value['supplier_id']);
            $value['real_stories_datasheet_name'] = (isset($value['real_stories_datasheet_id']) && $value['real_stories_datasheet_id'] !== 0) ? $this->real_stories_datasheet_name($value['real_stories_datasheet_id']) : '';

            return $value;
        }

        function format_value_for_api($value, $post_id, $field)
        {

            $value['supplier_name'] = $this->supplier_name($value['supplier_id']);
            $value['real_stories_datasheet_name'] = (isset($value['real_stories_datasheet_id']) && $value['real_stories_datasheet_id'] !== 0) ? $this->real_stories_datasheet_name($value['real_stories_datasheet_id']) : '';

            return $value;
        }

        function input_admin_enqueue_scripts()
        {
            // wp_register_script('historia-real-ficha-tecnica', $this->settings['url'] . 'js/real_stories.js', ['acf-input'], $this->settings['version']);

            // wp_localize_script('historia-real-ficha-tecnica', "AcfRealStories", [
            //     "ajaxurl" => admin_url("admin-ajax.php"),
            // ]);

            // scripts
            // wp_enqueue_script([
            //     'historia-real-ficha-tecnica',
            // ]);
        }

        /**
         * Retorna todos os fornecedores
         * @param string $new_wpdb conexão com o banco de dados original
         * @return array "ID" => "Nome"
         * @global type $wpdb
         */
        protected function list_real_stories_datasheet($new_wpdb)
        {
            $results = $new_wpdb->get_results("SELECT id, name FROM real_stories_datasheet_types ORDER BY name ASC");
            $real_stories_datasheet = [ "__" => __("Selecione") ];

            foreach ($results as $item) {
                $real_stories_datasheet[$item->id] = $item->name;
            }

            return $real_stories_datasheet;
        }

        /**
         * Retorna todos os fornecedores
         * @param string $new_wpdb conexão com o banco de dados original
         * @return array "ID" => "Nome"
         * @global type $wpdb
         */
        protected function list_suppliers($new_wpdb)
        {
            $results = $new_wpdb->get_results("SELECT id, name FROM suppliers ORDER BY name ASC");
            $suppliers = [ "__" => __("Selecione") ];

            foreach ($results as $item) {
                $suppliers[$item->id] = $item->name;
            }

            return $suppliers;
        }

        /**
         * Retorna o nome do fornecedor
         * @param int $supplier_id identificador da cidade
         * @return mixed
         * @global type $wpdb
         */
        protected function supplier_name($supplier_id)
        {
            // global $wpdb;
            $wpdb_mariee_original_otherdb = new wpdb('thaisa_dalmut_mariee2020', 'Tx6wf@90@#$', 'thaisa_dalmut_mariee2020', 'localhost');
            $wpdb_mariee_original_otherdb->show_errors();

            $supplier = $wpdb_mariee_original_otherdb->get_row("SELECT * FROM suppliers WHERE id = '{$supplier_id}'");

            if ($supplier) {
                return $supplier->name;
            } else {
                return false;
            }
        }

        /**
         *  Retorna o nome do tipo da ficha técnica
         * @param int $real_stories_datasheet_id
         * @return mixed
         * @global type $wpdb
         */
        protected function real_stories_datasheet_name($real_stories_datasheet_id)
        {
            // global $wpdb;
            $wpdb_mariee_original_otherdb = new wpdb('thaisa_dalmut_mariee2020', 'Tx6wf@90@#$', 'thaisa_dalmut_mariee2020', 'localhost');
            $wpdb_mariee_original_otherdb->show_errors();

            $real_stories_datasheet_types = $wpdb_mariee_original_otherdb->get_row("SELECT * FROM real_stories_datasheet_types WHERE id = '{$real_stories_datasheet_id}'");

            if ($real_stories_datasheet_types) {
                return $real_stories_datasheet_types->name;
            } else {
                return false;
            }
        }
    }

    new acf_historia_real_ficha_tecnica();
