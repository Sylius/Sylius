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
  moveProduct(positionInput) {
    const productIds = [];
    const element = this;

    element.api({
      method: 'PUT',
      beforeSend(settings) {
        /* eslint-disable-next-line no-param-reassign */
        settings.data = {
          productTaxons: productIds,
          _csrf_token: element.data('csrf-token'),
        };

        return settings;
      },
      onSuccess() {
        window.location.reload();
      },
    });

    positionInput.on('input', (event) => {
      const input = $(event.currentTarget);
      const id = input.data('id');
      const rowToEdit = productIds.filter(productTaxon => productTaxon.id == id);

      if (rowToEdit.length === 0) {
        productIds.push({
          id: input.data('id'),
          position: input.val(),
        });
      } else {
        rowToEdit[0].position = input.val();
      }
    });
  },
});
