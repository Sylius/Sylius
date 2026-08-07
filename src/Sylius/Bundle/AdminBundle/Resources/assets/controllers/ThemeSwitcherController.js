/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    toggle() {
        const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        try {
            localStorage.setItem('sylius-admin-theme', next);
        } catch {
            // storage unavailable (private mode, quota exceeded)
        }
        document.documentElement.setAttribute('data-bs-theme', next);
    }
}
