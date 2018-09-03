/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/popup';
import 'semantic-ui-css/components/rating';
import $ from 'jquery';

import 'sylius/ui/app';
import 'sylius/ui/sylius-api-login';
import 'sylius/ui/sylius-api-toggle';

import './sylius-add-to-cart';
import './sylius-address-book';
import './sylius-province-field';
import './sylius-remove-from-cart';
import './sylius-variant-images';
import './sylius-variants-prices';

$(document).ready(() => {
  $('.cart.button')
    .popup({
      popup: $('.cart.popup'),
      on: 'click',
    });

  $('.star.rating').rating({
    fireOnInit: true,
    onRate(value) {
      $('[name="sylius_product_review[rating]"]:checked').removeAttr('checked');
      $(`#sylius_product_review_rating_${value - 1}`).attr('checked', 'checked');
    },
  });

  $('#sylius_checkout_address_customer_email').apiToggle({
    dataType: 'json',
    method: 'GET',
    throttle: 1500,

    beforeSend(settings) {
      const email = $('#sylius_checkout_address_customer_email').val();

      if (email.length < 3) {
        return false;
      }

      /* eslint-disable-next-line no-param-reassign */
      settings.data = {
        email,
      };

      return settings;
    },

    successTest(response) {
      return $('#sylius_checkout_address_customer_email').val() === response.username;
    },
  }, $('#sylius-api-login-form'));

  $('#sylius-api-login').apiLogin({
    method: 'POST',
    throttle: 500,
  });

  $('.sylius-cart-remove-button').removeFromCart();
  $('#sylius-product-adding-to-cart').addToCart();

  $('#sylius-shipping-address').addressBook();
  $('#sylius-billing-address').addressBook();
  $(document).provinceField();
  $(document).variantPrices();
  $(document).variantImages();
});
