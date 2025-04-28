/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// eslint-disable-next-line no-unused-vars
import * as bootstrap from 'bootstrap';

// Fix dropdowns
(() => {
    document.querySelectorAll('.dropdown-static').forEach((dropdownToggleEl) => {
        const parent = dropdownToggleEl.closest('[data-bs-toggle="dropdown"]');
        if (parent) {
            new bootstrap.Dropdown(parent, {
                popperConfig(defaultBsPopperConfig) {
                    return { ...defaultBsPopperConfig, strategy: 'fixed' };
                }
            });
        }
    });
})();

// Initialize tooltips
(() => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((tooltipTriggerEl) => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
})();

window.bootstrap = bootstrap;
