<?php

/**
 * Plugin Name: CGT Generator Calculator
 * Plugin URI: https://www.conectglobal.com
 * Description: Calculate the total wattage of household appliances to determine the ideal generator size.
 * Version: 1.0.0
 * Author: CoNect Global Technology
 * Author URI: https://www.conectglobal.com
 * Text Domain: cgt-generator-calculator
 */

if (!defined('WPINC')) {
    die;
}

define('CGT_GENERATOR_CALCULATOR_VERSION', '1.0.0');
define('CGT_GENERATOR_CALCULATOR_DIR', plugin_dir_path(__FILE__));
define('CGT_GENERATOR_CALCULATOR_URL', plugin_dir_url(__FILE__));

include_once CGT_GENERATOR_CALCULATOR_DIR . 'admin/cgt-generator-calculator-admin.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-activator.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-deactivator.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-cpt.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-taxonomies.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'public/cgt-generator-calculator-ajax.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'public/cgt-generator-calculator-shortcode.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'public/cgt-generator-calculator-public.php';

register_activation_hook(__FILE__, 'cgt_generator_calculator_activate');
register_deactivation_hook(__FILE__, 'cgt_generator_calculator_deactivate');

function cgt_generator_calculator_activate()
{
    Cgt_Generator_Calculator_Activator::activate();
}

function cgt_generator_calculator_deactivate()
{
    Cgt_Generator_Calculator_Deactivator::deactivate();
}

function cgt_initialize_plugin()
{
    if (is_admin()) {
        new Cgt_Generator_Calculator_Admin();
    } else {
        new Cgt_Generator_Calculator_Public();
    }
}

add_action('plugins_loaded', 'cgt_initialize_plugin');

function cgt_register_appliance_post_type()
{
    $args = array(
        'public' => true,
        'label'  => 'Appliances',
        'supports' => array('title', 'editor', 'thumbnail')
    );
    register_post_type('appliance', $args);
}
add_action('init', 'cgt_register_appliance_post_type', 0);

function cgt_create_custom_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'appliance_data';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        wattage smallint(5) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
function register_cgt_shortcodes()
{
    add_shortcode('cgt-generator', 'cgt_generator_calculator_shortcode');
}
add_action('init', 'register_cgt_shortcodes');


function fetch_generators_data()
{
    if (!check_ajax_referer('cgt_ajax_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Nonce verification failed']);
        return;
    }

    $total_wattage = floatval($_GET['total_wattage']); // Assuming total wattage in kW

    // WooCommerce API credentials
    $consumer_key = 'ck_919df8ba13cfc8f8b34d797238280a2078fe1d22';
    $consumer_secret = 'cs_50cf7c2a87997ecb4512581ed18d89071fddd292';

    // Fetch all products from WooCommerce
    $products = fetch_all_products($consumer_key, $consumer_secret);

    // Determine the range
    $range_start = floor($total_wattage / 10) * 10;
    $range_end = $range_start + 9.9;

    // Filter products based on the range
    $filtered_products = array_filter($products, function ($product) use ($range_start, $range_end) {
        if (preg_match('/(\d+(\.\d+)?)(kW)/', $product['name'], $matches)) {
            $product_kw = floatval($matches[1]);
            return $product_kw >= $range_start && $product_kw <= $range_end;
        }
        return false;
    });

    // If no exact match is found, find the closest matches
    if (empty($filtered_products)) {
        usort($products, function ($a, $b) use ($total_wattage) {
            if (preg_match('/(\d+(\.\d+)?)(kW)/', $a['name'], $a_matches) && preg_match('/(\d+(\.\d+)?)(kW)/', $b['name'], $b_matches)) {
                $a_kw = floatval($a_matches[1]);
                $b_kw = floatval($b_matches[1]);
                return abs($a_kw - $total_wattage) <=> abs($b_kw - $total_wattage);
            }
            return 0;
        });

        // Select the top N closest matches, e.g., top 5
        $filtered_products = array_slice($products, 0, 5);
    }

    wp_send_json_success(['products' => array_values($filtered_products)]);
}

function fetch_all_products($consumer_key, $consumer_secret)
{
    $products = [];
    $page = 1;
    do {
        $url = "https://propertyinvestor.conectglobal.com/wp-json/wc/v3/products?consumer_key=$consumer_key&consumer_secret=$consumer_secret&page=$page";
        error_log('API Request URL: ' . $url);

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            error_log('API Request Error: ' . $response->get_error_message());
            break;
        }

        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Failed to decode JSON response: ' . json_last_error_msg());
            break;
        }

        if (empty($data)) {
            break;
        }

        $products = array_merge($products, $data);
        $page++;
    } while (count($data) === 10); // Adjust as needed to handle pagination correctly

    return $products;
}

add_action('wp_ajax_fetch_generators', 'fetch_generators_data');
add_action('wp_ajax_nopriv_fetch_generators', 'fetch_generators_data');
