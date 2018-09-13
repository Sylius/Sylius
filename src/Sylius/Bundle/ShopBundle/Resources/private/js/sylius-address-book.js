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

const parseKey = function parseKey(key) {
  return key.replace(/(_\w)/g, words => words[1].toUpperCase());
};

$.fn.extend({
  addressBook() {
    const element = this;
    const select = element.find('.address-book-select');
    const findByName = function findByName(name) {
      return element.find(`[name*=${parseKey(name)}]`);
    };

    select.dropdown({
      forceSelection: false,

      onChange(name, text, choice) {
        const { provinceCode, provinceName } = choice.data();
        const provinceContainer = select.parent().find('.province-container').get(0);

        element.find('input, select').each((index, input) => {
          $(input).val('');
        });

        Object.entries(choice.data()).forEach(([property, value]) => {
          const field = findByName(property);

          if (property.indexOf('countryCode') !== -1) {
            field.val(value).change();

            const exists = setInterval(() => {
              const provinceCodeField = findByName('provinceCode');
              const provinceNameField = findByName('provinceName');

              if (!provinceContainer.hasAttribute('data-loading')) {
                if (provinceCodeField.length !== 0 && (provinceCode !== '' || provinceCode != undefined)) {
                  provinceCodeField.val(provinceCode);

                  clearInterval(exists);
                } else if (provinceNameField.length !== 0 && (provinceName !== '' || provinceName != undefined)) {
                  provinceNameField.val(provinceName);

                  clearInterval(exists);
                }
              }
            }, 100);
          } else {
            field.val(value);
          }
        });
      },
    });
  },
});
