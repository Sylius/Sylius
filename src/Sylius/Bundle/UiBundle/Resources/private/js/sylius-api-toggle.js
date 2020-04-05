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
  apiToggle({
    method,
    dataType = 'json',
    throttle = 0,
    debug = false,
    beforeSend,
    successTest,
  }, toggleableElement, isHidden = true) {
    const element = this;

    if (isHidden) {
      toggleableElement.hide();
    }

    element.api({
      method,
      dataType,
      throttle,
      debug,

      beforeSend,
      successTest,

      onSuccess() {
        toggleableElement.show();
      },

      onFailure() {
        toggleableElement.hide();
      },
    });
  },
});
