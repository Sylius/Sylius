/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

document.addEventListener('show.bs.modal', function (event) {
    if (event.target.parentElement !== document.body) {
        document.body.appendChild(event.target);
    }
});
