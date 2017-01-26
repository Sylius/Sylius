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
        moveProductVariant: function (positionInput) {
            var productVariantIds = [];
            var element = $(this);

            element.api({
                method: 'PUT',
                beforeSend: function (settings) {
                    settings.data = {
                        productVariants: productVariantIds,
                        _csrf_token: element.data('csrf-token')
                    };

                    return settings;
                },
                onSuccess: function (response) {
                    location.reload();
                }
            });

            positionInput.on('input', function () {
                var id = $(this).data('id');
                var rowToEdit = productVariantIds.filter(function (productVariant){
                    return productVariant.id == id;
                });

                if(rowToEdit.length == 0) {
                    productVariantIds.push({
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
