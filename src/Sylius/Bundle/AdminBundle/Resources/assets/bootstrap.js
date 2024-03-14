/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {startStimulusApp} from '@symfony/stimulus-bridge';
import LiveController from '@symfony/ux-live-component';
import '@symfony/ux-live-component/styles/live.css';
import SlugController from "./controllers/SlugController";
import TaxonSlugController from "./controllers/TaxonSlugController";
import ProductAttributeAutocomplete from "./controllers/ProductAttributeAutocomplete";

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
  '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
  true,
  /\.[jt]sx?$/
));

app.register('live', LiveController);
app.register('slug', SlugController);
app.register('taxon-slug', TaxonSlugController);
app.register('product-attribute-autocomplete', ProductAttributeAutocomplete);
