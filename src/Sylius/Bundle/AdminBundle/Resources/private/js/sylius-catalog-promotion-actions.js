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
  loadCatalogPromotionActionConfiguration(target) {
    if (target == null || target.querySelector('#sylius_catalog_promotion_actions select[name*="type"]') == null) {
      return;
    }

    target.querySelector('#sylius_catalog_promotion_actions select[name*="type"]').onchange = function () {
      const parent = this.parentElement;
      const newConfig = document.createElement('div');
      newConfig.innerHTML = this.selectedOptions[0].getAttribute('data-configuration');
      const oldConfig = parent.nextElementSibling;

      parent.parentElement.replaceChild(newConfig, oldConfig);

      const oldConfigInputName = oldConfig.querySelector('input').getAttribute('name');
      let newConfigInputs = newConfig.querySelectorAll('input');

      newConfigInputs.forEach(element => {
        let newConfigInputName = element.getAttribute('name');

        newConfigInputName = oldConfigInputName.replace(
          oldConfigInputName.substring(oldConfigInputName.indexOf('[configuration]') + 15),
          newConfigInputName.substring(newConfigInputName.indexOf('configuration') + 13),
        );

        $(element).attr('name', newConfigInputName);
      });
    };
  },
});
