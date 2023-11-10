/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function syliusTree(tree) {
  const toggles = tree.querySelectorAll('[data-js-tree="toggle"]');
  const toggleAllButton = tree.querySelector('[data-js-tree="toggle-all"]');

  const syliusTreeToggle = function(trigger, open) {
    const wrapper = trigger.parentElement;
    const list = trigger.nextElementSibling;

    trigger.classList.remove(open ? 'collapsed' : 'expanded');
    trigger.classList.add(open ? 'expanded' : 'collapsed');
    wrapper.classList.remove(open ? 'collapsed' : 'expanded');
    wrapper.classList.add(open ? 'expanded' : 'collapsed');
    list.style.display = open ? 'block' : 'none';
  }

  toggles.forEach(function(toggle) {
    const toggleBtn = toggle.querySelector('[data-js-tree="toggle-btn"]');
    syliusTreeToggle(toggle, true);

    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        syliusTreeToggle(toggle, toggle.nextElementSibling.style.display === 'none');
      });
    }
  });

  if (toggleAllButton) {
    toggleAllButton.addEventListener('click', () => {
      const isAnyClosed = tree.querySelectorAll('.collapsed').length > 0;

      toggles.forEach(function(toggle) {
        syliusTreeToggle(toggle, isAnyClosed);
      });
    })
  }
}

(function() {
  document.querySelectorAll('[data-js-tree="container"]').forEach(syliusTree);
})();
