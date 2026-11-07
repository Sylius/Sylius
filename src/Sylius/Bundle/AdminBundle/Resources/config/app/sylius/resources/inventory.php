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
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.product_variant.class%')
    ->withSection('admin')
    ->withTemplatesDir("@SyliusAdmin/shared/crud")
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_inventory')")
    ->withOperations(new Operations(operations: [
        new Index(
            path: 'inventory/',
            routeName: '_sylius_admin_inventory_index',
            template: "@SyliusAdmin/inventory/index.html.twig",
            shortName: 'inventory',
            grid: 'sylius_admin_inventory'
        ),
    ]))
;
