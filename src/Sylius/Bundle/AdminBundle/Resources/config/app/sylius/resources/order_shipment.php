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
use Sylius\Bundle\CoreBundle\StateMachine\State\ApplyStateMachineTransitionProcessor;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%/orders/{orderId}/shipment/{id}')
    ->withClass('%sylius.model.shipment.class%')
    ->withSection('admin')
    ->withFormType(ShipmentShipType::class)
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_order_shipment')")
    ->withOperations(operations: new Operations(operations: [
        new Update(
            path: 'ship',
            routeName: '_sylius_admin_order_shipment_ship',
            processor: ApplyStateMachineTransitionProcessor::class,
            repositoryMethod: 'findOneByOrderId',
            repositoryArguments: [
                'shipmentId' => "request.attributes.get('id')",
                'orderId' => "request.attributes.get('orderId')",
            ],
            eventShortName: 'ship',
            redirectToRoute: 'sylius_admin_order_show',
            redirectArguments: ['id' => "@=request.attributes.get('orderId')"],
            vars: [
                'route' => [
                    'parameters' => [
                        'orderId' => "@=request.attributes.get('orderId')",
                        'id' => "@=request.attributes.get('id')",
                    ],
                ],
            ],
            stateMachineTransition: 'ship',
            stateMachineGraph: 'sylius_shipment',
        ),
    ]))
;
