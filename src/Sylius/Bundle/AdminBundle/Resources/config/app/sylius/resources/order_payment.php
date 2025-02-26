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
    ->withRoutePrefix('/admin/orders/{orderId}/payments/{id}')
    ->withClass('%sylius.model.payment.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withOperations(operations: new Operations(operations: [
        new ApplyStateMachineTransition(
            path: 'complete',
            routeName: 'sylius_admin_order_payment_complete',
            repositoryMethod: 'findOneByOrderId',
            repositoryArguments: [
                'paymentId' => "request.attributes.get('id')",
                'orderId' => "request.attributes.get('orderId')",
            ],
            eventShortName: 'complete',
            notificationMessage: 'sylius.resource.update',
            redirectToRoute: 'sylius_admin_order_show',
            redirectArguments: ['id' => "request.attributes.get('orderId')"],
            stateMachineTransition: 'complete',
            stateMachineGraph: 'sylius_payment'
        ),
        new ApplyStateMachineTransition(
            path: 'refund',
            routeName: 'sylius_admin_order_payment_refund',
            repositoryMethod: 'findOneByOrderId',
            repositoryArguments: [
                'paymentId' => "request.attributes.get('id')",
                'orderId' => "request.attributes.get('orderId')",
            ],
            notificationMessage: 'sylius.payment.refunded',
            redirectToRoute: 'sylius_admin_order_show',
            redirectArguments: ['id' => "request.attributes.get('orderId')"],
            stateMachineTransition: 'refund',
            stateMachineGraph: 'sylius_payment'
        ),
    ]))
;
