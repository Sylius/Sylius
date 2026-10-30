<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%/taxons/{taxonId}')
    ->withClass('%sylius.model.product_taxon.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_product_taxon')")
    ->withOperations(operations: new Operations(operations: [
        new Index(
            path: 'products/',
            routeName: '_sylius_admin_product_taxon_index',
            grid: 'sylius_admin_product_taxon',
        ),
    ]))
;
