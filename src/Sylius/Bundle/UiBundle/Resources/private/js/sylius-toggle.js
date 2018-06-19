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
  toggleElement() {
    this.each((idx, el) => {
      $(el).on('change', (event) => {
        event.preventDefault();

        const toggle = $(event.currentTarget);
        const targetElement = $(`#${toggle.data('toggles')}`);

        if (toggle.is(':checked')) {
          targetElement.show();
        } else {
          targetElement.hide();
        }
      });

      $(el).trigger('change');
    });
  },
});
