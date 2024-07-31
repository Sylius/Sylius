/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* global document */

function syliusBulkDelete(form) {
    const groupName = form.getAttribute('data-bulk-delete');
    const groupItems = Array.from(document.querySelectorAll(`input[data-check-all-group="${groupName}"]`));

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        groupItems.forEach((item) => {
            if (item.checked) {
                const newItem = document.createElement('input');
                newItem.setAttribute('type', 'hidden');
                newItem.setAttribute('name', 'ids[]');
                newItem.setAttribute('value', item.value);

                form.appendChild(newItem);
            }
        });
        e.target.submit();
    });
}

(function () {
    document.querySelectorAll('[data-bulk-delete]').forEach(syliusBulkDelete);
}());
