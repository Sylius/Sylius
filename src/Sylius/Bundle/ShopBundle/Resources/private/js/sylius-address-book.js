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
        addressBook: function () {
            var element = $(this);
            var select = element.find('.address-book-select');

            select.dropdown({
                onChange: function (name, text, choice) {

                    var inputs = element.find('input');

                    $.each(inputs, function (key, input) {
                        $(input).val('');
                    });
                    $.each(choice.data(), function (key, property) {
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
