(function ($) {
    'use strict';

    $(document).ready(function () {
        var button;

        $('table#job-table').on('click', '.btn-run--job', function (e) {
            e.preventDefault();

            button = $(this);

            if (button.is("a")) {
                $('#run-job-confirmation-modal #run-job-confirmation-modal-confirm').attr('href', button.attr('href'));

            }

            //We pass the job id to the modal, so we can retrieve it from there when the run button is clicked
            $('#run-job-confirmation-modal #run-job-confirmation-modal-confirm').data('job-id', button.data('job-id'));
            $('#run-job-confirmation-modal').modal('show');
        });

        $('#run-job-confirmation-modal-confirm').click(function (e) {
            var href = $('#run-job-confirmation-modal #run-job-confirmation-modal-confirm').attr('href');
            var job_id = $(this).data('job-id');
            e.preventDefault();
            $.get(href,
                function (response) {
                    $('#content').prepend(response).fadeIn(function () {
                        //Show the returned flash message
                        $('[data-flash="' + job_id + '"]').delay(4000).fadeOut();
                    });
                    //Show the spinner
                    $('[data-img="' + job_id + '"]').show();

                    //Don't toggle because in case we run a long process twice screen data would be inconsistent
                    $('[data-not-running-id="' + job_id + '"]').hide();
                    $('[data-running-id="' + job_id + '"]').show();


                    //Poll to check if the job has finished
                    poll(job_id);
                }, "html");

            //Hide the modal popup
            $('#run-job-confirmation-modal').modal('hide');
        });
    });

    function poll(job_id) {
        var dataRowSelector = '[data-row-id="' + job_id + '"]';
        var pollUrl = $(dataRowSelector).data('url-poll');
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: pollUrl,
            success: function (data) {
                var obj = JSON.parse(data);
                if (obj) {
                    //If the job hasn't finished we wait one second and poll again
                    setTimeout(function () {
                        poll(job_id);
                    }, 1000);
                } else {
                    var rowUrl = $(dataRowSelector).data('url-row-template');

                    //Once the job has finished we replace the table row with the updated data row
                    $.get(rowUrl,
                        function (response) {
                            $(dataRowSelector).replaceWith(response).fadeIn();
                        }, "html");
                }
            }
        });
    };

})(jQuery);
