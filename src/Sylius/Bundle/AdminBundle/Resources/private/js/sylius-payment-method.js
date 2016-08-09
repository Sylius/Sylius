/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
