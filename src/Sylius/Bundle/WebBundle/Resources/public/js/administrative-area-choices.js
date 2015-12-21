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
        $('select[name$="[country]"]').on('change', function(event) {
            var $select = $(event.currentTarget);
            var $administrativeAreaContainer = $select.closest('div.form-group').next('div.administrative-area-container');
            var administrativeAreaName = $select.attr('name').replace('country', 'administrative_area');

            if ('' === $select.val()) {
                $administrativeAreaContainer.fadeOut('slow', function () {
                    $administrativeAreaContainer.html('');
                });
                return;
            }

            $.get($administrativeAreaContainer.attr('data-url'), {countryId: $(this).val()}, function (response) {
                if (!response.content) {
                    $administrativeAreaContainer.fadeOut('slow', function () {
                        $administrativeAreaContainer.html('');
                    });
                } else {
                    $administrativeAreaContainer.fadeOut('slow', function () {
                        $administrativeAreaContainer.html(response.content.replace(
                            'name="sylius_address_administrative_area"',
                            'name="' + administrativeAreaName + '"'
                        ));

                        $administrativeAreaContainer.fadeIn();
                    });
                }
            });
        });

        if('' === $.trim($('div.administrative-area-container').text())) {
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
