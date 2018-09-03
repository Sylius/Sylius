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
  taxonMoveUp() {
    const element = this;

    element.api({
      method: 'PUT',
      on: 'click',
      beforeSend(settings) {
        /* eslint-disable-next-line no-param-reassign */
        settings.data = {
          position: element.data('position') - 1,
        };

        return settings;
      },
      onSuccess() {
        window.location.reload();
      },
    });
  },

  taxonMoveDown() {
    const element = this;

    element.api({
      method: 'PUT',
      on: 'click',
      beforeSend(settings) {
        /* eslint-disable-next-line no-param-reassign */
        settings.data = {
          position: element.data('position') + 1,
        };

        return settings;
      },
      onSuccess() {
        window.location.reload();
      },
    });
  },
});
