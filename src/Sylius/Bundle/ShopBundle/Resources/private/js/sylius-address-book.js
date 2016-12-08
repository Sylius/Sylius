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
                forceSelection: false,

                onChange: function (name, text, choice) {
                    var provinceCode = choice.data()['provinceCode'];
                    var provinceName = choice.data()['provinceName'];

                    $.each(element.find('input'), function (key, input) {
                        $(input).val('');
                    });
                    $.each(element.find('select'), function (key, select) {
                        $(select).val('');
                    });

                    $.each(choice.data(), function (property, value) {
                        var field = findByName(property);

                        if (-1 !== property.indexOf('countryCode')) {
                            field.val(value).change();

                            var exists = setInterval(function () {
                                var provinceCodeField = findByName('provinceCode');
                                var provinceNameField = findByName('provinceName');

                                if (0 !== provinceCodeField.length && ('' !== provinceCode || undefined != provinceCode)) {
                                    provinceCodeField.val(provinceCode);

                                    clearInterval(exists);
                                } else if (0 !== provinceNameField.length && ('' !== provinceName || undefined != provinceName)) {
                                    provinceNameField.val(provinceName);

                                    clearInterval(exists);
                                }
                            }, 100);
                        } else {
                            field.val(value);
                        }
                    });
                }
            });

            var parseKey = function (key) {
                return key.replace(/(_\w)/g, function (words) {return words[1].toUpperCase()});
            };
            var findByName = function (name) {
                return element.find('[name*=' + parseKey(name) + ']');
            };
        }
    });
})( jQuery );
