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
  loadCatalogPromotionScopeConfiguration(target) {
    if (target == null || target.querySelector('#sylius_catalog_promotion_scopes select[name*="type"]') == null) {
      return;
    }

    target.querySelector('#sylius_catalog_promotion_scopes select[name*="type"]').onchange = function () {
      const parent = this.parentElement;
      const newConfig = document.createElement('div');
      newConfig.innerHTML = this.selectedOptions[0].getAttribute('data-configuration');
      const oldConfig = parent.nextElementSibling;

      parent.parentElement.replaceChild(newConfig, oldConfig);

      const oldConfigInputName = oldConfig.querySelector('input').getAttribute('name');
      let newConfigInputName = newConfig.querySelector('input').getAttribute('name');

      newConfigInputName = oldConfigInputName.replace(
        oldConfigInputName.substring(oldConfigInputName.lastIndexOf('[') + 1, oldConfigInputName.lastIndexOf(']')),
        newConfigInputName.substring(newConfigInputName.indexOf('[') + 1, newConfigInputName.lastIndexOf(']')),
      );

      $(newConfig).find('input').attr('name', newConfigInputName);
      $(newConfig).find('.sylius-autocomplete').autoComplete();
    };
  },
});
