jQuery(document).ready(function($) {
    $('#filter-form').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = $(this).serialize(); // Serialize the form data

        $.ajax({
            url: ajax_object.ajax_url, // WordPress AJAX handler URL
            type: 'POST',
            data: {
                action: 'filter_form_submission', // Action hook to handle AJAX
                form_data: formData
            },
            success: function(response) {
                // Print the response (selected taxonomies) to the console
                //console.log(response);
                $('.properties-list').html(response);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });
});
