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
        provinceField: function () {
            var countrySelect = $('select[name$="[countryCode]"]');

            countrySelect.on('change', function(event) {
                var select = $(event.currentTarget);
                var provinceContainer = select.parents('.field').next('div.province-container');

                var provinceSelectFieldName = select.attr('name').replace('country', 'province');
                var provinceInputFieldName = select.attr('name').replace('countryCode', 'provinceName');

                if ('' === select.val() || undefined == select.val()) {
                    provinceContainer.fadeOut('slow', function () {
                        provinceContainer.html('');
                    });

                    return;
                }

                $.get(provinceContainer.attr('data-url'), {countryCode: $(this).val()}, function (response) {
                    if (!response.content) {
                        provinceContainer.fadeOut('slow', function () {
                            provinceContainer.html('');
                        });
                    } else if (-1 !== response.content.indexOf('select')) {
                        provinceContainer.fadeOut('slow', function () {

                            var provinceSelectValue = getProvinceInputValue(
                                $(provinceContainer).find('select > option[selected$="selected"]').val()
                            );

                            provinceContainer.html(response.content.replace(
                                'name="sylius_address_province"',
                                'name="' + provinceSelectFieldName + '"' + provinceSelectValue
                            )
                            .replace(
                                'option value="" selected="selected"',
                                'option value=""'
                            )
                            .replace(
                                'option ' + provinceSelectValue,
                                'option ' + provinceSelectValue + '" selected="selected"'
                            ));

                            provinceContainer.fadeIn();
                        });
                    } else {
                        provinceContainer.fadeOut('slow', function () {

                            var provinceInputValue = getProvinceInputValue($(provinceContainer).find('input').val());

                            provinceContainer.html(response.content.replace(
                                'name="sylius_address_province"',
                                'name="' + provinceInputFieldName + '"' + provinceInputValue
                            ));

                            provinceContainer.fadeIn();
                        });
                    }
                });
            });

            if('' !== countrySelect.val()) {
                countrySelect.trigger('change');
            }

            if('' === $.trim($('div.province-container').text())) {
                $('select.country-select').trigger('change');
            }

            var billingAddressCheckbox = $('input[type="checkbox"][name$="[differentBillingAddress]"]');
            var billingAddressContainer = $('#sylius-billing-address-container');
            var toggleBillingAddress = function() {
                billingAddressContainer.toggle(billingAddressCheckbox.prop('checked'));
            };
            toggleBillingAddress();
            billingAddressCheckbox.on('change', toggleBillingAddress);

            var getProvinceInputValue = function (valueSelector) {
                return undefined == valueSelector ? '' : 'value="'+ valueSelector +'"';
            };
        }
    });
})( jQuery );
