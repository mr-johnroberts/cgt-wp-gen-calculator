jQuery(document).ready(function($) {
    console.log("CGT Admin JS Loaded"); // Debugging log

    $('#cgt-save-settings').on('click', function(e) {
        e.preventDefault();

        var formData = $('#cgt-categories-wrapper').serialize();

        $.ajax({
            url: cgtCalculatorVars.ajax_url,
            type: 'POST',
            data: {
                action: 'save_cgt_configuration',
                nonce: cgtCalculatorVars.nonce_save,
                data: formData
            },
            success: function(response) {
                if (response.success) {
                    // Refresh the page immediately upon success
                    window.location.reload();
                } else {
                    $('#cgt-settings-message').html('<div class="notice notice-error is-dismissible"><p>Error: ' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $('#cgt-settings-message').html('<div class="notice notice-error is-dismissible"><p>An error occurred while saving the settings.</p></div>');
            }
        });
    });

    $('#cgt-delete-shortcode').on('click', function() {
        if (!confirm('Are you sure you want to delete the shortcode and associated settings? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: cgtCalculatorVars.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_cgt_shortcode',
                nonce: cgtCalculatorVars.nonce_delete
            },
            success: function(response) {
                if (response.success) {
                    // Refresh the page immediately upon success
                    window.location.reload();
                } else {
                    $('#cgt-settings-message').html('<div class="notice notice-error is-dismissible"><p>Error: ' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $('#cgt-settings-message').html('<div class="notice notice-error is-dismissible"><p>An error occurred while deleting the shortcode.</p></div>');
            }
        });
    });

    $(document).on('click', '#cgt-add-category', function(e) {
        e.preventDefault();
        var categoryIndex = $('#cgt-categories-wrapper .cgt-category-row').length;

        var mainCategory = $('<div class="cgt-category-row">' +
            '<input type="text" name="cgt_generator_calculator_options[categories][' + categoryIndex + '][name]" placeholder="Main Category" class="category-name">' +
            '<p class="cgt-instruction">Enter the main category and click "Save Changes" to add appliances.</p>' +
            '<button type="button" class="button cgt-add-sub-category" disabled>Add Appliance</button>' +
            '<div class="cgt-subcategories"></div>' +
            '<button type="button" class="button cgt-remove-category">Remove Main Category</button>' +
            '</div>');
        mainCategory.appendTo('#cgt-categories-wrapper');
    });

    $(document).on('click', '.cgt-add-sub-category', function(e) {
        e.preventDefault();
        var subIndex = $(this).siblings('.cgt-subcategories').children().length;
        var mainIndex = $(this).closest('.cgt-category-row').index();
        $(this).siblings('.cgt-subcategories').append(
            '<div class="cgt-subcategory-row">' +
            '<input type="text" name="cgt_generator_calculator_options[categories][' + mainIndex + '][sub_categories][' + subIndex + '][name]" placeholder="Appliance" class="subcategory-name">' +
            '<input type="number" name="cgt_generator_calculator_options[categories][' + mainIndex + '][sub_categories][' + subIndex + '][wattage]" placeholder="Default Wattage" class="subcategory-wattage">' +
            '<button type="button" class="button cgt-remove-sub-category">Remove</button>' +
            '</div>'
        );
    });

    $(document).on('click', '.cgt-remove-category', function(e) {
        e.preventDefault();
        $(this).closest('.cgt-category-row').remove();
    });

    $(document).on('click', '.cgt-remove-sub-category', function(e) {
        e.preventDefault();
        $(this).closest('.cgt-subcategory-row').remove();
    });

    // Remove instruction and enable "Add Subcategory" button after page reload
    $('#cgt-categories-wrapper .cgt-category-row').each(function() {
        $(this).find('.cgt-instruction').remove();
        $(this).find('.cgt-add-sub-category').prop('disabled', false);
    });
});
