/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/api';
import $ from 'jquery';

$.fn.extend({
  taxonMove() {
    const element = this;

    element.api({
      method: 'PUT',
      on: 'click',
      onSuccess() {
        window.location.reload();
      },
    });
  },
});
