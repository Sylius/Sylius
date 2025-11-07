/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import '@sylius/admin-bundle/entrypoint';

import {startStimulusApp} from '@symfony/stimulus-bridge';
import { getComponent } from '@symfony/ux-live-component';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

app.debug = process.env.NODE_ENV !== 'production';

/**
 * Re-connect Stimulus controllers after LiveComponent re-renders.
 *
 * Problem: When LiveComponent adds new DOM elements (e.g., scope/action with autocomplete),
 * Stimulus controllers don't automatically connect to these dynamically added elements.
 *
 * Solution: Hook into LiveComponent's render:finished event using proper API
 * and trigger Stimulus to re-scan the DOM.
 *
 * Note: This code is in app.js instead of a separate controller because CI uses
 * cached assets and a new controller file wouldn't be included in the bundle.
 */
document.addEventListener('DOMContentLoaded', async () => {
    // Find all LiveComponent elements
    const liveElements = document.querySelectorAll('[data-controller~="live"]');

    for (const element of liveElements) {
        try {
            const component = await getComponent(element);

            // Use proper LiveComponent API: component.on('render:finished', callback)
            component.on('render:finished', () => {
                // Use requestAnimationFrame to ensure DOM is fully settled
                requestAnimationFrame(() => {
                    // Re-scan DOM for new Stimulus controllers (e.g., autocomplete with tomselect)
                    app.load();
                });
            });
        } catch (error) {
            // Silently skip if element is not a LiveComponent
        }
    }
});
