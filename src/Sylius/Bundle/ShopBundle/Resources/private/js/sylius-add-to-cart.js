/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/api';
import $ from 'jquery';

$.fn.extend({
  addToCart() {
    const element = this;
    const url = $(element).attr('action');
    const redirectUrl = $(element).data('redirect');
    const validationElement = $('#sylius-cart-validation-error');

    element.api({
      method: 'POST',
      on: 'submit',
      cache: false,
      url,
      beforeSend(settings) {
        /* eslint-disable-next-line no-param-reassign */
        settings.data = element.serialize();

        return settings;
      },
      onSuccess() {
        validationElement.addClass('hidden');
        window.location.href = redirectUrl;
      },
      onFailure(response) {
        validationElement.removeClass('hidden');
        let validationMessage = '';

        if (response.hasOwnProperty('errors')) {
          Object.entries(response.errors.errors).forEach(([, message]) => {
            validationMessage += message;
          });
        } else if (response.hasOwnProperty('message')) {
          validationMessage = response.message;
        } else {
          validationMessage = 'Could not add your item to the cart.';
        }

        validationElement.html(validationMessage);
        $(element).removeClass('loading');
      },
    });
  },
});
