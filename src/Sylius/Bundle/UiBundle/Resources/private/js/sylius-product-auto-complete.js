/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/dropdown';
import $ from 'jquery';
import { sanitizeInput } from "./sylius-sanitizer";

$.fn.extend({
  productAutoComplete() {
    this.each((index, element) => {
      const $element = $(element);
      $element.dropdown('set selected', $element.find('input[name*="[associations]"]').val().split(',').filter(String));
    });

    this.dropdown({
      delay: {
        search: 250,
      },
      forceSelection: false,
      apiSettings: {
        dataType: 'JSON',
        cache: false,
        data: {
          criteria: { search: { type: 'contains', value: '' } },
        },
        beforeSend(settings) {
          /* eslint-disable-next-line no-param-reassign */
          settings.data.criteria.search.value = settings.urlData.query;

          return settings;
        },
        onResponse(response) {
          return {
            success: true,
            results: response._embedded.items.map(item => ({
              name: sanitizeInput(item.name),
              value: sanitizeInput(item.code),
            })),
          };
        },
      },
      onAdd(addedValue, addedText, $addedChoice) {
        const inputAssociation = $addedChoice.parents('.product-select').find('input[name*="[associations]"]');
        const associatedProductCodes = inputAssociation.val().length > 0 ? inputAssociation.val().split(',').filter(String) : [];

        associatedProductCodes.push(addedValue);
        $.unique(associatedProductCodes.sort());

        inputAssociation.attr('value', associatedProductCodes.join());
      },
      onRemove(removedValue, removedText, $removedChoice) {
        const inputAssociation = $removedChoice.parents('.product-select').find('input[name*="[associations]"]');
        const associatedProductCodes = inputAssociation.val().length > 0 ? inputAssociation.val().split(',').filter(String) : [];

        associatedProductCodes.splice($.inArray(removedValue, associatedProductCodes), 1);

        inputAssociation.attr('value', associatedProductCodes.join());
      },
    });
  },
});
