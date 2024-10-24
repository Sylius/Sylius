/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

window.addEventListener('sylius_admin:product:form:attributed_deleted', () => {
  let tabs = document.getElementById('product_attribute_tabs');
  let firstTab = tabs.querySelector('.list-group-item');

  window.bootstrap.Tab.getOrCreateInstance(firstTab).show();
});
