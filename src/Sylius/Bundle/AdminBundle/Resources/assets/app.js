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

// Debug logging to window object (avoid console.log for linter)
window.__liveComponentDebug = window.__liveComponentDebug || { logs: [], events: 0 };

function debugLog(message, ...args) {
    const timestamp = new Date().toISOString();
    const logEntry = { timestamp, message, args };
    window.__liveComponentDebug.logs.push(logEntry);

    // Also log to error_log for PHP to capture
    if (window.logToPhp) {
        window.logToPhp(message, ...args);
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    debugLog('[LIVECOMPONENT-DEBUG] DOMContentLoaded fired');

    // Find all LiveComponent elements
    const liveElements = document.querySelectorAll('[data-controller~="live"]');
    debugLog('[LIVECOMPONENT-DEBUG] Found', liveElements.length, 'LiveComponent elements');

    if (liveElements.length === 0) {
        debugLog('[LIVECOMPONENT-DEBUG] WARNING: No LiveComponent elements found');
        const allControllers = document.querySelectorAll('[data-controller]');
        debugLog('[LIVECOMPONENT-DEBUG] Total elements with data-controller:', allControllers.length);
    }

    for (const element of liveElements) {
        const elementInfo = element.getAttribute('data-live-name-value') || element.id || element.className;
        debugLog('[LIVECOMPONENT-DEBUG] Processing element:', elementInfo);

        try {
            const component = await getComponent(element);
            debugLog('[LIVECOMPONENT-DEBUG] getComponent() succeeded for:', elementInfo);

            // Use proper LiveComponent API: component.on('render:finished', callback)
            component.on('render:finished', () => {
                window.__liveComponentDebug.events++;
                debugLog('[LIVECOMPONENT-DEBUG] render:finished event fired for:', elementInfo);

                // Use requestAnimationFrame to ensure DOM is fully settled
                requestAnimationFrame(() => {
                    debugLog('[LIVECOMPONENT-DEBUG] Calling app.load() to re-scan Stimulus controllers');

                    // Re-scan DOM for new Stimulus controllers (e.g., autocomplete with tomselect)
                    app.load();

                    debugLog('[LIVECOMPONENT-DEBUG] app.load() completed');
                });
            });
        } catch (error) {
            debugLog('[LIVECOMPONENT-DEBUG] ERROR: Failed to getComponent() for:', elementInfo, error.message);
        }
    }

    debugLog('[LIVECOMPONENT-DEBUG] Setup complete');
});
