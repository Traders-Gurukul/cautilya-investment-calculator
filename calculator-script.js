jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.tab').on('click', function() {
        var tabName = $(this).data('tab');
        
        $('.tab').removeClass('active');
        $('.calculator-form').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabName + 'Form').addClass('active');

        // Clear input values when switching tabs
        $('.calculator-form').trigger('reset');
    });
});