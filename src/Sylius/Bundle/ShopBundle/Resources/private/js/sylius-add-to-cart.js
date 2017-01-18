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
            $(this).on('submit', function(event) {
                refresh(this, event);
            });
        },
        disable: function() {
            $('button[type=submit]', this).attr('disabled', 'disabled');
        },
        enable: function() {
            $('button[type=submit]', this).removeAttr('disabled');
        }
    });

    function refresh(element, event) {
        event.preventDefault();
        var data = $(element).serialize();
        var href = $(element).attr('action');
        var redirectUrl = $(element).data('redirect');
        var validationElement = $('#sylius-cart-validation-error');

        $.ajax({
            type: "POST",
            url: href,
            data: data,
            cache: false,
            success: function (response) {
                validationElement.addClass('hidden');
                window.location.replace(redirectUrl);
            },
            error: function (response) {
                validationElement.removeClass('hidden');
                var validationMessage = '';
                $.each(response.responseJSON.errors.errors, function (key, message) {
                    validationMessage += message;
                });
                validationElement.html(validationMessage);
                $(element).removeClass('loading');
            }
        })
    }

})( jQuery );
