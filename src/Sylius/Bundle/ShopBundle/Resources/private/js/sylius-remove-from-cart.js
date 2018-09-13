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
  removeFromCart() {
    this.each((index, element) => {
      const redirectUrl = $(element).data('redirect');
      const csrfToken = $(element).data('csrf-token');

      $(element).api({
        method: 'DELETE',
        on: 'click',
        beforeSend(settings) {
          /* eslint-disable-next-line no-param-reassign */
          settings.data = {
            _csrf_token: csrfToken,
          };

          return settings;
        },
        onSuccess() {
          window.location.replace(redirectUrl);
        },
      });
    });
  },
});
