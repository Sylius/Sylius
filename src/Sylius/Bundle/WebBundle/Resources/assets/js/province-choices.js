/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function ( $ ) {
    $(document).ready(function() {
        $('select[name$="[country]"]').on('change', function() {
            var provinceContainer = $(this).parents('div.well').find('div.province-container');
            var provinceName = $(this).attr('name').replace('country', 'province');

            $.get(provinceContainer.attr('data-url'), {countryId: $(this).val()}, function (response) {
                if (!response.content) {
                    provinceContainer.fadeOut('slow', function () {
                        provinceContainer.html('');
                    });
                } else {
                    provinceContainer.fadeOut('slow', function () {
                        provinceContainer.html(response.content.replace(
                            'name="sylius_address_province"',
                            'name="' + provinceName + '"'
                        ));

                        provinceContainer.fadeIn();
                    });
                }
            });
        });

        if('' === $.trim($('div.province-container').text())) {
            $('select.country-select').trigger('change');
        }

        var billingAddressCheckbox = $('input[type="checkbox"][name$="[differentBillingAddress]"]');
        var toggleBillingAddress = function() {
            $('#sylius-billing-address-container').toggle('checked' === billingAddressCheckbox.attr('checked'));
        };
        toggleBillingAddress();
        billingAddressCheckbox.on('change', toggleBillingAddress);
    });
})( jQuery );
