/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';

/**
 * Toggles the sidebar from outside of it (e.g. from the navbar).
 */
export default class extends Controller {
    toggle(event) {
        event.preventDefault();

        window.dispatchEvent(new CustomEvent('sidebar:toggle'));
    }
}
