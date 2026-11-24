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

use Sylius\Bundle\AdminBundle\Form\Type\ShipmentShipType;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.shipment.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_shipment')")
    ->withOperations(operations: new Operations(operations: [
        new Index(
            routeName: '_sylius_admin_shipment_index',
            grid: 'sylius_admin_shipment',
        ),
        new Show(
            routeName: '_sylius_admin_shipment_show',
        ),
        new ApplyStateMachineTransition(
            routeName: '_sylius_admin_shipment_ship',
            formType: ShipmentShipType::class,
            eventShortName: 'ship',
            notificationMessage: 'sylius.shipment.shipped',
            redirectToRoute: 'sylius_admin_shipment_index',
            stateMachineTransition: 'ship',
            stateMachineGraph: 'sylius_shipment',
        ),
    ]))
;
