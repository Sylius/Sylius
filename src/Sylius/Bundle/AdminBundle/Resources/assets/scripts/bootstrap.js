import * as bootstrap from 'bootstrap';

(function () {
  const dropdowns = document.querySelectorAll('.dropdown-toggle.dropdown-static');
  const dropdown = [...dropdowns].map((dropdownToggleEl) => new bootstrap.Dropdown(dropdownToggleEl, {
    popperConfig(defaultBsPopperConfig) {
      return { ...defaultBsPopperConfig, strategy: 'fixed' };
    },
  }));
}());

window.bootstrap = bootstrap;
