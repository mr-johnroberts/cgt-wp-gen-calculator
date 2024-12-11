<?php

function cgt_generator_calculator_shortcode() {
    // Start output buffering to capture the output.
    ob_start();

    // Query to retrieve appliance categories and associated appliances
    $categories = get_terms(array(
        'taxonomy' => 'appliance_category',
        'hide_empty' => false,
    ));

    ?>
    <div id="cgt-generator-calculator">
        <form id="cgt-generator-form">
            <div class="cgt-step" data-step="1">
                <p>Use our Generator Power Calculator to determine which generator model is best for you.</p>
                <label for="cgt_square_footage">Square Footage of Residence</label>
                <input type="number" id="cgt_square_footage" name="square_footage" required>
                <button type="button" class="cgt-next-btn" onclick="cgtMoveNextStep(1)">Start</button>
            </div>
            <?php
            $step_count = 2; // Start at step 2 since step 1 is the intro
            foreach ($categories as $category) {
                ?>
                <div class="cgt-step" data-step="<?php echo $step_count; ?>" style="display:none;">
                    <h2><?php echo esc_html($category->name); ?></h2>
                    <?php
                    $args = array(
                        'post_type' => 'appliance',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'appliance_category',
                                'field' => 'term_id',
                                'terms' => $category->term_id,
                            ),
                        ),
                    );
                    $appliances = new WP_Query($args);
                    if ($appliances->have_posts()) {
                        while ($appliances->have_posts()) {
                            $appliances->the_post();
                            $wattage = get_post_meta(get_the_ID(), 'wattage', true);
                            ?>
                            <label for="appliance-<?php the_ID(); ?>"><?php the_title(); ?> (Watts)</label>
                            <input type="number" id="appliance-<?php the_ID(); ?>" name="appliances[<?php the_ID(); ?>]" value="<?php echo esc_attr($wattage); ?>">
                            <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
                    <button type="button" class="cgt-prev-btn" onclick="cgtMovePreviousStep(<?php echo $step_count; ?>)">Previous</button>
                    <button type="button" class="cgt-next-btn" onclick="cgtMoveNextStep(<?php echo $step_count; ?>)">Next</button>
                </div>
                <?php
                $step_count++;
            }
            ?>
        </form>
        <div id="cgt-total-wattage" style="display:none;">
            Total Wattage Calculated: <span>0</span> Watts
        </div>
    </div>
    <?php

    // JavaScript for navigation between steps
    ?>
    <script type="text/javascript">
        function cgtMoveNextStep(currentStep) {
            var nextStep = currentStep + 1;
            jQuery('.cgt-step[data-step="' + currentStep + '"]').hide();
            jQuery('.cgt-step[data-step="' + nextStep + '"]').show();
        }

        function cgtMovePreviousStep(currentStep) {
            var prevStep = currentStep - 1;
            jQuery('.cgt-step[data-step="' + currentStep + '"]').hide();
            jQuery('.cgt-step[data-step="' + prevStep + '"]').show();
        }
    </script>
    <?php

    // End output buffering and return the output.
    return ob_get_clean();
}

add_shortcode('cgt_calculator', 'cgt_generator_calculator_shortcode');
