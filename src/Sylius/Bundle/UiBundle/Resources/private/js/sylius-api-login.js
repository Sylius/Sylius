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
  apiLogin({
    method,
    dataType = 'json',
    throttle = 0,
    debug = false,
  }) {
    const element = this;
    const passwordField = element.find('input[type="password"]');
    const emailField = element.find('input[type="email"]');
    const csrfTokenField = element.find('input[type="hidden"]');
    const signInButton = element.find('.button');
    const validationField = element.find('.red.label');

    signInButton.api({
      method,
      dataType,
      throttle,
      debug,

      beforeSend(settings) {
        /* eslint-disable-next-line no-param-reassign */
        settings.data = {
          _username: emailField.val(),
          _password: passwordField.val(),
          [csrfTokenField.attr('name')]: csrfTokenField.val(),
        };

        return settings;
      },

      successTest(response) {
        return response.success;
      },

      onSuccess() {
        element.remove();
        window.location.reload();
      },

      onFailure(response) {
        validationField.removeClass('hidden');
        validationField.html(response.message);
      },
    });
  },
});
