<?php

class Cgt_Generator_Calculator_Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
        add_action('admin_init', [$this, 'register_plugin_settings']);
        add_action('wp_ajax_cgt_add_appliance_category', [$this, 'handle_add_appliance_category']);
        add_action('wp_ajax_cgt_remove_appliance_category', [$this, 'handle_remove_appliance_category']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'toplevel_page_cgt-generator-calculator') {
            return;
        }
        wp_enqueue_style('cgt-admin-styles', CGT_GENERATOR_CALCULATOR_URL . 'assets/css/cgt-admin-styles.css', [], CGT_GENERATOR_CALCULATOR_VERSION, 'all');
        wp_enqueue_script('cgt-admin-js', CGT_GENERATOR_CALCULATOR_URL . 'assets/js/cgt-admin.js', ['jquery'], CGT_GENERATOR_CALCULATOR_VERSION, true);
        wp_localize_script('cgt-admin-js', 'cgtCalculatorVars', [
            'nonce' => wp_create_nonce('cgt_calculator_nonce'),
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }

    public function add_plugin_admin_menu()
    {
        add_menu_page(
            'CGT Generator Calculator Settings',
            'Generator Calculator',
            'manage_options',
            'cgt-generator-calculator',
            [$this, 'display_plugin_settings_page'],
            'dashicons-admin-generic'
        );
    }

    public function display_plugin_settings_page()
    {
        echo '<div class="wrap">';
        echo '<h1 class="cgt-main-heading">' . esc_html(get_admin_page_title()) . '</h1>';
        echo '<p class="cgt-sub-description">Kindly add the necessary appliance categories.</p>';
        echo '<form method="post" action="options.php">';
        settings_fields('cgt_generator_calculator_settings');
        do_settings_sections('cgt-generator-calculator-settings');
        submit_button();
        echo '</form></div>';
    }

    public function register_plugin_settings()
    {
        register_setting('cgt_generator_calculator_settings', 'cgt_generator_calculator_options', [$this, 'sanitize']);
        add_settings_section('cgt_generator_calculator_main_section', 'Main Settings', null, 'cgt-generator-calculator-settings');
        add_settings_field('cgt_appliance_categories', 'Appliance Categories', [$this, 'appliance_categories_field_callback'], 'cgt-generator-calculator-settings', 'cgt_generator_calculator_main_section');
    }

    public function appliance_categories_field_callback()
    {
        $options = get_option('cgt_generator_calculator_options');
        $categories = isset($options['categories']) ? $options['categories'] : [];
        foreach ($categories as $index => $category) {
            echo '<div class="cgt-category-section">';
            echo '<label for="category_name_' . $index . '">Appliance Category</label>';
            echo '<input type="text" id="category_name_' . $index . '" name="cgt_generator_calculator_options[categories][' . $index . '][name]" value="' . esc_attr($category['name']) . '" placeholder="Category Name">';
            echo '<label for="category_wattage_' . $index . '">Default Wattage</label>';
            echo '<input type="number" id="category_wattage_' . $index . '" name="cgt_generator_calculator_options[categories][' . $index . '][wattage]" value="' . esc_attr($category['wattage']) . '" placeholder="Default Wattage">';
            echo '<button type="button" class="button cgt-remove-category" data-index="' . $index . '">Remove</button>';
            echo '</div>';
        }
        echo '<button type="button" id="cgt-add-category" class="button">Add Category</button>';
    }

    public function sanitize($input)
    {
        $new_input = [];
        if (isset($input['categories'])) {
            foreach ($input['categories'] as $index => $category) {
                if (!empty($category['name'])) {
                    $new_input['categories'][$index]['name'] = sanitize_text_field($category['name']);
                    $new_input['categories'][$index]['wattage'] = sanitize_text_field($category['wattage']);
                }
            }
        }
        return $new_input;
    }

    public function handle_add_appliance_category()
    {
        check_ajax_referer('cgt_calculator_nonce', 'nonce');
        $name = sanitize_text_field($_POST['name']);
        $wattage = sanitize_text_field($_POST['wattage']);
        $options = get_option('cgt_generator_calculator_options');
        $options['categories'][] = ['name' => $name, 'wattage' => $wattage];
        update_option('cgt_generator_calculator_options', $options);
        wp_send_json_success(['message' => 'Category added successfully']);
    }

    public function handle_remove_appliance_category()
    {
        check_ajax_referer('cgt_calculator_nonce', 'nonce');
        $index = $_POST['index'];
        $options = get_option('cgt_generator_calculator_options');
        if (isset($options['categories'][$index])) {
            unset($options['categories'][$index]);
            $options['categories'] = array_values($options['categories']);
            update_option('cgt_generator_calculator_options', $options);
        }
        wp_send_json_success(['message' => 'Category removed successfully']);
    }
}
