/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* global document */

function syliusCheckAll(trigger) {
    const groupName = trigger.getAttribute('data-check-all');
    const groupItems = Array.from(document.querySelectorAll(`[data-check-all-group="${groupName}"]`));
    const actionButtons = Array.from(document.querySelectorAll(`[data-check-all-action="${groupName}"]`));

    trigger.addEventListener('change', () => {
        const checked = groupItems.some(item => item.checked);
        trigger.checked = !checked;
        groupItems.forEach(item => item.checked = !checked);
        actionButtonsRefresh();
    });

    groupItems.forEach((item) => {
        item.addEventListener('change', () => {
            switch (groupItems.filter((filteredItem) => filteredItem.checked).length) {
                case groupItems.length:
                    trigger.indeterminate = false;
                    trigger.checked = true;
                    break;
                case 0:
                    trigger.indeterminate = false;
                    trigger.checked = false;
                    break;
                default:
                    trigger.indeterminate = true;
                    trigger.checked = false;
                    break;
            }
            actionButtonsRefresh();
        });
    });

    const actionButtonsRefresh = () => {
        const isAnyChecked = groupItems.some((item) => item.checked);
        actionButtons.forEach((btn) => {
            btn.disabled = !isAnyChecked;
        });
    };
}

(function() {
    document.querySelectorAll('[data-check-all]').forEach(syliusCheckAll);
}());
