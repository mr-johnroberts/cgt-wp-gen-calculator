<?php

function cgt_register_appliance_taxonomy()
{
    $labels = [
        'name'              => _x('Categories', 'taxonomy general name', 'cgt-generator-calculator'),
        'singular_name'     => _x('Category', 'taxonomy singular name', 'cgt-generator-calculator'),
        'search_items'      => __('Search Categories', 'cgt-generator-calculator'),
        'all_items'         => __('All Categories', 'cgt-generator-calculator'),
        'parent_item'       => __('Parent Category', 'cgt-generator-calculator'),
        'parent_item_colon' => __('Parent Category:', 'cgt-generator-calculator'),
        'edit_item'         => __('Edit Category', 'cgt-generator-calculator'),
        'update_item'       => __('Update Category', 'cgt-generator-calculator'),
        'add_new_item'      => __('Add New Category', 'cgt-generator-calculator'),
        'new_item_name'     => __('New Category Name', 'cgt-generator-calculator'),
        'menu_name'         => __('Category', 'cgt-generator-calculator'),
    ];

    $args = [
        'hierarchical'      => true, // Make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'appliance-category'],
    ];

    register_taxonomy('appliance_category', ['appliance'], $args);
}

add_action('init', 'cgt_register_appliance_taxonomy');
