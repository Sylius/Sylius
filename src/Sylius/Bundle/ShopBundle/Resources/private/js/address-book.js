/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ( $ ) {
    $.fn.extend({
        addressBook: function () {
            var element = $(this);
            var addresses = [];
            var select = element.find('.address-book-select');

            select.dropdown({
                apiSettings: {
                    action: 'address book',
                    cache: false,

                    onResponse: function (syliusResponse) {
                        var response = {
                            success: true,
                            results: []
                        };

                        $.each(syliusResponse, function (index, address) {
                            addresses.push(address);

                            response.results.push({
                                name: address.city + ' ' + address.street,
                                value: address.id
                            });
                        });

                        return response;
                    }
                },

                onChange: function (name, text, choice) {
                    var selectedAddress = addresses.filter(function (address) {
                        return address.id === choice.data().value;
                    })[0];

                    var inputs = element.find('input');

                    $.each(inputs, function (key, input) {
                        $(input).val('');
                    });
                    $.each(selectedAddress, function (key, property) {
                        element.find('input[name*='+ parseKey(key) +']').val(property);
                    });
                }
            });

            var parseKey = function (key) {
                return key.replace(/(_\w)/g, function (m) {return m[1].toUpperCase()});
            }
        }
    });
})( jQuery );
