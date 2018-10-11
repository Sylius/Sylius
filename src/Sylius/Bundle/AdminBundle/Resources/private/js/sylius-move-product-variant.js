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
  moveProductVariant(positionInput) {
    const productVariantRows = [];
    const element = this;

    element.api({
      method: 'PUT',
      beforeSend(settings) {
        /* eslint-disable-next-line no-param-reassign */
        settings.data = {
          productVariants: productVariantRows,
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
      const productVariantId = input.data('id');
      const row = productVariantRows.find(({ id }) => id === productVariantId);

      if (!row) {
        productVariantRows.push({
          id: productVariantId,
          position: input.val(),
        });
      } else {
        row.position = input.val();
      }
    });
  },
});
