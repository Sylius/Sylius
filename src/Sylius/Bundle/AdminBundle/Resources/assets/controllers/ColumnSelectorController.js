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
    static targets = ['column'];

    static values = {
        parameter: String
    };

    connect() {
        this.refresh();
    }

    toggle(event) {
        event.preventDefault();

        const column = event.currentTarget;
        if (column.classList.contains('disabled')) {
            return;
        }

        this.setVisible(column, !this.isVisible(column));
        this.refresh();
    }

    apply(event) {
        const url = this.buildUrl();
        if (this.normalize(url) === this.normalize(new URL(window.location.href))) {
            return;
        }

        // The click closing the dropdown may target a link or a submit button, whose navigation would cancel ours
        event.clickEvent?.preventDefault();

        window.location.assign(url.toString());
    }

    buildUrl() {
        const url = new URL(window.location.href);
        const keys = new Set(url.searchParams.keys());

        keys.forEach((key) => {
            if (key === this.parameterValue || key.startsWith(`${this.parameterValue}[`)) {
                url.searchParams.delete(key);
            }
        });

        this.columnTargets
            .filter((column) => String(this.isVisible(column)) !== column.dataset.defaultVisible)
            .forEach((column) => url.searchParams.append(
                `${this.parameterValue}[${column.dataset.column}]`,
                this.isVisible(column) ? 'visible' : 'hidden'
            ));

        return url;
    }

    normalize(url) {
        const normalizedUrl = new URL(url);
        normalizedUrl.searchParams.sort();

        return normalizedUrl.toString();
    }

    isVisible(column) {
        return column.getAttribute('aria-checked') === 'true';
    }

    setVisible(column, isVisible) {
        column.setAttribute('aria-checked', String(isVisible));
        column.querySelector('[data-visible-icon]').classList.toggle('d-none', !isVisible);
        column.querySelector('[data-hidden-icon]').classList.toggle('d-none', isVisible);
    }

    refresh() {
        const visibleColumns = this.columnTargets.filter((column) => this.isVisible(column));

        this.columnTargets.forEach((column) => {
            const isLastVisibleColumn = visibleColumns.length === 1 && visibleColumns[0] === column;

            column.classList.toggle('disabled', isLastVisibleColumn);
            if (isLastVisibleColumn) {
                column.setAttribute('aria-disabled', 'true');
                column.setAttribute('tabindex', '-1');
            } else {
                column.removeAttribute('aria-disabled');
                column.removeAttribute('tabindex');
            }
        });
    }
}
