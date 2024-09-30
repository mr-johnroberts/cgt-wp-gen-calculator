// jQuery(document).ready(function($) {
//     $('#generate-shortcode').on('click', function(e) {
//         e.preventDefault();
//         var data = {
//             'action': 'cgt_generate_save_shortcode',
//             'nonce': cgtCalculatorVars.nonce
//         };
//         $.post(ajaxurl, data, function(response) {
//             if (response.success) {
//                 $('#shortcode-result').html('Generated Shortcode: <code>' + response.data + '</code>').show();
//             } else {
//                 $('#shortcode-result').html('Failed to generate shortcode.').show();
//             }
//         });
//     });

//     $('#remove-shortcode').on('click', function(e) {
//         e.preventDefault();
//         var data = {
//             'action': 'cgt_remove_saved_shortcode',
//             'nonce': cgtCalculatorVars.nonce
//         };
//         $.post(ajaxurl, data, function(response) {
//             if (response.success) {
//                 $('#shortcode-result').html('Shortcode removed successfully.').hide();
//             }
//         });
//     });
// });
