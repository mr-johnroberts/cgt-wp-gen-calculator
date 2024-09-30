<?php

class Cgt_Generator_Calculator_Public
{
    // public function __construct()
    // {
    //     add_action('wp', [$this, 'enqueue_styles_and_scripts']);
    //     add_shortcode('cgt_calculator', [$this, 'cgt_generator_calculator_shortcode']);
    // }

    // public function enqueue_styles_and_scripts()
    // {
    //     if (is_a(get_post(), 'WP_Post') && has_shortcode(get_post()->post_content, 'cgt_calculator')) {
    //         wp_enqueue_style(
    //             'cgt-generator-calculator-style',
    //             plugin_dir_url(__FILE__) . 'assets/css/cgt-generator-calculator-style.css',
    //             [],
    //             defined('CGT_GENERATOR_CALCULATOR_VERSION') ? CGT_GENERATOR_CALCULATOR_VERSION : '1.0.0'
    //         );

    //         wp_enqueue_script(
    //             'cgt-generator-calculator-script',
    //             plugin_dir_url(__FILE__) . 'assets/js/cgt-generator-calculator-script.js',
    //             ['jquery'],
    //             defined('CGT_GENERATOR_CALCULATOR_VERSION') ? CGT_GENERATOR_CALCULATOR_VERSION : '1.0.0',
    //             true
    //         );

    //         wp_localize_script(
    //             'cgt-generator-calculator-script',
    //             'cgtCalculatorVars',
    //             [
    //                 'ajax_url' => admin_url('admin-ajax.php'),
    //                 'nonce' => wp_create_nonce('cgt_calculator_nonce')
    //             ]
    //         );
    //     }
    // }

    public function cgt_generator_calculator_shortcode()
    {
        ob_start();
        // Check if the option is populated
        $options = get_option('cgt_generator_calculator_options');
        if (!isset($options['categories'])) {
            echo "<p>No configuration found for this calculator.</p>";
            return ob_get_clean();
        }

        echo '<div id="cgt-generator-calculator">';
        echo '<form id="cgt-generator-form">';
        echo '<div class="cgt-step" data-step="1">';
        echo '<p>Use our Generator Power Calculator to determine which generator model is best for you.</p>';
        echo '<label for="cgt_square_footage">Square Footage of Residence</label>';
        echo '<input type="number" id="cgt_square_footage" name="square_footage" required>';
        echo '<button type="button" class="cgt-next-btn">Start</button>';
        echo '</div>';

        $step_count = 2;
        foreach ($options['categories'] as $index => $category) {
            echo '<div class="cgt-step" data-step="' . $step_count . '" style="display:none;">';
            echo '<h3>' . esc_html($category['name']) . '</h3>';
            foreach ($category['sub_categories'] as $subIndex => $subCategory) {
                echo '<label for="appliance-' . $index . '-' . $subIndex . '">' . esc_html($subCategory['name']) . '</label>';
                echo '<input type="number" id="appliance-' . $index . '-' . $subIndex . '" name="appliances[' . $index . '][' . $subIndex . '][wattage]" value="' . esc_attr($subCategory['wattage']) . '" data-wattage="' . esc_attr($subCategory['wattage']) . '">';
            }
            echo '<button type="button" class="cgt-prev-btn">Previous</button>';
            echo '<button type="button" class="cgt-next-btn">Next</button>';
            echo '</div>';
            $step_count++;
        }

        echo '<div class="cgt-step" data-step="' . $step_count . '" style="display:none;">';
        echo '<button type="submit">Submit</button>';
        echo '</div>';
        echo '</form>';
        echo '<div id="cgt-total-wattage" style="display:none;">Total Wattage Calculated: <span>0</span> Watts</div>';
        echo '</div>';

        return ob_get_clean();
    }
}

if (!is_admin()) {
    new Cgt_Generator_Calculator_Public();
}
