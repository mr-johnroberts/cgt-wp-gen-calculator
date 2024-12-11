jQuery(document).ready(function($) {
    // Handle Next and Previous button clicks
    $('.cgt-next-btn, .cgt-prev-btn').on('click', function() {
        var currentStep = $(this).closest('.cgt-step');
        var targetStep = $(this).hasClass('cgt-next-btn') ? currentStep.next('.cgt-step') : currentStep.prev('.cgt-step');
        
        currentStep.hide();
        targetStep.show();
    });

    // Handle the wattage calculation on field change
    $('#cgt-generator-form').on('change', 'input[type="number"]', function() {
        var formData = $('#cgt-generator-form').serializeArray();
        
        $.ajax({
            url: cgtCalculatorVars.ajax_url,
            type: 'POST',
            data: {
                action: 'cgt_calculate_wattage',
                security: cgtCalculatorVars.nonce,
                data: formData
            },
            success: function(response) {
                if (response.success) {
                    $('#cgt-total-wattage span').text(response.data.total_wattage + ' Watts');
                    $('#cgt-total-wattage').show();
                } else {
                    console.error('Error: ' + response.data.message);
                }
            }
        });
    });
});
