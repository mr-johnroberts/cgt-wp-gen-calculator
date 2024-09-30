<?php
function cgt_generator_calculator_shortcode($atts)
{
    wp_enqueue_style('cgt-generator-calculator-style', CGT_GENERATOR_CALCULATOR_URL . 'public/assets/css/cgt-generator-calculator-style.css', [], time(), 'all');

    $script_path = CGT_GENERATOR_CALCULATOR_DIR . 'public/assets/js/cgt-generator-calculator-script.js';
    $script_version = filemtime($script_path);
    wp_register_script('cgt-generator-calculator-script', CGT_GENERATOR_CALCULATOR_URL . 'public/assets/js/cgt-generator-calculator-script.js', ['jquery'], $script_version, true);
    wp_enqueue_script('cgt-generator-calculator-script');

    wp_localize_script('cgt-generator-calculator-script', 'cgtCalculatorVars', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('cgt_ajax_nonce')
    ]);

    $options = get_option('cgt_generator_calculator_options');
    $categories = $options['categories'] ?? [];
    ob_start();
?>
    <div id="cgt-generator-calculator" class="cgt-form-container">
        <div>
            <h2 style="font-weight:600;">Generator Power Calculator</h2>
            <p style="text-align:center; margin-top: -15px !important; margin-bottom:30px; line-height: 20px !important;">Use our Generator Power Calculator<br> to determine which generator model is best for you.</p>
        </div>
        <form id="cgt-generator-form">
            <?php foreach ($categories as $index => $category) : ?>
                <div class="cgt-form-step">
                    <div class="square-foot">
                        <h3>Square Footage of Residence</h3>
                        <input type="number" name="square_footage" class="square-footage-input" value="0" placeholder="Square Footage of Residence" required>
                    </div>
                    <div class="category-title"><?php echo esc_html($category['name']); ?></div>
                    <?php foreach ($category['sub_categories'] as $subIndex => $subCategory) : ?>
                        <div class="sub-category-input-group">
                            <input type="text" readonly value="<?php echo esc_html($subCategory['name']); ?>">
                            <input type="number" value="<?php echo esc_attr($subCategory['wattage']); ?>" class="item-wattage" readonly data-wattage="<?php echo esc_attr($subCategory['wattage']); ?>">
                            <input type="number" name="appliances[<?php echo $index; ?>][<?php echo $subIndex; ?>][user_quantity]" placeholder="Quantity eg. 1,2,etc." class="item-quantity">
                        </div>
                    <?php endforeach; ?>
                    <div>
                        <p style="text-align:justify;margin-top:10px">Only add an "Appliance" and it's corresponding Wattage and indicate the quantity accordingly if it's not in the above default list of appliances for each section. Else, you can ignore and continue with your calculations. Thank you!</p>
                        <div class="item-addition-area">
                            <input type="text" placeholder="Add An Appliance" class="new-item-name">
                            <input type="number" placeholder="Watts" class="new-item-wattage">
                            <button type="button" class="add-item-btn">Add</button>
                        </div>
                    </div>
                    <div class="button-container">
                        <button type="button" class="cgt-prev-btn">Go Back</button>
                        <button type="button" class="cgt-next-btn">Continue</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="cgt-form-step">
                <div class="final-step">
                    <h5 style="text-align: center;">Review your total wattage and select a suitable generator.</h5>
                    <div id="generators-list"></div>
                    <div class="pagination-controls"></div>
                </div>
                <div class="button-container">
                    <button type="button" class="cgt-prev-btn">Go Back</button>
                </div>
            </div>
        </form>
        <div id="cgt-total-wattage" style="display:block;">
            Total Wattage: <span>0</span> kW
        </div>
        <h5 style="text-align: center; margin-top:30px">Please fill out the form above. If you are not able to find what you are looking for call one of our experts on <a href="tel:7138303280" style="color:#d90a07 !important;">713.830.3280</a></h5>
    </div>

<?php
    return ob_get_clean();
}
add_shortcode('cgt-generator', 'cgt_generator_calculator_shortcode');
?>