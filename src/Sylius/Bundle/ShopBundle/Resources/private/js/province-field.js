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

    $(document).ready(function() {
        $('select[name$="[countryCode]"]').on('change', function(event) {
            var $select = $(event.currentTarget);
            var $provinceContainer = $select.parents('.field').next('div.province-container');

            var provinceSelectFieldName = $select.attr('name').replace('country', 'province');
            var provinceInputFieldName = $select.attr('name').replace('countryCode', 'provinceName');

            if ('' === $select.val()) {
                $provinceContainer.fadeOut('slow', function () {
                    $provinceContainer.html('');
                });

                return;
            }

            $.get($provinceContainer.attr('data-url'), {countryCode: $(this).val()}, function (response) {
                if (!response.content) {
                    $provinceContainer.fadeOut('slow', function () {
                        $provinceContainer.html('');
                    });
                } else if (-1 !== response.content.indexOf('select')) {
                    $provinceContainer.fadeOut('slow', function () {
                        $provinceContainer.html(response.content.replace(
                            'name="sylius_address_province"',
                            'name="' + provinceSelectFieldName + '"'
                        ));

                        $provinceContainer.fadeIn();
                    });
                } else {
                    $provinceContainer.fadeOut('slow', function () {
                        $provinceContainer.html(response.content.replace(
                            'name="sylius_address_province"',
                            'name="' + provinceInputFieldName + '"'
                        ));

                        $provinceContainer.fadeIn();
                    });
                }
            });
        });

        if('' !== $('select[name$="[countryCode]"]').val()) {
            $('select[name$="[countryCode]"]').trigger('change');
        }

        if('' === $.trim($('div.province-container').text())) {
            $('select.country-select').trigger('change');
        }

        var $billingAddressCheckbox  = $('input[type="checkbox"][name$="[differentBillingAddress]"]');
        var $billingAddressContainer = $('#sylius-billing-address-container');
        var toggleBillingAddress = function() {
            $billingAddressContainer.toggle($billingAddressCheckbox.prop('checked'));
        };
        toggleBillingAddress();
        $billingAddressCheckbox.on('change', toggleBillingAddress);
    });
})( jQuery );
