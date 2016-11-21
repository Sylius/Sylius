(function ($) {
    'use strict';
    $(document).ready(function() {
        var productIds = [];

        $('.sylius-update-product-taxons').api({
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

        $('.sylius-product-taxon-position').on('input', function () {
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
    });

})( jQuery );
