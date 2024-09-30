<?php
function cgt_register_appliance_taxonomy()
{
    $labels = array(
        'name'              => _x('Appliance Categories', 'taxonomy general name', 'cgt-generator-calculator'),
        'singular_name'     => _x('Appliance Category', 'taxonomy singular name', 'cgt-generator-calculator'),
        'search_items'      => __('Search Appliance Categories', 'cgt-generator-calculator'),
        'all_items'         => __('All Appliance Categories', 'cgt-generator-calculator'),
        'parent_item'       => __('Parent Appliance Category', 'cgt-generator-calculator'),
        'parent_item_colon' => __('Parent Appliance Category:', 'cgt-generator-calculator'),
        'edit_item'         => __('Edit Appliance Category', 'cgt-generator-calculator'),
        'update_item'       => __('Update Appliance Category', 'cgt-generator-calculator'),
        'add_new_item'      => __('Add New Appliance Category', 'cgt-generator-calculator'),
        'new_item_name'     => __('New Appliance Category Name', 'cgt-generator-calculator'),
        'menu_name'         => __('Appliance Categories', 'cgt-generator-calculator'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'appliance-category'),
    );

    register_taxonomy('appliance_category', array('appliance'), $args);
}

add_action('init', 'cgt_register_appliance_taxonomy');
