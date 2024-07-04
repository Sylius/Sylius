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
import TaxonTree from "./controllers/TaxonTreeController";
import DeleteTaxon from "./controllers/DeleteTaxonController";
import ProductAttributeAutocomplete from "./controllers/ProductAttributeAutocomplete";
import ProductTaxonTree from "./controllers/ProductTaxonTreeController";
import SavePositionsController from "./controllers/SavePositionsController";
import CompoundFormErrorsController from "./controllers/CompoundFormErrorsController";

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
  '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
  true,
  /\.[jt]sx?$/
));

app.register('live', LiveController);
app.register('slug', SlugController);
app.register('taxon-slug', TaxonSlugController);
app.register('taxon-tree', TaxonTree);
app.register('delete-taxon', DeleteTaxon);
app.register('product-attribute-autocomplete', ProductAttributeAutocomplete);
app.register('product-taxon-tree', ProductTaxonTree);
app.register('save-positions', SavePositionsController);
app.register('compound-form-errors', CompoundFormErrorsController);
