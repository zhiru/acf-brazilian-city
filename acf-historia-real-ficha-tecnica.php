<?php
    /**
     * Ficha Técnica - História Real - Mariée
     *
     * @package           HistoriaRealFichaTecnica
     * @author            Felipe Almeman
     * @copyright         2021 Todos os direitos reservados a Felipe Almeman e Aireset
     * @license           GPL
     *
     * @acf-historia-real-ficha-tecnica
     * Plugin Name:       Ficha Técnica - História Real - Mariée
     * Description:       Adiciona ao Projeto da Mariée a opção de Ficha Ténica para ser usado no post type Histórias Reais.
     * Version:           1.0.3
     * Requires at least: 5.6
     * Requires PHP:      7.2
     * Author:            Felipe Almeman - Aireset
     * Author URI:        https://aireset.com.br
     * Text Domain:       historia-real-ficha-tecnica
     * License:           GPL
     */

    load_plugin_textdomain('acf-historia-real-ficha-tecnica-field', false, dirname(plugin_basename(__FILE__)) . '/lang/');

    function register_fields_historia_real_ficha_tecnica()
    {
        include_once('acf-historia-real-ficha-tecnica-field.php');
    }

    add_action('acf/include_field_types', 'register_fields_historia_real_ficha_tecnica');
