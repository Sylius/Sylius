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
    static targets = ['modal'];

    connect() {
        this.handleProductAdded = this.handleProductAdded.bind(this);
        this.modal = new window.bootstrap.Modal(this.modalTarget);
        document.addEventListener('sylius:shop:product_added_to_cart', this.handleProductAdded);
    }

    disconnect() {
        document.removeEventListener('sylius:shop:product_added_to_cart', this.handleProductAdded);
        this.modal.dispose();
    }

    handleProductAdded() {
        this.modal.show();
    }
}
