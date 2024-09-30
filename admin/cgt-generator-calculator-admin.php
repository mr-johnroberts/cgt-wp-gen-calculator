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
        add_action('wp_ajax_save_cgt_configuration', [$this, 'save_cgt_configuration']);
        add_action('wp_ajax_delete_cgt_shortcode', [$this, 'delete_cgt_shortcode']);
    }

    function enqueue_admin_scripts($hook)
    {
        $allowed_hooks = [
            'toplevel_page_cgt-generator-calculator',
            'toplevel_page_cgt-generator-models'
        ];

        if (!in_array($hook, $allowed_hooks)) {
            return;
        }

        wp_enqueue_style('cgt-admin-styles', CGT_GENERATOR_CALCULATOR_URL . 'assets/css/cgt-admin-style.css', [], CGT_GENERATOR_CALCULATOR_VERSION, 'all');

        // Use file modification time for cache busting
        $script_path = CGT_GENERATOR_CALCULATOR_DIR . 'assets/js/cgt-admin.js';
        $script_version = filemtime($script_path);
        wp_register_script('cgt-admin-js', CGT_GENERATOR_CALCULATOR_URL . 'assets/js/cgt-admin.js', ['jquery'], $script_version, true);
        wp_enqueue_script('cgt-admin-js');

        wp_localize_script('cgt-admin-js', 'cgtCalculatorVars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce_save' => wp_create_nonce('cgt_save_nonce'),
            'nonce_delete' => wp_create_nonce('cgt_delete_nonce')
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
        echo '<h1>Generator Calculator Settings</h1>';
        echo '<div id="cgt-settings-message"></div>';
        echo '<form id="cgt-categories-wrapper" method="post">';
        settings_fields('cgt_generator_calculator_settings');
        do_settings_sections('cgt-generator-calculator-settings');
        echo '<button id="cgt-save-settings" class="button button-primary">Save Changes</button>';
        echo '</form>';
        $saved_shortcode = get_option('cgt_saved_shortcode');
        echo '<div id="cgt-shortcode-display">';
        if ($saved_shortcode) {
            echo "<p>Use this shortcode to display the calculator on your site: <code>$saved_shortcode</code></p>";
            echo '<button id="cgt-delete-shortcode" class="button button-secondary">Delete Shortcode</button>';
        } else {
            echo '<p>No shortcode generated yet.</p>';
        }
        echo '</div>';
        echo '</div>';
    }

    public function register_plugin_settings()
    {
        register_setting('cgt_generator_calculator_settings', 'cgt_generator_calculator_options', [$this, 'sanitize']);
        add_settings_section('cgt_main_settings', 'Main Settings', null, 'cgt-generator-calculator-settings');
        add_settings_field('cgt_appliance_categories', 'Appliance Categories', [$this, 'appliance_categories_field_callback'], 'cgt-generator-calculator-settings', 'cgt_main_settings');
    }

    public function appliance_categories_field_callback()
    {
        $options = get_option('cgt_generator_calculator_options');
        $categories = isset($options['categories']) ? $options['categories'] : [];
        echo '<div id="cgt-categories-wrapper">';
        foreach ($categories as $index => $category) {
            echo '<div class="cgt-category-row">';
            echo '<input type="text" name="cgt_generator_calculator_options[categories][' . $index . '][name]" value="' . esc_attr($category['name']) . '" placeholder="Main Category" class="category-name">';
            echo '<button type="button" class="button cgt-add-sub-category">Add Appliance</button>';
            echo '<div class="cgt-subcategories">';
            if (isset($category['sub_categories'])) {
                foreach ($category['sub_categories'] as $subIndex => $subCategory) {
                    echo '<div class="cgt-subcategory-row">';
                    echo '<input type="text" name="cgt_generator_calculator_options[categories][' . $index . '][sub_categories][' . $subIndex . '][name]" value="' . esc_attr($subCategory['name']) . '" placeholder="Appliance" class="subcategory-name">';
                    echo '<input type="number" name="cgt_generator_calculator_options[categories][' . $index . '][sub_categories][' . $subIndex . '][wattage]" value="' . esc_attr($subCategory['wattage']) . '" placeholder="Default Wattage" class="subcategory-wattage">';
                    echo '<button type="button" class="button cgt-remove-sub-category">Remove</button>';
                    echo '</div>';
                }
            }
            echo '</div>';
            echo '<button type="button" class="button cgt-remove-category">Remove Main Category</button>';
            echo '</div>';
        }
        echo '</div>';
        echo '<button type="button" id="cgt-add-category" class="button">Add Main Category</button>';
    }

    public function save_cgt_configuration()
    {
        check_ajax_referer('cgt_save_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permission']);
            return;
        }

        parse_str($_POST['data'], $parsed_data);
        $options = ['categories' => []];
        if (isset($parsed_data['cgt_generator_calculator_options']['categories'])) {
            foreach ($parsed_data['cgt_generator_calculator_options']['categories'] as $index => $category_data) {
                if (!empty($category_data['name'])) {
                    $category = [
                        'name' => sanitize_text_field($category_data['name']),
                        'sub_categories' => []
                    ];
                    if (isset($category_data['sub_categories'])) {
                        foreach ($category_data['sub_categories'] as $sub_index => $sub_category_data) {
                            if (!empty($sub_category_data['name'])) {
                                $category['sub_categories'][$sub_index] = [
                                    'name' => sanitize_text_field($sub_category_data['name']),
                                    'wattage' => intval($sub_category_data['wattage'])
                                ];
                            }
                        }
                    }
                    $options['categories'][$index] = $category;
                }
            }
        }

        update_option('cgt_generator_calculator_options', $options);

        $existing_shortcode = get_option('cgt_saved_shortcode');
        if (!$existing_shortcode && !empty($options['categories'])) {
            $unique_id = wp_generate_password(12, false);
            $shortcode = "[cgt-generator id='{$unique_id}']";
            update_option('cgt_saved_shortcode', $shortcode);
            add_settings_error('cgt_generator_calculator_settings', 'shortcode_generated', 'New shortcode generated: ' . $shortcode, 'updated');
        } elseif (empty($options['categories'])) {
            delete_option('cgt_saved_shortcode');
            add_settings_error('cgt_generator_calculator_settings', 'shortcode_removed', 'All categories removed. Shortcode has been deleted.', 'error');
        } else {
            add_settings_error('cgt_generator_calculator_settings', 'settings_updated', 'Settings saved. Existing shortcode is still valid.', 'updated');
        }

        set_transient('settings_errors', get_settings_errors(), 30);

        // Send the response with the shortcode
        wp_send_json_success(['shortcode' => get_option('cgt_saved_shortcode')]);
    }

    public function delete_cgt_shortcode()
    {
        check_ajax_referer('cgt_delete_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'No permission']);
            return;
        }

        delete_option('cgt_generator_calculator_options');
        delete_option('cgt_saved_shortcode');

        if (get_option('cgt_saved_shortcode') === false && get_option('cgt_generator_calculator_options') === false) {
            wp_send_json_success(['message' => 'Shortcode and associated form deleted successfully.']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete shortcode and associated form.']);
        }
    }

    public function sanitize($input)
    {
        $new_input = ['categories' => []];
        if (isset($input['categories'])) {
            foreach ($input['categories'] as $index => $category) {
                if (!empty($category['name'])) {
                    $new_category = [
                        'name' => sanitize_text_field($category['name']),
                        'sub_categories' => []
                    ];
                    if (isset($category['sub_categories'])) {
                        foreach ($category['sub_categories'] as $sub_index => $sub_category) {
                            if (!empty($sub_category['name'])) {
                                $new_category['sub_categories'][$sub_index] = [
                                    'name' => sanitize_text_field($sub_category['name']),
                                    'wattage' => intval($sub_category['wattage'])
                                ];
                            }
                        }
                    }
                    $new_input['categories'][$index] = $new_category;
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
        if (!isset($options['categories'])) {
            $options['categories'] = [];
        }
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
            array_splice($options['categories'], $index, 1);
            update_option('cgt_generator_calculator_options', $options);
        }
        wp_send_json_success(['message' => 'Category removed successfully']);
    }
}
