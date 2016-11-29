/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ( $ ) {
    'use strict';

    $.fn.extend({
        addToCart: function () {
            var validationElement = $('#sylius-cart-validation-error');
            validationElement.hide();

            $(this).on('submit', function(event) {
                refresh(this, event, validationElement);
            });
        }
    });

    function refresh(element, event, validationElement) {
        event.preventDefault();
        var data = $(element).serialize();
        var href = $(element).attr('action');
        var redirectUrl = $(element).data('redirect');

        $.ajax({
            type: "POST",
            url: href,
            data: data,
            cache: false,
            success: function (response) {
                validationElement.hide();
                window.location.replace(redirectUrl);
            },
            error: function (response) {
                validationElement.show();
                validationElement.html(response.responseJSON.errors.errors[0]);
                $(element).removeClass('loading');
            }
        })
    }

})( jQuery );
