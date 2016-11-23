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
        apiToggle: function (apiSettings, toggleableElement, isHidden) {
            var element = $(this);
            var apiSettings = apiSettings;
            var toggleableElement = toggleableElement;
            var isHidden = isHidden || true;

            if (isHidden) {
                toggleableElement.hide();
            }

            element.api({
                method: apiSettings.method,
                dataType: apiSettings.dataType || 'json',
                throttle: apiSettings.throttle || 0,
                debug: apiSettings.debug || false,

                beforeSend: apiSettings.beforeSend,
                successTest: apiSettings.successTest,

                onSuccess: function (response) {
                    toggleableElement.show();
                },

                onFailure: function (response) {
                    toggleableElement.hide();
                }
            });

        }
    });
})( jQuery );
