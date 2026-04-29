/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function() {
    const el = document.querySelector('[data-sticky-header]');

    const observer = new IntersectionObserver(
        ([e]) => e.target.classList.toggle('is-sticky', e.intersectionRatio < 1),
        { threshold: [1] }
    );

    if (el) {
        observer.observe(el);
    }
})();
