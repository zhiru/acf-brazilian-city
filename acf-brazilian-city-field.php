<?php

    class acf_brazilian_city_field extends acf_field
    {

        var $settings, $defaults;

        public function __construct()
        {
            $this->name = 'brazilian_state_city';
            $this->label = __('Cidade Brasileira');
            $this->category = __("basic", 'acf');

            $this->defaults = [
                "city_name" => '',
                "state_name" => '',
                "city_id" => 0,
                "state_id" => '',
            ];

            parent::__construct();

            $this->settings = [
                'version' => '2.2.0',
                'url' => plugin_dir_url(__FILE__),
                'path' => plugin_dir_path(__FILE__)
            ];
        }

        function render_field($field)
        {
            global $wpdb;

            $field['value'] = isset($field['value']) ? $field['value'] : '';

            $fieldName = $field['name'];
            $city_id = (isset($field['value']['city_id'])) ? $field['value']['city_id'] : 0;
            $state_id = (isset($field['value']['state_id'])) ? $field['value']['state_id'] : 0;

            $cities = $this->list_cities($state_id);

            //Carregando Estados
            $states = ["" => "Selecione"];
            $statesResults = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "states ORDER BY name ASC");
            foreach ($statesResults as $state) {
                $states[$state->id] = $state->name;
            }
            ?>
            <?php $state_field = $field['name'] . '[state_id]'; ?>
                <div class="acf-fields">
                    <div id="field-<?php echo $state_field; ?>" class="acf-field acf-field-select" data-name="field-<?php echo $state_field; ?>" data-type="select" data-key="<?php echo $field['key'];?>_state" style="width: 50%;" data-width="50">
                        <div class="acf-label">
                            <label for="acf-<?php echo $field['key'];?>_state_id"><?php _e("Selecione o estado", 'acf'); ?></label>
                        </div>
                        <div class="acf-input">
                            <?php

                                acf_render_field([
                                    'id' => $field['key'].'_state',
                                    'type' => 'select',
                                    'name' => $state_field,
                                    'value' => $state_id,
                                    'ui' => 1,
                                    'choices' => $states,
                                ]);
                            ?>
                        </div>
                    </div>
                    <?php $city_field = $field['name'] . '[city_id]'; ?>
                    <div id="field-<?php echo $city_field; ?>" class="acf-field acf-field-select" data-name="field-<?php echo $city_field; ?>" data-type="select" data-key="<?php echo $field['key'];?>_city" style="width: 50%;" data-width="50">
                        <div class="acf-label">
                            <label for="acf-<?php echo $field['key'];?>_city_id"><?php _e("Selecione a cidade", 'acf'); ?></label>
                        </div>
                        <div class="acf-input">
                            <?php
                                acf_render_field([
                                    'id' => $field['key'].'_city',
                                    'type' => 'select',
                                    'name' => $city_field,
                                    'value' => $city_id,
                                    'ui' => 1,
                                    'choices' => $cities,
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
            <?php
        }

        function update_value($value, $post_id, $field)
        {
            $value['city_name'] = $this->city_name($value['city_id']);
            $value['state_name'] = (isset($value['state_id']) && $value['state_id'] !== 0) ? $this->state_name($value['state_id']) : '';

            return $value;
        }

        function format_value_for_api($value, $post_id, $field)
        {

            $value['city_name'] = $this->city_name($value['city_id']);
            $value['state_name'] = (isset($value['state_id']) && $value['state_id'] !== 0) ? $this->state_name($value['state_id']) : '';

            return $value;
        }

        function input_admin_enqueue_scripts()
        {
            wp_register_script('acf-brazilian-city', $this->settings['url'] . 'js/brazilian-city.js', ['acf-input'], $this->settings['version']);

            wp_localize_script('acf-brazilian-city', "AcfBrazilianCity", [
                "ajaxurl" => admin_url("admin-ajax.php"),
            ]);

            // scripts
            wp_enqueue_script([
                'acf-brazilian-city',
            ]);
        }

        /**
         * Retorna todas as cidades de um determinado estado
         * @param string $state_id identificador do estados Ex.: 'ES'
         * @return array "ID" => "Nome"
         * @global type $wpdb
         */
        protected function list_cities($state_id)
        {
            global $wpdb;
            $cities_results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "cities WHERE state_id ='" . $state_id . "' ORDER BY name ASC");
            $cities = [];

            foreach ($cities_results as $city) {
                $cities[$city->id] = $city->name;
            }

            return $cities;
        }

        /**
         * Retorna o nome de uma cidade especifica
         * @param int $city_id identificador da cidade
         * @return mixed
         * @global type $wpdb
         */
        protected function city_name($city_id)
        {
            global $wpdb;
            $city = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "cities WHERE id = '" . $city_id . "'");

            if ($city) {
                return $city->name;
            } else {
                return false;
            }
        }

        /**
         *  Retorna o nome de um estado especifico
         * @param int $state_id
         * @return mixed
         * @global type $wpdb
         */
        protected function state_name($state_id)
        {
            global $wpdb;
            $state = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "states WHERE id = '" . $state_id . "'");

            if ($state) {
                return $state->name;
            } else {
                return false;
            }
        }
    }

    add_action('wp_ajax_get_list_state_cities', 'get_list_state_cities');
    add_action('wp_ajax_nopriv_gt_list_state_cities', 'et_list_state_cities');

    /**
     * Disponibilia via ajax a lista de cidades para um determinado estado
     * @global type $wpdb
     */
    function get_list_state_cities()
    {
        global $wpdb;

        $state_id = substr(trim($_REQUEST['stateId']), 0, 2);

        $cities_results = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "cities WHERE state_id ='" . $state_id . "' ORDER BY name ASC");
        $cities = [];

        if ($cities_results) {
            foreach ($cities_results as $city) {
                $cities[$city->id] = $city->name;
            }
        }

        ob_end_clean();
        header("Content-Type: application/json");
        echo json_encode($cities);
        die();
    }

    new acf_brazilian_city_field();
