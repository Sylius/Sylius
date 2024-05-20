/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* global document */

(function () {
    const dialog = document.querySelector('[data-confirm-dialog]');

    if (dialog) {
        const confirmButton = dialog.querySelector('[data-confirm-btn-true]');
        const cancelButton = dialog.querySelector('[data-confirm-btn-false]');
        const submitButton = document.querySelectorAll('[data-confirm-btn-submit]');

        submitButton.forEach((btn) => {
            const confirm = () => {
                dialog.close();
                confirmButton.removeEventListener('click', confirm);
                cancelButton.removeEventListener('click', cancel);
                btn.closest('form').requestSubmit();
            };

            const cancel = () => {
                dialog.close();
                confirmButton.removeEventListener('click', confirm);
                cancelButton.removeEventListener('click', cancel);
            };

            btn.addEventListener('click', (e) => {
                e.preventDefault();

                confirmButton.addEventListener('click', confirm);
                cancelButton.addEventListener('click', cancel);

                dialog.showModal();
            });
        });
    }
}());
