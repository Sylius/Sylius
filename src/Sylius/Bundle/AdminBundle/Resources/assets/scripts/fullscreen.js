/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

document.querySelectorAll('[data-fullscreen-btn]').forEach(btn => {
    btn.addEventListener('click', () => {
        const containerId = btn.getAttribute('data-fullscreen-btn');
        const container = document.querySelector(`[data-fullscreen-container="${containerId}"]`);

        if (!container) {
            console.error(`Fullscreen container with ID "${containerId}" not found`);
            return;
        }

        if (!document.fullscreenElement) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    });
});
