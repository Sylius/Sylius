/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* global document */

(function() {
  const menuSearchInput = document.querySelector('[data-menu-search]');
  const menuClearButton = document.querySelector('[data-menu-search-clear]');

  const clearInput = () => {
    menuSearchInput.value = '';
    menuSearchInput.dispatchEvent(new Event('input'));
  };

  if (menuSearchInput) {
    menuSearchInput.addEventListener('input', function(e) {
      const query = e.target.value.toLowerCase();
      const navItems = document.querySelectorAll('.nav-item');

      navItems.forEach(navItem => {
        const navLink = navItem.querySelector('.nav-link');
        const dropdownMenu = navItem.querySelector('.dropdown-menu');
        const dropdownItems = navItem.querySelectorAll('.dropdown-item');
        let matchFound = false;

        dropdownItems.forEach(item => {
          const text = item.textContent.toLowerCase();
          if (query === '' || text.includes(query)) {
            item.style.display = '';
            matchFound = true;
          } else {
            item.style.display = 'none';
          }
        });

        if (matchFound || query === '') {
          navItem.style.display = '';
        } else {
          navItem.style.display = 'none';
        }

        if (query !== '') {
          if (navLink) navLink.classList.add('d-flex');
          if (dropdownMenu) dropdownMenu.classList.add('d-flex');
        } else {
          if (navLink) navLink.classList.remove('d-flex');
          if (dropdownMenu) dropdownMenu.classList.remove('d-flex');
        }
      });
    });

    menuSearchInput.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        clearInput();
      }
    });
  }

  if (menuClearButton) {
    menuClearButton.addEventListener('click', function() {
      clearInput();
    });
  }
})();
