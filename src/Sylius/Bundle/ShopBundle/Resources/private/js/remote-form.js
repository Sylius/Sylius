$(document).ready(function() {
    $('button[data-remote-form], .steps .step.completed[data-remote-form]').on('click', function(e){
        e.preventDefault();
        $("#" + $(this).attr('data-remote-form')).submit();
    });
});
