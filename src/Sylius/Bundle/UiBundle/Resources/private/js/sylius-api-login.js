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
        apiLogin: function (apiSettings) {
            var element = $(this);
            var apiSettings = apiSettings;
            var passwordField = element.find('input[type=\'password\']');
            var emailField = element.find('input[type=\'email\']');
            var csrfTokenField = element.find('input[type=\'hidden\']');
            var signInButton = element.find('.button');
            var validationField = element.find('.red.label');

            signInButton.api({
                method: apiSettings.method,
                dataType: apiSettings.dataType || 'json',
                throttle: apiSettings.throttle || 0,
                debug: apiSettings.debug || false,

                beforeSend: function (settings) {
                    settings.data = {
                        _username: emailField.val(),
                        _password: passwordField.val()
                    };
                    settings.data[csrfTokenField.attr('name')] = csrfTokenField.val();

                    return settings;
                },

                successTest: function (response) {
                    return response.success;
                },

                onSuccess: function (response) {
                    element.remove();
                    location.reload();
                },

                onFailure: function (response) {
                    validationField.removeClass('hidden');
                    validationField.html(response.message);
                }
            });

        }
    });
})( jQuery );
