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

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin version and paths.
define('CGT_GENERATOR_CALCULATOR_VERSION', '1.0.0');
define('CGT_GENERATOR_CALCULATOR_DIR', plugin_dir_path(__FILE__));
define('CGT_GENERATOR_CALCULATOR_URL', plugin_dir_url(__FILE__));

// Include the PHP dependencies.
include_once CGT_GENERATOR_CALCULATOR_DIR . 'admin/cgt-generator-calculator-admin.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-activator.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-deactivator.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-cpt.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'includes/cgt-generator-calculator-taxonomies.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'public/cgt-generator-calculator-ajax.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'public/cgt-generator-calculator-shortcode.php';
include_once CGT_GENERATOR_CALCULATOR_DIR . 'public/cgt-generator-calculator-public.php';

// Activation and deactivation hooks.
register_activation_hook(__FILE__, 'cgt_generator_calculator_activate');
register_deactivation_hook(__FILE__, 'cgt_generator_calculator_deactivate');

/**
 * The code that runs during plugin activation.
 */
function cgt_generator_calculator_activate()
{
    Cgt_Generator_Calculator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function cgt_generator_calculator_deactivate()
{
    Cgt_Generator_Calculator_Deactivator::deactivate();
}

function cgt_initialize_plugin()
{
    if (is_admin()) {
        // Ensure the admin class is only loaded if we're in the admin area
        require_once CGT_GENERATOR_CALCULATOR_DIR . 'admin/cgt-generator-calculator-admin.php';
        new Cgt_Generator_Calculator_Admin();
    } else {
        // Public-facing functionality
        require_once CGT_GENERATOR_CALCULATOR_DIR . 'public/cgt-generator-calculator-public.php';
        new Cgt_Generator_Calculator_Public();
    }
}

// Hook into the 'plugins_loaded' action to ensure all plugins are loaded before initializing.
add_action('plugins_loaded', 'cgt_initialize_plugin');
