/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import $ from 'jquery';

const methods = {
  init(options) {
    const settings = $.extend({
      prototypePrefix: false,
      containerSelector: false,
    }, options);

    const show = function show(element, replace) {
      const selectedValue = element.val();
      let prototypePrefix = element.attr('id');
      if (settings.prototypePrefix != false) {
        ({ prototypePrefix } = settings);
      }

      const prototypeElement = $(`#${prototypePrefix}_${selectedValue}`);
      let container;

      if (settings.containerSelector) {
        container = $(settings.containerSelector);
      } else {
        container = $(prototypeElement.data('container'));
      }

      if (!container.length) {
        return;
      }

      if (!prototypeElement.length) {
        container.empty();
        return;
      }

      if (replace || !container.html().trim()) {
        container.html(prototypeElement.data('prototype'));
      }
    };

    return this.each((index, element) => {
      show($(element), false);
      $(element).change((event) => {
        show($(event.currentTarget), true);
      });
    });
  },
};

$.fn.handlePrototypes = function handlePrototypes(method, ...args) {
  if (methods[method]) {
    return methods[method].apply(this, args);
  } else if (typeof method === 'object' || !method) {
    return methods.init.apply(this, [method, ...args]);
  }

  $.error(`Method ${method} does not exist on jQuery.handlePrototypes`);

  return undefined;
};
