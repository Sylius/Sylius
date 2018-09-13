/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import 'semantic-ui-css/components/dropdown';
import $ from 'jquery';

$.fn.extend({
  autoComplete() {
    this.each((idx, el) => {
      const element = $(el);
      const criteriaName = element.data('criteria-name');
      const choiceName = element.data('choice-name');
      const choiceValue = element.data('choice-value');
      const autocompleteValue = element.find('input.autocomplete').val();
      const loadForEditUrl = element.data('load-edit-url');

      element.dropdown({
        delay: {
          search: 250,
        },
        forceSelection: false,
        apiSettings: {
          dataType: 'JSON',
          cache: false,
          beforeSend(settings) {
            /* eslint-disable-next-line no-param-reassign */
            settings.data[criteriaName] = settings.urlData.query;

            return settings;
          },
          onResponse(response) {
            return {
              success: true,
              results: response.map(item => ({
                name: item[choiceName],
                value: item[choiceValue],
              })),
            };
          },
        },
      });

      if (autocompleteValue.split(',').filter(String).length > 0) {
        const menuElement = element.find('div.menu');

        menuElement.api({
          on: 'now',
          method: 'GET',
          url: loadForEditUrl,
          beforeSend(settings) {
            /* eslint-disable-next-line no-param-reassign */
            settings.data[choiceValue] = autocompleteValue.split(',').filter(String);

            return settings;
          },
          onSuccess(response) {
            response.forEach((item) => {
              menuElement.append((
                $(`<div class="item" data-value="${item[choiceValue]}">${item[choiceName]}</div>`)
              ));
            });
          },
        });
      }

      window.setTimeout(() => {
        element.dropdown('set selected', element.find('input.autocomplete').val().split(',').filter(String));
      }, 5000);
    });
  },
});
