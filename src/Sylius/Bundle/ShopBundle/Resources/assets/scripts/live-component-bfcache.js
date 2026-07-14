/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Safari (macOS/iOS) keeps pages in its back/forward cache (bfcache). When a
 * Live Component action ends with a redirect (e.g. "add to cart"), the browser
 * navigates away while the component's loading overlay is still visible. On a
 * back navigation Safari restores the frozen page as-is, so the overlay stays
 * stuck forever and the aborted request surfaces as "TypeError: Load failed".
 * Reloading the restored page reinitializes the Live Component in a clean,
 * idle state.
 *
 * See https://github.com/Sylius/Sylius/issues/18547
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
