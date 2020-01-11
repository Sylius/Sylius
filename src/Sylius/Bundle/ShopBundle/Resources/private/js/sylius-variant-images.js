/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const changeMainImage = function changeMainImage(newImageDiv) {
  const mainImageLink = $('a.ui.fluid.image');
  const mainImage = $('a.ui.fluid.image > img');

  const newImage = $(newImageDiv).find('img');
  const newImageLink = $(newImageDiv).find('a');

  if (newImage.length === 0 && newImageLink.length === 0) {
    mainImage.attr('src', $('div[data-product-image]').attr('data-product-image'));
    newImageLink.attr('href', $('div[data-product-link]').attr('data-product-link'));

    return;
  }

  mainImageLink.attr('href', newImageLink.attr('href'));
  mainImage.attr('src', newImage.attr('data-large-thumbnail'));
};

const handleProductOptionImages = function handleProductOptionImages() {
  let options = '';

  $('#sylius-product-adding-to-cart select').each((index, select) => {
    options += `${$(select).find('option:selected').val()} `;
  });

  const imagesWithOptions = [];
  const optionsArray = options.trim().split(' ');

  $('[data-variant-options]').each((index, element) => {
    const imageOptions = $(element).attr('data-variant-options');
    const imageHasOptions = optionsArray.every(option => imageOptions.indexOf(option) > -1);

    if (imageHasOptions) {
      imagesWithOptions.push($(element).closest('div.ui.image'));
    }
  });

  changeMainImage(imagesWithOptions.shift());
};

const handleProductOptionChange = function handleProductOptionChange() {
  $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', () => {
    handleProductOptionImages();
  });
};

const handleProductVariantImages = function handleProductVariantImages(variantElement) {
  const variantCode = $(variantElement).attr('value');
  const imagesWithVariantCode = [];

  $(`[data-variant-code*="${variantCode}"]`).each((index, element) => {
    imagesWithVariantCode.push($(element).closest('div.ui.image'));
  });

  changeMainImage(imagesWithVariantCode.shift());
};

const handleProductVariantChange = function handleProductVariantChange() {
  $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', (event) => {
    handleProductVariantImages($(event.currentTarget));
  });
};

$.fn.extend({
  variantImages() {
    if ($('[data-variant-options]').length > 0) {
      handleProductOptionImages();
      handleProductOptionChange();
    } else if ($('[data-variant-code]').length > 0) {
      handleProductVariantImages($('[name="sylius_add_to_cart[cartItem][variant]"]'));
      handleProductVariantChange();
    }
  },
});
