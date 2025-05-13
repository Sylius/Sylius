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
  static targets = ['select'];

  connect() {
    console.log('--------');
    console.log('TomSelectValidationController connected');
    console.log('--------');
    this.element.addEventListener('submit', () => this.propagate(), true);
    this.selectTargets.forEach(select => {
      select.addEventListener('input',  () => this.propagate());
      select.addEventListener('change', () => this.propagate());
    });
  }

  propagate() {
    console.log('--------');
    console.log('TomSelectValidationController propagation');
    console.log('--------');

    this.selectTargets.forEach(select => {
      const wrapper = select.closest('.ts-wrapper');
      const control = wrapper?.querySelector('.ts-control');
      if (!wrapper || !control) return;

      if (select.classList.contains('is-invalid')) {
        wrapper.classList.add('is-invalid');
        control.classList.add('is-invalid');
      } else {
        wrapper.classList.remove('is-invalid');
        control.classList.remove('is-invalid');
      }
    });
  }
}
