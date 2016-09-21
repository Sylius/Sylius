$(document).ready(function() {

    if ($('#variantsPrices').length > 0) {
        handleProductOptionsChange();
    } else if ($("#sylius-product-variants").length > 0) {
        handleProductVariantsChange();
    }
});

function handleProductOptionsChange() {
    $('[name*="sylius_cart_item[variant]"]').on('change', function() {
        $selector = '';

        $('#sylius-product-adding-to-cart select').each(function() {
            $selector += '[data-' + $(this).attr('data-option') + '="' + $(this).find('option:selected').text() + '"]';
        });

        $price = $($selector).attr('data-value');

        if ($price !== undefined) {
            $('#product-price').text($price);
        } else {
            $('#product-price').text($('#variantsPrices').attr('data-unavailable-text'));
        }
    });
}

function handleProductVariantsChange() {
    $('[name="sylius_cart_item[variant]"]').on('change', function() {
        $price = $(this).parents('tr').find('td:nth-child(2)').text();
        $('#product-price').text($price);
    });
}
