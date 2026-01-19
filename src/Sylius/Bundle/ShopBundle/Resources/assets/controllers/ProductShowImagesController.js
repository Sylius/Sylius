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
    connect() {
        const mainImage = this.element.querySelector('#main-image');
        if (!mainImage) {
            return;
        }

        this._onMainImageClick = (e) => {
            const thumbnails = this.element.querySelectorAll('.spotlight');
            if (!thumbnails || thumbnails.length === 0 || typeof Spotlight === 'undefined') {
                return;
            }

            e.preventDefault();

            Spotlight.show(thumbnails, 0);
        };

        mainImage.addEventListener('click', this._onMainImageClick);
    }

    disconnect() {
        const mainImage = this.element.querySelector('#main-image');
        if (mainImage && this._onMainImageClick) {
            mainImage.removeEventListener('click', this._onMainImageClick);
        }
    }
}
