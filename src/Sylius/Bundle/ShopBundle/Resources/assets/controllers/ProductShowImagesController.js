/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* global Spotlight */

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    initialize() {
        const mainImage = document.getElementById('main-image');
        const thumbnails = mainImage.closest('.spotlight-group').querySelectorAll('.spotlight');
        mainImage.addEventListener('click', (e) => {
            e.preventDefault();
            Spotlight.show(thumbnails, 0);
        });
    }
}
