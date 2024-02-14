/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import AutocompleteController from '@symfony/ux-autocomplete';

export default class extends AutocompleteController {
  observer;

  initialize() {
    super.initialize();

    this.element.addEventListener('change', () => {
      this.tomSelect.sync();
    });
    this.observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        this.tomSelect.sync();
      });
    });
  }

  connect() {
    super.connect();

    this.observer.observe(this.element, { attributes: true });
  }

  disconnect() {
    super.disconnect();

    this.observer.disconnect();
  }
}
