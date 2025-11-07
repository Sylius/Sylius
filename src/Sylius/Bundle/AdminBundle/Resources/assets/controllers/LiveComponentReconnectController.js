/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { ApplicationController } from 'stimulus-use';
import { getComponent } from '@symfony/ux-live-component';

/**
 * Re-connects Stimulus controllers after LiveComponent re-renders.
 *
 * Problem:
 * When LiveComponent adds new DOM elements (e.g., adding a scope/action with autocomplete),
 * Stimulus controllers don't automatically connect to these dynamically added elements.
 * This causes tomselect to never initialize, resulting in test timeouts.
 *
 * Solution:
 * Hook into LiveComponent's render:finished event and trigger Stimulus to re-scan
 * the DOM for new controllers.
 *
 * Usage:
 * This controller automatically attaches to <body> and scans for all LiveComponents.
 * Add to any element that contains LiveComponents (typically body):
 * <body data-controller="livecomponent-reconnect">
 */
export default class extends ApplicationController {
    async connect() {
        // Find all LiveComponent elements (they have data-controller="live")
        const liveComponents = this.element.querySelectorAll('[data-controller~="live"]');

        for (const element of liveComponents) {
            try {
                const component = await getComponent(element);

                component.on('render:finished', () => {
                    // Use requestAnimationFrame to ensure DOM is fully settled before re-scanning
                    requestAnimationFrame(() => {
                        // Re-scan DOM for new Stimulus controllers (e.g., autocomplete with tomselect)
                        this.application.load();
                    });
                });
            } catch (error) {
                // Silently skip if element is not a LiveComponent
                // (might have 'live' in data-controller for other reasons)
            }
        }
    }
}
