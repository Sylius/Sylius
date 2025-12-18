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
    static values = { url: String };
    static targets = ['email', 'password', 'csrfToken', 'error', 'errorPrototype'];

    login() {
        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                _username: this.emailTarget.value,
                _password: this.passwordTarget.value,
                [this.csrfTokenTarget.name]: this.csrfTokenTarget.value
            })
        };

        fetch(this.urlValue, requestOptions)
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    window.location.reload();
                } else {
                    const errorElement = this.errorPrototypeTarget.cloneNode(true);
                    errorElement.innerHTML = response.message;
                    this.errorTarget.innerHTML = errorElement.outerHTML;
                }
            })
        ;
    }
}
