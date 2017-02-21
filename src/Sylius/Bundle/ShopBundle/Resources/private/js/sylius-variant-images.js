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
        variantImages: function () {
            if ($('[data-variant-options]').length > 0) {
                handleProductOptionImages();
                handleProductOptionChange();
            } else if ($('[data-variant-code]').length > 0) {
                handleProductVariantImages($('[name="sylius_add_to_cart[cartItem][variant]"]'));
                handleProductVariantChange();
            }
        }
    });
})( jQuery );

function handleProductOptionChange() {
    $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', function () {
        handleProductOptionImages();
    });
}

function handleProductVariantChange() {
    $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', function () {
        handleProductVariantImages($(this))
    });
}

function handleProductOptionImages() {
    var options = '';

    $('#sylius-product-adding-to-cart select').each(function() {
        options += $(this).find('option:selected').val() + ' ';
    });

    var imagesWithOptions = [];
    var optionsArray = options.trim().split(' ');

    $('[data-variant-options]').each(function () {
        var imageOptions = $(this).attr('data-variant-options');
        var imageHasOptions = optionsArray.every(function(option) {
            return imageOptions.indexOf(option) > -1;
        });

        if (imageHasOptions) {
            imagesWithOptions.push($(this).closest('div.ui.image'));
        }
    });

    changeMainImage(imagesWithOptions.shift());
}

function handleProductVariantImages(element) {
    var variantCode = $(element).attr('value');
    var imagesWithVariantCode = [];

    $('[data-variant-code*="'+ variantCode +'"]').each(function () {
        imagesWithVariantCode.push($(this).closest('div.ui.image'));
    });

    changeMainImage(imagesWithVariantCode.shift());
}

function changeMainImage(newImageDiv) {
    var mainImageLink = $('a.ui.fluid.image');
    var mainImage = $('a.ui.fluid.image > img');

    var newImage = $(newImageDiv).find('img');
    var newImageLink = $(newImageDiv).find('a');

    if (newImage.length == 0 && newImageLink.length == 0) {
        mainImage.attr('src', $('div[data-product-image]').attr('data-product-image'));
        newImageLink.attr('href', $('div[data-product-link]').attr('data-product-link'));

        return;
    }

    mainImageLink.attr('href', newImageLink.attr('href'));
    mainImage.attr('src', newImage.attr('data-large-thumbnail'));
}
