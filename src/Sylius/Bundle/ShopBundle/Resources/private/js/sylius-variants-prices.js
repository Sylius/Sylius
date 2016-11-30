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
        variantPrices: function () {
            if ($('#sylius-variants-pricing').length > 0) {
                handleProductOptionsChange();
            } else if ($("#sylius-product-variants").length > 0) {
                handleProductVariantsChange();
            }
        }
    });
})( jQuery );

function handleProductOptionsChange() {
    $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', function() {
        $selector = '';

        $('#sylius-product-adding-to-cart select').each(function() {
            $selector += '[data-' + $(this).attr('data-option') + '="' + $(this).find('option:selected').text() + '"]';
        });

        $price = $($selector).attr('data-value');

        if ($price !== undefined) {
            $('#product-price').text($price);
        } else {
            $('#product-price').text($('#sylius-variants-pricing').attr('data-unavailable-text'));
        }
    });
}

function handleProductVariantsChange() {
    $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', function() {
        $price = $(this).parents('tr').find('td:nth-child(2)').text();
        $('#product-price').text($price);
    });
}
