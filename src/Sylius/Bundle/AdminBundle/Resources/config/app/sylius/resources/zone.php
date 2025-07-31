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

use Sylius\Bundle\AdminBundle\Form\Type\ZoneType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.zone.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(ZoneType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_zone')")
    ->withOperations(new Operations(operations: [
        new Update(
            routeName: '_sylius_admin_zone_update',
            redirectToRoute: 'sylius_admin_zone_update',
        ),
        new Delete(
            routeName: '_sylius_admin_zone_delete',
            redirectToRoute: 'sylius_admin_zone_index',
        ),
        new BulkDelete(
            routeName: '_sylius_admin_zone_bulk_delete',
            redirectToRoute: 'sylius_admin_zone_index',
        ),
        new Index(
            routeName: '_sylius_admin_zone_index',
            grid: 'sylius_admin_zone',
        ),
        new Create(
            path: 'zones/{type}/new',
            routeName: '_sylius_admin_zone_create',
            routeRequirements: ['type' => 'country|province|zone'],
            factoryMethod: 'createTyped',
            factoryArguments: ["request.attributes.get('type')"],
            redirectToRoute: 'sylius_admin_zone_update',
        ),
    ]))
;
