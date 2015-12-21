/*
 * This file is part of the Sylius sandbox application.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function ( $ ) {
    'use strict';

    $(document).ready(function() {
        $("select.country-select").each(function () {
            var $this = $(this);

            $this.on('change', function() {
                var administrativeAreaContainer = $('div.administrative_area-container');

                $.get(administrativeAreaContainer.attr('data-url'), {countryId: $this.val()}, function (response) {
                    if (!response.content) {
                        administrativeAreaContainer.fadeOut('slow', function () {
                            administrativeAreaContainer.html('');
                        });
                    } else {
                        administrativeAreaContainer.fadeOut('slow', function () {
                            $('select.select2').select2();
                            administrativeAreaContainer.html(response.content.replace('name="sylius_address_administrative_area"', 'name="sylius_address[administrative_area]"'));
                            administrativeAreaContainer.fadeIn();
                        });
                    }
                });

            });
        });

        if($.trim($('div.administrative_area-container').text()) === '') {
            $("select.country-select").trigger("change");
        }
    });
})( jQuery );

