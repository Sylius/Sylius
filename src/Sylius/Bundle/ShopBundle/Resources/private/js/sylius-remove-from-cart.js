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
        removeFromCart: function () {
            $.each($(this), function (index, element) {
                var redirectUrl = $(element).data('redirect');
                var csrfToken = $(element).data('csrf-token');

                $(element).api({
                    method: 'DELETE',
                    on: 'click',
                    beforeSend: function (settings) {
                        settings.data = {
                            _csrf_token: csrfToken
                        };

                        return settings;
                    },
                    onSuccess: function (response) {
                        window.location.replace(redirectUrl);
                    }
                });
            });
        }
    });

})( jQuery );
