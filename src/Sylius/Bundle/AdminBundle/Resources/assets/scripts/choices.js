/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import Choices from 'choices.js';

(function () {
  // eslint-disable-next-line no-undef
  const items = document.querySelectorAll('[data-choices]');

  if (items.length) {
    items.forEach((el) => {
      // eslint-disable-next-line no-new
      new Choices(el);
    });
  }
}());
