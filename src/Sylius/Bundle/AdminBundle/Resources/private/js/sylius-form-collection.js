/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

$.fn.extend({
  setFromCollectionOnClickEventHandler(fieldsetId, typeName) {
    $(`#${fieldsetId}`)
      .find('a[data-form-collection="add"]')
      .on('click', event => $(document).triggerChangeOnTypeField(event, typeName));
  },

  triggerChangeOnTypeField(event, typeName) {
    const name = $(event.target).closest('form').attr('name');

    setTimeout(() => {
      $(`select[name^="${name}[${typeName}]"][name$="[type]"]`).last().change();
    }, 50);
  },
});
