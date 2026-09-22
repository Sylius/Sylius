/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Safari bfcache restores the page mid-redirect, freezing the Live Component's loading overlay
 * and surfacing the aborted request as "TypeError: Load failed".
 */
window.addEventListener('pageshow', (event) => {
    if (!event.persisted) {
        return;
    }

    const isLoaderStuck = Array.from(document.querySelectorAll('[data-loading]'))
        .some((element) => window.getComputedStyle(element).display !== 'none');

    if (isLoaderStuck) {
        window.location.reload();
    }
});
