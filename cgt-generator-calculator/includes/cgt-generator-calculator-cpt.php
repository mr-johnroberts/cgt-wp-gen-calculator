<?php

function cgt_register_appliance_cpt()
{
    $labels = [
        'name'                  => _x('Appliances', 'Post type general name', 'cgt-generator-calculator'),
        'singular_name'         => _x('Appliance', 'Post type singular name', 'cgt-generator-calculator'),
        'menu_name'             => _x('Appliances', 'Admin Menu text', 'cgt-generator-calculator'),
        'name_admin_bar'        => _x('Appliance', 'Add New on Toolbar', 'cgt-generator-calculator'),
        'add_new'               => __('Add New', 'cgt-generator-calculator'),
        'add_new_item'          => __('Add New Appliance', 'cgt-generator-calculator'),
        'new_item'              => __('New Appliance', 'cgt-generator-calculator'),
        'edit_item'             => __('Edit Appliance', 'cgt-generator-calculator'),
        'view_item'             => __('View Appliance', 'cgt-generator-calculator'),
        'all_items'             => __('All Appliances', 'cgt-generator-calculator'),
        'search_items'          => __('Search Appliances', 'cgt-generator-calculator'),
        'not_found'             => __('No appliances found.', 'cgt-generator-calculator'),
        'not_found_in_trash'    => __('No appliances found in Trash.', 'cgt-generator-calculator'),
        'featured_image'        => _x('Appliance Cover Image', 'Overrides the “Featured Image” phrase.', 'cgt-generator-calculator'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase.', 'cgt-generator-calculator'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase.', 'cgt-generator-calculator'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase.', 'cgt-generator-calculator'),
        'archives'              => _x('Appliance archives', 'The post type archive label used in nav menus.', 'cgt-generator-calculator'),
        'insert_into_item'      => _x('Insert into appliance', 'Overrides the “Insert into post”/“Insert into page” phrase.', 'cgt-generator-calculator'),
        'uploaded_to_this_item' => _x('Uploaded to this appliance', 'Overrides the “Uploaded to this post”/“Uploaded to this page” phrase.', 'cgt-generator-calculator'),
        'filter_items_list'     => _x('Filter appliances list', 'Screen reader text for the filter links.', 'cgt-generator-calculator'),
        'items_list_navigation' => _x('Appliances list navigation', 'Screen reader text for the pagination.', 'cgt-generator-calculator'),
        'items_list'            => _x('Appliances list', 'Screen reader text for the items list.', 'cgt-generator-calculator'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'appliance'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => ['title', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('appliance', $args);
}

add_action('init', 'cgt_register_appliance_cpt');
