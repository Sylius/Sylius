(function($) {
    $(document).ready(function() {
        $('.calendar').calendar();
        $('#rangestart').calendar({
            endCalendar: $('#rangeend')
        });
        $('#rangeend').calendar({
            startCalendar: $('#rangestart')
        });
    });
})(jQuery);
