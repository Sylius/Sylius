!function ($) {

    "use strict";

    $(document).ready(function () {
        $('#sylius_payment_method_gateway').on('change', function (event) {
            var $element = $(event.currentTarget),
                value = $element.val(),
                $container = $element.parent().siblings('[data-form-type="gateway"]');

            var gateway = $(this).parent().parent().children('[data-form-gateway="' + value + '"]').val();

            $container.empty().append(gateway);
        });
    });

}(jQuery);
