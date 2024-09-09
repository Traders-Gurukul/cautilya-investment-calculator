jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.tab').on('click', function() {
        var tabName = $(this).data('tab');
        
        $('.tab').removeClass('active');
        $('.calculator-form').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabName + 'Form').addClass('active');

        // Store active tab in localStorage
        localStorage.setItem('activeTab', tabName);
    });

    // Set active tab on page load
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('.tab[data-tab="' + activeTab + '"]').click();
    }

    // Check if form was just submitted
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('form_submitted') === '1') {
        // Remove the query parameter
        var newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});


jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.tab').on('click', function() {
        var tabName = $(this).data('tab');
        
        $('.tab').removeClass('active');
        $('.calculator-form').removeClass('active');
        
        $(this).addClass('active');
        $('#' + tabName + 'Form').addClass('active');

    });
});