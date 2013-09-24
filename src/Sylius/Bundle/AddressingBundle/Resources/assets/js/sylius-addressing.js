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
                var provinceContainer = $('div.province-container');

                $.get(provinceContainer.attr('data-url'), {countryId: $this.val()}, function (response) {
                    if (!response.content) {
                        provinceContainer.fadeOut('slow', function () {
                            provinceContainer.html('');
                        });
                    } else {
                        provinceContainer.fadeOut('slow', function () {
                            $('select.select2').select2();
                            provinceContainer.html(response.content.replace('name="sylius_address_province"', 'name="sylius_address[province]"'));
                            provinceContainer.fadeIn();
                        });
                    }
                });

            });
        });

        if($.trim($('div.province-container').text()) === '') {
            $("select.country-select").trigger("change");
        }
    });
})( jQuery );

