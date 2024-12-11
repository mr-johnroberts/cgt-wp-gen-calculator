<?php

class Cgt_Generator_Calculator_Public
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_shortcode('cgt_calculator', [$this, 'cgt_generator_calculator_shortcode']);
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'cgt-generator-calculator-style',
            CGT_GENERATOR_CALCULATOR_URL . 'assets/css/cgt-generator-calculator-style.css',
            [],
            CGT_GENERATOR_CALCULATOR_VERSION
        );
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'cgt-generator-calculator-script',
            CGT_GENERATOR_CALCULATOR_URL . 'assets/js/cgt-generator-calculator-script.js',
            ['jquery'],
            CGT_GENERATOR_CALCULATOR_VERSION,
            true
        );

        wp_localize_script(
            'cgt-generator-calculator-script',
            'cgtCalculatorVars',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cgt_calculator_nonce')
            ]
        );
    }

    public function cgt_generator_calculator_shortcode()
    {
        ob_start();

        // Query for appliances data
        $args = array(
            'post_type'      => 'appliance',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC'
        );

        $appliance_query = new WP_Query($args);

        echo '<div id="cgt-generator-calculator">';
        echo '<form id="cgt-generator-form">';

        // Introduction step
        echo '<div class="cgt-step" data-step="1">';
        echo '<p>Use our Generator Power Calculator to determine which generator model is best for you.</p>';
        echo '<label for="cgt_square_footage">Square Footage of Residence</label>';
        echo '<input type="number" id="cgt_square_footage" name="square_footage" required>';
        echo '<button type="button" class="cgt-next-btn">Start</button>';
        echo '</div>';

        $step_count = 2; // Start at step 2 since step 1 is the intro
        while ($appliance_query->have_posts()) {
            $appliance_query->the_post();
            $wattage = get_post_meta(get_the_ID(), 'wattage', true); // Assuming 'wattage' is a custom field for appliances

            echo '<div class="cgt-step" data-step="' . $step_count . '" style="display:none;">';
            echo '<label for="appliance-' . get_the_ID() . '">' . get_the_title() . '</label>';
            echo '<input type="number" id="appliance-' . get_the_ID() . '" name="appliances[' . get_the_ID() . ']" value="' . esc_attr($wattage) . '" data-wattage="' . esc_attr($wattage) . '">';
            echo '<button type="button" class="cgt-next-btn">Next</button>';
            echo '<button type="button" class="cgt-prev-btn">Previous</button>';
            echo '</div>';

            $step_count++;
        }

        wp_reset_postdata();

        echo '</form>';
        echo '<div id="cgt-total-wattage" style="display:none;">';
        echo 'Total Wattage Calculated: <span>0</span> Watts';
        echo '</div>';
        echo '</div>';

        return ob_get_clean();
    }
}

if (!is_admin()) {
    new Cgt_Generator_Calculator_Public();
}
