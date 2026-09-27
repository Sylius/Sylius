/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';
import Viewer from 'viewerjs';
import 'viewerjs/dist/viewer.css';

export default class extends Controller {
    connect() {
        const galleryLength = this.element.querySelectorAll('a.spotlight').length;
        if (galleryLength === 0) {
            return;
        }

        this.viewer = new Viewer(this.element, {
            filter: (image) => image.closest('a.spotlight') !== null,
            url: (image) => image.closest('a.spotlight').getAttribute('href'),
            navbar: galleryLength > 1,
            title: false,
            rotatable: false,
            scalable: false,
            toolbar: {
                prev: galleryLength > 1,
                next: galleryLength > 1,
                zoomIn: true,
                zoomOut: true,
                oneToOne: true,
                reset: true
            }
        });

        this._onClick = (event) => {
            if (event.target.closest('a.spotlight')) {
                event.preventDefault();

                return;
            }

            if (event.target.closest('#main-image')) {
                event.preventDefault();

                this.viewer.view(0);
            }
        };

        this.element.addEventListener('click', this._onClick);
    }

    disconnect() {
        if (this._onClick) {
            this.element.removeEventListener('click', this._onClick);
            this._onClick = null;
        }

        if (this.viewer) {
            this.viewer.destroy();
            this.viewer = null;
        }
    }
}
