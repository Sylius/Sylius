/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

function formatAppliedPromotions(appliedPromotions) {
  let appliedPromotionsElement = '';
  $('#appliedPromotions').html('');

  if (appliedPromotions !== '[]') {
    $.each(appliedPromotions, (index, promotion) => {
      appliedPromotionsElement += `<div style="margin-top: 20px;">
        <div class="ui label promotion_label">
          <div class="row ui small sylius_catalog_promotion">${promotion.label}</div>
        </div>
        <span class="text-teal">${promotion.description}</span>
      </div>`;
    });
    $('#appliedPromotions').html(appliedPromotionsElement);
  }
}

const handleProductOptionsChange = function handleProductOptionsChange() {
  $('[name*="sylius_add_to_cart[cartItem][variant]"]').on('change', () => {
    let selector = '';

    $('#sylius-product-adding-to-cart select[data-option]').each((index, element) => {
      const select = $(element);
      const option = select.find('option:selected').val();
      selector += `[data-${select.attr('data-option')}="${option}"]`;
    });

    const price = $('#sylius-variants-pricing').find(selector).attr('data-value');
    const originalPrice = $('#sylius-variants-pricing').find(selector).attr('data-original-price');
    let appliedPromotions = $('#sylius-variants-pricing').find(selector).attr('data-applied_promotions');
    if (appliedPromotions !== undefined) {
      appliedPromotions = JSON.parse(appliedPromotions);
    }

    if (price !== undefined) {
      $('#product-price').text(price);
      $('button[type=submit]').removeAttr('disabled');

      if (originalPrice !== undefined) {
        $('#product-original-price').css('display', 'inline').html(`<del>${originalPrice}</del>`);
      } else {
        $('#product-original-price').css('display', 'none');
      }

      formatAppliedPromotions(appliedPromotions);
    } else {
      $('#product-price').text($('#sylius-variants-pricing').attr('data-unavailable-text'));
      $('button[type=submit]').attr('disabled', 'disabled');
    }

    const lowestPriceBeforeDiscount = $('#sylius-variants-pricing').find(selector).attr('data-lowest-price-before-discount');

    if (lowestPriceBeforeDiscount !== undefined) {
      $('#lowest-price-before-discount')
        .html(lowestPriceBeforeDiscount)
        .css({ 'white-space': 'nowrap', display: 'inline' });
    } else {
      $('#lowest-price-before-discount').css('display', 'none');
    }
  });
};

const handleProductVariantsChange = function handleProductVariantsChange() {
  $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', (event) => {
    const priceRow = $(event.currentTarget).parents('tr').find('.sylius-product-variant-price');
    const price = priceRow.text();
    const originalPrice = priceRow.attr('data-original-price');
    let appliedPromotions = priceRow.attr('data-applied-promotions');
    if (appliedPromotions !== '[]') {
      appliedPromotions = JSON.parse(appliedPromotions);
    }

    $('#product-price').text(price);
    formatAppliedPromotions(appliedPromotions);

    if (originalPrice !== undefined) {
      $('#product-original-price').css('display', 'inline').html(`<del>${originalPrice}</del>`);
    } else {
      $('#product-original-price').css('display', 'none');
    }

    const lowestPriceBeforeDiscount = priceRow.attr('data-lowest-price-before-discount');

    if (lowestPriceBeforeDiscount !== undefined) {
      $('#lowest-price-before-discount')
        .html(lowestPriceBeforeDiscount)
        .css({ 'white-space': 'nowrap', display: 'inline' });
    } else {
      $('#lowest-price-before-discount').css('display', 'none');
    }
  });
};

$.fn.extend({
  variantPrices() {
    if ($('#sylius-variants-pricing').length > 0) {
      handleProductOptionsChange();
    } else if ($('#sylius-product-variants').length > 0) {
      handleProductVariantsChange();
    }
  },
});
