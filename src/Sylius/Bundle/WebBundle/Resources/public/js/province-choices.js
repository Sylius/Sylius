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
            var $provinceContainer = $select.closest('div.form-group').next('div.province-container');
            var provinceName = $select.attr('name').replace('country', 'province');

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
                } else {
                    $provinceContainer.fadeOut('slow', function () {
                        $provinceContainer.html(response.content.replace(
                            'name="sylius_address_province"',
                            'name="' + provinceName + '"'
                        ));

                        $provinceContainer.fadeIn();
                    });
                }
            });
        });

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
