/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

$.fn.extend({
  removeAvatarImageButtonVisibility() {
    if (!$('#add-avatar .ui.small.bordered.image').length) {
      $('#remove-avatar-image').hide();
    } else {
      $('#remove-avatar-image').show();
    }
  },
});
