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
  connected = false;

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
    window.addEventListener('sylius_admin.product_attribute_autocomplete.clear_requested', () => {
      this.tomSelect.clear();
    });
  }

  connect() {
    super.connect();

    this.observer.observe(this.element, { attributes: true });

    this.connected = true;
  }

  disconnect() {
    super.disconnect();

    this.observer.disconnect();

    this.connected = false;
  }

  urlValueChanged() {
    if (!this.connected) {
      return;
    }

    this.disconnect();
    this.connect();
    this.tomSelect.refreshItems();
  }
}
