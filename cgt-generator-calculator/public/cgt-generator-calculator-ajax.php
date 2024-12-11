<?php

function cgt_calculate_total_wattage()
{
    // Verify the nonce to secure the AJAX request
    check_ajax_referer('cgt_calculator_nonce', 'security');

    if (isset($_POST['data'])) {
        $data = $_POST['data'];
        $total_wattage = 0;

        // Parse the data and calculate the total wattage
        foreach ($data as $item) {
            if (isset($item['name'], $item['value']) && strpos($item['name'], 'appliances[') !== false) {
                $appliance_id = intval(str_replace(array('appliances[', ']'), '', $item['name']));
                $wattage = intval($item['value']); // Ensure wattage is treated as an integer
                $total_wattage += $wattage;
            }
        }

        // Send the result back to the client
        wp_send_json_success(array('total_wattage' => $total_wattage));
    } else {
        // Send an error if data is not received
        wp_send_json_error(array('message' => 'No data received.'));
    }
}

add_action('wp_ajax_cgt_calculate_wattage', 'cgt_calculate_total_wattage');
add_action('wp_ajax_nopriv_cgt_calculate_wattage', 'cgt_calculate_total_wattage');
