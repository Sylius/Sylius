/* eslint-env browser */
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
