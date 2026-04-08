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
    static targets = ['source', 'controlled'];

    connect() {
        this.toggle();
    }

    toggle() {
        const checkbox = this.sourceTarget.querySelector('input[type="checkbox"]');
        const isEnabled = checkbox?.checked ?? true;

        console.log(isEnabled);
        this.controlledTargets.forEach((el) => {
          console.log(el);
            el.querySelectorAll('input, select, textarea').forEach((input) => {
                input.disabled = !isEnabled;
            });
            el.classList.toggle('opacity-50', !isEnabled);
        });
    }
}
