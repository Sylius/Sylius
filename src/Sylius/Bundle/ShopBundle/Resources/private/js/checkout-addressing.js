/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function () {
    var email = $('#sylius_checkout_address_customer_email');
    email.apiToggle({
        action: 'user check',
        dataType: 'json',
        method: 'GET',
        throttle: 1500,

        beforeSend: function (settings) {
            settings.data = {
                email: email.val()
            };

            return settings;
        },

        successTest: function (response) {
            return email.val() === response.username;
        }
    }, $('#sylius-api-login-form'));

    $('#sylius-api-login').apiLogin({
        action: 'login check',
        method: 'POST',
        throttle: 500
    });

    $('#sylius-shipping-address').addressBook();
    $('#sylius-billing-address').addressBook();
});