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

use Sylius\Bundle\AdminBundle\Form\Type\OrderType;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.order.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withFormType(OrderType::class)
    ->withOperations(new Operations(operations: [
        new Index(grid: 'sylius_admin_order'),
        new Show(
            repositoryMethod: 'findOrderById',
        ),
        new Show(
            template: '@SyliusAdmin/order/history.html.twig',
            shortName: 'history',
            repositoryMethod: 'findOrderById',
        ),
        new Update(
            repositoryMethod: 'findOrderById',
            formOptions: ['validation_groups' => ['sylius_shipping_address_update']],
        ),
        new ApplyStateMachineTransition(
            repositoryMethod: 'findOrderById',
            notificationMessage: 'sylius.resource.update',
            redirectToRoute: 'sylius_admin_order_show',
            stateMachineTransition: 'cancel',
            stateMachineGraph: 'sylius_order',
        ),
    ]))
;
