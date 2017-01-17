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
        moveProduct: function (positionInput) {
            var productIds = [];

            $(this).api({
                method: 'PUT',
                beforeSend: function (settings) {
                    settings.data = {
                        productTaxons: productIds
                    };

                    return settings;
                },
                onSuccess: function (response) {
                    location.reload();
                }
            });

            positionInput.on('input', function () {
                var id = $(this).data('id');
                var rowToEdit = productIds.filter(function (productTaxon){
                    return productTaxon.id == id;
                });

                if(rowToEdit.length == 0) {
                    productIds.push({
                        id: $(this).data('id'),
                        position: $(this).val()
                    });
                } else {
                    rowToEdit[0].position = $(this).val();
                }
            });
        }
    });
})(jQuery);
