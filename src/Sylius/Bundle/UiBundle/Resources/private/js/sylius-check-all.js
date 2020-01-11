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
  checkAll() {
    this.each((idx, el) => {
      const $checkboxAll = $(el);
      const $checkboxes = $($checkboxAll.attr('data-js-bulk-checkboxes'));
      const $buttons = $($checkboxAll.attr('data-js-bulk-buttons'));

      const isAnyChecked = () => {
        let checked = false;
        $checkboxes.each((i, checkbox) => {
          if (checkbox.checked) checked = true;
        });
        return checked;
      };

      const buttonsPropRefresh = () => {
        $buttons.find('button').prop('disabled', !isAnyChecked());
      };

      $checkboxAll.on('change', () => {
        $checkboxes.prop('checked', $(this).is(':checked'));
        buttonsPropRefresh();
      });

      $checkboxes.on('change', () => {
        $checkboxAll.prop('checked', isAnyChecked());
        buttonsPropRefresh();
      });

      buttonsPropRefresh();
    });
  },
});
