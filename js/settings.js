/**
 * Speed Booster Plugin Settings JavaScript
 * Optional enhancements for settings page
 */

$(document).ready(function() {
    // Optional: Add any client-side validation or UX improvements here
    
    // Example: Show warning when disabling all options
    $('#speedBoosterSettings input[type="checkbox"]').on('change', function() {
        var anyChecked = $('#speedBoosterSettings input[type="checkbox"]:checked').length > 0;
        
        if (!anyChecked) {
            // Optional: Show a gentle reminder
            console.log('Note: All minification options are disabled. The plugin will not modify output.');
        }
    });
});