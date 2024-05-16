/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    tabErrorBadgeClass = 'tab-error';
    accordionErrorBadgeClass = 'accordion-error';
    observer;

    initialize() {
        super.initialize();

        this.observer = new MutationObserver(() => {
            this.reloadBadges();
        });
    }

    connect() {
        super.connect();

        this.updateFormErrors();
        this.observe();
    }

    disconnect() {
        super.disconnect();

        this.observer.disconnect();
    }

    observe() {
        this.observer.observe(this.element, { attributes: false, childList: true, subtree: true });
    }

    reloadBadges() {
        this.observer.disconnect();
        this.clearBadges();
        this.updateFormErrors();
        this.observe();
    }

    clearBadges() {
        this.element.querySelectorAll(".tab-error").forEach(el => el.remove());
        this.element.querySelectorAll(".accordion-error").forEach(el => el.remove());
    }

    updateFormErrors() {
        this.updateErrorBadges(this.element.querySelector('[role="tablist"]'), this.tabErrorBadgeClass);
        this.updateErrorBadges(this.element.querySelector('div.accordion'), this.accordionErrorBadgeClass);
    }

    updateErrorBadges(controlElementsContainer, badgeClass) {
        if (null === controlElementsContainer) {
            return;
        }

        const document = controlElementsContainer.ownerDocument;
        controlElementsContainer.querySelectorAll('button[type="button"][data-bs-toggle]').forEach((controlElement) => {
            const errorsCount = this.countErrors(controlElement);
            if (errorsCount > 0) {
                const errorElement = document.createElement('div');
                errorElement.classList.add(badgeClass);
                errorElement.innerText = errorsCount.toString();
                controlElement.appendChild(errorElement);
            }
        });
    }

    countErrors(element) {
        const elementTarget = this.element.querySelector(element.getAttribute('data-bs-target'));

        return elementTarget.querySelectorAll('.is-invalid').length +
            elementTarget.querySelectorAll('.alert-danger').length;
    }
}
