jQuery(document).ready(function($) {
    $('#cgt-add-category').click(function(e) {
        e.preventDefault();
        let categoryName = prompt("Enter the new category name", "");
        let categoryWattage = prompt("Enter the default wattage for this category", "");

        if (categoryName && categoryWattage) {
            $.ajax({
                method: "POST",
                url: cgtCalculatorVars.ajax_url,
                data: {
                    action: 'cgt_add_appliance_category',
                    name: categoryName,
                    wattage: categoryWattage,
                    nonce: cgtCalculatorVars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Category added successfully.');
                        // Refresh the page to show the new category
                        location.reload();
                    } else {
                        alert('Failed to add category.');
                    }
                }
            });
        }
    });

    $('.cgt-remove-category').click(function(e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to remove this category?")) {
            return;
        }
        let index = $(this).data('index');
        $.ajax({
            method: "POST",
            url: cgtCalculatorVars.ajax_url,
            data: {
                action: 'cgt_remove_appliance_category',
                index: index,
                nonce: cgtCalculatorVars.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Category removed successfully.');
                    // Refresh the page to reflect the removed category
                    location.reload();
                } else {
                    alert('Failed to remove category.');
                }
            }
        });
    });
});
