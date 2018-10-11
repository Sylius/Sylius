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
  addTabErrors() {
    const element = this;

    $('.ui.segment > .ui.tab').each((idx, el) => {
      const errors = $(el).find('.sylius-validation-error');

      if (errors.length !== 0) {
        const tabName = $(el).attr('data-tab');
        const tabWithErrors = $(element).find(`a.item[data-tab="${tabName}"]`);

        const label = tabWithErrors.html();
        const newLabel = `${label}<span class="ui small horizontal circular label" style="background-color: #DB2828">${errors.length}</span>`;

        tabWithErrors.html(newLabel);
      }
    });
  },

  addAccordionErrors() {
    const element = this;
    const accordionElements = element.find('.ui.content');

    $(accordionElements).each((idx, el) => {
      const errors = $(el).find('.sylius-validation-error');

      if (errors.length !== 0) {
        const ribWithErrors = $(el).closest('[data-locale]').find('.title');

        ribWithErrors.css('color', '#DB2828');
      }
    });
  },
});
