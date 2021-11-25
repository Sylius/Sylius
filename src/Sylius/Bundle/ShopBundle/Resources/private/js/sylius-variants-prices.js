/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

function formatAppliedPromotions(appliedPromotions, locale = 'en_US') {
  let appliedPromotionsElement = '';
  $('#appliedPromotions').html('');

  if (appliedPromotions !== '[]') {
    $.each(appliedPromotions, (index, promotion) => {
      if (promotion[locale].description !== null) {
        appliedPromotionsElement += `<div class="ui blue label promotion_label" style="margin: 1rem 0;"><div class="row ui small sylius_catalog_promotion">${promotion[locale].name} - ${promotion[locale].description}</div></div>`;
      } else {
        appliedPromotionsElement += `<div class="ui blue label promotion_label" style="margin: 1rem 0;"><div class="row ui small sylius_catalog_promotion">${promotion[locale].name}</div></div>`;
      }
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
    const locale = $('#appliedPromotions').attr('data-applied-promotions-locale');
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

      formatAppliedPromotions(appliedPromotions, locale);
    } else {
      $('#product-price').text($('#sylius-variants-pricing').attr('data-unavailable-text'));
      $('button[type=submit]').attr('disabled', 'disabled');
    }
  });
};

const handleProductVariantsChange = function handleProductVariantsChange() {
  $('[name="sylius_add_to_cart[cartItem][variant]"]').on('change', (event) => {
    const priceRow = $(event.currentTarget).parents('tr').find('.sylius-product-variant-price');
    const price = priceRow.text();
    const originalPrice = priceRow.attr('data-original-price');
    const locale = $('#appliedPromotions').attr('data-applied-promotions-locale');
    let appliedPromotions = priceRow.attr('data-applied-promotions');
    if (appliedPromotions !== '[]') {
      appliedPromotions = JSON.parse(appliedPromotions);
    }

    $('#product-price').text(price);
    formatAppliedPromotions(appliedPromotions, locale);

    if (originalPrice !== undefined) {
      $('#product-original-price').css('display', 'inline').html(`<del>${originalPrice}</del>`);
    } else {
      $('#product-original-price').css('display', 'none');
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
