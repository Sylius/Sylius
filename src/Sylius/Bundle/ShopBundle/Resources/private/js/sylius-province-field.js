/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const getProvinceInputValue = function getProvinceInputValue(valueSelector) {
  return valueSelector == undefined ? '' : `value="${valueSelector}"`;
};

$.fn.extend({
  provinceField() {
    const countrySelect = $('select[name$="[countryCode]"]');

    countrySelect.on('change', (event) => {
      const select = $(event.currentTarget);
      const provinceContainer = select.parents('.field').next('div.province-container');

      const provinceSelectFieldName = select.attr('name').replace('country', 'province');
      const provinceInputFieldName = select.attr('name').replace('countryCode', 'provinceName');

      const provinceSelectFieldId = select.attr('id').replace('country', 'province');
      const provinceInputFieldId = select.attr('id').replace('countryCode', 'provinceName');

      const form = select.parents('form');

      if (select.val() === '' || select.val() == undefined) {
        provinceContainer.fadeOut('slow', () => {
          provinceContainer.html('');
        });

        return;
      }

      provinceContainer.attr('data-loading', true);
      form.addClass('loading');

      $.get(provinceContainer.attr('data-url'), { countryCode: select.val() }, (response) => {
        if (!response.content) {
          provinceContainer.fadeOut('slow', () => {
            provinceContainer.html('');

            provinceContainer.removeAttr('data-loading');
            form.removeClass('loading');
          });
        } else if (response.content.indexOf('select') !== -1) {
          provinceContainer.fadeOut('slow', () => {
            const provinceSelectValue = getProvinceInputValue((
              $(provinceContainer).find('select > option[selected$="selected"]').val()
            ));

            provinceContainer.html((
              response.content
                .replace('name="sylius_address_province"', `name="${provinceSelectFieldName}"${provinceSelectValue}`)
                .replace('id="sylius_address_province"', `id="${provinceSelectFieldId}"`)
                .replace('option value="" selected="selected"', 'option value=""')
                .replace(`option ${provinceSelectValue}`, `option ${provinceSelectValue}" selected="selected"`)
            ));

            provinceContainer.removeAttr('data-loading');

            provinceContainer.fadeIn('fast', () => {
              form.removeClass('loading');
            });
          });
        } else {
          provinceContainer.fadeOut('slow', () => {
            const provinceInputValue = getProvinceInputValue($(provinceContainer).find('input').val());

            provinceContainer.html((
              response.content
                .replace('name="sylius_address_province"', `name="${provinceInputFieldName}"${provinceInputValue}`)
                .replace('id="sylius_address_province"', `id="${provinceInputFieldId}"`)
            ));

            provinceContainer.removeAttr('data-loading');

            provinceContainer.fadeIn('fast', () => {
              form.removeClass('loading');
            });
          });
        }
      });
    });

    if (countrySelect.val() !== '') {
      countrySelect.trigger('change');
    }

    if ($.trim($('div.province-container').text()) === '') {
      $('select.country-select').trigger('change');
    }

    const shippingAddressCheckbox = $('input[type="checkbox"][name$="[differentShippingAddress]"]');
    const shippingAddressContainer = $('#sylius-shipping-address-container');
    const toggleShippingAddress = function toggleShippingAddress() {
      shippingAddressContainer.toggle(shippingAddressCheckbox.prop('checked'));
    };
    toggleShippingAddress();
    shippingAddressCheckbox.on('change', toggleShippingAddress);
  },
});
