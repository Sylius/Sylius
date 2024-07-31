/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static values = {
    url: String,
    csrfToken: String,
    inputSelector: String,
    dataKey: String
  };
  changedPositions = [];

  connect() {
    document.querySelectorAll(this.inputSelectorValue).forEach(input => {
      input.addEventListener('change', (event) => this.handlePositionChange(event));
    });
  }

  handlePositionChange(event) {
    const input = event.target;
    const elementId = input.getAttribute('data-id');
    const changedPosition = this.changedPositions.find(({ id }) => id === elementId);

    if (!changedPosition) {
      this.changedPositions.push({ id: elementId, position: input.value });
    } else {
      changedPosition.position = input.value;
    }
  }

  submit() {
    const requestOptions = {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ [this.dataKeyValue]: this.changedPositions, _csrf_token: this.csrfTokenValue}),
    };

    fetch(this.urlValue, requestOptions)
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to move positions.');
        }

        window.location.reload();
      })
      .catch(error => console.error(error.message));
  }
}
